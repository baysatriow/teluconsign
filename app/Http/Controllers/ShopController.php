<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Category;
use App\Enums\ProductStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;

class ShopController extends Controller
{
    /* ======================================================================
     * DASHBOARD & STORE MANAGEMENT
     * ====================================================================== */

    public function index()
    {
        $user = User::find(Auth::id());

        if ($user->role !== 'seller') {
            return view('shop.onboarding');
        }

        $userId = $user->user_id;

        $stats = [
            'new' => Order::where('seller_id', $userId)->where('status', 'pending')->count(),
            'shipping' => Order::where('seller_id', $userId)->where('status', 'shipped')->count(),
            'completed' => Order::where('seller_id', $userId)->where('status', 'completed')->count(),
            'total_sales' => Order::where('seller_id', $userId)->where('status', 'completed')->sum('subtotal_amount'),
        ];

        $products = Product::where('seller_id', $userId)
            ->where('status', ProductStatus::Active)
            ->latest()
            ->paginate(10, ['*'], 'active_page');

        $drafts = Product::where('seller_id', $userId)
            ->whereIn('status', [ProductStatus::Archived, ProductStatus::Suspended, ProductStatus::Sold])
            ->latest()
            ->paginate(10, ['*'], 'draft_page');

        return view('shop.index', compact('stats', 'products', 'drafts'));
    }

    public function registerStore()
    {
        $user = User::find(Auth::id());

        if ($user->role === 'buyer') {
            $user->update(['role' => 'seller']);
            return redirect()->route('shop.index')->with('success', 'Selamat! Toko Anda berhasil dibuat. Mulai jualan sekarang!');
        }

        return redirect()->route('shop.index');
    }

    public function orders()
    {
        $orders = Order::with(['buyer', 'items'])
            ->where('seller_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('shop.orders.index', compact('orders'));
    }


    /* ======================================================================
     * PRODUCT CRUD
     * ====================================================================== */

    public function createProduct()
    {
        if (Auth::user()->role !== 'seller') {
            return redirect()->route('shop.index')->with('error', 'Anda harus menjadi penjual untuk mengakses halaman ini.');
        }

        $categories = Category::all();

        return view('shop.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:160',
            'category_id' => 'required|exists:categories,category_id',
            'price'       => 'required|numeric|min:100',
            'stock'       => 'required|numeric|min:1',
            'condition'   => 'required|in:new,used',
            'description' => 'required|string',
            'status_input'=> ['required', 'in:active,archived'],
            'images'      => 'required|array|min:1|max:5',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'images.required' => 'Wajib mengupload minimal 1 foto produk.',
            'images.max'      => 'Maksimal 5 foto produk.',
            'images.*.image'  => 'File harus berupa gambar.',
            'images.*.max'    => 'Ukuran foto maksimal 2MB per file.',
        ]);

        try {
            DB::beginTransaction();

            $sellerId = Auth::id();
            $mainImagePath = null;

            $statusEnum = $request->status_input === 'active' ? ProductStatus::Active : ProductStatus::Archived;

            $product = Product::create([
                'seller_id'   => $sellerId,
                'category_id' => $request->category_id,
                'title'       => $request->title,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'condition'   => $request->condition,
                'status'      => $statusEnum,
                'main_image'  => null,
                'location'    => Auth::user()->addresses()->where('is_default', true)->value('city') ?? 'Indonesia',
            ]);

            if ($request->hasFile('images')) {
                $folderName = 'uploads/products';
                if (!Storage::disk('public')->exists($folderName)) {
                    Storage::disk('public')->makeDirectory($folderName);
                }

                foreach ($request->file('images') as $index => $file) {
                    $filename = time() . '_' . $index . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs($folderName, $filename, 'public');

                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'url'        => $path,
                        'is_primary' => ($index === 0),
                        'sort_order' => $index,
                    ]);

                    if ($index === 0) {
                        $mainImagePath = $path;
                    }
                }
            }

            if ($mainImagePath) {
                $product->update(['main_image' => $mainImagePath]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $statusEnum === ProductStatus::Active ? 'Produk berhasil diterbitkan!' : 'Produk disimpan sebagai draft.',
                    'redirect_url' => route('shop.index')
                ]);
            }

            return redirect()->route('shop.index')->with('success', 'Produk berhasil diterbitkan!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan produk: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        }
    }

    public function editProduct($id)
    {
        $product = Product::with('images')
            ->where('seller_id', Auth::id())
            ->findOrFail($id);

        $categories = Category::all();

        return view('shop.products.edit', compact('product', 'categories'));
    }

    /**
     * UPDATE PRODUCT (FIXED)
     */
    public function updateProduct(Request $request, $id)
    {
        $product = Product::where('seller_id', Auth::id())->findOrFail($id);

        // Validasi
        // Perhatikan 'images' nullable saat update
        $request->validate([
            'title'       => 'required|string|max:160',
            'category_id' => 'required|exists:categories,category_id',
            'price'       => 'required|numeric|min:100',
            'stock'       => 'required|numeric|min:0',
            'condition'   => 'required|in:new,used',
            'description' => 'required|string',
            'status_input'=> ['required', 'in:active,archived'],
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $statusEnum = $request->status_input === 'active' ? ProductStatus::Active : ProductStatus::Archived;

            $product->update([
                'category_id' => $request->category_id,
                'title'       => $request->title,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'condition'   => $request->condition,
                'status'      => $statusEnum,
            ]);

            // Tambah Foto Baru (Jika Ada)
            if ($request->hasFile('images')) {
                $folderName = 'uploads/products';
                if (!Storage::disk('public')->exists($folderName)) {
                    Storage::disk('public')->makeDirectory($folderName);
                }

                $lastSort = $product->images()->max('sort_order') ?? 0;

                foreach ($request->file('images') as $index => $file) {
                    $filename = time() . '_u_' . $index . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs($folderName, $filename, 'public');

                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'url'        => $path,
                        'is_primary' => false,
                        'sort_order' => $lastSort + $index + 1,
                    ]);
                }
            }

            // Cek integritas Main Image
            if (!$product->main_image || !ProductImage::where('url', $product->main_image)->exists()) {
                $firstImg = $product->images()->orderBy('sort_order')->first();
                if ($firstImg) {
                    $product->update(['main_image' => $firstImg->url]);
                    $firstImg->update(['is_primary' => true]);
                }
            }

            DB::commit();

            // PENTING: Response JSON untuk AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produk berhasil diperbarui!',
                    'redirect_url' => route('shop.index')
                ]);
            }
            return redirect()->route('shop.index')->with('success', 'Produk diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Tangkap error dan return JSON jika AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal update: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::where('seller_id', Auth::id())->findOrFail($id);

        $hasOrders = OrderItem::where('product_id', $id)->exists();

        if ($hasOrders) {
            $msg = 'Produk tidak dapat dihapus karena ada riwayat transaksi. Arsipkan saja.';
            if(request()->ajax()) return response()->json(['status' => 'error', 'message' => $msg], 403);
            return back()->with('error', $msg);
        }

        foreach($product->images as $img) {
            if (Storage::disk('public')->exists($img->url)) {
                Storage::disk('public')->delete($img->url);
            }
            $img->delete();
        }

        $product->delete();

        if(request()->ajax()) return response()->json(['status' => 'success', 'message' => 'Produk dihapus.']);
        return back()->with('success', 'Produk dihapus.');
    }

    public function deleteProductImage($id)
    {
        $image = ProductImage::findOrFail($id);

        $product = Product::where('product_id', $image->product_id)
                          ->where('seller_id', Auth::id())
                          ->firstOrFail();

        if (Storage::disk('public')->exists($image->url)) {
            Storage::disk('public')->delete($image->url);
        }

        $image->delete();

        if ($product->main_image === $image->url) {
            $nextImg = $product->images()->first();
            if ($nextImg) {
                $product->update(['main_image' => $nextImg->url]);
                $nextImg->update(['is_primary' => true]);
            } else {
                $product->update(['main_image' => null]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Foto berhasil dihapus']);
    }
}

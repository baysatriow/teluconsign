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
     * PUBLIC SHOP PROFILE
     * ====================================================================== */
    public function show($id)
    {
        $seller = User::where('user_id', $id)->where('role', 'seller')->firstOrFail();
        
        $products = Product::where('seller_id', $seller->user_id)
            ->where('status', ProductStatus::Active)
            ->latest()
            ->paginate(12);

        // Stats (Optional)
        $totalSales = OrderItem::whereHas('product', function($q) use ($id) {
            $q->where('seller_id', $id);
        })->whereHas('order', function($q){
            $q->where('status', 'completed');
        })->count();

        // Rating dummy (or implement real rating logic)
        $rating = 4.8; 

        return view('shop.show', compact('seller', 'products', 'totalSales', 'rating'));
    }

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

        // Dashboard Stats
        $stats = [
            'new' => Order::where('seller_id', $userId)->where('status', 'pending')->count(),
            'shipping' => Order::where('seller_id', $userId)->where('status', 'shipped')->count(),
            'completed' => Order::where('seller_id', $userId)->where('status', 'completed')->count(),
            'total_sales' => Order::where('seller_id', $userId)->where('status', 'completed')->sum('subtotal_amount'),
            'low_stock' => Product::where('seller_id', $userId)->where('stock', '<=', 3)->count(),
        ];

        // Recent Orders (Limit 5)
        $recentOrders = Order::with('items')
            ->where('seller_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Chart Data (Last 7 Days)
        $chartData = Order::where('seller_id', $userId)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(subtotal_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();
            
        // Fill missing dates with 0
        $dates = collect();
        $totals = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates->push(now()->subDays($i)->format('d M'));
            $totals->push($chartData[$date] ?? 0);
        }

        return view('shop.index', compact('stats', 'recentOrders', 'dates', 'totals'));
    }

    /* ======================================================================
     * PRODUCTS MANAGEMENT
     * ====================================================================== */
    public function products()
    {
        $userId = Auth::id();

        $products = Product::where('seller_id', $userId)
            ->where('status', ProductStatus::Active)
            ->latest()
            ->paginate(10, ['*'], 'active_page');

        $drafts = Product::where('seller_id', $userId)
            ->whereIn('status', [ProductStatus::Archived, ProductStatus::Suspended, ProductStatus::Sold])
            ->latest()
            ->paginate(10, ['*'], 'draft_page');

        return view('shop.products.index', compact('products', 'drafts'));
    }

    /* ======================================================================
     * REPORTS
     * ====================================================================== */
    public function reports(Request $request)
    {
        $userId = Auth::id();
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Query Sales per Day in selected month
        $dailySales = Order::where('seller_id', $userId)
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('COUNT(*) as count'), 
                DB::raw('SUM(subtotal_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $dailySales->sum('revenue');
        $totalOrders = $dailySales->sum('count');

        return view('shop.reports.index', compact('dailySales', 'totalRevenue', 'totalOrders', 'month', 'year'));
    }

    /* ======================================================================
     * PAYOUTS (SALDO & PENARIKAN)
     * ====================================================================== */
    public function payouts()
    {
        $userId = Auth::id();
        $user = Auth::user();

        // 1. Calculate Balance
        $totalSales = Order::where('seller_id', $userId)->where('status', 'completed')->sum('seller_earnings');
        $totalPayouts = \App\Models\PayoutRequest::where('seller_id', $userId)
            ->whereIn('status', ['paid', 'approved', 'requested']) // Requested also deducts available balance visualization usually, but strictly speaking balance = income - paid. 
                                                                  // For simplicity: Available Balance = Total Earnings - (Paid + Processing Payouts)
            ->sum('amount');
        
        $currentBalance = $totalSales - $totalPayouts;

        // 2. Bank Accounts
        $bankAccounts = \App\Models\BankAccount::where('user_id', $userId)->get();

        // 3. Payout History
        $payouts = \App\Models\PayoutRequest::where('seller_id', $userId)->latest()->paginate(10);

        return view('shop.payouts.index', compact('currentBalance', 'bankAccounts', 'payouts'));
    }

    public function storePayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_account_id' => 'required|exists:bank_accounts,bank_account_id'
        ]);

        $userId = Auth::id();

        // Recalculate Balance to server-side validate
        $totalSales = Order::where('seller_id', $userId)->where('status', 'completed')->sum('seller_earnings');
        $totalPayouts = \App\Models\PayoutRequest::where('seller_id', $userId)
            ->whereIn('status', ['paid', 'approved', 'requested'])
            ->sum('amount');
        
        $availableBalance = $totalSales - $totalPayouts;

        if ($request->amount > $availableBalance) {
            return back()->with('error', 'Saldo tidak mencukupi.');
        }

        // Check Working Hours (Mon-Fri) - Optional soft check/warning
        $isWeekend = now()->isWeekend();
        
        \App\Models\PayoutRequest::create([
            'seller_id' => $userId,
            'amount' => $request->amount,
            'bank_account_id' => $request->bank_account_id,
            'status' => 'requested',
            'requested_at' => now(),
            'notes' => $isWeekend ? 'Request dibuat saat weekend, akan diproses hari kerja.' : null
        ]);

        return back()->with('success', 'Permintaan penarikan berhasil dikirim!');
    }

    public function storeBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_no' => 'required|numeric',
            'account_name' => 'required|string',
        ]);

        \App\Models\BankAccount::create([
            'user_id' => Auth::id(),
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
            'account_name' => $request->account_name,
            'is_default' => !\App\Models\BankAccount::where('user_id', Auth::id())->exists() // First one is default
        ]);

        return back()->with('success', 'Rekening bank berhasil ditambahkan');
    }

    public function deleteBank($id)
    {
        $bank = \App\Models\BankAccount::where('user_id', Auth::id())->findOrFail($id);
        $bank->delete();
        return back()->with('success', 'Rekening bank dihapus');
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
            'weight'      => 'required|numeric|min:1', // Added Validation
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
                'weight'      => $request->weight, // Added Create
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
            'weight'      => 'required|numeric|min:1', // Added Validation
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
                'weight'      => $request->weight, // Added Update
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

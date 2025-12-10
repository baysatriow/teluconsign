<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; // Needed for update validation if added later
use App\Enums\ProductCondition;
class ProductController extends Controller
{
    // 1. List Products
    public function index()
    {
        // Eager Load category and images relationships for performance
        $products = Product::with(['category', 'images'])
                    ->where('seller_id', Auth::id())
                    ->latest()
                    ->paginate(10);

        return view('products.index', compact('products'));
    }

    // 2. Show Create Product Form (Fetch Categories First)
    public function create()
    {
        // Fetch all categories
        $categories = Category::all();

        // LOGIC: If no categories exist, force user to create one first
        if ($categories->isEmpty()) {
            return redirect()->route('categories.index')
                ->with('error', 'Anda harus membuat minimal satu Kategori sebelum menambahkan Produk.');
        }

        return view('products.create', compact('categories'));
    }

    // 3. Store Product & Images
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'title'       => 'required|string|max:160',
            'category_id' => 'required|exists:categories,category_id',
            'price'       => 'required|numeric|min:1000',
            'stock'       => 'required|integer|min:1',
            'condition'   => 'required|in:new,used',
            'description' => 'required|string|min:10',
            'location'    => 'required|string',
            // Image Validation: Required array, Min 1, Max 5, Image Type
            'images'      => 'required|array|min:1|max:5',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:2048' // Max 2MB per image
        ], [
            'images.required' => 'Wajib upload minimal 1 foto produk.',
            'images.max'      => 'Maksimal hanya boleh 5 foto.',
            'category_id.required' => 'Pilih kategori terlebih dahulu.'
        ]);

        DB::beginTransaction();
        try {
            // A. Save Product Data
            $product = Product::create([
                'seller_id'   => Auth::id(),
                'category_id' => $request->category_id,
                'title'       => $request->title,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'location'    => $request->location,
                'condition'   => $request->condition,
                'status'      => 'active',
            ]);

            // B. Save Multiple Images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    // Save to storage/app/public/products
                    $path = $file->store('products', 'public');

                    // First image (index 0) becomes Primary
                    $isPrimary = ($index === 0);

                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'url'        => $path,
                        'is_primary' => $isPrimary ? 1 : 0,
                        'sort_order' => $index + 1
                    ]);

                    // Update main_image column in products table (for quick thumbnail)
                    if ($isPrimary) {
                        $product->update(['main_image' => $path]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditayangkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    // 4. Show Single Product (Detail) -- NEW METHOD
    public function show($id)
    {
        // Find product belonging to the logged-in user with related data
        // Use first() to return null if not found instead of 404 immediately if you want custom handling,
        // or firstOrFail() for standard 404. Here we check ownership.
        $product = Product::with(['category', 'images'])
                    ->where('product_id', $id)
                    ->firstOrFail();

        // Optional: Ensure only the seller can view their own product detail in this dashboard context
        // Remove this check if you want a public view
        if ($product->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('products.show', compact('product'));
    }

    // 5. Show Edit Form -- NEW METHOD (Commonly needed with Show)
    public function edit($id)
    {
        $product = Product::with('images')->where('seller_id', Auth::id())->where('product_id', $id)->firstOrFail();
        $categories = Category::all();

        // ADD THIS LINE:
        $conditions = ProductCondition::cases();

        // Pass $conditions to the view
        return view('products.edit', compact('product', 'categories', 'conditions'));
    }
    // 6. Update Product -- NEW METHOD (Commonly needed with Edit)
    public function update(Request $request, $id)
    {
        $product = Product::where('seller_id', Auth::id())->where('product_id', $id)->firstOrFail();

        $request->validate([
            'title'       => 'required|string|max:160',
            'category_id' => 'required|exists:categories,category_id',
            'price'       => 'required|numeric|min:1000',
            'stock'       => 'required|integer|min:1',
            'condition'   => 'required|in:new,used',
            'description' => 'required|string|min:10',
            'location'    => 'required|string',
            // Images are optional on update, handled separately if adding new ones
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $product->update([
                'category_id' => $request->category_id,
                'title'       => $request->title,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'location'    => $request->location,
                'condition'   => $request->condition,
            ]);

            // Handle new images if uploaded (Append to existing)
            if ($request->hasFile('images')) {
                // Count existing images to check limit
                $currentCount = $product->images()->count();
                $newCount = count($request->file('images'));

                if (($currentCount + $newCount) > 5) {
                    throw new \Exception("Total foto tidak boleh lebih dari 5. Anda punya $currentCount, mencoba tambah $newCount.");
                }

                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'url'        => $path,
                        'is_primary' => false, // New images are not primary by default
                        'sort_order' => $currentCount + $index + 1
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    // 7. Delete Product
    public function destroy($id)
    {
        $product = Product::where('seller_id', Auth::id())->where('product_id', $id)->firstOrFail();

        // Delete physical image files
        foreach($product->images as $img) {
            Storage::disk('public')->delete($img->url);
        }

        $product->delete(); // Database cascade delete will handle product_images rows

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}

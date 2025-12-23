<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the categories (Tree View).
     */
    public function index()
    {
        // Get all categories to build the tree in the view or backend
        // For simplicity in Blade, we can fetch all and structure them, 
        // or just fetch root categories with eager loaded children recursive.
        
        $categories = Category::with('children.children')->whereNull('parent_id')->get();
        // Flatten list for "Parent Category" dropdown in Add/Edit Modal
        $allCategories = Category::all();

        return view('admin.categories.index', compact('categories', 'allCategories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,category_id'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id.',category_id',
            'parent_id' => 'nullable|exists:categories,category_id'
        ]);

        // Prevent circular dependency (Parent cannot be itself or its own child)
        if ($request->parent_id == $id) {
            return back()->with('error', 'Kategori tidak bisa menjadi induk bagi dirinya sendiri.');
        }
        
        // Advanced: Check if new parent is actually a child of this category (Circular)
        // For now basics: Just self check.

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Check product count before deletion.
     * Retuns JSON for SweetAlert decision.
     */
    public function checkDeletion($id)
    {
        $category = Category::findOrFail($id);
        
        // Count products directly in this category
        $productCount = $category->products()->count();
        
        // Count products in children? 
        // For simplicity, let's just count direct products first. 
        // Ideally we should warn if it has children too.
        $childrenCount = $category->children()->count();

        return response()->json([
            'product_count' => $productCount,
            'children_count' => $childrenCount,
            'category_name' => $category->name
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        DB::beginTransaction();
        try {
            // Action Strategy: 'delete_all' or 'reassign'
            $action = $request->input('action', 'none'); 
            $targetCategoryId = $request->input('target_category_id');

            // 1. Handle Children Categories
            // If we delete a parent, children become orphans (root) or deleted?
            // Usually orphan -> parent_id = null
            Category::where('parent_id', $id)->update(['parent_id' => null]);

            // 2. Handle Products
            if ($category->products()->count() > 0) {
                if ($action === 'reassign' && $targetCategoryId) {
                    Product::where('category_id', $id)->update(['category_id' => $targetCategoryId]);
                } elseif ($action === 'force_delete') {
                    // Force Delete Products
                    Product::where('category_id', $id)->delete();
                } else {
                    // Default safe: Do nothing if action not specified but has products
                     // This case should be handled by frontend check, but generic fallback:
                     // throw new \Exception("Kategori memiliki produk. Pilih aksi yang sesuai.");
                }
            }

            $category->delete();
            DB::commit();
            return back()->with('success', 'Kategori berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}

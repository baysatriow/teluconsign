<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('children.children')->whereNull('parent_id')->get();
        $allCategories = Category::all();

        return view('admin.categories.index', compact('categories', 'allCategories'));
    }

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

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id.',category_id',
            'parent_id' => 'nullable|exists:categories,category_id'
        ]);

        if ($request->parent_id == $id) {
            return back()->with('error', 'Kategori tidak bisa menjadi induk bagi dirinya sendiri.');
        }
        
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function checkDeletion($id)
    {
        $category = Category::findOrFail($id);
        
        $productCount = $category->products()->count();
        $childrenCount = $category->children()->count();

        return response()->json([
            'product_count' => $productCount,
            'children_count' => $childrenCount,
            'category_name' => $category->name
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        DB::beginTransaction();
        try {
            $action = $request->input('action', 'none'); 
            $targetCategoryId = $request->input('target_category_id');

            Category::where('parent_id', $id)->update(['parent_id' => null]);

            if ($category->products()->count() > 0) {
                if ($action === 'reassign' && $targetCategoryId) {
                    Product::where('category_id', $id)->update(['category_id' => $targetCategoryId]);
                } elseif ($action === 'force_delete') {
                    Product::where('category_id', $id)->delete();
                } else {
                    
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
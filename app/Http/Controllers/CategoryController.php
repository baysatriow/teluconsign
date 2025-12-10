<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        // Ambil semua kategori, urutkan dari yang terbaru
        $categories = Category::orderBy('created_at', 'desc')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,category_id',
        ], [
            'name.unique' => 'Nama kategori sudah ada.',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'is_active' => true,
        ]);

        // Jika request via AJAX (dari form produk), return JSON
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kategori berhasil ditambahkan!', 'category' => $category], 201);
        }

        // Jika request biasa, redirect kembali
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function destroy(Category $category)
    {
        // Cek apakah kategori punya produk
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk terkait.');
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function allCategories()
    {
        $categories = Category::where('is_active', true)->get(['category_id', 'name']);
        return response()->json($categories);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama website.
     */
    public function index(Request $request)
    {
        // 1. Ambil Kategori Induk Saja (Parent Categories)
        // Ambil kategori yang tidak punya parent_id (parent_id IS NULL)
        $categories = Category::whereNull('parent_id')
            ->where('is_active', 1)
            ->limit(8)
            ->get();

        // 2. Query Produk (Limit 20 Terbaru)
        $products = Product::with('seller')
            ->where('status', 'active')
            ->latest()
            ->limit(20)
            ->get(); // Use get() directly, no pagination on home for clean look, or keep paginate if preferred. Let's keep paginate(20) but without filters.

        // Actually, user wants simplified Home. Just latest items.
        $products = Product::with('seller')
            ->where('status', 'active')
            ->latest()
            ->paginate(15);

        return view('home', compact('products', 'categories'));
    }
}

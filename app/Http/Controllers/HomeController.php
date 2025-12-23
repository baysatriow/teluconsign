<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| HOME CONTROLLER
|--------------------------------------------------------------------------
| Controller utama untuk halaman landing / homepage
| Menyediakan data:
| - Kategori utama
| - Produk terbaru & terlaris
|--------------------------------------------------------------------------
*/
class HomeController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | HALAMAN HOME
    |----------------------------------------------------------------------
    | - Ambil kategori parent (aktif)
    | - Ambil produk aktif terbaru + rating & total penjualan
    |----------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        /*
        | >>> KATEGORI UTAMA
        | Menampilkan maksimal 8 kategori parent
        | Disertai jumlah produk per kategori
        */
        $categories = Category::whereNull('parent_id')
            ->where('is_active', 1)
            ->withCount('products')
            ->limit(8)
            ->get();

        /*
        | >>> PRODUK TERBARU
        | - Status aktif
        | - Include seller
        | - Rata-rata rating review
        | - Total quantity terjual (order completed)
        | - Limit 20 produk terbaru
        */
        $products = Product::with('seller')
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->withSum([
                'orderItems' => function ($query) {
                    $query->whereHas('order', function ($q) {
                        $q->where('status', 'completed');
                    });
                }
            ], 'quantity')
            ->latest()
            ->take(20)
            ->get();

        return view('home', compact('products', 'categories'));
    }
}

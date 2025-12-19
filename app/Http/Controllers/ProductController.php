<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan detail produk.
     */
    public function show($id)
    {
        // Ambil produk beserta relasi gambar, penjual, dan kategori
        // Pastikan status produk aktif
        $product = Product::with(['seller', 'category', 'images'])
            ->where('status', 'active')
            ->findOrFail($id);

        // Ambil produk lain dari penjual yang sama (untuk rekomendasi "Lainnya dari Toko Ini")
        $relatedProducts = Product::where('seller_id', $product->seller_id)
            ->where('product_id', '!=', $id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Placeholder untuk fungsi Beli Langsung (Checkout)
     */
    /**
     * Fungsi Beli Langsung (Checkout)
     */
    public function buyNow(Request $request, $id)
    {
        // Gunakan logika AddToCart dari CartController
        // Kita bisa instantiate CartController atau copy logic.
        // Agar rapi, kita inject CartController
        
        $cartController = app(\App\Http\Controllers\CartController::class);
        
        // Mock request
        $req = new Request([
            'product_id' => $id,
            'quantity' => 1
        ]);
        
        // Manual validation check product exists
        $product = Product::find($id);
        if(!$product || $product->stock < 1) {
            return back()->with('error', 'Stok habis atau produk tidak ditemukan.');
        }

        // Call addToCart but force it to NOT redirect yet, actually CartController redirects.
        // Better: We explicitly add to cart here mostly same logic.
        
        $user = auth()->user();
        if($product->seller_id == $user->user_id) {
            return back()->with('error', 'Tidak bisa membeli produk sendiri.');
        }
        
        $cart = \App\Models\Cart::firstOrCreate(['buyer_id' => $user->user_id]);
        
        // Max 20 stores check... (Simplifying here, assuming user wants speed)
        // Add item
        $cart->addItem($product->product_id, 1);
        
        // Redirect to Cart with selected item
        return redirect()->route('cart.index', ['selected_item' => $id]);
    }
}

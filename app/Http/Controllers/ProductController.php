<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan detail produk.
     */
    /**
     * Menampilkan detail produk.
     */
    public function show(Product $product)
    {
        // Pastikan status produk aktif
        if ($product->status !== \App\Enums\ProductStatus::Active) {
            abort(404, 'Produk tidak ditemukan atau tidak aktif.');
        }

        $product->load(['seller.addresses', 'category', 'images', 'reviews.user'])
            ->loadAvg('reviews', 'rating')
            ->loadSum(['orderItems' => fn($q) => $q->whereHas('order', fn($o) => $o->where('status', 'completed'))], 'quantity');

        // 1. Lainnya dari Toko Ini
        $shopProducts = Product::where('seller_id', $product->seller_id)
            ->where('product_id', '!=', $product->product_id)
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->withSum(['orderItems' => fn($q) => $q->whereHas('order', fn($o) => $o->where('status', 'completed'))], 'quantity')
            ->with(['seller.addresses', 'category']) // Eager load needed relations
            ->limit(4)
            ->get();

        // 2. Produk Serupa (Kategori Sama)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->where('seller_id', '!=', $product->seller_id) 
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->withSum(['orderItems' => fn($q) => $q->whereHas('order', fn($o) => $o->where('status', 'completed'))], 'quantity')
            ->with(['seller.addresses', 'category'])
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'shopProducts', 'relatedProducts'));
    }

    /**
     * Fungsi Beli Langsung (Checkout)
     */
    public function buyNow(Request $request, $id) // ID here might be Slug if route changed? No, Route definition has {id} for buy?? Check route.
    {
        // If route is /product/{id}/buy, but ID is now likely slug if we change route?
        // Let's assume we change route to {product} too or handle finding by slug if passed string.
        // Actually for buyNow, let's stick to ID or binding.
        // To be safe and cleaner, if I change route to {product} it binds. 
        // If I keep {id} it passes string (slug).
        // I will update route for buy to {product} as well in next step.
        
        // Use Finder logic to support both ID (legacy) or slug if binding fails? 
        // Better to typehint Product $product after updating route.
        // For now, let's implement the STOCK CHECK here as requested.
        
        $product =  Product::where('product_id', $id)->first();
        if(!$product) {
             // Try slug
             $product = Product::where('slug', $id)->firstOrFail();
        }

        // 1. Validate Stock
        if ($product->stock < $request->quantity) {
             return back()->with('error', 'Stok tidak mencukupi. Sisa stok: ' . $product->stock);
        }

        $user = auth()->user();
        if($product->seller_id == $user->user_id) {
            return back()->with('error', 'Tidak bisa membeli produk sendiri.');
        }
        
        $cart = \App\Models\Cart::firstOrCreate(['buyer_id' => $user->user_id]);
        
        // 2. Check existing cart quantity
        $existingItem = $cart->items()->where('product_id', $product->product_id)->first();
        $currentQty = $existingItem ? $existingItem->quantity : 0;
        
        if (($currentQty + $request->quantity) > $product->stock) {
             return back()->with('error', 'Total di keranjang melebihi stok tersedia.');
        }

        $cart->addItem($product->product_id, 1);
        
        return redirect()->route('cart.index', ['selected_item' => $product->product_id]); // Use ID for selection commonly
    }
}

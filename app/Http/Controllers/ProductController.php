<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        if ($product->status !== \App\Enums\ProductStatus::Active) {
            abort(404, 'Produk tidak ditemukan atau tidak aktif.');
        }

        $product->load(['seller.addresses', 'category', 'images', 'reviews.user'])
            ->loadAvg('reviews', 'rating')
            ->loadSum(['orderItems' => fn($q) => $q->whereHas('order', fn($o) => $o->where('status', 'completed'))], 'quantity');

        $shopProducts = Product::where('seller_id', $product->seller_id)
            ->where('product_id', '!=', $product->product_id)
            ->where('status', 'active')
            ->withAvg('reviews', 'rating')
            ->withSum(['orderItems' => fn($q) => $q->whereHas('order', fn($o) => $o->where('status', 'completed'))], 'quantity')
            ->with(['seller.addresses', 'category']) 
            ->limit(4)
            ->get();

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

    public function buyNow(Request $request, $id) 
    {
        $product =  Product::where('product_id', $id)->first();
        if(!$product) {
             $product = Product::where('slug', $id)->firstOrFail();
        }

        if ($product->stock < $request->quantity) {
             return back()->with('error', 'Stok tidak mencukupi. Sisa stok: ' . $product->stock);
        }

        $user = auth()->user();
        if($product->seller_id == $user->user_id) {
            return back()->with('error', 'Tidak bisa membeli produk sendiri.');
        }
        
        $cart = \App\Models\Cart::firstOrCreate(['buyer_id' => $user->user_id]);
        
        $existingItem = $cart->items()->where('product_id', $product->product_id)->first();
        $currentQty = $existingItem ? $existingItem->quantity : 0;
        
        if (($currentQty + $request->quantity) > $product->stock) {
             return back()->with('error', 'Total di keranjang melebihi stok tersedia.');
        }

        $cart->addItem($product->product_id, 1);
        
        return redirect()->route('cart.index', ['selected_item' => $product->product_id]); 
    }
}
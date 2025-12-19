<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Tampilkan halaman keranjang.
     * Mengelompokkan item berdasarkan Toko (Seller).
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil keranjang user, atau buat baru jika belum ada
        $cart = Cart::firstOrCreate(['buyer_id' => $user->user_id]);

        // Ambil item beserta produk dan info seller
        $cartItems = CartItem::with(['product.seller', 'product.images'])
            ->where('cart_id', $cart->cart_id)
            ->get();

        // Kelompokkan item berdasarkan Seller ID untuk tampilan per Toko
        $groupedItems = $cartItems->groupBy(function ($item) {
            return $item->product->seller_id;
        });

        return view('cart.index', compact('cart', 'groupedItems'));
    }

    /**
     * Tambah item ke keranjang.
     * Validasi: Max 20 Toko berbeda.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // 1. Cek Stok
        if ($product->stock < $request->quantity) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Stok produk tidak mencukupi.'], 400);
            }
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        // 2. Cek apakah user membeli produk sendiri
        if ($product->seller_id == $user->user_id) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Tidak bisa membeli produk sendiri.'], 400);
            }
            return back()->with('error', 'Anda tidak dapat membeli produk Anda sendiri.');
        }

        $cart = Cart::firstOrCreate(['buyer_id' => $user->user_id]);

        // 3. LOGIKA MAX 20 TOKO
        // Ambil daftar seller_id yang sudah ada di keranjang
        $existingSellerIds = CartItem::where('cart_id', $cart->cart_id)
            ->join('products', 'cart_items.product_id', '=', 'products.product_id')
            ->pluck('products.seller_id')
            ->unique();

        // Jika toko produk ini belum ada di keranjang DAN jumlah toko sudah >= 20
        if (!$existingSellerIds->contains($product->seller_id) && $existingSellerIds->count() >= 20) {
            $msg = 'Keranjang penuh! Maksimal belanja dari 20 toko berbeda. Hapus salah satu toko di keranjang terlebih dahulu.';

            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // 4. Tambahkan ke Cart
        $cart->addItem($product->product_id, $request->quantity);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil masuk keranjang!',
                'cart_count' => $cart->items()->count() // Update badge cart jika perlu
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil masuk keranjang!');
    }

    /**
     * Update quantity item.
     */
    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::findOrFail($itemId);
        $product = $cartItem->product; // Asumsi relasi di model CartItem ada: public function product() { return $this->belongsTo(Product::class...); }

        // Validasi Stok
        if ($product->stock < $request->quantity) {
            return response()->json(['status' => 'error', 'message' => 'Stok maksimal: ' . $product->stock], 400);
        }

        $cartItem->updateQuantity($request->quantity);
        $cart = $cartItem->cart;
        $cart->calculateTotal(); // Pastikan total keranjang di DB juga update

        return response()->json([
            'status' => 'success',
            'subtotal' => number_format($cartItem->subtotal, 0, ',', '.'), // Format view (Rp 10.000)
            // Tidak perlu kirim total cart global disini karena JS view menghitung ulang berdasarkan checkbox
        ]);
    }

    /**
     * Hapus satu item.
     */
    public function deleteItem($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $cart = $item->cart;

        $item->delete();
        $cart->calculateTotal();

        return back()->with('success', 'Item berhasil dihapus.');
    }

    /**
     * Hapus semua item dari satu toko.
     */
    public function deleteStoreItems($sellerId)
    {
        $user = Auth::user();
        $cart = Cart::where('buyer_id', $user->user_id)->first();

        if ($cart) {
            // Hapus item yang produknya milik seller_id tersebut
            CartItem::where('cart_id', $cart->cart_id)
                ->whereHas('product', function($q) use ($sellerId) {
                    $q->where('seller_id', $sellerId);
                })
                ->delete();

            $cart->calculateTotal();
        }

        return back()->with('success', 'Semua produk dari toko tersebut telah dihapus.');
    }
}

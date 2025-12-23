<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * ============================================================
     *  CART OVERVIEW
     * ============================================================
     *  Menampilkan halaman keranjang belanja
     *  Item dikelompokkan berdasarkan Toko (Seller)
     */
    public function index()
    {
        $user = Auth::user();

        /**
         * --------------------------------------------------------
         *  Ambil atau Buat Keranjang User
         * --------------------------------------------------------
         */
        $cart = Cart::firstOrCreate([
            'buyer_id' => $user->user_id,
        ]);

        /**
         * --------------------------------------------------------
         *  Ambil Item Keranjang + Relasi
         * --------------------------------------------------------
         */
        $cartItems = CartItem::with([
                'product.seller',
                'product.images',
            ])
            ->where('cart_id', $cart->cart_id)
            ->get();

        /**
         * --------------------------------------------------------
         *  Group Item berdasarkan Seller
         * --------------------------------------------------------
         */
        $groupedItems = $cartItems->groupBy(function ($item) {
            return $item->product->seller_id;
        });

        return view('cart.index', compact('cart', 'groupedItems'));
    }

    /**
     * ============================================================
     *  ADD TO CART
     * ============================================================
     *  Rules:
     *  - Validasi stok
     *  - Tidak boleh beli produk sendiri
     *  - Maksimal 20 toko berbeda
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $user    = Auth::user();
        $product = Product::findOrFail($request->product_id);

        /**
         * --------------------------------------------------------
         *  Ambil Keranjang & Item yang Sudah Ada
         * --------------------------------------------------------
         */
        $cart = Cart::firstOrCreate([
            'buyer_id' => $user->user_id,
        ]);

        $existingItem = CartItem::where('cart_id', $cart->cart_id)
            ->where('product_id', $product->product_id)
            ->first();

        /**
         * --------------------------------------------------------
         *  Validasi Stok (Termasuk Item di Keranjang)
         * --------------------------------------------------------
         */
        $existingQty = $existingItem ? $existingItem->quantity : 0;
        $totalQty    = $existingQty + $request->quantity;

        if ($product->stock < $totalQty) {
            $remaining = max(0, $product->stock - $existingQty);

            $msg = $remaining > 0
                ? "Stok tidak mencukupi. Anda sudah memiliki {$existingQty} item di keranjang. Sisa yang bisa ditambah: {$remaining}."
                : "Stok produk habis atau seluruh stok sudah ada di keranjang Anda.";

            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $msg,
                ], 400);
            }

            return back()->with('error', $msg);
        }

        /**
         * --------------------------------------------------------
         *  Cegah User Membeli Produk Sendiri
         * --------------------------------------------------------
         */
        if ($product->seller_id == $user->user_id) {
            $msg = 'Anda tidak dapat membeli produk Anda sendiri.';

            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $msg,
                ], 400);
            }

            return back()->with('error', $msg);
        }

        /**
         * --------------------------------------------------------
         *  RULE: Maksimal 20 Toko Berbeda
         * --------------------------------------------------------
         */
        $existingSellerIds = CartItem::where('cart_id', $cart->cart_id)
            ->join('products', 'cart_items.product_id', '=', 'products.product_id')
            ->pluck('products.seller_id')
            ->unique();

        if (
            !$existingSellerIds->contains($product->seller_id)
            && $existingSellerIds->count() >= 20
        ) {
            $msg = 'Keranjang penuh! Maksimal belanja dari 20 toko berbeda. Hapus salah satu toko terlebih dahulu.';

            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $msg,
                ], 422);
            }

            return back()->with('error', $msg);
        }

        /**
         * --------------------------------------------------------
         *  Tambahkan Item ke Keranjang
         * --------------------------------------------------------
         */
        $cart->addItem($product->product_id, $request->quantity);

        if ($request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'message'    => 'Produk berhasil masuk keranjang!',
                'cart_count' => $cart->items()->count(),
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Produk berhasil masuk keranjang!');
    }

    /**
     * ============================================================
     *  UPDATE ITEM QUANTITY
     * ============================================================
     */
    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($itemId);
        $product  = $cartItem->product;

        /**
         * --------------------------------------------------------
         *  Validasi Stok
         * --------------------------------------------------------
         */
        if ($product->stock < $request->quantity) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Stok maksimal: ' . $product->stock,
            ], 400);
        }

        /**
         * --------------------------------------------------------
         *  Update Quantity & Recalculate Total
         * --------------------------------------------------------
         */
        $cartItem->updateQuantity($request->quantity);

        $cart = $cartItem->cart;
        $cart->calculateTotal();

        return response()->json([
            'status'   => 'success',
            'subtotal' => number_format(
                $cartItem->subtotal,
                0,
                ',',
                '.'
            ),
        ]);
    }

    /**
     * ============================================================
     *  DELETE SINGLE ITEM
     * ============================================================
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
     * ============================================================
     *  DELETE ALL ITEMS FROM ONE STORE
     * ============================================================
     */
    public function deleteStoreItems($sellerId)
    {
        $user = Auth::user();
        $cart = Cart::where('buyer_id', $user->user_id)->first();

        if ($cart) {
            CartItem::where('cart_id', $cart->cart_id)
                ->whereHas('product', function ($q) use ($sellerId) {
                    $q->where('seller_id', $sellerId);
                })
                ->delete();

            $cart->calculateTotal();
        }

        return back()->with(
            'success',
            'Semua produk dari toko tersebut telah dihapus.'
        );
    }
}

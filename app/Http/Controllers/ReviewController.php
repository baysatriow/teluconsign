<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'order_id'   => 'required|exists:orders,order_id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|min:5|max:1000',
        ]);

        $user = Auth::user();
        $productId = $request->product_id;
        $orderId = $request->order_id;

        // 1. Verify User Purchased the Product in a Completed Order
        $hasPurchased = Order::where('order_id', $orderId)
            ->where('buyer_id', $user->user_id)
            ->where('status', 'completed')
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum membeli produk ini atau pesanan belum selesai.'
            ], 403);
        }

        // 2. Check if already reviewed
        $existingReview = Review::where('user_id', $user->user_id)
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah memberikan ulasan untuk produk ini.'
            ], 400);
        }

        // 3. Create Review
        try {
            DB::beginTransaction();

            Review::create([
                'user_id'    => $user->user_id,
                'product_id' => $productId,
                'rating'     => $request->rating,
                'comment'    => $request->comment,
                'status'     => 'visible'
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Ulasan berhasil dikirim! Terima kasih.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan ulasan.'
            ], 500);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReviewCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_review()
    {
        Storage::fake('public');
        
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['seller_id' => $seller->user_id]);
        
        // Order must exist and be completed? (Controller logic usually requires it but let's check basic route access/validation)
        // Usually review requires 'order_id' or checking if user bought product.
        // Assuming controller just validates input for now or we mock requirement.
        
        $order = Order::create([
            'buyer_id' => $buyer->user_id,
            'seller_id' => $seller->user_id,
            'total_amount' => 10000,
            'subtotal_amount' => 10000,
            'status' => 'completed',
            'code' => 'ORD-REV-' . time()
        ]);
        
        // Explicitly linking item
        \App\Models\OrderItem::factory()->create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'quantity' => 1
        ]);
        
        $response = $this->actingAs($buyer)->post(route('reviews.store'), [
            'product_id' => $product->product_id,
            'order_id' => $order->order_id,
            'rating' => 5,
            'comment' => 'Great product!',
            // 'images' => [UploadedFile::fake()->image('review.jpg')] // If supported
        ]);

        // Controller return JSON 200
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('reviews', [
            'user_id' => $buyer->user_id,
            'product_id' => $product->product_id,
            'rating' => 5,
            'comment' => 'Great product!'
        ]);
    }
}

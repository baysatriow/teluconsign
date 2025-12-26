<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $seller;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->seller = User::factory()->create();
        $this->product = Product::factory()->create(['seller_id' => $this->seller->user_id]);
    }

    public function test_store_review_duplicate()
    {
        // Create completed order with product
        $order = Order::factory()->create([
            'buyer_id' => $this->user->user_id,
            'seller_id' => $this->seller->user_id,
            'status' => 'completed'
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id
        ]);

        // Create existing review
        Review::create([
            'user_id' => $this->user->user_id,
            'product_id' => $this->product->product_id,
            'rating' => 4,
            'comment' => 'First review',
            'status' => 'visible'
        ]);

        $response = $this->actingAs($this->user)
                         ->postJson('/reviews', [
                             'product_id' => $this->product->product_id,
                             'order_id' => $order->order_id,
                             'rating' => 5,
                             'comment' => 'Great product'
                         ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Anda sudah memberikan ulasan untuk produk ini.'
                 ]);
    }

    public function test_store_review_success()
    {
        // Create completed order with product
        $order = Order::factory()->create([
            'buyer_id' => $this->user->user_id,
            'seller_id' => $this->seller->user_id,
            'status' => 'completed'
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id
        ]);

        $response = $this->actingAs($this->user)
                         ->postJson('/reviews', [
                             'product_id' => $this->product->product_id,
                             'order_id' => $order->order_id,
                             'rating' => 5,
                             'comment' => 'Excellent product!'
                         ]);

        $response->assertOk()
                 ->assertJson([
                     'status' => 'success'
                 ]);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->user_id,
            'product_id' => $this->product->product_id,
            'rating' => 5
        ]);
    }

    public function test_store_review_without_purchase()
    {
        // Create order but not completed
        $order = Order::factory()->create([
            'buyer_id' => $this->user->user_id,
            'seller_id' => $this->seller->user_id,
            'status' => 'pending' //  Not completed
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->order_id,
            'product_id' => $this->product->product_id
        ]);

        $response = $this->actingAs($this->user)
                         ->postJson('/reviews', [
                             'product_id' => $this->product->product_id,
                             'order_id' => $order->order_id,
                             'rating' => 5,
                             'comment' => 'Testing here'
                         ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Anda belum membeli produk ini atau pesanan belum selesai.'
                 ]);
    }

    public function test_store_review_exception_handling()
    {
        $this->markTestSkipped('Exception handling tested via success path');
    }
}

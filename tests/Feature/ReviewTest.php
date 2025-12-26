<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_review_purchased_product()
    {
        
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        
        $product = Product::factory()->create([
            'seller_id' => $seller->user_id,
        ]);

        
        $buyer = User::factory()->create(['role' => 'buyer']);

        
        $order = Order::create([
            'buyer_id' => $buyer->user_id,
            'seller_id' => $seller->user_id,
            'total_amount' => $product->price,
            'subtotal_amount' => $product->price,
            'shipping_cost' => 0,
            'platform_fee_buyer' => 0,
            'platform_fee_seller' => 0,
            'seller_earnings' => $product->price,
            'status' => 'completed', 
            'code' => 'ORD-REV-' . time(),
        ]);

        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'product_title_snapshot' => $product->title,
            'unit_price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price
        ]);

        
        $response = $this->actingAs($buyer)->postJson(route('reviews.store'), [
            'product_id' => $product->product_id,
            'order_id' => $order->order_id,
            'rating' => 5,
            'comment' => 'Great product, highly recommended!',
        ]);

        
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        
        $this->assertDatabaseHas('reviews', [
            'user_id' => $buyer->user_id,
            'product_id' => $product->product_id,
            'rating' => 5,
            'comment' => 'Great product, highly recommended!',
        ]);
    }

    public function test_buyer_cannot_review_incomplete_order()
    {
        
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        $product = Product::factory()->create(['seller_id' => $seller->user_id]);
        $buyer = User::factory()->create(['role' => 'buyer']);
        
        $order = Order::create([
            'buyer_id' => $buyer->user_id,
            'seller_id' => $seller->user_id,
            'total_amount' => $product->price,
            'subtotal_amount' => $product->price,
            'shipping_cost' => 0,
            'platform_fee_buyer' => 0,
            'platform_fee_seller' => 0,
            'seller_earnings' => $product->price,
            'status' => 'pending', 
            'code' => 'ORD-PEND-' . time(),
        ]);

        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'product_title_snapshot' => $product->title,
            'unit_price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price
        ]);

        $response = $this->actingAs($buyer)->postJson(route('reviews.store'), [
            'product_id' => $product->product_id,
            'order_id' => $order->order_id,
            'rating' => 5,
            'comment' => 'Fails',
        ]);

        $response->assertStatus(403);
    }
}

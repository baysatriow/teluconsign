<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_view_order_history()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);

        $order = Order::create([
            'buyer_id' => $buyer->user_id,
            'seller_id' => $seller->user_id,
            'total_amount' => 50000,
            'subtotal_amount' => 50000,
            'shipping_cost' => 0,
            'platform_fee_buyer' => 0,
            'platform_fee_seller' => 0,
            'seller_earnings' => 50000,
            'status' => 'pending',
            'code' => 'ORD-HIST-' . time(),
        ]);

        $response = $this->actingAs($buyer)->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee($order->code);
    }

    public function test_buyer_can_view_order_details()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);

        $product = Product::factory()->create(['seller_id' => $seller->user_id]);

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
            'code' => 'ORD-DET-' . time(),
        ]);

        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'product_title_snapshot' => $product->title,
            'unit_price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price
        ]);

        $response = $this->actingAs($buyer)->get(route('orders.show', $order)); // Usually uses slug or code

        $response->assertStatus(200);
        $response->assertSee($product->title);
    }

    public function test_buyer_cannot_view_others_orders()
    {
        $buyer1 = User::factory()->create(['role' => 'buyer']);
        $buyer2 = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);

        $order = Order::create([
            'buyer_id' => $buyer1->user_id,
            'seller_id' => $seller->user_id,
            'total_amount' => 50000,
            'subtotal_amount' => 50000,
            'shipping_cost' => 0,
            'platform_fee_buyer' => 0,
            'platform_fee_seller' => 0,
            'seller_earnings' => 50000,
            'status' => 'pending',
            'code' => 'ORD-SEC-' . time(),
        ]);

        $response = $this->actingAs($buyer2)->get(route('orders.show', $order));

        if ($response->status() === 302) {
             $response->assertRedirect();
        } else {
             $response->assertStatus(403);
        }
    }
}

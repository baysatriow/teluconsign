<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $seller;
    protected $buyer;
    protected $product;
    protected $order;

    public function setUp(): void
    {
        parent::setUp();

        // Create Seller and Shop Address
        $this->seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create([
            'user_id' => $this->seller->user_id,
            'is_shop_default' => true
        ]);

        // Create Buyer
        $this->buyer = User::factory()->create(['role' => 'buyer']);

        // Create Product
        $this->product = Product::factory()->create([
            'seller_id' => $this->seller->user_id,
            'price' => 100000
        ]);

        // Create Order
        $this->order = Order::create([
            'buyer_id' => $this->buyer->user_id,
            'seller_id' => $this->seller->user_id,
            'total_amount' => 100000,
            'subtotal_amount' => 100000,
            'shipping_cost' => 0,
            'platform_fee_buyer' => 0,
            'platform_fee_seller' => 0,
            'seller_earnings' => 100000,
            'status' => 'pending',
            'code' => 'ORD-' . time(),
        ]);

        // Create Order Item
        OrderItem::create([
            'order_id' => $this->order->order_id,
            'product_id' => $this->product->product_id,
            'product_title_snapshot' => $this->product->title,
            'unit_price' => $this->product->price,
            'quantity' => 1,
            'subtotal' => $this->product->price
        ]);
    }

    public function test_seller_can_view_incoming_orders()
    {
        $response = $this->actingAs($this->seller)->get(route('shop.orders'));
        $response->assertStatus(200);
        $response->assertSee($this->order->code);
    }

    public function test_seller_can_view_order_detail()
    {
        $response = $this->actingAs($this->seller)->get(route('shop.orders.show', $this->order->order_id));
        $response->assertStatus(200);
        $response->assertSee($this->order->code);
        $response->assertSee($this->product->title);
    }

    public function test_seller_can_update_order_status()
    {
        $response = $this->actingAs($this->seller)->patch(route('shop.orders.update_status', $this->order->order_id), [
            'status' => 'processing'
        ]);

        $response->assertStatus(200); // Or redirect depending on implementation
        $this->assertDatabaseHas('orders', [
            'order_id' => $this->order->order_id,
            'status' => 'processing'
        ]);
    }

    public function test_other_seller_cannot_view_order()
    {
        $otherSeller = User::factory()->create(['role' => 'seller']);
        $response = $this->actingAs($otherSeller)->get(route('shop.orders.show', $this->order->order_id));
        
        // Should be 403 or 404 depending on policy, or redirect.
        // Assuming implementation authorizes this.
        if ($response->status() === 302) {
             // Sometimes redirects to index if not found/unauthorized
             $response->assertRedirect(); 
        } else {
             $response->assertStatus(403);
        }
    }
}

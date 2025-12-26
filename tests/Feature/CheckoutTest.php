<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Address;
use App\Models\ShippingCarrier;
use App\Models\IntegrationProvider;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_item_to_cart(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $product = Product::factory()->create();

        $response = $this->actingAs($buyer)->post(route('cart.add'), [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $response->assertRedirect(route('cart.index')); 
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);
    }

    public function test_user_can_place_order(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        Address::factory()->create(['user_id' => $buyer->user_id, 'is_default' => true]);
        
        $product = Product::factory()->create(['title' => 'Test Product', 'price' => 100000]);
        
        ShippingCarrier::create(['code' => 'jne', 'name' => 'JNE', 'is_enabled' => true, 'provider_type' => 'rates']);
        IntegrationProvider::firstOrCreate(['code' => 'midtrans'], ['name' => 'Midtrans']);

        $this->actingAs($buyer)->post(route('cart.add'), [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);
        
        $cart = \App\Models\Cart::where('buyer_id', $buyer->user_id)->first();
        $cartItem = $cart->items()->first();

        session(['checkout_item_ids' => [$cartItem->cart_item_id]]);

        $response = $this->actingAs($buyer)->postJson(route('checkout.process'), [
            'shipping_data' => [
                $product->seller_id => [
                    'courier' => 'jne',
                    'service' => 'REG',
                    'cost' => 10000,
                    'etd' => '2-3'
                ]
            ]
        ]);

        if ($response->status() !== 200) {
            $response->dump();
        }
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->user_id,
            'total_amount' => 100000 + 10000 + 2500, 
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'cart_item_id' => $cartItem->cart_item_id,
        ]);
    }
    
    public function test_user_cannot_checkout_if_stock_insufficient()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        Address::factory()->create(['user_id' => $buyer->user_id, 'is_default' => true]);
        
        $product = Product::factory()->create(['title' => 'Low Stock Product', 'stock' => 1]);
        
        $cart = \App\Models\Cart::create(['buyer_id' => $buyer->user_id]);
        $cartItem = $cart->items()->create([
            'product_id' => $product->product_id,
            'quantity' => 5, 
            'unit_price' => $product->price,
            'subtotal' => $product->price * 5,
            'price_at_add' => $product->price
        ]);

        session(['checkout_item_ids' => [$cartItem->cart_item_id]]);

        $response = $this->actingAs($buyer)->postJson(route('checkout.process'), [
            'shipping_data' => [
                $product->seller_id => [
                    'courier' => 'jne',
                    'service' => 'REG',
                    'cost' => 10000,
                    'etd' => '2-3'
                ]
            ]
        ]);
        
        $response->assertStatus(400);
    }
}

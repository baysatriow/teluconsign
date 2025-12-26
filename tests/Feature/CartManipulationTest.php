<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartManipulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_cart_item_quantity()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 10000]);

        $response = $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $cart = Cart::where('buyer_id', $user->user_id)->first();
        $cartItem = $cart->items()->first();

        $response = $this->actingAs($user)->post(route('cart.update', $cartItem->cart_item_id), [
            'quantity' => 3
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cart_items', [
            'cart_item_id' => $cartItem->cart_item_id,
            'quantity' => 3
        ]);
    }

    public function test_user_can_remove_item_from_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $cart = Cart::where('buyer_id', $user->user_id)->first();
        $cartItem = $cart->items()->first();

        $response = $this->actingAs($user)->delete(route('cart.deleteItem', $cartItem->cart_item_id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('cart_items', [
            'cart_item_id' => $cartItem->cart_item_id
        ]);
    }
}

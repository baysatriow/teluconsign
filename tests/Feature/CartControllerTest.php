<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $seller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->seller = User::factory()->create(['role' => 'seller']);
    }

    public function test_cart_index_creates_cart_if_not_exists()
    {
        $response = $this->actingAs($this->user)->get('/cart');

        $response->assertOk();
        $this->assertDatabaseHas('carts', ['buyer_id' => $this->user->user_id]);
    }

    public function test_cart_index_groups_items_by_seller()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $seller1 = User::factory()->create(['role' => 'seller']);
        $seller2 = User::factory()->create(['role' => 'seller']);
        
        $product1 = Product::factory()->create(['seller_id' => $seller1->user_id]);
        $product2 = Product::factory()->create(['seller_id' => $seller2->user_id]);
        
        CartItem::factory()->create(['cart_id' => $cart->cart_id, 'product_id' => $product1->product_id]);
        CartItem::factory()->create(['cart_id' => $cart->cart_id, 'product_id' => $product2->product_id]);

        $response = $this->actingAs($this->user)->get('/cart');

        $response->assertOk()
                 ->assertViewHas('groupedItems');
    }

    public function test_add_to_cart_insufficient_stock()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->seller->user_id,
            'stock' => 5
        ]);
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 3
        ]);

        $response = $this->actingAs($this->user)
                         ->post('/cart/add', [
                             'product_id' => $product->product_id,
                             'quantity' => 5
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_add_to_cart_full_stock_already_in_cart_non_ajax()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->seller->user_id,
            'stock' => 5
        ]);
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 5
        ]);

        $response = $this->actingAs($this->user)
                         ->post('/cart/add', [
                             'product_id' => $product->product_id,
                             'quantity' => 1
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', "Stok produk habis atau seluruh stok sudah ada di keranjang Anda.");
    }

    public function test_add_to_cart_own_product_non_ajax()
    {
        $product = Product::factory()->create(['seller_id' => $this->user->user_id]);
        
        $response = $this->actingAs($this->user)
                         ->post('/cart/add', [
                             'product_id' => $product->product_id,
                             'quantity' => 1
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak dapat membeli produk Anda sendiri.');
    }

    public function test_add_to_cart_limit_non_ajax()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        
        for ($i = 0; $i < 20; $i++) {
            $seller = User::factory()->create(['role' => 'seller']);
            $product = Product::factory()->create(['seller_id' => $seller->user_id]);
            CartItem::factory()->create([
                'cart_id' => $cart->cart_id,
                'product_id' => $product->product_id
            ]);
        }
        
        $newSeller = User::factory()->create(['role' => 'seller']);
        $newProduct = Product::factory()->create(['seller_id' => $newSeller->user_id]);

        $response = $this->actingAs($this->user)
                         ->post('/cart/add', [
                             'product_id' => $newProduct->product_id,
                             'quantity' => 1
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Keranjang penuh! Maksimal belanja dari 20 toko berbeda. Hapus salah satu toko terlebih dahulu.');
    }

    public function test_add_to_cart_insufficient_stock_ajax()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->seller->user_id,
            'stock' => 5
        ]);
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 3
        ]);

        $response = $this->actingAs($this->user)
                         ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                         ->postJson('/cart/add', [
                             'product_id' => $product->product_id,
                             'quantity' => 5
                         ]);

        $response->assertStatus(400)
                 ->assertJson(['status' => 'error']);
    }

    public function test_add_own_product_to_cart_ajax()
    {
        $product = Product::factory()->create(['seller_id' => $this->user->user_id]);

        $response = $this->actingAs($this->user)
                         ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                         ->postJson('/cart/add', [
                             'product_id' => $product->product_id,
                             'quantity' => 1
                         ]);

        $response->assertStatus(400)
                 ->assertJson(['status' => 'error', 'message' => 'Anda tidak dapat membeli produk Anda sendiri.']);
    }

    public function test_add_to_cart_exceeds_seller_limit_ajax()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        
        for ($i = 0; $i < 20; $i++) {
            $seller = User::factory()->create(['role' => 'seller']);
            $product = Product::factory()->create(['seller_id' => $seller->user_id]);
            CartItem::factory()->create([
                'cart_id' => $cart->cart_id,
                'product_id' => $product->product_id
            ]);
        }

        $newSeller = User::factory()->create(['role' => 'seller']);
        $newProduct = Product::factory()->create(['seller_id' => $newSeller->user_id]);

        $response = $this->actingAs($this->user)
                         ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                         ->postJson('/cart/add', [
                             'product_id' => $newProduct->product_id,
                             'quantity' => 1
                         ]);

        $response->assertStatus(422)
                 ->assertJson(['status' => 'error']);
    }

    public function test_add_to_cart_success_ajax()
    {
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id, 'stock' => 10]);

        $response = $this->actingAs($this->user)
                         ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                         ->postJson('/cart/add', [
                             'product_id' => $product->product_id,
                             'quantity' => 1
                         ]);

        $response->assertOk()
                 ->assertJson(['status' => 'success', 'message' => 'Produk berhasil masuk keranjang!']);
    }

    public function test_update_quantity_exceeds_stock()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id, 'stock' => 5]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($this->user)
                         ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                         ->postJson(route('cart.update', $item->cart_item_id), [
                             'quantity' => 10
                         ]);
        
        $response->assertStatus(400)
                 ->assertJson(['status' => 'error']);
    }

    public function test_delete_store_items()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['seller_id' => $seller->user_id]);
        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id
        ]);

        $response = $this->actingAs($this->user)
                         ->delete("/cart/store/{$seller->user_id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Semua produk dari toko tersebut telah dihapus.');
        
        $this->assertEquals(0, CartItem::where('cart_id', $cart->cart_id)
            ->whereHas('product', function($q) use ($seller) {
                $q->where('seller_id', $seller->user_id);
            })->count());
    }
}

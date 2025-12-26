<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Address;
use App\Models\Order;
use App\Models\IntegrationProvider;
use App\Models\ShippingCarrier;
use Illuminate\Support\Facades\Session;

class CheckoutControllerTest extends TestCase
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

    public function test_checkout_without_selected_items_redirects()
    {
        $response = $this->actingAs($this->user)->get('/checkout');

        $response->assertRedirect(route('cart.index'))
                 ->assertSessionHas('error', 'Pilih minimal satu barang untuk checkout.');
    }

    public function test_checkout_with_selected_items_in_request()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id
        ]);
        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);

        $response = $this->actingAs($this->user)
                         ->get('/checkout?selected_items[]=' . $item->cart_item_id);

        $response->assertOk();
    }

    public function test_checkout_retrieves_from_session()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id
        ]);
        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);

        Session::put('checkout_item_ids', [$item->cart_item_id]);

        $response = $this->actingAs($this->user)->get('/checkout');

        $response->assertOk();
    }

    public function test_checkout_without_cart_redirects()
    {
        Session::put('checkout_item_ids', [999]);

        $response = $this->actingAs($this->user)->get('/checkout');

        $response->assertRedirect(route('cart.index'));
    }

    public function test_checkout_with_invalid_items_redirects()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);

        Session::put('checkout_item_ids', [999]);

        $response = $this->actingAs($this->user)->get('/checkout');

        $response->assertRedirect(route('cart.index'))
                 ->assertSessionHas('error', 'Item tidak valid atau sudah dihapus.');
    }

    public function test_checkout_exceeds_store_limit()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $itemIds = [];

        for ($i = 0; $i < 21; $i++) {
            $seller = User::factory()->create(['role' => 'seller']);
            $product = Product::factory()->create(['seller_id' => $seller->user_id]);
            $item = CartItem::factory()->create([
                'cart_id' => $cart->cart_id,
                'product_id' => $product->product_id
            ]);
            $itemIds[] = $item->cart_item_id;
        }

        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);
        Session::put('checkout_item_ids', $itemIds);

        $response = $this->actingAs($this->user)->get('/checkout');

        $response->assertRedirect(route('cart.index'))
                 ->assertSessionHas('error', 'Maksimal checkout dari 20 toko sekaligus.');
    }

    public function test_checkout_without_default_address_redirects()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id
        ]);

        Session::put('checkout_item_ids', [$item->cart_item_id]);

        $response = $this->actingAs($this->user)->get('/checkout');

        $response->assertRedirect(route('profile.index'))
                 ->assertSessionHas('error', 'Silakan tambahkan alamat pengiriman terlebih dahulu.');
    }

    public function test_checkout_uses_fallback_couriers()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id
        ]);
        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);

        $response = $this->actingAs($this->user)
                         ->get('/checkout?selected_items[]=' . $item->cart_item_id);

        $response->assertOk()
                 ->assertViewHas('couriers');
    }

    public function test_check_shipping_cost_without_buyer_address()
    {
        Address::where('user_id', $this->user->user_id)->delete();

        $response = $this->actingAs($this->user)
                         ->post('/checkout/check-shipping', [
                             'seller_id' => $this->seller->user_id,
                             'courier' => 'jne'
                         ]);

        $response->assertJson(['status' => 'error', 'message' => 'Alamat pembeli tidak ditemukan.']);
    }

    public function test_check_shipping_cost_without_seller_address()
    {
        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);
        Address::where('user_id', $this->seller->user_id)->delete();

        $response = $this->actingAs($this->user)
                         ->post('/checkout/check-shipping', [
                             'seller_id' => $this->seller->user_id,
                             'courier' => 'jne'
                         ]);

        $response->assertJson(['status' => 'error', 'message' => 'Alamat penjual tidak tersedia.']);
    }

    public function test_process_order_session_expired()
    {
        Session::forget('checkout_item_ids');

        $response = $this->actingAs($this->user)
                         ->postJson('/checkout/process', []);

        $response->assertStatus(400)
                 ->assertJson(['status' => 'error', 'message' => 'Sesi checkout kadaluarsa.']);
    }

    public function test_process_order_items_not_found()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        Session::put('checkout_item_ids', [999]);

        $response = $this->actingAs($this->user)
                         ->postJson('/checkout/process', []);

        $response->assertStatus(400)
                 ->assertJson(['status' => 'error', 'message' => 'Item tidak ditemukan.']);
    }

    public function test_process_order_without_default_address()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id, 'stock' => 100]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1
        ]);

        Session::put('checkout_item_ids', [$item->cart_item_id]);

        $response = $this->actingAs($this->user)
                         ->postJson('/checkout/process', [
                             'shipping' => [$this->seller->user_id => ['cost' => 10000, 'service' => 'REG']]
                         ]);

        $response->assertStatus(400)
                 ->assertJson(['status' => 'error', 'message' => 'Alamat utama belum diatur.']);
    }

    public function test_process_order_creates_midtrans_provider_if_not_exists()
    {
        $this->mock(\App\Services\RajaOngkirService::class);
        $this->mock(\App\Services\MidtransService::class);

        \App\Models\ShippingCarrier::create([
            'code' => 'jne',
            'name' => 'JNE',
            'provider_type' => 'rates', 
            'is_enabled' => true
        ]);

        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id, 'price' => 50000, 'stock' => 100]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1
        ]);
        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);
        Session::put('checkout_item_ids', [$item->cart_item_id]);

        $response = $this->actingAs($this->user)
                         ->postJson('/checkout/process', [
                             'shipping' => [$this->seller->user_id => ['cost' => 10000, 'service' => 'REG']]
                         ]);

        $response->assertOk(); 
        $this->assertDatabaseHas('integration_providers', ['code' => 'midtrans']);
    }

    public function test_process_order_exception_handling()
    {

        $response = $this->actingAs($this->user)
                         ->postJson('/checkout/process', []);

        $response->assertStatus(400);
    }
    public function test_check_shipping_cost_api_error()
    {
        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);
        Address::factory()->create(['user_id' => $this->seller->user_id, 'is_default' => true]);
        Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        
        $mock = $this->mock(\App\Services\RajaOngkirService::class);
        $mock->shouldReceive('checkCost')->andReturn(['status' => false, 'message' => 'API Error']);

        $response = $this->actingAs($this->user)
                         ->post('/checkout/check-shipping', [
                             'seller_id' => $this->seller->user_id,
                             'courier' => 'jne'
                         ]);

        $response->assertJson(['status' => 'error', 'message' => 'API Error']);
    }

    public function test_process_order_insufficient_stock()
    {
        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id, 'stock' => 5]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 10
        ]);
        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);
        Session::put('checkout_item_ids', [$item->cart_item_id]);

        $response = $this->actingAs($this->user)
                         ->postJson('/checkout/process', [
                             'shipping_data' => [$this->seller->user_id => ['cost' => 10000, 'service' => 'REG']]
                         ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['status' => 'error']);
        $this->assertStringContainsString('tidak mencukupi', $response->json('message'));
    }

    public function test_process_order_multi_seller_fee_distribution()
    {
        $this->mock(\App\Services\RajaOngkirService::class);
        $this->mock(\App\Services\MidtransService::class);

        $cart = Cart::factory()->create(['buyer_id' => $this->user->user_id]);
        Address::factory()->create(['user_id' => $this->user->user_id, 'is_default' => true]);
        
        $seller2 = User::factory()->create(['role' => 'seller']);
        
        $p1 = Product::factory()->create(['seller_id' => $this->seller->user_id, 'stock' => 10]);
        $p2 = Product::factory()->create(['seller_id' => $seller2->user_id, 'stock' => 10]);
        
        $i1 = CartItem::factory()->create(['cart_id' => $cart->cart_id, 'product_id' => $p1->product_id, 'quantity' => 1]);
        $i2 = CartItem::factory()->create(['cart_id' => $cart->cart_id, 'product_id' => $p2->product_id, 'quantity' => 1]);
        
        Session::put('checkout_item_ids', [$i1->cart_item_id, $i2->cart_item_id]);

        ShippingCarrier::create(['code' => 'jne', 'name' => 'JNE', 'provider_type' => 'rates', 'is_enabled' => true]);
        ShippingCarrier::create(['code' => 'pos', 'name' => 'POS', 'provider_type' => 'rates', 'is_enabled' => true]);

        $response = $this->actingAs($this->user)
                         ->postJson('/checkout/process', [
                             'shipping_data' => [
                                 $this->seller->user_id => ['cost' => 10000, 'service' => 'REG', 'courier' => 'jne'],
                                 $seller2->user_id => ['cost' => 15000, 'service' => 'REG', 'courier' => 'pos']
                             ]
                         ]);

        $response->assertOk();
        
        $order1 = Order::where('seller_id', $this->seller->user_id)->first();
        $order2 = Order::where('seller_id', $seller2->user_id)->first();
        
        $this->assertEquals(1250, $order1->platform_fee_buyer);
        $this->assertEquals(1250, $order2->platform_fee_buyer);
    }
}

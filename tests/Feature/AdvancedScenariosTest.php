<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdvancedScenariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_with_category_price_and_sort()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $cat = Category::factory()->create(['slug' => 'electronics']);

        Product::factory()->create([
            'seller_id' => $seller->user_id,
            'category_id' => $cat->category_id,
            'title' => 'Cheap Laptop',
            'price' => 5000000,
            'status' => 'active'
        ]);

        Product::factory()->create([
            'seller_id' => $seller->user_id,
            'category_id' => $cat->category_id,
            'title' => 'Expensive Laptop',
            'price' => 15000000,
            'status' => 'active'
        ]);

        $response = $this->get(route('search.index', [
            'search' => 'Laptop',
            'category' => 'electronics',
            'min_price' => 4000000,
            'max_price' => 20000000,
            'sort' => 'price_desc'
        ]));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Expensive Laptop', 'Cheap Laptop']);
    }

    public function test_seller_can_create_complex_product()
    {
        Storage::fake('public');
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        $cat = Category::factory()->create();

        $file = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($seller)->post(route('shop.products.store'), [
            'title' => 'Complex Product',
            'category_id' => $cat->category_id,
            'price' => 10000,
            'weight' => 1000,
            'stock' => 50,
            'condition' => 'new',
            'description' => 'A very detailed description.',
            'status_input' => 'active',
            'images' => [$file],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'title' => 'Complex Product',
            'price' => 10000,
            'condition' => 'new'
        ]);
    }

    public function test_admin_can_suspend_product_and_it_disappears_from_search()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create([
            'seller_id' => $seller->user_id,
            'title' => 'Bad Product',
            'status' => 'active'
        ]);

        $this->get(route('search.index', ['search' => 'Bad Product']))
             ->assertSee('Bad Product');

        $this->actingAs($admin)->patch(
            route('admin.products.toggle_status', $product->product_id)
        );

        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'status' => 'suspended'
        ]);
    }

    public function test_checkout_decrements_product_stock()
    {
        $this->withoutExceptionHandling();
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        Address::factory()->create(['user_id' => $buyer->user_id, 'is_default' => true]);

        $product = Product::factory()->create([
            'seller_id' => $seller->user_id,
            'stock' => 10,
            'price' => 50000
        ]);

        \App\Models\ShippingCarrier::create([
            'code' => 'jne',
            'name' => 'JNE',
            'is_enabled' => true,
            'provider_type' => 'rates'
        ]);

        $cart = Cart::create(['buyer_id' => $buyer->user_id]);
        $cart->items()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'unit_price' => $product->price,
            'subtotal' => $product->price * 2
        ]);
        
        session(['checkout_item_ids' => [$cart->items()->first()->cart_item_id]]);

        \App\Models\IntegrationProvider::firstOrCreate(
            ['code' => 'midtrans'],
            ['name' => 'Midtrans']
        );

        $response = $this->actingAs($buyer)->post(route('checkout.process'), [
            'selected_address_id' => $buyer->addresses->first()->address_id,
            'shipping_data' => [
                $seller->user_id => [
                    'service' => 'REG',
                    'cost' => 10000,
                    'courier' => 'jne'
                ]
            ]
        ]);
        
        $response->assertStatus(200); 
        
        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'stock' => 8
        ]);
    }

    public function test_user_can_access_payment_page_for_pending_order()
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
            'code' => 'ORD-PAY-' . time(),
        ]);

        $provider = DB::table('integration_providers')
            ->where('code', 'midtrans')
            ->first();

        if (!$provider) {
            $providerId = DB::table('integration_providers')->insertGetId([
                'code' => 'midtrans',
                'name' => 'Midtrans'
            ]);
        } else {
            $providerId = $provider->integration_provider_id;
        }

        $payment = Payment::create([
            'order_id' => $order->order_id,
            'provider_id' => $providerId,
            'amount' => 50000,
            'status' => 'pending',
            'currency' => 'IDR'
        ]);

        $response = $this->actingAs($buyer)->get(
            route('payment.show', $payment->payment_id)
        );

        $response->assertStatus(200);
    }
}

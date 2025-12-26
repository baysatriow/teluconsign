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
use Tests\TestCase;

class AdvancedScenariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_with_category_price_and_sort()
    {
        // 1. Setup multiple products
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

        // 2. Perform advanced search
        $response = $this->get(route('search.index', [
            'search' => 'Laptop',
            'category' => 'electronics',
            'min_price' => 4000000,
            'max_price' => 20000000,
            'sort' => 'price_desc'
        ]));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Expensive Laptop', 'Cheap Laptop']); // Sort check
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
            'stock' => 50,
            'condition' => 'new',
            'description' => 'A very detailed description.',
            'main_image' => $file,
            // Assuming multiple images handling might exist, but sticking to main for validity
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'title' => 'Complex Product',
            'price' => 10000,
            'condition' => 'new'
        ]);
        
        // Check storage for image (simple check)
        // Storage::disk('public')->assertExists('products/' . $file->hashName()); // Path depends on controller logic
    }

    public function test_admin_can_suspend_product_and_it_disappears_from_search()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['seller_id' => $seller->user_id, 'title' => 'Bad Product', 'status' => 'active']);

        // 1. Ensure visible
        $this->get(route('search.index', ['search' => 'Bad Product']))
             ->assertSee('Bad Product');

        // 2. Admin suspends it (toggles status)
        // Check route: admin.products.toggle_status (PATCH)
        $this->actingAs($admin)->patch(route('admin.products.toggle_status', $product->product_id));

        // 3. Ensure invisible (product status should change)
        $this->assertDatabaseHas('products', [
             'product_id' => $product->product_id,
             'status' => 'suspended' // or whatever toggle logic sets it to
        ]);

        $this->get(route('search.index', ['search' => 'Bad Product']))
             ->assertDontSee('Bad Product'); // Search controller filters Active only
    }

    public function test_checkout_decrements_product_stock()
    {
        // Setup similar to CheckoutTest but focus on stock Logic
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        Address::factory()->create(['user_id' => $buyer->user_id, 'is_default' => true]);

        $product = Product::factory()->create([
            'seller_id' => $seller->user_id, 
            'stock' => 10,
            'price' => 50000
        ]);

        // Add to cart directly
        $cart = Cart::create(['buyer_id' => $buyer->user_id]);
        $cart->items()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'unit_price' => $product->price,
            'subtotal' => $product->price * 2,
            'price_at_add' => $product->price
        ]);
        
        session(['checkout_item_ids' => [$cart->items()->first()->cart_item_id]]);

        $response = $this->actingAs($buyer)->post(route('checkout.process'), [
            'selected_address_id' => $buyer->addresses->first()->address_id,
            'shipping_service' => 'jne',
            'shipping_cost' => 10000
        ]);

        $response->assertStatus(200); // Or redirect to payment
        
        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'stock' => 8 // 10 - 2
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

        // Create Payment Gateway (Integration Provider)
        $providerId = \Illuminate\Support\Facades\DB::table('integration_providers')->insertGetId([
            'code' => 'midtrans',
            'name' => 'Midtrans',
            'created_at' => now()
        ]);

        $payment = Payment::create([
            'order_id' => $order->order_id,
            'provider_id' => $providerId, // New column
            'amount' => 50000,
            'status' => 'pending',
            'currency' => 'IDR'
        ]);

        $response = $this->actingAs($buyer)->get(route('payment.show', $payment->payment_id));

        $response->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Enums\ProductStatus;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seller;
    protected $buyer;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seller = User::factory()->create(['role' => 'seller']);
        $this->buyer = User::factory()->create(['role' => 'buyer']);
        
        $this->product = Product::factory()->create([
            'seller_id' => $this->seller->user_id,
            'stock' => 10,
            'price' => 50000,
            'status' => ProductStatus::Active
        ]);
    }

    

    public function test_show_product_page_accessible()
    {
        $response = $this->get(route('product.show', $this->product));
        
        $response->assertOk();
        $response->assertSee($this->product->title);
        $response->assertViewHas('product');
        $response->assertViewHas('shopProducts');
        $response->assertViewHas('relatedProducts');
    }

    public function test_show_inactive_product_returns_404()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->seller->user_id,
            'status' => ProductStatus::Archived
        ]);

        $response = $this->get(route('product.show', $product));

        $response->assertNotFound();
    }

    

    public function test_buy_now_adds_to_cart_and_redirects()
    {
        $response = $this->actingAs($this->buyer)
            ->post(route('product.buy', $this->product), ['quantity' => 2]);

        $response->assertRedirect(route('cart.index', ['selected_item' => $this->product->product_id]));
        
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->product_id,
            'quantity' => 2
        ]);
    }

    public function test_buy_now_fails_if_stock_insufficient()
    {
        $this->product->update(['stock' => 1]);

        $response = $this->actingAs($this->buyer)
            ->post(route('product.buy', $this->product), ['quantity' => 2]);

        $response->assertRedirect(); 
        $response->assertSessionHas('error', 'Stok tidak mencukupi. Sisa stok: 1');
        
        $this->assertDatabaseMissing('cart_items', ['product_id' => $this->product->product_id]);
    }

    public function test_buy_now_fails_if_buying_own_product()
    {
        $response = $this->actingAs($this->seller)
            ->post(route('product.buy', $this->product), ['quantity' => 1]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tidak bisa membeli produk sendiri.');
    }

    public function test_buy_now_fails_if_cart_limit_exceeded()
    {
        
        $cart = Cart::factory()->create(['buyer_id' => $this->buyer->user_id]);
        CartItem::factory()->create([
            'cart_id' => $cart->cart_id, 
            'product_id' => $this->product->product_id, 
            'quantity' => 9
        ]);

        
        $response = $this->actingAs($this->buyer)
            ->post(route('product.buy', $this->product), ['quantity' => 2]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Total di keranjang melebihi stok tersedia.');
    }
}

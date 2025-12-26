<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create([
            'user_id' => $seller->user_id,
            'is_shop_default' => true
        ]);
        
        
        Product::factory()->create([
            'title' => 'Gaming Laptop',
            'description' => 'High performance laptop',
            'price' => 15000000,
            'seller_id' => $seller->user_id,
            'status' => 'active'
        ]);

        Product::factory()->create([
            'title' => 'Office Chair',
            'description' => 'Comfortable chair',
            'price' => 1000000,
            'seller_id' => $seller->user_id,
            'status' => 'active'
        ]);
    }

    public function test_user_can_view_search_page()
    {
        $response = $this->get(route('search.index'));
        $response->assertStatus(200);
        $response->assertSee('Gaming Laptop');
        $response->assertSee('Office Chair');
    }

    public function test_user_can_search_by_keyword()
    {
        $response = $this->get(route('search.index', ['search' => 'Laptop']));
        $response->assertStatus(200);
        $response->assertSee('Gaming Laptop');
        $response->assertDontSee('Office Chair');
    }

    public function test_user_can_view_product_detail()
    {
        $product = Product::first();
        $response = $this->get(route('product.show', $product->slug));
        
        $response->assertStatus(200);
        $response->assertSee($product->title);
        $response->assertSee(number_format($product->price, 0, ',', '.'));
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_accessible()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    public function test_home_page_displays_categories()
    {
        $category = Category::factory()->create(['name' => 'Elektronik', 'is_active' => true]);
        $inactive = Category::factory()->create(['name' => 'HiddenCat', 'is_active' => false]);
        
        $response = $this->get(route('home'));
        
        $response->assertOk();
        $response->assertSee('Elektronik');
        $response->assertDontSee('HiddenCat');
    }

    public function test_home_page_displays_products()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create([
            'seller_id' => $seller->user_id,
            'title' => 'iPhone 15 Pro',
            'status' => 'active'
        ]);
        
        $draft = Product::factory()->create([
            'seller_id' => $seller->user_id,
            'title' => 'Draft Product',
            'status' => 'archived'
        ]);
        
        $response = $this->get(route('home'));
        
        $response->assertOk();
        $response->assertSee('iPhone 15 Pro');
        $response->assertDontSee('Draft Product');
    }
}

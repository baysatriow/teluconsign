<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Enums\ProductCondition;
use App\Enums\ProductStatus;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_with_category_hierarchy()
    {
        $parentCategory = Category::factory()->create(['parent_id' => null]);
        $childCategory = Category::factory()->create(['parent_id' => $parentCategory->category_id]);
        
        Product::factory()->create([
            'category_id' => $childCategory->category_id,
            'status' => ProductStatus::Active
        ]);

        $response = $this->get("/search?category={$parentCategory->category_id}");

        $response->assertOk();
        $response->assertViewHas('selectedCategory');
    }

    public function test_search_filter_by_condition_new()
    {
        Product::factory()->create([
            'condition' => ProductCondition::New,
            'status' => ProductStatus::Active
        ]);

        $response = $this->get('/search?condition=new');

        $response->assertOk();
    }

    public function test_search_filter_by_condition_used()
    {
        Product::factory()->create([
            'condition' => ProductCondition::Used,
            'status' => ProductStatus::Active
        ]);

        $response = $this->get('/search?condition=used');

        $response->assertOk();
    }

    public function test_search_sort_by_price_asc()
    {
        Product::factory()->create(['price' => 50000, 'status' => ProductStatus::Active]);
        Product::factory()->create(['price' => 100000, 'status' => ProductStatus::Active]);

        $response = $this->get('/search?sort=price_asc');

        $response->assertOk();
    }

    public function test_search_sort_by_price_desc()
    {
        Product::factory()->create(['price' => 50000, 'status' => ProductStatus::Active]);
        Product::factory()->create(['price' => 100000, 'status' => ProductStatus::Active]);

        $response = $this->get('/search?sort=price_desc');

        $response->assertOk();
    }

    public function test_search_sort_by_newest()
    {
        Product::factory()->create(['status' => ProductStatus::Active]);

        $response = $this->get('/search?sort=newest');

        $response->assertOk();
    }

    public function test_search_with_keyword()
    {
        Product::factory()->create([
            'title' => 'iPhone 14',
            'status' => ProductStatus::Active
        ]);

        $response = $this->get('/search?q=iPhone');

        $response->assertOk();
    }
}

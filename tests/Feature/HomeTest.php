<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_correctly(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_homepage_displays_products(): void
    {
        $product = Product::factory()->create();

        $response = $this->get('/');

        $response->assertSee($product->title);
    }
}

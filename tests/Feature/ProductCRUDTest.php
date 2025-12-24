<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_view_product_list(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        \App\Models\Address::factory()->create([
            'user_id' => $seller->user_id,
            'is_shop_default' => true
        ]);
        
        $product = Product::factory()->create(['seller_id' => $seller->user_id]);

        $response = $this->actingAs($seller)->get(route('shop.products.index'));

        $response->assertStatus(200);
        $response->assertSee($product->title);
    }

    public function test_seller_can_create_product(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create(['role' => 'seller']);
        \App\Models\Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        $category = Category::factory()->create();

        $file = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($seller)->post(route('shop.products.store'), [
            'title' => 'New Product',
            'description' => 'Product Description',
            'price' => 150000,
            'stock' => 10,
            'weight' => 2000,
            'condition' => 'new',
            'category_id' => $category->category_id,
            'status_input' => 'active',
            'images' => [$file],
        ]);

        $response->assertRedirect(route('shop.products.index'));
        $this->assertDatabaseHas('products', [
            'title' => 'New Product',
            'seller_id' => $seller->user_id,
        ]);
    }

    public function test_seller_can_update_product(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        \App\Models\Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        $product = Product::factory()->create(['seller_id' => $seller->user_id]);

        $response = $this->actingAs($seller)->put(route('shop.products.update', $product), [
            'title' => 'Updated Title',
            'description' => $product->description,
            'price' => 200000,
            'stock' => $product->stock,
            'weight' => 2000,
            'condition' => $product->condition,
            'category_id' => $product->category_id,
            'status_input' => 'active',
        ]);

        $response->assertRedirect(route('shop.products.index'));
        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_seller_can_delete_product(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        \App\Models\Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        $product = Product::factory()->create(['seller_id' => $seller->user_id]);

        $response = $this->actingAs($seller)->delete(route('shop.products.delete', $product->product_id));

        $response->assertRedirect(route('shop.products.index'));
        $this->assertDatabaseMissing('products', [
            'product_id' => $product->product_id,
        ]);
    }
}

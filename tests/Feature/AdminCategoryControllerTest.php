<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class AdminCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ============ INDEX TESTS ============

    public function test_admin_can_view_categories_index()
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
                         ->get('/admin/categories');

        $response->assertOk()
                 ->assertViewIs('admin.categories.index')
                 ->assertViewHas('categories')
                 ->assertViewHas('allCategories');
    }

    public function test_non_admin_cannot_access_categories_index()
    {
        $user = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($user)
                         ->get('/admin/categories');

        $response->assertRedirect('/');
    }

    // ============ STORE TESTS ============

    public function test_admin_can_create_category()
    {
        $response = $this->actingAs($this->admin)
                         ->post('/admin/categories', [
                             'name' => 'Electronics',
                             'parent_id' => null
                         ]);

        $response->assertSessionHas('success', 'Kategori berhasil ditambahkan.');
        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'slug' => 'electronics'
        ]);
    }

    public function test_admin_can_create_subcategory()
    {
        $parent = Category::factory()->create(['name' => 'Electronics']);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/categories', [
                             'name' => 'Smartphones',
                             'parent_id' => $parent->category_id
                         ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', [
            'name' => 'Smartphones',
            'parent_id' => $parent->category_id
        ]);
    }

    public function test_create_category_requires_unique_name()
    {
        Category::factory()->create(['name' => 'Electronics']);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/categories', [
                             'name' => 'Electronics'
                         ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_create_category_validates_parent_id_exists()
    {
        $response = $this->actingAs($this->admin)
                         ->post('/admin/categories', [
                             'name' => 'Test Category',
                             'parent_id' => 99999
                         ]);

        $response->assertSessionHasErrors('parent_id');
    }

    // ============ UPDATE TESTS ============

    public function test_admin_can_update_category()
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin)
                         ->put("/admin/categories/{$category->category_id}", [
                             'name' => 'New Name',
                             'parent_id' => null
                         ]);

        $response->assertSessionHas('success', 'Kategori berhasil diperbarui.');
        $this->assertDatabaseHas('categories', [
            'category_id' => $category->category_id,
            'name' => 'New Name',
            'slug' => 'new-name'
        ]);
    }

    public function test_update_category_prevents_self_parent()
    {
        $category = Category::factory()->create(['name' => 'Test Category']);

        $response = $this->actingAs($this->admin)
                         ->put("/admin/categories/{$category->category_id}", [
                             'name' => 'Test Category',
                             'parent_id' => $category->category_id
                         ]);

        $response->assertSessionHas('error', 'Kategori tidak bisa menjadi induk bagi dirinya sendiri.');
    }

    public function test_update_category_allows_unique_name_for_same_category()
    {
        $category = Category::factory()->create(['name' => 'Electronics']);

        $response = $this->actingAs($this->admin)
                         ->put("/admin/categories/{$category->category_id}", [
                             'name' => 'Electronics',
                             'parent_id' => null
                         ]);

        $response->assertSessionHas('success');
    }


    // ============ DESTROY TESTS ============

    public function test_admin_can_delete_empty_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->delete("/admin/categories/{$category->category_id}");

        $response->assertSessionHas('success', 'Kategori berhasil dihapus.');
        $this->assertDatabaseMissing('categories', [
            'category_id' => $category->category_id
        ]);
    }

    public function test_delete_category_orphans_children()
    {
        $parent = Category::factory()->create();
        $child1 = Category::factory()->create(['parent_id' => $parent->category_id]);
        $child2 = Category::factory()->create(['parent_id' => $parent->category_id]);

        $response = $this->actingAs($this->admin)
                         ->delete("/admin/categories/{$parent->category_id}");

        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('categories', [
            'category_id' => $child1->category_id,
            'parent_id' => null
        ]);
        $this->assertDatabaseHas('categories', [
            'category_id' => $child2->category_id,
            'parent_id' => null
        ]);
    }

    public function test_delete_category_with_products_reassign_action()
    {
        $categoryToDelete = Category::factory()->create(['name' => 'Old Category']);
        $targetCategory = Category::factory()->create(['name' => 'Target Category']);
        
        Product::factory()->count(3)->create(['category_id' => $categoryToDelete->category_id]);

        $response = $this->actingAs($this->admin)
                         ->delete("/admin/categories/{$categoryToDelete->category_id}", [
                             'action' => 'reassign',
                             'target_category_id' => $targetCategory->category_id
                         ]);

        $response->assertSessionHas('success');
        
        $this->assertEquals(3, Product::where('category_id', $targetCategory->category_id)->count());
        $this->assertDatabaseMissing('categories', [
            'category_id' => $categoryToDelete->category_id
        ]);
    }

    public function test_delete_category_with_products_force_delete_action()
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->category_id]);

        $response = $this->actingAs($this->admin)
                         ->delete("/admin/categories/{$category->category_id}", [
                             'action' => 'force_delete'
                         ]);

        $response->assertSessionHas('success');
        
        $this->assertEquals(0, Product::where('category_id', $category->category_id)->count());
        $this->assertDatabaseMissing('categories', [
            'category_id' => $category->category_id
        ]);
    }

    public function test_delete_category_with_products_no_action()
    {
        $category = Category::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $category->category_id]);

        $response = $this->actingAs($this->admin)
                         ->delete("/admin/categories/{$category->category_id}", [
                             'action' => 'none'
                         ]);

        $response->assertSessionHas('success');
        
        // Products should still exist with old category_id (orphaned)
        $this->assertDatabaseMissing('categories', [
            'category_id' => $category->category_id
        ]);
    }

    public function test_delete_category_handles_exception()
    {
        $category = Category::factory()->create();

        // Mock a scenario that would throw exception
        // We'll use a non-existent category ID to trigger findOrFail exception
        $response = $this->actingAs($this->admin)
                         ->delete("/admin/categories/99999");

        $response->assertNotFound();
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_category_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_category()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'New Category',
            'slug' => 'new-category',
            'icon' => 'fa-test'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'slug' => 'new-category'
        ]);
    }

    public function test_admin_can_delete_user()
    {
        $userToDelete = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $userToDelete->user_id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', [
            'user_id' => $userToDelete->user_id
        ]);
    }

    public function test_non_admin_cannot_manage_categories()
    {
        $user = User::factory()->create(['role' => 'buyer']);
        
        $response = $this->actingAs($user)->get(route('admin.categories.index'));
        // Middleware redirects to home
        $response->assertRedirect('/');

        $response = $this->actingAs($user)->post(route('admin.categories.store'), [
             'name' => 'Hacker Category'
        ]);
        $response->assertRedirect('/');
    }
}

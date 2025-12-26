<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ProfileValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_info()
    {
        $user = User::factory()->create([
            'is_verified' => true
        ]);
        
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Updated Name',
            'username' => 'updated_username',
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_can_update_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
            'is_verified' => true
        ]);

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'oldpassword123',
            'new_password' => 'NewPassword123!',
            'new_password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }

    public function test_user_can_add_address()
    {
        $user = User::factory()->create(['is_verified' => true]);

        $response = $this->actingAs($user)->post(route('profile.address.add'), [
            'label' => 'Home',
            'recipient' => 'John Doe',
            'phone' => '081234567890',
            'postal_code' => '12345',
            'detail_address' => 'Jl. Test No. 123',
            'province' => 'Test Province',
            'city' => 'Test City',
            'district' => 'Test District',
            'village' => 'Test Village',
        ]);

        $response->assertRedirect();
        
        // Sometimes address creation fails silently if validation fails.
        // Let's assert database count first or check session errors.
        if (session('errors')) {
             dump(session('errors')->all());
        }
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->user_id,
            'recipient' => 'John Doe',
        ]);
    }

    public function test_user_can_update_address()
    {
        $user = User::factory()->create(['is_verified' => true]);
        $address = Address::factory()->create(['user_id' => $user->user_id]);

        $response = $this->actingAs($user)->put(route('profile.address.update', $address->address_id), [
            'label' => 'Office',
            'recipient' => 'Updated Recipient',
            'phone' => '08987654321',
            'postal_code' => '54321',
            'detail_address' => 'Jl. Baru No. 321',
            'province' => 'New Province',
            'city' => 'New City',
            'district' => 'New District',
            'village' => 'New Village',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('addresses', [
            'address_id' => $address->address_id,
            'recipient' => 'Updated Recipient',
        ]);
    }

    public function test_user_can_delete_address()
    {
        $user = User::factory()->create(['is_verified' => true]);
        $address = Address::factory()->create(['user_id' => $user->user_id]);

        $response = $this->actingAs($user)->delete(route('profile.address.delete', $address->address_id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('addresses', [
            'address_id' => $address->address_id,
        ]);
    }

    public function test_user_can_set_default_address()
    {
        $user = User::factory()->create(['is_verified' => true]);
        $address1 = Address::factory()->create(['user_id' => $user->user_id, 'is_default' => false]);
        $address2 = Address::factory()->create(['user_id' => $user->user_id, 'is_default' => true]);

        $response = $this->actingAs($user)->patch(route('profile.address.default', $address1->address_id));

        $response->assertRedirect();
        $this->assertTrue($address1->fresh()->is_default);
        $this->assertFalse($address2->fresh()->is_default);
    }
}

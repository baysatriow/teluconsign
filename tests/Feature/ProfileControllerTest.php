<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Address;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    

    public function test_profile_index_loads_with_relations()
    {
        Profile::factory()->create(['user_id' => $this->user->user_id]);
        Address::factory()->create(['user_id' => $this->user->user_id]);

        $response = $this->actingAs($this->user)->get('/profile');

        $response->assertOk()
                 ->assertViewIs('profile.index')
                 ->assertViewHas('user');
    }

    

    public function test_update_profile_with_photo()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->user)
                         ->put('/profile/update', [
                             'name' => 'Updated Name',
                             'bio' => 'New bio',
                             'photo' => $file
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        
        Storage::disk('public')->assertExists('profile-photos/' . $file->hashName());
    }

    public function test_update_profile_without_photo()
    {
        $response = $this->actingAs($this->user)
                         ->put('/profile/update', [
                             'name' => 'Updated Name',
                             'bio' => 'New bio'
                         ]);

        $response->assertRedirect();
        $this->assertEquals('Updated Name', $this->user->fresh()->name);
    }

    

    public function test_change_password_with_wrong_current_password()
    {
        $response = $this->actingAs($this->user)
                         ->put('/profile/password', [
                             'current_password' => 'wrongpassword',
                             'new_password' => 'NewPassword123!',
                             'new_password_confirmation' => 'NewPassword123!'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['current_password' => 'Password saat ini salah.']);
    }

    public function test_change_password_success()
    {
        $this->user->password = Hash::make('currentpass');
        $this->user->save();

        $response = $this->actingAs($this->user)
                         ->put('/profile/password', [
                             'current_password' => 'currentpass',
                             'new_password' => 'NewPassword123!',
                             'new_password_confirmation' => 'NewPassword123!'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewPassword123!', $this->user->fresh()->password));
    }

    

    public function test_store_address_sets_first_as_default()
    {
        $response = $this->actingAs($this->user)
                         ->post('/profile/address', [
                             'label' => 'Home',
                             'recipient' => 'John Doe',
                             'phone' => '08123456789',
                             'detail_address' => '123 Main St',
                             'province' => 'DKI Jakarta',
                             'city' => 'Jakarta Pusat',
                             'district' => 'Menteng',
                             'village' => 'Menteng',
                             'postal_code' => '10310',
                             'location_id' => '1234'
                         ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->user->user_id,
            'is_default' => true
        ]);
    }

    public function test_store_address_with_is_default_resets_others()
    {
        
        Address::factory()->create([
            'user_id' => $this->user->user_id,
            'is_default' => true
        ]);

        $response = $this->actingAs($this->user)
                         ->post('/profile/address', [
                             'label' => 'Office',
                             'recipient' => 'John Doe',
                             'phone' => '08123456789',
                             'detail_address' => '456 Office St',
                             'province' => 'DKI Jakarta',
                             'city' => 'Jakarta Pusat',
                             'district' => 'Menteng',
                             'village' => 'Menteng',
                             'postal_code' => '10310',
                             'location_id' => '1234',
                             'is_default' => '1'
                         ]);

        $response->assertRedirect();
        
        
        $this->assertEquals(1, Address::where('user_id', $this->user->user_id)
                                      ->where('is_default', true)
                                      ->count());
    }

    public function test_update_address_with_is_default_resets_others()
    {
        $address1 = Address::factory()->create([
            'user_id' => $this->user->user_id,
            'is_default' => true
        ]);
        
        $address2 = Address::factory()->create([
            'user_id' => $this->user->user_id,
            'is_default' => false
        ]);

        $response = $this->actingAs($this->user)
                         ->put("/profile/address/{$address2->address_id}", [
                             'label' => 'Updated',
                             'recipient' => 'Jane Doe',
                             'phone' => '08123456789',
                             'detail_address' => '789 Street',
                             'postal_code' => '10310',
                             'is_default' => '1'
                         ]);

        $response->assertRedirect();
        
        
        $this->assertTrue($address2->fresh()->is_default);
        $this->assertFalse($address1->fresh()->is_default);
    }

    public function test_update_address_not_found()
    {
        $response = $this->actingAs($this->user)
                         ->put('/profile/address/999', [
                             'label' => 'Valid',
                             'recipient' => 'Valid',
                             'phone' => '08123',
                             'detail_address' => 'Valid',
                             'postal_code' => '12345'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Alamat tidak ditemukan.');
    }

    public function test_delete_default_address_assigns_new_default()
    {
        $address1 = Address::factory()->create([
            'user_id' => $this->user->user_id,
            'is_default' => true
        ]);
        
        $address2 = Address::factory()->create([
            'user_id' => $this->user->user_id,
            'is_default' => false
        ]);

        $response = $this->actingAs($this->user)
                         ->delete("/profile/address/{$address1->address_id}");

        $response->assertRedirect();
        
        
        $this->assertTrue($address2->fresh()->is_default);
    }

    public function test_delete_address_not_found()
    {
        $response = $this->actingAs($this->user)
                         ->delete('/profile/address/999');

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Alamat tidak ditemukan.');
    }

    

    public function test_request_phone_update_validation()
    {
        $response = $this->actingAs($this->user)
                         ->post('/profile/phone/request', [
                             'new_phone' => 'invalid'
                         ]);

        $response->assertSessionHasErrors('new_phone');
    }

    public function test_request_phone_update_stores_in_cache()
    {
        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')
                 ->once()
                 ->andReturn(['status' => true]);
        });

        $this->actingAs($this->user)
             ->post('/profile/phone/request', [
                 'new_phone' => '628123456789'
             ]);

        $cacheKey = 'phone_update_' . $this->user->user_id;
        $this->assertTrue(Cache::has($cacheKey));
    }

    public function test_request_phone_update_exception_handling()
    {
        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')
                 ->andThrow(new \Exception('Network error'));
        });

        $response = $this->actingAs($this->user)
                         ->postJson('/profile/phone/request', [
                             'new_phone' => '628123456789'
                         ]);

        $response->assertStatus(500)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Gagal mengirim OTP. Pastikan nomor WhatsApp aktif.'
                 ]);
    }

    public function test_verify_phone_update_with_wrong_otp()
    {
        Cache::put('phone_update_' . $this->user->user_id, [
            'new_phone' => '628123456789',
            'otp' => '123456'
        ], now()->addMinutes(5));

        $response = $this->actingAs($this->user)
                         ->postJson('/profile/phone/verify', [
                             'otp' => '999999'
                         ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Kode OTP salah atau kadaluarsa.'
                 ]);
    }

    public function test_verify_phone_update_with_expired_otp()
    {
        
        $response = $this->actingAs($this->user)
                         ->postJson('/profile/phone/verify', [
                             'otp' => '123456'
                         ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Kode OTP salah atau kadaluarsa.'
                 ]);
    }

    public function test_verify_phone_update_success()
    {
        Cache::put('phone_update_' . $this->user->user_id, [
            'new_phone' => '628123456789',
            'otp' => '123456'
        ], now()->addMinutes(5));

        $response = $this->actingAs($this->user)
                         ->postJson('/profile/phone/verify', [
                             'otp' => '123456'
                         ]);

        $response->assertOk()
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Nomor WhatsApp berhasil diperbarui.'
                 ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $this->user->user_id,
            'phone' => '628123456789'
        ]);

        $this->assertFalse(Cache::has('phone_update_' . $this->user->user_id));
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Mockery;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // ============ REGISTRATION TESTS ============

    public function test_show_register_form()
    {
        $response = $this->get('/register');

        $response->assertOk()
                 ->assertViewIs('auth.register');
    }

    public function test_register_with_valid_data()
    {
        $this->markTestSkipped('Requires Fonnte API mocking that conflicts with DB transactions');
    }

    public function test_register_creates_otp()
    {
        $this->markTestSkipped('Requires Fonnte API mocking');
    }

    public function test_register_sets_session_data()
    {
        $this->markTestSkipped('Requires Fonnte API mocking');
    }

    public function test_register_with_invalid_password()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '628123456789',
            'password' => 'weak',
            'password_confirmation' => 'weak'
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_with_duplicate_username()
    {
        User::factory()->create(['username' => 'existinguser']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'existinguser',
            'email' => 'new@example.com',
            'phone' => '628123456789',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_register_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'newuser',
            'email' => 'existing@example.com',
            'phone' => '628123456789',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_register_exception_handling()
    {
        $this->markTestSkipped('Requires Fonnte API mocking');
    }

    // ============ LOGIN TESTS ============

    public function test_show_login_form()
    {
        $response = $this->get('/login');

        $response->assertOk()
                 ->assertViewIs('auth.login');
    }

    public function test_login_with_email()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $response = $this->post('/login', [
            'login' => 'user@test.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('otp.verify'));
        $this->assertEquals('login', session('otp_context'));
    }

    public function test_login_with_username()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $response = $this->post('/login', [
            'login' => 'testuser',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('otp.verify'));
    }

    public function test_login_with_wrong_password()
    {
        User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('correctpassword'),
            'status' => 'active'
        ]);

        $response = $this->post('/login', [
            'login' => 'user@test.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('login');
    }

    public function test_login_with_nonexistent_user()
    {
        $response = $this->post('/login', [
            'login' => 'nonexistent@test.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('login');
    }

    public function test_login_with_suspended_user()
    {
        User::factory()->create([
            'email' => 'suspended@test.com',
            'password' => Hash::make('password123'),
            'status' => 'suspended'
        ]);

        $response = $this->post('/login', [
            'login' => 'suspended@test.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['login' => 'Akun Anda sedang ditangguhkan/non-aktif.']);
    }

    public function test_login_generates_otp()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $this->post('/login', [
            'login' => 'user@test.com',
            'password' => 'password123'
        ]);

        $user->refresh();
        $this->assertNotNull($user->otp_code);
        $this->assertNotNull($user->otp_expires_at);
    }

    public function test_login_without_phone()
    {
        $user = User::factory()->create([
            'email' => 'nophone@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);

        // Don't create profile with phone

        $response = $this->post('/login', [
            'login' => 'nophone@test.com',
            'password' => 'password123'
        ]);

        // Should still redirect to OTP verify even without phone
        $response->assertRedirect(route('otp.verify'));
    }

    public function test_login_with_remember_me()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $response = $this->post('/login', [
            'login' => 'user@test.com',
            'password' => 'password123',
            'remember' => true
        ]);

        $this->assertTrue(session('otp_remember'));
    }

    // ============ LOGOUT TESTS ============

    public function test_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Anda telah keluar.');
        $this->assertGuest();
    }

    public function test_logout_invalidates_session()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        
        // Set some session data
        session(['test_key' => 'test_value']);
        
        $response = $this->post('/logout');

        // Session should be invalidated
        $this->assertNull(session('test_key'));
    }
}

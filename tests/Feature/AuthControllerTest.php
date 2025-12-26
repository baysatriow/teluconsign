<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mockery;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_show_register_form()
    {
        $response = $this->get('/register');

        $response->assertOk()
                 ->assertViewIs('auth.register');
    }

    public function test_register_with_valid_data()
    {
        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once()->andReturn(['status' => true]);
        });

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '628123456789',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ]);

        $response->assertRedirect(route('otp.verify'));
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $this->assertDatabaseHas('profiles', ['phone' => '628123456789']);
        $this->assertEquals('activation', session('otp_context'));
    }

    public function test_register_creates_otp()
    {
        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $this->post('/register', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '628123456789',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user->otp_code);
        $this->assertNotNull($user->otp_expires_at);
    }

    public function test_register_sets_session_data()
    {
        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $this->post('/register', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '628123456789',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertEquals($user->user_id, session('otp_user_id'));
        $this->assertEquals('activation', session('otp_context'));
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
        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->andThrow(new \Exception('Fonnte error'));
        });

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '628123456789',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('users', ['email' => 'john@example.com']);
    }

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
            'password' => Hash::make('Password123!'),
            'status' => 'active'
        ]);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $response = $this->post('/login', [
            'login' => 'user@test.com',
            'password' => 'Password123!'
        ]);

        $response->assertRedirect(route('otp.verify'));
        $this->assertEquals('login', session('otp_context'));
        $this->assertEquals($user->user_id, session('otp_user_id'));
    }

    public function test_login_with_username()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('Password123!'),
            'status' => 'active'
        ]);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $response = $this->post('/login', [
            'login' => 'testuser',
            'password' => 'Password123!'
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
            'password' => Hash::make('Password123!'),
            'status' => 'suspended'
        ]);

        $response = $this->post('/login', [
            'login' => 'suspended@test.com',
            'password' => 'Password123!'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['login' => 'Akun Anda sedang ditangguhkan/non-aktif.']);
    }

    public function test_login_generates_otp()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('Password123!'),
            'status' => 'active'
        ]);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $this->post('/login', [
            'login' => 'user@test.com',
            'password' => 'Password123!'
        ]);

        $user->refresh();
        $this->assertNotNull($user->otp_code);
        $this->assertNotNull($user->otp_expires_at);
    }

    public function test_login_without_phone()
    {
        $user = User::factory()->create([
            'email' => 'nophone@test.com',
            'password' => Hash::make('Password123!'),
            'status' => 'active'
        ]);

        $response = $this->post('/login', [
            'login' => 'nophone@test.com',
            'password' => 'Password123!'
        ]);

        $response->assertRedirect(route('otp.verify'));
    }

    public function test_login_with_remember_me()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('Password123!'),
            'status' => 'active'
        ]);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $this->post('/login', [
            'login' => 'user@test.com',
            'password' => 'Password123!',
            'remember' => true
        ]);

        $this->assertTrue(session('otp_remember'));
    }

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
        session(['test_key' => 'test_value']);

        $response = $this->post('/logout');

        $this->assertNull(session('test_key'));
    }
}

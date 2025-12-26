<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Services\FonnteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Mockery;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_show_search_form()
    {
        $response = $this->get(route('password.request'));
        $response->assertOk()->assertViewIs('auth.forgot-password.search');
    }

    public function test_search_user_found_with_email()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '081234567890']);

        $response = $this->post(route('password.search'), [
            'credential' => 'test@example.com'
        ]);

        $response->assertRedirect(route('password.verify.show'));
        $this->assertEquals($user->user_id, session('reset_user_id'));
    }

    public function test_search_user_found_with_username()
    {
        $user = User::factory()->create(['username' => 'testuser']);
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '081234567890']);

        $response = $this->post(route('password.search'), [
            'credential' => 'testuser'
        ]);

        $response->assertRedirect(route('password.verify.show'));
        $this->assertEquals($user->user_id, session('reset_user_id'));
    }

    public function test_search_user_not_found()
    {
        $response = $this->post(route('password.search'), [
            'credential' => 'nonexistent'
        ]);

        $response->assertSessionHas('error', 'Akun tidak ditemukan.');
    }

    public function test_search_user_no_phone()
    {
        $user = User::factory()->create();

        $response = $this->post(route('password.search'), [
            'credential' => $user->email
        ]);

        $response->assertSessionHas('error', 'Akun ini tidak memiliki nomor telepon terdaftar. Hubungi admin.');
    }

    public function test_show_verify_form_redirects_if_no_session()
    {
        $response = $this->get(route('password.verify.show'));
        $response->assertRedirect(route('password.request'));
    }

    public function test_show_verify_form_success()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '081234567890']);
        session(['reset_user_id' => $user->user_id]);

        $response = $this->get(route('password.verify.show'));
        
        $response->assertOk()
                 ->assertViewIs('auth.forgot-password.verify')
                 ->assertViewHas('maskedPhone');
    }

    public function test_show_verify_form_user_not_found_in_db()
    {
        session(['reset_user_id' => 99999]); 
        
        $response = $this->get(route('password.verify.show'));
        $response->assertRedirect(route('password.request'));
    }

    public function test_verify_phone_wrong_number()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '081234567890']);
        session(['reset_user_id' => $user->user_id]);

        $response = $this->post(route('password.verify.submit'), [
            'phone' => '089999999999'
        ]);

        $response->assertSessionHas('error', 'Nomor telepon tidak cocok dengan data kami.');
    }

    public function test_verify_session_missing_redirects()
    {
        $response = $this->post(route('password.verify.submit'), [
            'phone' => '081234567890'
        ]);
        
        $response->assertRedirect(route('password.request'));
    }

    public function test_verify_phone_normalization_logic()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '6281234567890']);
        session(['reset_user_id' => $user->user_id]);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->andReturn(['status' => true]);
        });

        $response = $this->post(route('password.verify.submit'), ['phone' => '081234567890']);
        $response->assertRedirect(route('login'));

        session(['reset_user_id' => $user->user_id]);
        $response = $this->post(route('password.verify.submit'), ['phone' => '81234567890']);
        $response->assertRedirect(route('login'));
    }

    public function test_verify_phone_success_sends_token()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '081234567890']);
        session(['reset_user_id' => $user->user_id]);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once()->withArgs(function ($phone, $message) {
                return $phone === '081234567890' && str_contains($message, 'reset password');
            });
        });

        $response = $this->post(route('password.verify.submit'), [
            'phone' => '081234567890'
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
        $this->assertFalse(session()->has('reset_user_id'));
    }

    public function test_show_reset_form()
    {
        $response = $this->get(route('password.reset', ['token' => 'abc', 'email' => 'test@test.com']));
        $response->assertOk()
                 ->assertViewIs('auth.forgot-password.reset')
                 ->assertViewHas('token', 'abc');
    }

    public function test_reset_password_invalid_token()
    {
        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => 'test@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ]);

        $response->assertSessionHas('error', 'Link reset password tidak valid atau salah.');
    }

    public function test_reset_password_expired_token()
    {
        $email = 'test@test.com';
        $token = 'expired-token';
        
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()->subMinutes(61)
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ]);

        $response->assertSessionHas('error', 'Link reset password sudah kadaluarsa. Silakan minta ulang.');
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $email]);
    }

    public function test_reset_password_user_deleted()
    {
        $email = 'deleted@test.com';
        $token = 'valid-token';
        
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!'
        ]);

        $response->assertSessionHas('error', 'User tidak ditemukan.');
    }

    public function test_reset_password_success()
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('OldPassword123!')
        ]);
        
        $token = 'valid-token';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!'
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }
}

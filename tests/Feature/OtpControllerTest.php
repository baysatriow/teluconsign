<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Mockery;

class OtpControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    

    public function test_show_verify_form_without_session_redirects()
    {
        $response = $this->get('/otp-verify');

        $response->assertRedirect(route('login'));
    }

    public function test_show_verify_form_with_session()
    {
        $user = User::factory()->create();
        session(['otp_user_id' => $user->user_id]);

        $response = $this->get('/otp-verify');

        $response->assertOk()
                 ->assertViewIs('auth.otp')
                 ->assertViewHas('cooldown');
    }

    public function test_show_verify_form_displays_cooldown()
    {
        $user = User::factory()->create();
        session(['otp_user_id' => $user->user_id]);

        
        $cacheKey = 'otp_resend_cooldown_' . $user->user_id;
        $expiresAt = Carbon::now()->addMinutes(2);
        Cache::put($cacheKey, $expiresAt, $expiresAt);

        $response = $this->get('/otp-verify');

        $response->assertOk();
        $cooldown = $response->viewData('cooldown');
        $this->assertGreaterThan(0, $cooldown);
    }

    public function test_show_verify_form_cooldown_expired()
    {
        $user = User::factory()->create();
        session(['otp_user_id' => $user->user_id]);

        
        $cacheKey = 'otp_resend_cooldown_' . $user->user_id;
        Cache::forget($cacheKey);

        $response = $this->get('/otp-verify');

        $response->assertOk();
        $cooldown = $response->viewData('cooldown');
        $response->assertOk();
        $cooldown = $response->viewData('cooldown');
        $this->assertEquals(0, $cooldown);
    }

    public function test_show_verify_form_cooldown_calculation_logic()
    {
        $user = User::factory()->create();
        session(['otp_user_id' => $user->user_id]);

        $cacheKey = 'otp_resend_cooldown_' . $user->user_id;
        
        $pastTime = Carbon::now()->subSeconds(10);
        Cache::put($cacheKey, $pastTime, 60); 

        $response = $this->get('/otp-verify');
        $this->assertEquals(0, $response->viewData('cooldown'));

        
        $futureTime = Carbon::now()->addSeconds(30);
        Cache::put($cacheKey, $futureTime, 60);

        $response = $this->get('/otp-verify');
        
        $this->assertEqualsWithDelta(30, $response->viewData('cooldown'), 1);
    }

    

    public function test_verify_otp_success()
    {
        $user = User::factory()->create(['is_verified' => false]);
        $otp = $user->generateOtp();
        
        session([
            'otp_user_id' => $user->user_id,
            'otp_context' => 'login',
            'otp_remember' => false
        ]);

        $response = $this->post('/otp-verify', [
            'otp' => $otp
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');
        
        $user->refresh();
        $this->assertTrue($user->is_verified);
        $this->assertNull($user->otp_code);
        $this->assertNull($user->otp_expires_at);
        $this->assertTrue(Auth::check());
    }

    public function test_verify_otp_admin_redirect()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_verified' => false
        ]);
        $otp = $admin->generateOtp();
        
        session(['otp_user_id' => $admin->user_id, 'otp_context' => 'login']);

        $response = $this->post('/otp-verify', [
            'otp' => $otp
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_verify_otp_wrong_code()
    {
        $user = User::factory()->create();
        $user->generateOtp();
        
        session(['otp_user_id' => $user->user_id]);

        $response = $this->post('/otp-verify', [
            'otp' => '123456' 
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('otp');
        $this->assertFalse(Auth::check());
    }

    public function test_verify_otp_expired()
    {
        $user = User::factory()->create();
        $otp = $user->generateOtp();
        
        
        $user->update(['otp_expires_at' => Carbon::now()->subMinutes(10)]);
        
        session(['otp_user_id' => $user->user_id]);

        $response = $this->post('/otp-verify', [
            'otp' => $otp
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['otp' => 'Kode OTP sudah kadaluarsa.']);
    }

    public function test_verify_otp_without_session()
    {
        $response = $this->post('/otp-verify', [
            'otp' => '123456'
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Sesi verifikasi habis. Silakan login ulang.');
    }

    public function test_verify_otp_clears_session_data()
    {
        $user = User::factory()->create();
        $otp = $user->generateOtp();
        
        session([
            'otp_user_id' => $user->user_id,
            'otp_context' => 'activation',
            'otp_remember' => true
        ]);

        $this->post('/otp-verify', ['otp' => $otp]);

        $this->assertNull(session('otp_user_id'));
        $this->assertNull(session('otp_context'));
        $this->assertNull(session('otp_remember'));
    }

    public function test_verify_otp_clears_cache()
    {
        $user = User::factory()->create();
        $otp = $user->generateOtp();
        
        session(['otp_user_id' => $user->user_id]);

        
        $cooldownKey = 'otp_resend_cooldown_' . $user->user_id;
        $attemptsKey = 'otp_resend_attempts_' . $user->user_id;
        Cache::put($cooldownKey, now()->addMinutes(2), now()->addMinutes(2));
        Cache::put($attemptsKey, 3, now()->addHour());

        $this->post('/otp-verify', ['otp' => $otp]);

        $this->assertFalse(Cache::has($cooldownKey));
        $this->assertFalse(Cache::has($attemptsKey));
    }

    public function test_verify_otp_with_remember_me()
    {
        $user = User::factory()->create();
        $otp = $user->generateOtp();
        
        session([
            'otp_user_id' => $user->user_id,
            'otp_remember' => true
        ]);

        $this->post('/otp-verify', ['otp' => $otp]);

        
        $this->assertTrue(Auth::check());
    }

    

    public function test_resend_otp_without_session()
    {
        $response = $this->post('/otp-resend');

        $response->assertRedirect(route('login'));
    }

    public function test_resend_otp_success()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);
        session(['otp_user_id' => $user->user_id]);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
        });

        $response = $this->post('/otp-resend');

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $user->refresh();
        $this->assertNotNull($user->otp_code);
    }

    public function test_resend_otp_with_cooldown()
    {
        $user = User::factory()->create();
        session(['otp_user_id' => $user->user_id]);

        
        $cooldownKey = 'otp_resend_cooldown_' . $user->user_id;
        Cache::put($cooldownKey, Carbon::now()->addMinutes(2), Carbon::now()->addMinutes(2));

        $response = $this->post('/otp-resend');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_resend_otp_increments_attempts()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '628123456789']);
        session(['otp_user_id' => $user->user_id]);

        $this->mock(FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->twice();
        });

        $this->post('/otp-resend');
        $this->assertEquals(1, Cache::get('otp_resend_attempts_' . $user->user_id));

        Cache::forget('otp_resend_cooldown_' . $user->user_id);
        
        $this->post('/otp-resend');
        $this->assertEquals(2, Cache::get('otp_resend_attempts_' . $user->user_id));
    }

    public function test_resend_otp_without_phone()
    {
        $user = User::factory()->create();
        
        session(['otp_user_id' => $user->user_id]);

        $response = $this->post('/otp-resend');

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Nomor telepon tidak ditemukan.');
    }
}

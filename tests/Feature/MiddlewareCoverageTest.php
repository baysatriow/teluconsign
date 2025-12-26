<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Route;

class MiddlewareCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_middleware_allows_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $middleware = new AdminMiddleware();
        $request = Request::create('/admin/dashboard', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals('OK', $response->content());
    }

    public function test_admin_middleware_redirects_non_admin()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $this->actingAs($seller);

        $middleware = new AdminMiddleware();
        $request = Request::create('/admin/dashboard', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertTrue($response->isRedirect());
        $this->assertEquals(url('/'), $response->getTargetUrl());
    }

    public function test_otp_middleware_allows_verified_user()
    {
        $user = User::factory()->create(['is_verified' => true]);
        $this->actingAs($user);

        $middleware = new EnsureOtpVerified();
        $request = Request::create('/dashboard', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals('OK', $response->content());
    }

    public function test_otp_middleware_logs_out_and_redirects_unverified_user()
    {
        $user = User::factory()->create(['is_verified' => false]);
        $this->actingAs($user);

        $middleware = new EnsureOtpVerified();
        $request = Request::create('/dashboard', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertTrue($response->isRedirect());
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertGuest();
    }
}

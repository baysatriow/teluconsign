<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\URL;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_forces_https_in_production()
    {
        URL::spy();
        
        $this->app['env'] = 'production';

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        URL::shouldHaveReceived('forceScheme')
            ->once()
            ->with('https');
    }

    public function test_view_composer_for_layouts_admin()
    {
        \App\Models\PayoutRequest::factory()->create(['status' => 'requested']);

        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertOk();
    }
}

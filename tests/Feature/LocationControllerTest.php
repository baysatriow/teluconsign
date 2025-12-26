<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\IntegrationProvider;
use App\Models\IntegrationKey;
use Illuminate\Support\Facades\DB;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = \App\Models\User::factory()->create();
    }

    public function test_search_with_short_query()
    {
        $response = $this->actingAs($this->user)->getJson('/location/search?q=ab');

        $response->assertOk()
                 ->assertJson([]);
    }

    public function test_search_with_no_query()
    {
        $response = $this->actingAs($this->user)->getJson('/location/search');

        $response->assertOk()
                 ->assertJson([]);
    }

    public function test_search_without_rajaongkir_provider()
    {
        $response = $this->actingAs($this->user)->getJson('/location/search?q=jakarta');

        $response->assertOk()
                 ->assertJson([]);
    }

    public function test_search_without_active_key()
    {
        $provider = IntegrationProvider::create([
            'code' => 'rajaongkir',
            'name' => 'RajaOngkir',
            'app_type' => 'shipping'
        ]);

        // No active key

        $response = $this->actingAs($this->user)->getJson('/location/search?q=jakarta');

        $response->assertOk()
                 ->assertJson([]);
    }

    public function test_search_with_valid_setup()
    {
        $this->markTestSkipped('Requires actual RajaOngkir API setup and cURL mock');
    }
}

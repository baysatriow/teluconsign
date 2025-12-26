<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\IntegrationProvider;
use App\Models\IntegrationKey;
use Illuminate\Support\Facades\Http;

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
        IntegrationProvider::create([
            'code' => 'rajaongkir',
            'name' => 'RajaOngkir',
            'app_type' => 'shipping'
        ]);

        $response = $this->actingAs($this->user)->getJson('/location/search?q=jakarta');

        $response->assertOk()
                 ->assertJson([]);
    }

    public function test_search_success()
    {
        $provider = IntegrationProvider::create([
            'code' => 'rajaongkir',
            'name' => 'RajaOngkir',
            'app_type' => 'shipping'
        ]);

        IntegrationKey::create([
            'provider_id' => $provider->integration_provider_id,
            'public_k' => 'test-key',
            'is_active' => true,
            'label' => 'RajaOngkir Test'
        ]);

        Http::fake([
            '*/destination/domestic-destination*' => Http::response([
                'status' => 'success',
                'data' => [
                    ['id' => 1, 'label' => 'Jakarta Selatan, DKI Jakarta'],
                    ['id' => 2, 'label' => 'Jakarta Pusat, DKI Jakarta']
                ]
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->getJson('/location/search?q=jakarta');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson([
            ['id' => 1, 'label' => 'Jakarta Selatan, DKI Jakarta']
        ]);
    }

    public function test_search_api_error_returns_empty()
    {
        $provider = IntegrationProvider::create([
            'code' => 'rajaongkir',
            'name' => 'RajaOngkir',
            'app_type' => 'shipping'
        ]);

        IntegrationKey::create([
            'provider_id' => $provider->integration_provider_id,
            'public_k' => 'test-key',
            'is_active' => true,
            'label' => 'RajaOngkir Test'
        ]);

        Http::fake([
            '*/destination/domestic-destination*' => Http::response(['status' => 'error'], 200),
        ]);

        $response = $this->actingAs($this->user)->getJson('/location/search?q=error');

        $response->assertOk();
        $response->assertJson([]);
    }
    public function test_search_api_failed()
    {
        $provider = IntegrationProvider::create([
            'code' => 'rajaongkir',
            'name' => 'RajaOngkir',
            'app_type' => 'shipping'
        ]);

        IntegrationKey::create([
            'provider_id' => $provider->integration_provider_id,
            'public_k' => 'test-key',
            'is_active' => true,
            'label' => 'RajaOngkir Test'
        ]);

        Http::fake([
            '*/destination/domestic-destination*' => Http::response('Server Error', 500),
        ]);

        $response = $this->actingAs($this->user)->getJson('/location/search?q=failed');

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Server Error']);
    }

    public function test_search_exception()
    {
        Http::fake(function() {
            throw new \Exception('Unexpected Error');
        });

        $response = $this->actingAs($this->user)->getJson('/location/search?q=exception');

        $provider = IntegrationProvider::create(['code' => 'rajaongkir', 'name' => 'RO', 'app_type' => 'shipping']);
        IntegrationKey::create(['provider_id' => $provider->integration_provider_id, 'public_k' => 'key', 'is_active' => true, 'label' => 'Test']);

        $response = $this->actingAs($this->user)->getJson('/location/search?q=exception');

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Unexpected Error']);
    }
}

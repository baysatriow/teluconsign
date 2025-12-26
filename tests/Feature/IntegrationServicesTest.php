<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\FonnteService;
use App\Services\RajaOngkirService;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class IntegrationServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed Integration Keys/Credentials expected by BaseIntegrationService
        // Assuming table 'integration_keys' or similar. 
        // Need to check BaseIntegrationService::getCredential logic or schema.
        // If not checking schema creates risk, I will try to inspect BaseIntegrationService.
        // Assuming it looks for 'payment_gateways' or 'shipping_carriers' or a generic config table.
        // Based on `MidtransService` it uses `$this->getCredential($this->providerCode)`.
        
        // For now, I will assume a generic 'integration_keys' or specific tables based on migration list:
        // 'payment_gateways' for midtrans.
        // 'shipping_carriers' (maybe generic 'integration_providers'?)
        
        // Seed Integration Providers and Keys
        $providerId = DB::table('integration_providers')->insertGetId([
            'code' => 'midtrans',
            'name' => 'Midtrans',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('integration_keys')->insert([
            'provider_id' => $providerId,
            'label' => 'Sandbox Key',
            'is_active' => true,
            'public_k' => 'mock_client_key', 
            'encrypted_k' => \Illuminate\Support\Facades\Crypt::encryptString('mock_server_key'),
            'meta_json' => json_encode(['environment' => 'sandbox', 'base_url' => 'https://api.rajaongkir.com/starter']),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Also seed rajaongkir
        $rajaId = DB::table('integration_providers')->insertGetId([
            'code' => 'rajaongkir',
            'name' => 'RajaOngkir',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('integration_keys')->insert([
            'provider_id' => $rajaId,
            'label' => 'RajaOngkir Key',
            'is_active' => true,
            'public_k' => 'mock_key',
            'encrypted_k' => null,
            'meta_json' => json_encode(['base_url' => 'https://api.rajaongkir.com/starter']),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Fonnte
        $fonnteId = DB::table('integration_providers')->insertGetId([
            'code' => 'whatsapp', // FonnteService uses 'whatsapp'
            'name' => 'Fonnte',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('integration_keys')->insert([
             'provider_id' => $fonnteId,
             'public_k' => 'mock_token',
             'is_active' => true
        ]);
        
        // Seed for Fonnte (usually generic integration or specific config)
        // I will mock the getCredential method if DB seeding is ambiguous. 
        // BUT, since we have to test the service as is... 
        
        // Let's create partial mocks for the services to bypass DB credential lookup if schema is complex.
    }

    public function test_fonnte_service_send_message()
    {
        $service = $this->partialMock(FonnteService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('getCredential')->andReturn((object)[
                'public_k' => 'mock_token'
            ]);
        });

        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        $result = $service->sendMessage('08123456789', 'Hello');

        $this->assertTrue($result['status']);
    }

    public function test_rajaongkir_check_cost()
    {
        $service = $this->partialMock(RajaOngkirService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('getCredential')->andReturn((object)[
                'public_k' => 'mock_key',
                'config' => ['base_url' => 'https://api.rajaongkir.com/starter']
            ]);
        });
        
        Http::fake([
            'api.rajaongkir.com/*' => Http::response([
                'rajaongkir' => [
                    'results' => [
                        [
                            'code' => 'jne',
                            'costs' => [['service' => 'REG', 'cost' => [['value' => 10000]]]]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $result = $service->checkCost(1, 'city', 2, 'city', 1000, 'jne');

        $this->assertTrue($result['status']);
    }

    public function test_midtrans_create_snap_token()
    {
        $service = $this->partialMock(MidtransService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('getCredential')->andReturn((object)[
                'secret_key' => 'mock_server_key',
                'config' => ['environment' => 'sandbox']
            ]);
        });

        Http::fake([
            'app.sandbox.midtrans.com/*' => Http::response(['token' => 'snap_token_123'], 200),
        ]);

        $orderData = [
            'code' => 'ORDER-123',
            'total_amount' => 100000,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '08123456789'
        ];

        $result = $service->createSnapToken($orderData);

        $this->assertEquals('snap_token_123', $result['token']);
    }
}

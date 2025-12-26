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

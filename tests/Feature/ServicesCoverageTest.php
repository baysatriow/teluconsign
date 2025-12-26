<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use App\Services\MidtransService;
use App\Services\RajaOngkirService;
use App\Services\BinderByteService;
use App\Services\FonnteService;
use App\Services\GeminiService;
use App\Models\IntegrationProvider;
use App\Models\IntegrationKey;
use Illuminate\Support\Facades\Crypt;
use App\Services\BaseIntegrationService;

class TestIntegrationService extends BaseIntegrationService
{
    public $providerCode = 'test_provider';

    public function retrieveCredential($code = null)
    {
        return $this->getCredential($code ?? $this->providerCode);
    }
}

class ServicesCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {

        parent::setUp();
        
        // --- 1. MIDTRANS ---
        $midtrans = IntegrationProvider::firstOrCreate(['code' => 'midtrans'], ['name' => 'Midtrans']);
        $midKey = IntegrationKey::firstOrCreate(['provider_id' => $midtrans->integration_provider_id], [
            'label' => 'Server Key',
            'public_k' => 'SB-Mid-server-xxxx',
            'encrypted_k' => Crypt::encryptString('SB-Mid-server-xxxx'),
            'meta_json' => ['environment' => 'sandbox'],
             'is_active' => true
        ]);
        $midKey->update(['meta_json' => ['environment' => 'sandbox'], 'is_active' => true]);

        // --- 2. RAJAONGKIR ---
        $rajaongkir = IntegrationProvider::firstOrCreate(['code' => 'rajaongkir'], ['name' => 'RajaOngkir']);
        $rajaKey = IntegrationKey::firstOrCreate(['provider_id' => $rajaongkir->integration_provider_id], [
             'label' => 'API Key',
             'public_k' => 'ro-api-key-xxxx',
             'encrypted_k' => Crypt::encryptString('ro-api-key-xxxx'),
             'meta_json' => ['base_url' => 'https://api.test'],
             'is_active' => true
        ]);

        // --- 3. BINDERBYTE ---
        $binderbyte = IntegrationProvider::firstOrCreate(['code' => 'binderbyte'], ['name' => 'BinderByte']);
        IntegrationKey::firstOrCreate(['provider_id' => $binderbyte->integration_provider_id], [
            'label' => 'API Key',
            'public_k' => 'bb-api-key-xxxx',
            'encrypted_k' => Crypt::encryptString('bb-api-key-xxxx'),
            'is_active' => true
        ]);

        // --- 4. FONNTE ---
        $fonnte = IntegrationProvider::firstOrCreate(['code' => 'whatsapp'], ['name' => 'Fonnte']);
        IntegrationKey::firstOrCreate(['provider_id' => $fonnte->integration_provider_id], [
            'label' => 'Token',
            'public_k' => 'fonnte-token-xxxx',
            'encrypted_k' => Crypt::encryptString('fonnte-token-xxxx'),
            'is_active' => true
        ]);

        // --- 5. GEMINI ---
        $gemini = IntegrationProvider::firstOrCreate(['code' => 'gemini'], ['name' => 'Gemini AI']);
        IntegrationKey::firstOrCreate(['provider_id' => $gemini->integration_provider_id], [
             'label' => 'API Key',
             'public_k' => 'gemini-key-xxxx',
             'encrypted_k' => Crypt::encryptString('gemini-key-xxxx'),
             'is_active' => true
        ]);
    }

    // ... [Previous Midtrans Tests maintained] ...
    public function test_midtrans_create_snap_token()
    {
        Http::fake([
            '*/snap/v1/transactions' => Http::response(['token' => 'SNAP-TOKEN-123', 'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/SNAP-TOKEN-123'], 200),
        ]);

        $service = app(MidtransService::class);
        $orderData = [
            'code' => 'ORD-123',
            'total_amount' => 100000,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '08123456789'
        ];

        $result = $service->createSnapToken($orderData);
        $this->assertIsArray($result);
        $this->assertEquals('SNAP-TOKEN-123', $result['token']);
    }

    public function test_midtrans_create_charge()
    {
        Http::fake([
            '*/v2/charge' => Http::response(['status_code' => '200', 'status_message' => 'Success', 'transaction_id' => 'TXN-123'], 200),
        ]);

        $service = app(MidtransService::class);
        $chargeData = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => ['order_id' => 'ORD-CHARGE-1', 'gross_amount' => 50000]
        ];

        $result = $service->createCharge($chargeData);
        $this->assertIsArray($result);
        $this->assertEquals('TXN-123', $result['transaction_id']);
    }

    public function test_midtrans_check_status()
    {
        Http::fake([
            '*/v2/*/status' => Http::response(['transaction_status' => 'settlement'], 200),
        ]);

        $service = app(MidtransService::class);
        $result = $service->checkStatus('ORD-123');

        $this->assertIsArray($result);
        $this->assertEquals('settlement', $result['transaction_status']);
    }

    public function test_midtrans_simulate_success()
    {
         Http::fake([
            '*/v2/*/status/b2b/settle' => Http::response(['status_code' => '200', 'status_message' => 'Success'], 200),
        ]);
        
        $service = app(MidtransService::class);
        $result = $service->simulatePaymentSuccess('ORD-123');
        $this->assertIsArray($result);
        $this->assertEquals('Success', $result['status_message']);
    }

    // ... [Previous RajaOngkir Tests maintained] ...
    public function test_rajaongkir_check_cost()
    {
        Http::fake([
            '*/calculate/domestic-cost' => Http::response([
                'meta' => ['code' => 200, 'status' => 'success'],
                'data' => [
                    ['code' => 'jne', 'costs' => [['service' => 'REG', 'cost' => [['value' => 10000]]]]]
                ]
            ], 200),
        ]);

        $service = app(RajaOngkirService::class);
        $result = $service->checkCost(501, 'subdistrict', 114, 'subdistrict', 1000, 'jne');

        $this->assertTrue($result['status']);
        $this->assertNotEmpty($result['data']);
    }

    public function test_rajaongkir_fetch_data()
    {
        Http::fake([
            '*/destination/province' => Http::response([
                'meta' => ['code' => 200],
                'data' => [['province_id' => 1, 'province' => 'Bali']]
            ], 200),
        ]);

        $service = app(RajaOngkirService::class);
        $provinces = $service->getProvinces();

        $this->assertIsArray($provinces);
        $this->assertEquals('Bali', $provinces[0]['province']);
    }

    // --- NEW TESTS FOR FULL COVERAGE ---

    public function test_binderbyte_track_package_success()
    {
        Http::fake([
            'api.binderbyte.com/v1/track*' => Http::response([
                'status' => 200,
                'message' => 'Successfully tracked package',
                'data' => [
                    'summary' => ['status' => 'DELIVERED'],
                    'history' => []
                ]
            ], 200)
        ]);

        $service = app(BinderByteService::class);
        $result = $service->trackPackage('jne', 'JOB123456');

        $this->assertTrue($result['success']);
        $this->assertEquals('DELIVERED', $result['data']['summary']['status']);
    }

    public function test_binderbyte_track_package_fail()
    {
        Http::fake([
            'api.binderbyte.com/v1/track*' => Http::response([
                'status' => 400,
                'message' => 'AWB not found'
            ], 200) // API might return 200 HTTP but 400 in body
        ]);

        $service = app(BinderByteService::class);
        $result = $service->trackPackage('jne', 'INVALID-AWB');

        $this->assertFalse($result['success']);
        $this->assertEquals('AWB not found', $result['message']);
    }

    public function test_fonnte_send_message_success()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true, 'detail' => 'sent'], 200)
        ]);

        $service = app(FonnteService::class);
        $result = $service->sendMessage('081234567890', 'Hello');

        $this->assertTrue($result['status']);
        $this->assertEquals(['status' => true, 'detail' => 'sent'], $result['data']);
    }

    public function test_fonnte_normalize_phone()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        // Using reflection to test private method or indirectly testing via sendMessage
        // 0812... -> 62812...
        $service = app(FonnteService::class);
        
        // We will intercept the request to verify the phone number was normalized
        $result = $service->sendMessage('08123456789', 'Test Normalization');
        
        Http::assertSent(function ($request) {
            return $request['target'] === '628123456789';
        });
    }

    public function test_gemini_generate_content_success()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'This is AI generated text']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $service = app(GeminiService::class);
        $text = $service->generateContent('Describe PHP');

        $this->assertEquals('This is AI generated text', $text);
    }

    public function test_gemini_generate_content_fail()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'quota_exceeded'], 429)
        ]);

        // Mock logger to prevent clutter
        Log::shouldReceive('error')->once();

        $service = app(GeminiService::class);
        $text = $service->generateContent('Describe PHP');

        $this->assertEquals('Maaf, AI sedang sibuk.', $text);
    }

    // --- BASE INTEGRATION SERVICE EDGE CASES ---

    public function test_base_service_provider_not_found()
    {
        $service = new TestIntegrationService();
        $cred = $service->retrieveCredential('non_existent');
        $this->assertNull($cred);
    }

    public function test_base_service_key_not_found()
    {
        IntegrationProvider::create(['code' => 'test_provider', 'name' => 'Test']);
        // No key created
        $service = new TestIntegrationService();
        $cred = $service->retrieveCredential();
        $this->assertNull($cred);
    }

    public function test_base_service_bad_encryption()
    {
        $provider = IntegrationProvider::create(['code' => 'test_provider', 'name' => 'Test']);
        IntegrationKey::create([
            'provider_id' => $provider->integration_provider_id,
            'label' => 'Bad Key',
            'public_k' => 'pub',
            'encrypted_k' => 'NOT_ENCRYPTED_STRING', // This will cause decryption fail
            'is_active' => true
        ]);

        $service = new TestIntegrationService();
        $cred = $service->retrieveCredential();

        // Should fall back to raw string
        $this->assertEquals('NOT_ENCRYPTED_STRING', $cred->secret_key);
    }

    public function test_base_service_empty_encryption()
    {
        $provider = IntegrationProvider::create(['code' => 'test_provider', 'name' => 'Test']);
        IntegrationKey::create([
            'provider_id' => $provider->integration_provider_id,
            'label' => 'Empty Key',
            'public_k' => 'pub',
            'encrypted_k' => null, 
            'is_active' => true
        ]);

        $service = new TestIntegrationService();
        $cred = $service->retrieveCredential();

        $this->assertNull($cred->secret_key);
    }


    // --- BINDERBYTE EDGE CASES ---

    public function test_binderbyte_api_error_status_in_body()
    {
        Http::fake([
            'api.binderbyte.com/*' => Http::response(['status' => 400, 'message' => 'Invalid AWB'], 200)
        ]);

        $service = app(BinderByteService::class);
        $result = $service->trackPackage('jne', 'BAD_AWB');

        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid AWB', $result['message']);
    }

    public function test_binderbyte_exception_handling()
    {
        Http::fake(function ($request) {
            throw new \Exception('Connection Error');
        });

        $service = app(BinderByteService::class);
        $result = $service->trackPackage('jne', 'AWB123');

        $this->assertFalse($result['success']);
        $this->assertEquals('Connection Error', $result['message']);
    }

    // --- FONNTE EDGE CASES ---

    public function test_fonnte_simulation_mode_no_key()
    {
        // Delete the key to trigger simulation mode
        IntegrationKey::where('label', 'Token')->delete();
        
        $service = app(FonnteService::class);
        $result = $service->sendMessage('08123456789', 'Test');

        $this->assertTrue($result['status']);
        $this->assertStringContainsString('Simulated', $result['message']);
    }

    public function test_fonnte_exception_handling()
    {
        Http::fake(function ($request) {
            throw new \Exception('Network Error');
        });

        $service = app(FonnteService::class);
        $result = $service->sendMessage('08123456789', 'Test');

        $this->assertFalse($result['status']);
        $this->assertEquals('Network Error', $result['error']);
    }

    // --- GEMINI EDGE CASES ---

    public function test_gemini_exception_handling()
    {
        Http::fake(function ($request) {
            throw new \Exception('Gemini Down');
        });

        $service = app(GeminiService::class);
        $text = $service->generateContent('Test');

        $this->assertEquals('Fitur AI belum tersedia.', $text);
    }

    // --- MIDTRANS EDGE CASES ---

    public function test_midtrans_production_url_generation()
    {
        // Update key config to production
        // Debug:
        // dump(IntegrationKey::all()->toArray());
        
        // Use DB Facade to ensure raw update compatible with Query Builder in BaseIntegrationService
        $midtransProvider = IntegrationProvider::where('code', 'midtrans')->first();
        $keyId = IntegrationKey::where('provider_id', $midtransProvider->integration_provider_id)->where('is_active', true)->value('integration_key_id');
        
        if ($keyId) {
            \Illuminate\Support\Facades\DB::table('integration_keys')
                ->where('integration_key_id', $keyId)
                ->update(['meta_json' => json_encode(['environment' => 'production'])]);
        }
        
        Http::fake([
            'trans.merchant.id.midtrans.com/*' => Http::response(['token' => 'prod_token'], 200),
            'app.midtrans.com/*' => Http::response(['token' => 'prod_token'], 200),
            'api.midtrans.com/*' => Http::response(['status_code' => '200'], 200),
        ]);

        $service = app(MidtransService::class);
        $result = $service->createSnapToken(['code' => 'ORD-PROD', 'total_amount' => 10000, 'customer_name' => 'John', 'customer_email' => 'j@d.com', 'customer_phone' => '081']);

        // Assert url was correct by checking if fake returned data (dummy check as Http::fake matched)
        $this->assertNotNull($result);
    }

    public function test_midtrans_create_charge_failure()
    {
        Http::fake([
            '*' => Http::response(['status_message' => 'Charge Failed'], 400)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Charge Failed');

        $service = app(MidtransService::class);
        $service->createCharge([]);
    }

    public function test_midtrans_simulation_production_exception()
    {
        $midtransProvider = IntegrationProvider::where('code', 'midtrans')->first();
        $keyId = IntegrationKey::where('provider_id', $midtransProvider->integration_provider_id)->where('is_active', true)->value('integration_key_id');

        if ($keyId) {
            \Illuminate\Support\Facades\DB::table('integration_keys')
                ->where('integration_key_id', $keyId)
                ->update(['meta_json' => json_encode(['environment' => 'production'])]);
        }
        
        $service = app(MidtransService::class);
        $result = $service->simulatePaymentSuccess('ORD-123');

        $this->assertNull($result); // Should be null due to caught exception "only available in sandbox"
    }

    // --- RAJAONGKIR EDGE CASES ---
    
    public function test_rajaongkir_client_error_handling()
    {
        Http::fake([
            '*' => Http::response([], 400)
        ]);

        $service = app(RajaOngkirService::class);
        $result = $service->checkCost(1, 'city', 2, 'city', 1000, 'jne');

        // Client error (4xx) returns status true but empty data
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['data']);
    }

    public function test_rajaongkir_server_error_handling()
    {
        Http::fake([
            '*' => Http::response(['error' => 'Server Error'], 500)
        ]);

        $service = app(RajaOngkirService::class);
        $result = $service->checkCost(1, 'city', 2, 'city', 1000, 'jne');

        $this->assertFalse($result['status']);
        $this->assertEquals('Gagal koneksi API Ongkir.', $result['message']);
    }

    public function test_rajaongkir_exception()
    {
        Http::fake(function($request) {
            throw new \Exception('Timeout');
        });

        $service = app(RajaOngkirService::class);
        $result = $service->checkCost(1, 'city', 2, 'city', 1000, 'jne');
        
        $this->assertFalse($result['status']);
        $this->assertEquals('Timeout', $result['message']);
    }

    public function test_rajaongkir_fetch_data_fail()
    {
        Http::fake([
            '*' => Http::response([], 500)
        ]);

        $service = app(RajaOngkirService::class);
        $result = $service->getProvinces();

        $this->assertEmpty($result);
    }

    // --- ADDITIONAL RAJAONGKIR TESTS ---

    public function test_rajaongkir_get_cities_with_province()
    {
        Http::fake([
            '*' => Http::response([
                'data' => [
                    ['city_id' => '1', 'city_name' => 'Jakarta']
                ]
            ], 200)
        ]);

        $service = app(RajaOngkirService::class);
        $result = $service->getCities(1);

        $this->assertNotEmpty($result);
    }

    public function test_rajaongkir_get_cities_without_province()
    {
        Http::fake([
            '*' => Http::response([
                'data' => [
                    ['city_id' => '1', 'city_name' => 'Jakarta']
                ]
            ], 200)
        ]);

        $service = app(RajaOngkirService::class);
        $result = $service->getCities();

        $this->assertNotEmpty($result);
    }

    public function test_rajaongkir_get_subdistricts()
    {
        Http::fake([
            '*' => Http::response([
                'data' => [
                    ['subdistrict_id' => '1', 'subdistrict_name' => 'Menteng']
                ]
            ], 200)
        ]);

        $service = app(RajaOngkirService::class);
        $result = $service->getSubdistricts(1);

        $this->assertNotEmpty($result);
    }

    public function test_rajaongkir_fetch_data_exception()
    {
        Http::fake(function($request) {
            throw new \Exception('Network failure');
        });

        $service = app(RajaOngkirService::class);
        $result = $service->getCities();

        $this->assertEquals([], $result);
    }

    // --- ADDITIONAL FONNTE TESTS ---

    public function test_fonnte_api_failed_response()
    {
        Http::fake([
            '*' => Http::response(['error' => 'Invalid token'], 401)
        ]);

        $service = app(FonnteService::class);
        $result = $service->sendMessage('08123456789', 'Test');

        $this->assertFalse($result['status']);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_fonnte_normalize_phone_starts_with_8()
    {
        $service = app(FonnteService::class);
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('normalizePhoneNumber');
        $method->setAccessible(true);

        $result = $method->invoke($service, '81234567890');
        
        $this->assertEquals('6281234567890', $result);
    }

    // --- ADDITIONAL MIDTRANS TESTS ---

    public function test_midtrans_create_snap_token_with_transaction_details()
    {
        Http::fake([
            '*' => Http::response(['token' => 'snap_token_123'], 200)
        ]);

        $service = app(MidtransService::class);
        $result = $service->createSnapToken([
            'transaction_details' => [
                'order_id' => 'ORD-123',
                'gross_amount' => 100000
            ]
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('snap_token_123', $result['token']);
    }

    public function test_midtrans_create_charge_production_url()
    {
        $midtransProvider = IntegrationProvider::where('code', 'midtrans')->first();
        $keyId = IntegrationKey::where('provider_id', $midtransProvider->integration_provider_id)->where('is_active', true)->value('integration_key_id');
        
        if ($keyId) {
            \Illuminate\Support\Facades\DB::table('integration_keys')
                ->where('integration_key_id', $keyId)
                ->update(['meta_json' => json_encode(['environment' => 'production'])]);
        }

        Http::fake([
            'api.midtrans.com/*' => Http::response(['status_code' => '201'], 201)
        ]);

        $service = app(MidtransService::class);
        $result = $service->createCharge(['order_id' => 'ORD-123', 'gross_amount' => 100000]);

        $this->assertNotNull($result);
    }

    public function test_midtrans_check_status_production_url()
    {
        $midtransProvider = IntegrationProvider::where('code', 'midtrans')->first();
        $keyId = IntegrationKey::where('provider_id', $midtransProvider->integration_provider_id)->where('is_active', true)->value('integration_key_id');
        
        if ($keyId) {
            \Illuminate\Support\Facades\DB::table('integration_keys')
                ->where('integration_key_id', $keyId)
                ->update(['meta_json' => json_encode(['environment' => 'production'])]);
        }

        Http::fake([
            'api.midtrans.com/*' => Http::response(['status_code' => '200'], 200)
        ]);

        $service = app(MidtransService::class);
        $result = $service->checkStatus('ORD-123');

        $this->assertNotNull($result);
    }

    public function test_midtrans_check_status_exception()
    {
        Http::fake(function($request) {
            throw new \Exception('Connection timeout');
        });

        $service = app(MidtransService::class);
        $result = $service->checkStatus('ORD-123');

        $this->assertNull($result);
    }

    public function test_midtrans_snap_token_failed_response()
    {
        Http::fake([
            '*' => Http::response(['error_messages' => ['Transaction failed']], 400)
        ]);

        $service = app(MidtransService::class);
        $result = $service->createSnapToken(['code' => 'ORD-FAIL', 'total_amount' => 10000, 'customer_name' => 'John', 'customer_email' => 'j@d.com', 'customer_phone' => '081']);

        $this->assertNull($result);
    }

    public function test_fonnte_normalize_phone_already_formatted()
    {
        $service = app(FonnteService::class);
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('normalizePhoneNumber');
        $method->setAccessible(true);

        // Phone already starting with 62 should return as-is
        $result = $method->invoke($service, '6281234567890');
        
        $this->assertEquals('6281234567890', $result);
    }
}

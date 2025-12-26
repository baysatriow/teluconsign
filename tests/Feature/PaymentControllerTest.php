<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\IntegrationProvider;
use App\Models\IntegrationKey;
use App\Services\MidtransService;
use Mockery;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $buyer;
    protected $payment;
    protected $midtransService;
    protected $provider;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->buyer = User::factory()->create(['role' => 'buyer']);
        $order = Order::factory()->create(['buyer_id' => $this->buyer->user_id]);
        
        $this->provider = IntegrationProvider::factory()->create(['code' => 'midtrans']);
        IntegrationKey::create([
             'provider_id' => $this->provider->integration_provider_id,
             'is_active' => true,
             'label' => 'Midtrans Sandbox',
             'public_k' => 'test',
             'encrypted_k' => 'test',
             'meta_json' => ['environment' => 'sandbox']
        ]);

        $this->payment = Payment::factory()->create([
            'order_id' => $order->order_id,
            'provider_id' => $this->provider->integration_provider_id,
            'status' => 'pending',
            'amount' => 100000,
            'provider_order_id' => 'ORDER-' . uniqid()
        ]);
        
        $order->update(['notes' => 'Payment ID: ' . $this->payment->provider_order_id]);

        $this->midtransService = Mockery::mock(MidtransService::class);
        $this->app->instance(MidtransService::class, $this->midtransService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    

    public function test_show_payment_page_accessible()
    {
        $response = $this->actingAs($this->buyer)->get(route('payment.show', $this->payment->payment_id));
        
        $response->assertOk();
        $response->assertViewHas('payment');
        $response->assertSee('Rp100.000');
    }

    public function test_show_payment_forbidden_for_others()
    {
        $other = User::factory()->create();
        
        $response = $this->actingAs($other)->get(route('payment.show', $this->payment->payment_id));
        
        $response->assertForbidden();
    }

    

    public function test_create_charge_success()
    {
        $this->midtransService->shouldReceive('createCharge')
            ->once()
            ->andReturn([
                'transaction_id' => 'TXN-123',
                'status_code' => '201'
            ]);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('payment.charge', $this->payment->payment_id), [
                'method' => 'gopay'
            ]);

        $response->assertOk();
        $response->assertJson(['status' => 'success']);
        
        $this->assertEquals('gopay', $this->payment->fresh()->method_code);
        $this->assertEquals('TXN-123', $this->payment->fresh()->provider_txn_id);
    }

    

    public function test_check_status()
    {
        $response = $this->actingAs($this->buyer)
            ->get(route('payment.status', $this->payment->payment_id));

        $response->assertOk();
        $response->assertJson(['status' => 'pending']);
    }

    

    public function test_demo_payment_success()
    {
        
        $this->midtransService->shouldReceive('simulatePaymentSuccess')
            ->once()
            ->andReturn(true);

        $response = $this->actingAs($this->buyer)
            ->post(route('payment.demo', $this->payment->payment_id));

        $response->assertOk();
        $response->assertJson(['status' => 'success']);
        
        $this->assertEquals('settlement', $this->payment->fresh()->status);
        
        if ($this->payment->order->fresh()->status !== 'paid') {
             dump('Provider Order ID: ' . $this->payment->provider_order_id);
             dump('Order Notes: ' . $this->payment->order->fresh()->notes);
             dump('Order ID: ' . $this->payment->order_id);
        }

        $this->assertEquals('paid', $this->payment->order->fresh()->status);
    }
    public function test_show_payment_already_paid()
    {
        $this->payment->update(['status' => 'settlement']);
        
        $response = $this->actingAs($this->buyer)->get(route('payment.show', $this->payment->payment_id));
        
        $response->assertRedirect(route('orders.show', $this->payment->order_id));
        $response->assertSessionHas('success', 'Pembayaran sudah berhasil!');
    }

    public function test_create_charge_various_methods()
    {
        
        $this->midtransService->shouldReceive('createCharge')->twice()->andReturn(['transaction_id' => 'TXN-VA']);
        
        $this->actingAs($this->buyer)->postJson(route('payment.charge', $this->payment->payment_id), ['method' => 'bca_va'])->assertOk();
        
        
        $this->actingAs($this->buyer)->postJson(route('payment.charge', $this->payment->payment_id), ['method' => 'qris'])->assertOk();
    }

    public function test_create_charge_failure()
    {
        $this->midtransService->shouldReceive('createCharge')->andThrow(new \Exception('API Error'));

        $response = $this->actingAs($this->buyer)
            ->postJson(route('payment.charge', $this->payment->payment_id), [
                'method' => 'gopay'
            ]);

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error', 'message' => 'Gagal membuat pembayaran: API Error']);
    }

    public function test_demo_payment_not_sandbox()
    {
        
        \App\Models\IntegrationKey::where('label', 'Midtrans Sandbox')->update(['meta_json' => ['environment' => 'production']]);
        
        $response = $this->actingAs($this->buyer)
            ->post(route('payment.demo', $this->payment->payment_id));

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Demo payment only available in sandbox mode']);
    }

    public function test_demo_payment_exception()
    {
        $this->midtransService->shouldReceive('simulatePaymentSuccess')->andThrow(new \Exception('Sim Error'));

        $response = $this->actingAs($this->buyer)
            ->post(route('payment.demo', $this->payment->payment_id));

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
    }
}

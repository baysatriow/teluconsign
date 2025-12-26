<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\IntegrationProvider;

class OrdersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $buyer;
    protected $seller;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->buyer = User::factory()->create(['role' => 'buyer']);
        $this->seller = User::factory()->create(['role' => 'seller']);
        
        $this->order = Order::factory()->create([
            'buyer_id' => $this->buyer->user_id,
            'seller_id' => $this->seller->user_id,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);
    }

    public function test_orders_index_accessible()
    {
        $response = $this->actingAs($this->buyer)->get(route('orders.index'));
        
        $response->assertOk();
        $response->assertViewHas('orders');
        $response->assertViewHas('stats');
    }

    public function test_orders_index_filters_status()
    {
        Order::factory()->create(['buyer_id' => $this->buyer->user_id, 'status' => 'completed']);
        
        $response = $this->actingAs($this->buyer)->get(route('orders.index', ['status' => 'completed']));
        
        $orders = $response->viewData('orders');
        $this->assertCount(1, $orders);
        $this->assertEquals('completed', $orders->first()->status);
    }

    

    public function test_show_order_accessible_by_buyer()
    {
        $response = $this->actingAs($this->buyer)->get(route('orders.show', $this->order));
        
        $response->assertOk();
        $response->assertSee($this->order->code);
    }

    public function test_show_order_forbidden_for_other_user()
    {
        $otherUser = User::factory()->create();
        
        $response = $this->actingAs($otherUser)->get(route('orders.show', $this->order));
        
        $response->assertForbidden();
    }

    

    public function test_pay_redirects_to_existing_payment()
    {
        $provider = IntegrationProvider::factory()->create(['code' => 'midtrans']);
        $payment = Payment::factory()->create([
            'order_id' => $this->order->order_id,
            'provider_id' => $provider->integration_provider_id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->buyer)->post(route('orders.pay', $this->order->order_id));

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'redirect_url' => route('payment.show', $payment->payment_id)
        ]);
    }

    public function test_pay_order_already_paid()
    {
        $this->order->update(['payment_status' => 'settlement']);

        $response = $this->actingAs($this->buyer)->post(route('orders.pay', $this->order->order_id));

        $response->assertStatus(400);
        $response->assertJson(['status' => 'error', 'message' => 'Pesanan sudah dibayar.']);
    }

    public function test_pay_no_payment_found()
    {
        
        $response = $this->actingAs($this->buyer)->post(route('orders.pay', $this->order->order_id));

        $response->assertStatus(404);
        $response->assertJson(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan. Silakan hubungi bantuan.']);
    }
    public function test_orders_index_all_filters()
    {
        Order::factory()->create(['buyer_id' => $this->buyer->user_id, 'status' => 'pending', 'payment_status' => 'pending']);
        Order::factory()->create(['buyer_id' => $this->buyer->user_id, 'status' => 'processed']);
        Order::factory()->create(['buyer_id' => $this->buyer->user_id, 'status' => 'shipped']);
        Order::factory()->create(['buyer_id' => $this->buyer->user_id, 'status' => 'cancelled']);

        $statuses = ['pending', 'processed', 'shipped', 'completed', 'cancelled', 'all'];

        foreach ($statuses as $status) {
            $response = $this->actingAs($this->buyer)->get(route('orders.index', ['status' => $status]));
            $response->assertOk();
        }
    }

    public function test_show_order_accessible_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get(route('orders.show', $this->order));
        
        $response->assertOk();
        $response->assertSee($this->order->code);
    }

    public function test_pay_with_group_code_in_notes()
    {
        $groupCode = 'PAY-GR123';
        $this->order->update(['notes' => "Group Payment: {$groupCode}"]);
        
        $provider = IntegrationProvider::factory()->create(['code' => 'midtrans']);
        $payment = Payment::factory()->create([
            'provider_order_id' => $groupCode, 
            'provider_id' => $provider->integration_provider_id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->buyer)->post(route('orders.pay', $this->order->order_id));

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'redirect_url' => route('payment.show', $payment->payment_id)
        ]);
    }
}

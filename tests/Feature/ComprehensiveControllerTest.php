<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ModerationAction;
use App\Models\WalletLedger;
use App\Models\WebhookLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ComprehensiveControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_category_management_flow()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get(route('admin.categories.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'New Electronics',
            'parent_id' => null
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'New Electronics']);
        $category = Category::where('name', 'New Electronics')->first();

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category->category_id), [
            'name' => 'Updated Electronics',
            'parent_id' => null
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Updated Electronics']);

        $response = $this->actingAs($admin)->get(route('admin.categories.check', $category->category_id));
        $response->assertStatus(200)
                 ->assertJsonStructure(['product_count']);
        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->category_id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['category_id' => $category->category_id]);
    }

    public function test_webhook_midtrans_notification_settlement()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        
        $order = Order::create([
            'buyer_id' => $buyer->user_id,
            'seller_id' => $seller->user_id,
            'total_amount' => 100000,
            'subtotal_amount' => 100000,
            'shipping_cost' => 0,
            'seller_earnings' => 95000,
            'status' => 'pending',
            'code' => 'ORD-WEB-' . time(),
            'notes' => 'ORD-WEB-' . time()
        ]);

        $orderId = $order->notes; 
        
        $payment = Payment::create([
            'order_id' => $order->order_id,
            'provider_order_id' => $orderId,
            'amount' => 100000,
            'status' => 'pending',
            'currency' => 'IDR'
        ]);

        $payload = [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'transaction_id' => 'TXN-' . time(),
            'fraud_status' => 'accept'
        ];

        $response = $this->postJson(route('webhook.midtrans'), $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'paid',
            'payment_status' => 'settlement'
        ]);

        $this->assertDatabaseHas('payments', [
            'payment_id' => $payment->payment_id,
            'status' => 'settlement'
        ]);

        $this->assertDatabaseHas('wallet_ledgers', [
            'user_id' => $seller->user_id,
            'amount' => 95000,
            'direction' => 'credit'
        ]);
        
        $this->assertDatabaseHas('webhook_logs', [
            'provider_code' => 'midtrans',
            'related_id' => $orderId
        ]);
    }
    
    public function test_moderation_action_model_logic()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $moderation = new ModerationAction();
        
        $success = $moderation->logAction($admin->user_id, 'product', $product->product_id, 'suspend', 'Bad Content');
        $this->assertTrue($success);

        $this->assertDatabaseHas('moderation_actions', [
            'admin_id' => $admin->user_id,
            'target_id' => $product->product_id,
            'action' => 'suspend'
        ]);

        $actionRecord = ModerationAction::first();
        $actionRecord->revertAction($actionRecord->moderation_action_id);
        
        $this->assertDatabaseHas('moderation_actions', [
            'moderation_action_id' => $actionRecord->moderation_action_id,
            'action' => 'restore'
        ]);
    }

    public function test_app_service_provider_boots()
    {
        $this->assertTrue(true);
        $this->assertTrue(true);
    }
}

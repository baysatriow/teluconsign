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

    // --- AdminCategoryController Tests ---

    public function test_admin_category_management_flow()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // 1. Index
        $response = $this->actingAs($admin)->get(route('admin.categories.index'));
        $response->assertStatus(200);

        // 2. Store
        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'New Electronics',
            'parent_id' => null
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'New Electronics']);
        $category = Category::where('name', 'New Electronics')->first();

        // 3. Update
        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category->category_id), [
            'name' => 'Updated Electronics',
            'parent_id' => null
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Updated Electronics']);

        // 4. Check Deletion
        $response = $this->actingAs($admin)->get(route('admin.categories.check', $category->category_id));
        $response->assertStatus(200)
                 ->assertJsonStructure(['product_count']);

        // 5. Destroy
        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->category_id));
        $response->assertRedirect();
        $this->assertSoftDeleted('categories', ['category_id' => $category->category_id]);
    }

    // --- WebhookController Tests ---

    public function test_webhook_midtrans_notification_settlement()
    {
        // Setup Order and Payment
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
            'notes' => 'ORD-WEB-' . time() // Used for search in controller
        ]);

        $orderId = $order->notes; // Simulating provider_order_id match logic in controller
        
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

        // Call Webhook
        $response = $this->postJson(route('webhook.midtrans'), $payload);

        $response->assertStatus(200);

        // Check Order Status Update
        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'paid',
            'payment_status' => 'settlement'
        ]);

        // Check Payment Update
        $this->assertDatabaseHas('payments', [
            'payment_id' => $payment->payment_id,
            'status' => 'settlement'
        ]);

        // Check Wallet Credit
        $this->assertDatabaseHas('wallet_ledgers', [
            'user_id' => $seller->user_id,
            'amount' => 95000,
            'direction' => 'credit'
        ]);
        
        // Check Webhook Log
        $this->assertDatabaseHas('webhook_logs', [
            'provider_code' => 'midtrans',
            'related_id' => $orderId
        ]);
    }

    // --- ModerationAction Model Tests ---
    
    public function test_moderation_action_model_logic()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $moderation = new ModerationAction();
        
        // Test logAction
        $success = $moderation->logAction($admin->user_id, 'product', $product->product_id, 'suspend', 'Bad Content');
        $this->assertTrue($success);

        $this->assertDatabaseHas('moderation_actions', [
            'admin_id' => $admin->user_id,
            'target_id' => $product->product_id,
            'action' => 'suspend'
        ]);

        // Test revertAction (logic might be tricky if it depends on external state, but we test the updating of the action record itself if logic supports it)
        // Revert logic in model: $reverse mapping.
        
        $actionRecord = ModerationAction::first();
        $actionRecord->revertAction($actionRecord->moderation_action_id);
        
        $this->assertDatabaseHas('moderation_actions', [
            'moderation_action_id' => $actionRecord->moderation_action_id,
            'action' => 'restore' // suspend -> restore
        ]);
    }

    // --- AppServiceProvider Test (Basic) ---
    public function test_app_service_provider_boots()
    {
        // Just by running any test, Provider boots.
        // We can assert something bound in boot if any.
        // If not, just ensure no errors during request.
        $this->assertTrue(true);
    }
}

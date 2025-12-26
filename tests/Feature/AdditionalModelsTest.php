<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem; 
use App\Models\Order;
use App\Models\IntegrationKey;
use App\Models\PayoutRequest; 
use App\Models\ProductImage;
use App\Models\Shipment;
use App\Models\WalletLedger;
use App\Models\WebhookLog;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\ModerationAction;
use App\Models\OrderItem;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdditionalModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_item_relationships_and_attributes()
    {
        $user = User::factory()->create();
        $cart = Cart::create(['buyer_id' => $user->user_id]);
        $product = Product::factory()->create();

        $item = CartItem::create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 2,
            'unit_price' => 10000,
            'subtotal' => 20000,
            'price_at_add' => 10000,
        ]);

        $this->assertInstanceOf(Cart::class, $item->cart);
        $this->assertInstanceOf(Product::class, $item->product);
        $this->assertEquals(20000, $item->subtotal);
    }

    public function test_integration_key_creation()
    {
        $key = new IntegrationKey();
        $this->assertInstanceOf(IntegrationKey::class, $key);
    }

    public function test_payout_request_relationships()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $bank = BankAccount::create([
            'user_id' => $seller->user_id,
            'bank_name' => 'BCA',
            'account_no' => '123',
            'account_name' => 'Seller'
        ]);

        $payout = PayoutRequest::create([
            'seller_id' => $seller->user_id,
            'amount' => 100000,
            'status' => 'requested',
            'bank_account_id' => $bank->bank_account_id
        ]);

        $this->assertInstanceOf(User::class, $payout->seller);
        $this->assertInstanceOf(BankAccount::class, $payout->bankAccount);
    }

    public function test_product_image_relationships()
    {
        $product = Product::factory()->create();
        
        $image = ProductImage::create([
            'product_id' => $product->product_id,
            'url' => 'test.jpg',
            'is_primary' => true,
            'sort_order' => 1
        ]);

        $this->assertInstanceOf(Product::class, $image->product);
        $this->assertTrue($image->is_primary);
    }

    public function test_shipment_creation()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create(['user_id' => $seller->user_id, 'is_shop_default' => true]);
        $buyer = User::factory()->create(['role' => 'buyer']);
        
        $order = Order::create([
            'buyer_id' => $buyer->user_id,
            'seller_id' => $seller->user_id,
            'total_amount' => 10000,
            'subtotal_amount' => 10000,
            'status' => 'pending',
            'code' => 'ORD-SHIP-' . time(),
        ]);

        $shipment = Shipment::create([
            'order_id' => $order->order_id,
            'tracking_number' => 'AWB123456',
            'status' => 'in_transit',
            'cost' => 5000
        ]);

        $this->assertInstanceOf(Order::class, $shipment->order);
        $this->assertEquals('AWB123456', $shipment->tracking_number);
    }

    public function test_wallet_ledger_creation()
    {
        $user = User::factory()->create();
        
        $ledger = WalletLedger::create([
            'user_id' => $user->user_id,
            'direction' => 'credit',
            'source_type' => 'adjustment',
            'amount' => 50000,
            'balance_after' => 50000,
            'memo' => 'Bonus',
            'posted_at' => now()
        ]);

        $this->assertInstanceOf(User::class, $ledger->user);
        $this->assertEquals(50000, $ledger->amount);
    }

    public function test_webhook_log_creation()
    {
        $log = WebhookLog::create([
            'provider_code' => 'midtrans',
            'payload' => json_encode(['data' => 'test']),
            'event_type' => 'test_event'
        ]);

        $this->assertEquals('midtrans', $log->provider_code);
    }
    
    public function test_profile_relationship()
    {
        $user = User::factory()->create();
        
        $profile = Profile::create([
            'user_id' => $user->user_id,
            'bio' => 'Tester',
            'phone' => '08123'
        ]);
        
        $this->assertInstanceOf(User::class, $profile->user);
        $this->assertEquals('Tester', $profile->bio);
    }

    public function test_moderation_action_revert_non_existent()
    {
        $action = new ModerationAction();
        $action->revertAction(99999);
        $this->assertTrue(true);
    }

    public function test_order_item_link_non_existent_product()
    {
        $order = Order::factory()->create();
        $item = OrderItem::create([
            'order_id' => $order->order_id,
            'unit_price' => 1000,
            'quantity' => 1,
            'subtotal' => 1000,
            'product_title_snapshot' => 'Initial'
        ]);

        $item->linkProduct(99999);
        $this->assertEquals('Initial', $item->product_title_snapshot);
    }

    public function test_product_change_status_logic()
    {
        $product = Product::factory()->create(['status' => ProductStatus::Active]);
        
        $product->changeStatus('suspended');
        $this->assertEquals(ProductStatus::Suspended, $product->status);

        $product->changeStatus(ProductStatus::Active);
        $this->assertEquals(ProductStatus::Active, $product->status);
    }

    public function test_product_get_reviews()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();
        
        Review::create([
            'product_id' => $product->product_id,
            'user_id' => $user->user_id,
            'rating' => 5,
            'comment' => 'Great!'
        ]);

        $reviews = $product->getReviews();
        $this->assertCount(1, $reviews);
        $this->assertEquals('Great!', $reviews->first()->comment);
    }
}

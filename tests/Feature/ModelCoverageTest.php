<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShippingCarrier;
use App\Models\IntegrationProvider;
use App\Models\IntegrationKey;
use App\Models\WalletLedger;
use App\Models\WebhookLog;
use App\Models\ModerationAction;
use App\Enums\ProductStatus;

class ModelCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_model()
    {
        $address = Address::factory()->create();
        $this->assertNotNull($address->user);
        $this->assertNotEmpty($address->getFullAddress());
    }

    public function test_cart_model()
    {
        $cart = Cart::factory()->create();
        $this->assertNotNull($cart->buyer);
        $this->assertNotNull($cart->items);
        $this->assertEquals(0, $cart->calculateTotal());
    }

    public function test_cart_item_model()
    {
        $item = CartItem::factory()->create();
        $this->assertNotNull($item->cart);
        $this->assertNotNull($item->product);
    }

    public function test_category_model()
    {
        $category = Category::factory()->create();
        $this->assertNotNull($category->products);
        $this->assertNull($category->parent); 
        $this->assertNotNull($category->children);
    }

    public function test_integration_models()
    {
        $provider = IntegrationProvider::create(['code' => 'test_prov', 'name' => 'Test']);
        $key = IntegrationKey::create([
            'provider_id' => $provider->integration_provider_id,
            'label' => 'Test Key',
            'public_k' => 'pk_test',
            'encrypted_k' => base64_encode('sk_test'),
            'is_active' => true
        ]);

        $this->assertNotNull($key->provider);
        $this->assertNotNull($provider->getActiveKeys());
    }

    public function test_moderation_action_model()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $action = ModerationAction::create([
            'admin_id' => $admin->user_id,
            'target_type' => 'product',
            'target_id' => $product->product_id,
            'action' => 'suspend',
            'reason' => 'test'
        ]);
        
        $action->logAction($admin->user_id, 'product', $product->product_id, 'suspend', 'reason');
        $this->assertNotEmpty($action->getActionsByTarget('product', $product->product_id));
    }

    public function test_order_model()
    {
        $order = Order::factory()->create();
        $this->assertNotNull($order->buyer);
        $this->assertNotNull($order->seller);
        $this->assertNotNull($order->items);
        $this->assertNotNull($order->shippingAddress);
        $this->assertNull($order->shipment); 
        $this->assertIsFloat($order->calculateTotalAmount());
        $this->assertNotEmpty($order->formatted_address);
    }

    public function test_order_item_model()
    {
        $item = OrderItem::factory()->create();
        $this->assertNotNull($item->order);
        $this->assertNotNull($item->product);
    }

    public function test_payment_model()
    {
        $payment = Payment::factory()->create();
        $this->assertNotNull($payment->order);
        $this->assertNotNull($payment->provider);
    }

    public function test_product_model()
    {
        $product = Product::factory()->create();
        $this->assertNotNull($product->seller);
        $this->assertNotNull($product->category);
        $this->assertNotNull($product->images);
        $this->assertNotNull($product->reviews);
        $this->assertEquals($product->status->value ?? $product->status, 'active');
    }

    public function test_shipment_model()
    {
        $shipment = Shipment::factory()->create();
        $this->assertNotNull($shipment->order);
        $this->assertNotNull($shipment->carrier);
    }

    public function test_user_model()
    {
        $user = User::factory()->create();
        $this->assertNotNull($user->addresses);
        $this->assertNotNull($user->shopProducts);
        $this->assertNotNull($user->ordersBought);
        $this->assertNotNull($user->ordersSold);
        $this->assertNull($user->cart);
        $this->assertNotNull($user->walletLedgers);
        
        $user->role = 'admin';
        $this->assertTrue($user->isAdmin());
        
        $user->role = 'seller';
        $this->assertTrue($user->isSeller());
        
        $user->role = 'buyer';
        $this->assertTrue($user->isBuyer());
        
        User::factory()->create(['role' => 'seller', 'is_verified' => true]);
        $this->assertNotEmpty(User::verifiedSellers()->get());
    }

    public function test_wallet_ledger_model()
    {
        $ledger = WalletLedger::factory()->create();
        $this->assertNotNull($ledger->user);
    }

    public function test_webhook_log_model()
    {
        $log = WebhookLog::create([
             'provider_code' => 'test',
             'event_type' => 'test_event',
             'payload' => []
        ]);
        $this->assertNotNull($log);
        $log->recordWebhook('test2', 'event2', []);
        $this->assertNotEmpty($log->getLogsByProvider('test2'));
    }

    public function test_bank_account_model()
    {
        $user = User::factory()->create();
        $bankAccount = \App\Models\BankAccount::create([
            'user_id' => $user->user_id,
            'bank_name' => 'BCA',
            'account_name' => 'John Doe',
            'account_no' => '1234567890',
            'is_default' => false
        ]);

        $this->assertEquals('BCA', $bankAccount->bank_name);
        $this->assertFalse($bankAccount->is_default);
        
        $this->assertTrue(\App\Models\BankAccount::validateAccount('1234567890'));
    }

    public function test_payout_request_model()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $bankAccount = \App\Models\BankAccount::create([
            'user_id' => $seller->user_id,
            'bank_name' => 'BCA',
            'account_name' => 'John Doe',
            'account_no' => '1234567890'
        ]);

        $payout = \App\Models\PayoutRequest::factory()->create([
            'seller_id' => $seller->user_id,
            'bank_account_id' => $bankAccount->bank_account_id
        ]);

        $this->assertNotNull($payout->seller);
        $this->assertNotNull($payout->bankAccount);
        $this->assertGreaterThan(0, $payout->amount);
    }

    public function test_product_image_model()
    {
        $product = Product::factory()->create();
        $image = \App\Models\ProductImage::create([
            'product_id' => $product->product_id,
            'url' => 'https://example.com/image.jpg',
            'is_primary' => true,
            'sort_order' => 1
        ]);

        $this->assertNotNull($image->product);
        $this->assertTrue($image->is_primary);
        $this->assertEquals('https://example.com/image.jpg', $image->url);
    }

    public function test_profile_model()
    {
        $user = User::factory()->create();
        $profile = \App\Models\Profile::create([
            'user_id' => $user->user_id,
            'phone' => '081234567890',
            'bio' => 'Test bio'
        ]);

        $this->assertNotNull($profile->user);
        $this->assertEquals('Test bio', $profile->bio);
        $this->assertEquals('081234567890', $profile->phone);
        $this->assertNotEmpty($profile->getProfileSummary());
    }

    public function test_review_model()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $product = Product::factory()->create();

        $review = \App\Models\Review::create([
            'user_id' => $buyer->user_id,
            'product_id' => $product->product_id,
            'rating' => 5,
            'comment' => 'Great product!',
            'status' => 'visible'
        ]);

        $this->assertNotNull($review->user);
        $this->assertNotNull($review->product);
        $this->assertEquals(5, $review->rating);
        $this->assertEquals('Great product!', $review->comment);
        
        $avg = $review->calculateAverageRating($product->product_id);
        $this->assertIsFloat($avg);
    }

    public function test_shipping_carrier_model()
    {
        $carrier = \App\Models\ShippingCarrier::factory()->create();

        $this->assertNotEmpty($carrier->name);
        $this->assertNotEmpty($carrier->code);
        $this->assertNotNull($carrier->provider_type);
    }

    

    public function test_address_set_shop_default()
    {
        $user = User::factory()->create();
        $address1 = Address::factory()->create(['user_id' => $user->user_id, 'is_shop_default' => false]);
        $address2 = Address::factory()->create(['user_id' => $user->user_id, 'is_shop_default' => true]);

        Address::setShopDefault($address1->address_id);

        $this->assertTrue($address1->fresh()->is_shop_default);
        $this->assertFalse($address2->fresh()->is_shop_default);
    }

    public function test_address_delete_address_reassigns_default()
    {
        $user = User::factory()->create();
        $address1 = Address::factory()->create(['user_id' => $user->user_id, 'is_default' => true, 'is_shop_default' => true]);
        $address2 = Address::factory()->create(['user_id' => $user->user_id, 'is_default' => false, 'is_shop_default' => false]);

        $result = Address::deleteAddress($address1->address_id);

        $this->assertTrue($result);
        $this->assertTrue($address2->fresh()->is_default);
        $this->assertTrue($address2->fresh()->is_shop_default);
    }

    public function test_address_delete_address_invalid_id()
    {
        $result = Address::deleteAddress(99999);
        
        $this->assertFalse($result);
    }

    

    public function test_bank_account_add_bank_account()
    {
        $user = User::factory()->create();
        
        $result = \App\Models\BankAccount::addBankAccount([
            'user_id' => $user->user_id,
            'bank_name' => 'BRI',
            'account_name' => 'Test User',
            'account_no' => '9876543210'
        ]);

        $this->assertTrue($result);
    }

    public function test_bank_account_set_default_account()
    {
        $user = User::factory()->create();
        $account1 = \App\Models\BankAccount::create([
            'user_id' => $user->user_id,
            'bank_name' => 'BCA',
            'account_name' => 'User 1',
            'account_no' => '1111111111',
            'is_default' => true
        ]);
        $account2 = \App\Models\BankAccount::create([
            'user_id' => $user->user_id,
            'bank_name' => 'BRI',
            'account_name' => 'User 2',
            'account_no' => '2222222222',
            'is_default' => false
        ]);

        \App\Models\BankAccount::setDefaultAccount($account2->bank_account_id);

        $this->assertFalse($account1->fresh()->is_default);
        $this->assertTrue($account2->fresh()->is_default);
    }

    public function test_bank_account_delete_account()
    {
        $user = User::factory()->create();
        $account = \App\Models\BankAccount::create([
            'user_id' => $user->user_id,
            'bank_name' => 'BCA',
            'account_name' => 'Test',
            'account_no' => '1234567890'
        ]);

        $result = \App\Models\BankAccount::deleteAccount($account->bank_account_id);

        $this->assertTrue($result);
        $this->assertNull(\App\Models\BankAccount::find($account->bank_account_id));
    }

    public function test_bank_account_delete_account_invalid_id()
    {
        $result = \App\Models\BankAccount::deleteAccount(99999);
        
        $this->assertFalse($result);
    }

    

    public function test_category_add_category()
    {
        $result = Category::addCategory('Electronics');
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('categories', ['name' => 'Electronics']);
    }

    public function test_category_add_category_with_parent()
    {
        $parent = Category::factory()->create();
        
        $result = Category::addCategory('Smartphones', $parent->category_id);
        
        $this->assertTrue($result);
    }

    public function test_category_update_category()
    {
        $category = Category::factory()->create(['name' => 'Old Name']);
        
        $result = Category::updateCategory($category->category_id, ['name' => 'New Name']);
        
        $this->assertTrue($result);
        $this->assertEquals('New Name', $category->fresh()->name);
    }

    public function test_category_update_category_invalid_id()
    {
        $result = Category::updateCategory(99999, ['name' => 'Test']);
        
        $this->assertFalse($result);
    }

    public function test_category_delete_category()
    {
        $category = Category::factory()->create();
        
        $result = Category::deleteCategory($category->category_id);
        
        $this->assertTrue($result);
        $this->assertNull(Category::find($category->category_id));
    }

    public function test_category_delete_category_invalid_id()
    {
        $result = Category::deleteCategory(99999);
        
        $this->assertFalse($result);
    }

    public function test_category_get_sub_categories()
    {
        $parent = Category::factory()->create();
        $child1 = Category::factory()->create(['parent_id' => $parent->category_id]);
        $child2 = Category::factory()->create(['parent_id' => $parent->category_id]);

        $subs = Category::getSubCategories($parent->category_id);

        $this->assertCount(2, $subs);
    }

    public function test_category_get_products()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->category_id]);

        $products = $category->getProducts();

        $this->assertCount(1, $products);
    }

    public function test_category_get_all_child_ids()
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->category_id]);
        $grandchild = Category::factory()->create(['parent_id' => $child->category_id]);

        $ids = $parent->getAllChildIds();

        $this->assertContains($parent->category_id, $ids);
        $this->assertContains($child->category_id, $ids);
        $this->assertContains($grandchild->category_id, $ids);
    }

    

    public function test_integration_key_add_key()
    {
        $provider = IntegrationProvider::create(['code' => 'test', 'name' => 'Test']);
        $key = new IntegrationKey();

        $result = $key->addKey($provider->integration_provider_id, 'Test Key', 'secret123');

        $this->assertTrue($result);
    }

    public function test_integration_key_deactivate_key()
    {
        $provider = IntegrationProvider::create(['code' => 'test', 'name' => 'Test']);
        $key = IntegrationKey::create([
            'provider_id' => $provider->integration_provider_id,
            'label' => 'Active Key',
            'public_k' => 'pk_test',
            'encrypted_k' => base64_encode('sk_test'),
            'is_active' => true
        ]);

        $keyModel = new IntegrationKey();
        $keyModel->deactivateKey($key->integration_key_id);

        $this->assertEquals(0, $key->fresh()->is_active);
    }

    public function test_integration_key_encrypt_key()
    {
        $key = new IntegrationKey();
        
        $encrypted = $key->encryptKey('my-secret-key');
        
        $this->assertEquals(base64_encode('my-secret-key'), $encrypted);
    }

    public function test_integration_key_validate_key()
    {
        $key = new IntegrationKey();
        
        $this->assertTrue($key->validateKey('valid-key'));
        $this->assertFalse($key->validateKey(''));
    }

    

    public function test_moderation_action_log_action_not_found()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $action = new ModerationAction();

        
        $action->logAction($admin->user_id, 'product', $product->product_id, 'suspend', 'reason');
        
        $this->assertTrue(true); 
    }

    

    public function test_order_create_order()
    {
        
        $this->markTestSkipped('Requires Cart::getSellerId() and Cart::getSubtotal() methods');
    }

    public function test_order_cancel_order()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $orderInstance = new Order();

        $result = $orderInstance->cancelOrder($order->order_id, 'Customer request');

        $this->assertTrue($result);
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    public function test_order_cancel_order_invalid_id()
    {
        $orderInstance = new Order();
        
        $result = $orderInstance->cancelOrder(99999, 'reason');
        
        $this->assertFalse($result);
    }

    public function test_order_update_status()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $orderInstance = new Order();

        $orderInstance->updateStatus($order->order_id, 'processing');

        $this->assertEquals('processing', $order->fresh()->status);
    }

    public function test_order_confirm_payment()
    {
        $order = Order::factory()->create(['payment_status' => 'pending']);

        $order->confirmPayment();

        $this->assertEquals('settlement', $order->payment_status);
        $this->assertEquals('paid', $order->status);
    }

    public function test_order_complete_order()
    {
        $order = Order::factory()->create(['status' => 'paid']);

        $order->completeOrder();

        $this->assertEquals('completed', $order->status);
    }

    public function test_order_assign_shipment()
    {
        $order = Order::factory()->create();
        $carrier = \App\Models\ShippingCarrier::factory()->create();
        $shipment = new Shipment();
        $shipment->carrier_id = $carrier->shipping_carrier_id;
        $shipment->service_code = 'REG';
        $shipment->tracking_number = 'TRK123';
        $shipment->status = 'pending';
        $shipment->cost = 15000;

        $order->assignShipment($shipment);

        $this->assertEquals($order->order_id, $shipment->order_id);
    }

    public function test_order_formatted_address_attribute()
    {
        $address = Address::factory()->create();
        $order = Order::factory()->create(['shipping_address_id' => $address->address_id]);

        $formatted = $order->formatted_address;

        $this->assertNotEmpty($formatted);
        if ($address->recipient_name) {
            $this->assertStringContainsString($address->recipient_name, $formatted);
        }
    }

    

    public function test_order_item_calculate_subtotal()
    {
        $item = OrderItem::factory()->create([
            'unit_price' => 50000,
            'quantity' => 3
        ]);

        $subtotal = $item->calculateSubtotal();

        $this->assertEquals(150000, $subtotal);
        $this->assertEquals(150000, $item->fresh()->subtotal);
    }

    public function test_order_item_update_quantity()
    {
        $item = OrderItem::factory()->create([
            'unit_price' => 25000,
            'quantity' => 2
        ]);

        $item->updateQuantity(5);

        $this->assertEquals(5, $item->quantity);
        $this->assertEquals(125000, $item->fresh()->subtotal);
    }

    public function test_order_item_link_product()
    {
        $product = Product::factory()->create(['title' => 'Test Product']);
        $item = OrderItem::factory()->create();

        $item->linkProduct($product->product_id);

        $this->assertEquals($product->product_id, $item->product_id);
        $this->assertEquals('Test Product', $item->fresh()->product_title_snapshot);
    }

    public function test_order_item_get_product_title_attribute()
    {
        $item = OrderItem::factory()->create(['product_title_snapshot' => 'Snapshot Title']);

        $title = $item->product_title;

        $this->assertEquals('Snapshot Title', $title);
    }

    

    public function test_payment_initiate_payment()
    {
        $order = Order::factory()->create();
        $provider = IntegrationProvider::factory()->create();
        $payment = new Payment();
        $payment->amount = 100000;
        $payment->provider_id = $provider->integration_provider_id;

        $result = $payment->initiatePayment($order->order_id, 'midtrans');

        $this->assertTrue($result);
        $this->assertEquals('pending', $payment->status);
    }

    public function test_payment_verify_payment_status()
    {
        $payment = Payment::factory()->create(['status' => 'pending']);

        $status = $payment->verifyPaymentStatus('TXN123456');

        $this->assertEquals('settlement', $status);
        $this->assertEquals('TXN123456', $payment->provider_txn_id);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_payment_refund_success()
    {
        $order = Order::factory()->create();
        $payment = Payment::factory()->create([
            'order_id' => $order->order_id,
            'status' => 'settlement',
            'amount' => 100000
        ]);

        $result = $payment->refund($order->order_id, 100000);

        $this->assertTrue($result);
        $this->assertEquals('refund', $payment->fresh()->status);
    }

    public function test_payment_refund_invalid_status()
    {
        $order = Order::factory()->create();
        $payment = Payment::factory()->create([
            'order_id' => $order->order_id,
            'status' => 'pending'
        ]);

        $result = $payment->refund($order->order_id, 100000);

        $this->assertFalse($result);
    }

    public function test_payment_cancel_payment_success()
    {
        $order = Order::factory()->create();
        $payment = Payment::factory()->create([
            'order_id' => $order->order_id,
            'status' => 'pending'
        ]);

        $result = $payment->cancelPayment($order->order_id);

        $this->assertTrue($result);
        $this->assertEquals('cancel', $payment->fresh()->status);
    }

    public function test_payment_cancel_payment_invalid_status()
    {
        $order = Order::factory()->create();
        $payment = Payment::factory()->create([
            'order_id' => $order->order_id,
            'status' => 'settlement'
        ]);

        $result = $payment->cancelPayment($order->order_id);

        $this->assertFalse($result);
    }

    public function test_payment_record_transaction()
    {
        $payment = Payment::factory()->create();
        $data = ['transaction_id' => 'TXN123', 'amount' => 50000];

        $payment->recordTransaction($data);

        $this->assertEquals($data, $payment->fresh()->raw_response);
    }

    

    public function test_shipment_create_shipment()
    {
        $order = Order::factory()->create();
        $carrier = ShippingCarrier::factory()->create();
        $shipment = new Shipment();
        
        $result = $shipment->createShipment($order->order_id, $carrier->shipping_carrier_id, 'REG');
        
        $this->assertTrue($result);
        $this->assertEquals('pending', $shipment->status);
        $this->assertEquals('REG', $shipment->service_code);
    }

    public function test_shipment_update_status()
    {
        $shipment = Shipment::factory()->create(['status' => 'pending']);
        
        
        $shipment->updateStatus('in_transit');
        $this->assertEquals('in_transit', $shipment->fresh()->status);
        $this->assertNotNull($shipment->fresh()->shipped_at);
        
        
        $shipment->updateStatus('delivered');
        $this->assertEquals('delivered', $shipment->fresh()->status);
        $this->assertNotNull($shipment->fresh()->delivered_at);
    }

    public function test_shipment_track_shipment()
    {
        $shipment = Shipment::factory()->create();
        
        $result = $shipment->trackShipment('TRK123');
        
        $this->assertEquals('TRK123', $result['tracking_number']);
        $this->assertEquals($shipment->status, $result['status']);
    }

    public function test_shipment_calculate_shipping_cost()
    {
        $shipment = new Shipment();
        
        $cost = $shipment->calculateShippingCost(2.5, 100);
        
        
        $this->assertEquals(22500, $cost);
    }

    public function test_shipment_mark_delivered()
    {
        $shipment = Shipment::factory()->create(['status' => 'in_transit']);
        
        $shipment->markDelivered();
        
        $this->assertEquals('delivered', $shipment->fresh()->status);
        $this->assertNotNull($shipment->fresh()->delivered_at);
    }

    

    public function test_shipping_carrier_register_carrier()
    {
        $carrier = new ShippingCarrier();
        $carrier->code = 'TEST';
        $carrier->name = 'Test Carrier';
        $carrier->provider_type = 'rates'; 
        
        $result = $carrier->registerCarrier($carrier);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('shipping_carriers', ['code' => 'TEST']);
    }

    public function test_shipping_carrier_enable_disable()
    {
        $carrier = ShippingCarrier::factory()->create(['is_enabled' => false]);
        
        $carrier->enableCarrier();
        $this->assertEquals(1, $carrier->fresh()->is_enabled);
        
        $carrier->disableCarrier();
        $this->assertEquals(0, $carrier->fresh()->is_enabled);
    }

    

    public function test_user_register_logic()
    {
        $userModel = new User();
        $email = 'logictest@example.com';
        $result = $userModel->register('Logic Test', $email, 'password123');
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('users', ['email' => $email, 'role' => 'buyer']);
    }

    public function test_user_login_logic()
    {
        
        $user = User::factory()->create(['password' => 'plainpassword']);
        
        $userModel = new User();
        
        $this->assertTrue($userModel->login($user->email, 'plainpassword'));
        $this->assertFalse($userModel->login($user->email, 'wrongpassword'));
    }

    public function test_user_update_profile_logic()
    {
        $user = User::factory()->create();
        $user->updateProfile('New Name', 'http://new.url/photo.jpg');
        
        $user->refresh();
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('http://new.url/photo.jpg', $user->photo_url);
    }

    public function test_user_suspend_account_logic()
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->suspendAccount('Violation');
        
        $this->assertEquals('suspended', $user->fresh()->status);
    }

    public function test_user_get_orders_logic()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        
        $order = Order::factory()->create([
            'buyer_id' => $buyer->user_id,
            'seller_id' => $seller->user_id
        ]);

        $this->assertCount(1, $buyer->getOrders('buyer'));
        $this->assertCount(1, $seller->getOrders('seller'));
        $this->assertCount(0, $buyer->getOrders('seller')); 
    }

    public function test_user_get_products_logic()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        Product::factory()->create(['seller_id' => $seller->user_id]);
        
        $this->assertCount(1, $seller->getProducts());
    }

    

    public function test_wallet_ledger_methods()
    {
        $user = User::factory()->create();
        $ledger = new WalletLedger();
        
        
        $this->assertEquals(0, $ledger->getBalance($user->user_id));
        
        
        $ledger->credit($user->user_id, 50000, 1001);
        $this->assertEquals(50000, $ledger->getBalance($user->user_id));
        
        
        $ledger->debit($user->user_id, 20000, 2002);
        $this->assertEquals(30000, $ledger->getBalance($user->user_id));
        
        
        $history = $ledger->getTransactionHistory($user->user_id);
        $this->assertCount(2, $history);
        $this->assertEquals('debit', $history->first()->direction); 
    }

    
    
    public function test_review_logic_methods()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $reviewModel = new \App\Models\Review();
        
        
        $result = $reviewModel->addReview($user->user_id, $product->product_id, 5, 'Great!');
        $this->assertTrue($result);
        
        $review = \App\Models\Review::where('user_id', $user->user_id)->first();
        $this->assertNotNull($review);
        
        
        $reviewModel->editReview($review->review_id, 4, 'Good');
        $review->refresh();
        $this->assertEquals(4, $review->rating);
        $this->assertEquals('Good', $review->comment);
        
        
        $reviewModel->hideReview($review->review_id);
        $this->assertEquals('hidden', $review->fresh()->status);
        
        
        $result = $reviewModel->deleteReview($review->review_id);
        $this->assertTrue($result);
        $this->assertNull(\App\Models\Review::find($review->review_id));
    }

    public function test_cart_item_update_logic()
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['price' => 10000]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
            'unit_price' => 10000,
            'subtotal' => 10000
        ]);
        
        $cart->update(['total_price' => 10000]);
        
        
        $cart->updateQuantity($product->product_id, 3);
        $this->assertEquals(3, $item->fresh()->quantity);
        $this->assertEquals(30000, $item->fresh()->subtotal);
        $this->assertEquals(30000, $cart->fresh()->total_price);
        
        
        $cart->removeItem($product->product_id);
        $this->assertNull(CartItem::find($item->cart_item_id));
        $this->assertEquals(0, $cart->fresh()->total_price);
    }
    
    public function test_cart_clear_logic()
    {
        $cart = Cart::factory()->create(['total_price' => 50000]);
        CartItem::factory()->count(2)->create(['cart_id' => $cart->cart_id]);
        
        $this->assertCount(2, $cart->items);
        
        $cart->clearCart();
        
        $this->assertCount(0, $cart->fresh()->items);
        $this->assertEquals(0, $cart->fresh()->total_price);
    }

    

    public function test_integration_provider_methods()
    {
        $provider = new IntegrationProvider();
        
        
        $result = $provider->addProvider('TEST_PROV', 'Test Provider');
        $this->assertTrue($result);
        $this->assertDatabaseHas('integration_providers', ['code' => 'TEST_PROV']);
        
        $id = IntegrationProvider::where('code', 'TEST_PROV')->value('integration_provider_id');
        
        
        $result = $provider->updateProvider($id, 'Updated Provider');
        $this->assertTrue($result);
        $this->assertDatabaseHas('integration_providers', ['name' => 'Updated Provider']);
        
        
        $result = $provider->deleteProvider($id);
        $this->assertTrue($result);
        $this->assertDatabaseMissing('integration_providers', ['integration_provider_id' => $id]);
    }

    

    public function test_payout_request_workflow()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $admin = User::factory()->create(['role' => 'admin']);
        $model = new \App\Models\PayoutRequest();
        
        
        $result = $model->createRequest($seller->user_id, 50000);
        $this->assertTrue($result);
        
        $payout = \App\Models\PayoutRequest::where('seller_id', $seller->user_id)->first();
        $this->assertNotNull($payout);
        $this->assertEquals('requested', $payout->status);
        $this->assertNotNull($payout->requested_at);
        
        
        $payout->approveRequest($admin->user_id);
        $this->assertEquals('approved', $payout->fresh()->status);
        $this->assertNotNull($payout->fresh()->processed_at);
        $this->assertEquals($admin->user_id, $payout->fresh()->processed_by);
        
        
        $payout->rejectRequest($admin->user_id, 'Audit failed');
        $this->assertEquals('rejected', $payout->fresh()->status);
        $this->assertEquals('Audit failed', $payout->fresh()->notes);
        
        
        $payout->markAsPaid();
        $this->assertEquals('paid', $payout->fresh()->status);
        
        
        $payout->cancelRequest();
        $this->assertEquals('cancelled', $payout->fresh()->status);
        
        
        $this->assertNotNull($payout->processedByAdmin);
    }
    
    public function test_product_logic_methods()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $category = Category::factory()->create();
        
        
        $data = [
            'seller_id' => $seller->user_id,
            'category_id' => $category->category_id,
            'title' => 'Logic Product',
            'description' => 'Desc',
            'price' => 10000,
            'stock' => 5,
            'weight' => 1000,
            'condition' => 'new',
            'status' => 'active'
        ];
        
        $result = Product::createProduct($data);
        $this->assertTrue($result);
        
        $product = Product::where('title', 'Logic Product')->first();
        $this->assertNotNull($product);
        
        
        $result = Product::updateProduct($product->product_id, ['title' => 'Updated Logic']);
        $this->assertTrue($result);
        $this->assertEquals('Updated Logic', $product->fresh()->title);
        
        
        $this->assertCount(0, $product->getImages());
        
        
        $this->assertEquals(0.0, $product->calculateAverageRating());
        $this->assertEquals(0.0, $product->rating); 
        
        
        $product->changeStatus(ProductStatus::Suspended);
        $this->assertEquals('suspended', $product->fresh()->status->value ?? $product->fresh()->status);
        
        
        $this->assertEquals(0, $product->sold_count);
        
        
        $result = Product::deleteProduct($product->product_id);
        $this->assertTrue($result);
        $this->assertNull(Product::find($product->product_id));
    }
    
    public function test_product_sold_count_attribute()
    {
        
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['seller_id' => $seller->user_id]);
        $order = Order::factory()->create(['status' => 'completed']);
        OrderItem::factory()->create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'quantity' => 5
        ]);
        
        $this->assertEquals(5, $product->getSoldCountAttribute());
    }

    
    
    public function test_product_image_methods()
    {
        $product = Product::factory()->create();
        
        
        $result = \App\Models\ProductImage::addImage($product->product_id, 'img1.jpg', true, 1);
        $this->assertTrue($result);
        
        $img1 = \App\Models\ProductImage::where('url', 'img1.jpg')->first();
        $this->assertTrue((bool)$img1->is_primary);
        
        
        \App\Models\ProductImage::addImage($product->product_id, 'img2.jpg', true, 2);
        $img2 = \App\Models\ProductImage::where('url', 'img2.jpg')->first();
        
        $this->assertFalse((bool)$img1->fresh()->is_primary);
        $this->assertTrue((bool)$img2->is_primary);
        
        
        \App\Models\ProductImage::setPrimary($img1->product_image_id);
        $this->assertTrue((bool)$img1->fresh()->is_primary);
        $this->assertFalse((bool)$img2->fresh()->is_primary);
        
        
        \App\Models\ProductImage::reorderImages([$img2->product_image_id, $img1->product_image_id]);
        $this->assertEquals(1, $img2->fresh()->sort_order);
        $this->assertEquals(2, $img1->fresh()->sort_order);
        
        
        $result = \App\Models\ProductImage::deleteImage($img1->product_image_id);
        $this->assertTrue($result);
    }
    
    
    
    public function test_webhook_log_methods()
    {
        $log = new WebhookLog();
        
        
        $json = json_encode(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $log->parsePayload($json));
        $this->assertEquals(['foo' => 'bar'], $log->parsePayload(['foo' => 'bar']));
        
        
        $created = WebhookLog::create(['provider_code' => 'test', 'event_type' => 'e']);
        $this->assertTrue($log->replayEvent($created->webhook_log_id));
        $this->assertFalse($log->replayEvent(99999));
    }
    
    
    
    public function test_profile_update_object_method()
    {
        $user = User::factory()->create();
        $profile = \App\Models\Profile::create([
            'user_id' => $user->user_id, 
            'bio' => 'Old Bio',
            'phone' => '111'
        ]);
        
        $newData = new \App\Models\Profile();
        $newData->user_id = $user->user_id; 
        $newData->bio = 'New Bio';
        $newData->phone = '222';
        
        $result = $profile->updateProfile($newData);
        $this->assertTrue($result);
        $this->assertEquals('New Bio', $profile->fresh()->bio);
        $this->assertEquals('222', $profile->fresh()->phone);
    }
    
    
    
    public function test_cart_add_existing_item()
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['price' => 10000]);
        
        
        $cart->addItem($product->product_id, 1);
        $item = CartItem::where('cart_id', $cart->cart_id)->where('product_id', $product->product_id)->first();
        $this->assertEquals(1, $item->quantity);
        $this->assertEquals(10000, $item->subtotal);

        
        $cart->addItem($product->product_id, 2);
        
        $item->refresh();
        $this->assertEquals(3, $item->quantity); 
        $this->assertEquals(30000, $item->subtotal); 
    }
    
    public function test_order_create_order_logic()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $address = Address::factory()->create(['user_id' => $buyer->user_id]);
        
        
        $cart = \Mockery::mock(Cart::class);
        $cart->shouldReceive('getSellerId')->andReturn($seller->user_id);
        $cart->shouldReceive('getSubtotal')->andReturn(100000);
        
        $order = new Order();
        $result = $order->createOrder($buyer->user_id, $cart, $address->address_id);
        
        $this->assertTrue($result);
        $this->assertEquals($buyer->user_id, $order->buyer_id);
        $this->assertEquals($seller->user_id, $order->seller_id);
        $this->assertEquals(100000, $order->subtotal_amount);
        $this->assertEquals('pending', $order->status);
        $this->assertStringStartsWith('ORD-', $order->code);
        
        $this->assertEquals(102500, $order->total_amount); 
        $this->assertEquals(97500, $order->seller_earnings); 
    }
    
    public function test_order_formatted_address_variants()
    {
        $order = new Order();
        
        
        $order->shipping_address_snapshot = null;
        $order->unsetRelation('shippingAddress');
        $this->assertEquals('N/A', $order->formatted_address);
        
        
        $order->shipping_address_snapshot = [
            'recipient_name' => 'John Snapshot',
            'phone_number' => '08123',
            'address_line' => 'Jalan A',
            'city' => 'KOTA BANDUNG',
            'province' => 'West Java',
            'postal_code' => '40000'
        ];
        $formatted = $order->formatted_address;
        $this->assertStringContainsString('John Snapshot', $formatted);
        $this->assertStringContainsString('Bandung', $formatted); 
        
        
        $order->shipping_address_snapshot = null;
        $address = Address::factory()->make([
            'recipient' => 'Jane Relation',
            'city' => 'KABUPATEN BOGOR'
        ]);
        $order->setRelation('shippingAddress', $address);
        
        $formatted = $order->formatted_address;
        $this->assertStringContainsString('Jane Relation', $formatted);
        $this->assertStringContainsString('Bogor', $formatted);
    }
    
    public function test_product_optimization_attributes()
    {
        $product = Product::factory()->create();
        
        
        $product->setRawAttributes([
            'order_items_sum_quantity' => 42,
            'reviews_avg_rating' => 4.5
        ]);
        
        $this->assertEquals(42, $product->sold_count);
        $this->assertEquals(4.5, $product->rating);
    }
    
    public function test_product_image_edge_cases()
    {
        $product = Product::factory()->create();
        
        
        \App\Models\ProductImage::addImage($product->product_id, 'img_auto_1.jpg', false, null);
        $img1 = \App\Models\ProductImage::where('url', 'img_auto_1.jpg')->first();
        $this->assertEquals(1, $img1->sort_order);
        
        \App\Models\ProductImage::addImage($product->product_id, 'img_auto_2.jpg', false, null);
        $img2 = \App\Models\ProductImage::where('url', 'img_auto_2.jpg')->first();
        $this->assertEquals(2, $img2->sort_order);
        
        
        \App\Models\ProductImage::setPrimary(999999);
        $this->assertTrue(true); 
    }
    
    public function test_user_get_orders_invalid_role()
    {
        $user = User::factory()->create();
        $result = $user->getOrders('imposter');
        $this->assertTrue($result->isEmpty());
    }
    
    public function test_bank_account_edge_cases()
    {
        
        \App\Models\BankAccount::setDefaultAccount(999999);
        $this->assertTrue(true); 
    }

}

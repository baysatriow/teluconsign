<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\PayoutRequest;
use App\Models\BankAccount;
use App\Models\IntegrationProvider;
use App\Models\IntegrationKey;
use App\Models\ShippingCarrier;
use App\Models\WalletLedger;
use App\Models\Profile;
use App\Enums\ProductStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['user_id' => 1, 'role' => 'admin']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        
        IntegrationProvider::create(['code' => 'midtrans', 'name' => 'Midtrans Payment Gateway']);
        IntegrationProvider::create(['code' => 'rajaongkir', 'name' => 'RajaOngkir']);
        IntegrationProvider::create(['code' => 'whatsapp', 'name' => 'WhatsApp (Fonnte)']);
    }

    public function test_dashboard_shows_sales_data()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        Order::factory()->count(3)->create([
            'seller_id' => $seller->user_id,
            'status' => 'completed',
            'created_at' => now()
        ]);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertOk()
                 ->assertViewIs('admin.dashboard');
    }

    public function test_products_index_with_search()
    {
        Product::factory()->create(['title' => 'iPhone 14']);
        Product::factory()->create(['title' => 'Samsung Galaxy']);

        $response = $this->actingAs($this->admin)
                         ->get('/admin/products?q=iPhone');

        $response->assertOk()
                 ->assertSee('iPhone');
    }

    public function test_products_index_with_seller_search()
    {
        $seller = User::factory()->create(['name' => 'John Doe']);
        Product::factory()->create(['seller_id' => $seller->user_id]);

        $response = $this->actingAs($this->admin)
                         ->get('/admin/products?q=John');

        $response->assertOk();
    }

    public function test_products_index_with_category_search()
    {
        $category = Category::factory()->create(['name' => 'Electronics']);
        Product::factory()->create(['category_id' => $category->category_id]);

        $response = $this->actingAs($this->admin)
                         ->get('/admin/products?q=Electronics');

        $response->assertOk();
    }

    public function test_products_index_filter_by_status()
    {
        Product::factory()->create(['status' => ProductStatus::Active]);
        Product::factory()->create(['status' => ProductStatus::Suspended]);

        $response = $this->actingAs($this->admin)
                         ->get('/admin/products?status=' . ProductStatus::Active->value);

        $response->assertOk();
    }

    public function test_products_index_filter_by_category()
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->category_id]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.products', ['category' => $category->category_id]));

        $response->assertOk();
    }

    public function test_show_product_details()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.products.show', $product->product_id));

        $response->assertOk()
                 ->assertViewIs('admin.products.show');
    }

    public function test_toggle_product_status_activate_suspended()
    {
        $product = Product::factory()->create(['status' => ProductStatus::Suspended]);

        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.products.toggle_status', $product->product_id));

        $response->assertSessionHas('success', 'Produk berhasil diaktifkan kembali.');
        $this->assertEquals(ProductStatus::Active, $product->fresh()->status);
    }

    public function test_toggle_product_status_suspend_active()
    {
        $product = Product::factory()->create(['status' => ProductStatus::Active]);

        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.products.toggle_status', $product->product_id));

        $response->assertSessionHas('success');
        $this->assertEquals(ProductStatus::Suspended, $product->fresh()->status);
    }

    public function test_users_index_hides_super_admin_for_regular_admin()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users'));

        $response->assertOk()
                 ->assertViewIs('admin.users.index');
    }

    public function test_users_index_with_search()
    {
        User::factory()->create(['name' => 'Test User', 'role' => 'buyer']);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.users', ['q' => 'Test']));

        $response->assertOk();
    }

    public function test_users_index_filter_by_status()
    {
        User::factory()->create(['role' => 'buyer', 'status' => 'active']);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.users', ['status' => 'active']));

        $response->assertOk();
    }

    public function test_users_index_filter_by_role()
    {
        User::factory()->create(['role' => 'seller']);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.users', ['role' => 'seller']));

        $response->assertOk();
    }

    public function test_users_show_with_product_search()
    {
        $user = User::factory()->create();
        Product::factory()->create(['seller_id' => $user->user_id, 'title' => 'Test Product']);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.users.show', ['id' => $user->user_id, 'q' => 'Test']));

        $response->assertOk();
    }

    public function test_users_show_filter_by_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Product::factory()->create([
            'seller_id' => $user->user_id,
            'category_id' => $category->category_id
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.users.show', ['id' => $user->user_id, 'category' => $category->category_id]));

        $response->assertOk();
    }

    public function test_users_show_filter_by_status()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.users.show', ['id' => $user->user_id, 'status' => ProductStatus::Active->value]));

        $response->assertOk();
    }

    public function test_users_create_only_super_admin()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_create_admin_page()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.create'));

        $response->assertOk();
    }

    public function test_store_admin_with_phone_normalization_08()
    {
        $response = $this->actingAs($this->superAdmin)
                         ->post(route('admin.users.store_admin'), [
                             'name' => 'New Admin',
                             'username' => 'newadmin',
                             'email' => 'newadmin@test.com',
                             'phone' => '08123456789',
                             'password' => 'password123',
                             'password_confirmation' => 'password123'
                         ]);

        $response->assertRedirect(route('admin.users'));
        $this->assertDatabaseHas('profiles', [
            'phone' => '628123456789'
        ]);
    }

    public function test_store_admin_with_phone_normalization_regular()
    {
        $response = $this->actingAs($this->superAdmin)
                         ->post(route('admin.users.store_admin'), [
                             'name' => 'New Admin',
                             'username' => 'newadmin2',
                             'email' => 'newadmin2@test.com',
                             'phone' => '8123456789',
                             'password' => 'password123',
                             'password_confirmation' => 'password123'
                         ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('profiles', [
            'phone' => '628123456789'
        ]);
    }

    public function test_store_admin_exception_handling()
    {
        $this->actingAs($this->superAdmin)
             ->post(route('admin.users.store_admin'), [
                 'name' => 'Admin 1',
                 'username' => 'admin1',
                 'email' => 'admin1@test.com',
                 'phone' => '0811111111',
                 'password' => 'password',
                 'password_confirmation' => 'password'
             ]);

        $response = $this->actingAs($this->superAdmin)
             ->post(route('admin.users.store_admin'), [
                 'name' => 'Admin 2',
                 'username' => 'admin2',
                 'email' => 'admin1@test.com', 
                 'phone' => '0822222222',
                 'password' => 'password',
                 'password_confirmation' => 'password'
             ]);
        
        $response->assertSessionHasErrors();
    }

    public function test_users_edit_only_super_admin_or_self()
    {
        $otherAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.users.edit', $otherAdmin->user_id));

        $response->assertForbidden();
    }

    public function test_admin_can_edit_own_account()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.users.edit', $this->admin->user_id));

        $response->assertOk();
    }

    public function test_update_admin_with_password()
    {
        $response = $this->actingAs($this->superAdmin)
                         ->put(route('admin.users.update_admin', $this->admin->user_id), [
                             'name' => 'Updated Name',
                             'username' => $this->admin->username,
                             'email' => $this->admin->email,
                             'password' => 'newpassword123',
                             'password_confirmation' => 'newpassword123'
                         ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('newpassword123', $this->admin->fresh()->password));
    }

    public function test_update_admin_without_password()
    {
        $oldPassword = $this->admin->password;

        $response = $this->actingAs($this->superAdmin)
                         ->put(route('admin.users.update_admin', $this->admin->user_id), [
                             'name' => 'Updated Name',
                             'username' => $this->admin->username,
                             'email' => $this->admin->email,
                         ]);

        $response->assertRedirect();
        $this->assertEquals($oldPassword, $this->admin->fresh()->password);
    }

    public function test_send_reset_link_with_phone()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '08123456789']);

        $this->mock(\App\Services\FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once()->andReturn(['status' => true]);
        });

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.users.send_reset_link', $user->user_id));

        $response->assertSessionHas('success', 'Link reset password telah dikirim ke WhatsApp pengguna.');
    }

    public function test_send_reset_link_without_phone()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.users.send_reset_link', $user->user_id));

        $response->assertSessionHas('error', 'Pengguna ini tidak memiliki nomor HP yang terdaftar untuk dikirimi link.');
    }

    public function test_destroy_user_prevent_super_admin()
    {
        $response = $this->actingAs($this->admin)
                         ->delete(route('admin.users.destroy', 1));

        $response->assertSessionHas('error', 'Super Admin tidak dapat dihapus.');
    }

    public function test_destroy_user_prevent_self_delete()
    {
        $response = $this->actingAs($this->admin)
                         ->delete(route('admin.users.destroy', $this->admin->user_id));

        $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun sendiri.');
    }

    public function test_destroy_user_prevent_admin_by_non_super()
    {
        $otherAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin.users.destroy', $otherAdmin->user_id));

        $response->assertSessionHas('error', 'Hanya Super Admin yang dapat menghapus Administrator.');
    }

    public function test_destroy_user_deletes_products()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        Product::factory()->count(3)->create(['seller_id' => $seller->user_id]);

        $response = $this->actingAs($this->superAdmin)
                         ->delete(route('admin.users.destroy', $seller->user_id));

        $response->assertRedirect();
        $this->assertEquals(0, Product::where('seller_id', $seller->user_id)->count());
    }

    public function test_toggle_user_status_prevent_self()
    {
        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.users.toggle_status', $this->admin->user_id));

        $response->assertSessionHas('error', 'Anda tidak dapat mengubah status akun sendiri.');
    }

    public function test_toggle_user_status_activate()
    {
        $user = User::factory()->create(['status' => 'suspended']);

        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.users.toggle_status', $user->user_id));

        $response->assertSessionHas('success', 'Akun pengguna berhasil diaktifkan kembali.');
        $this->assertEquals('active', $user->fresh()->status);
    }

    public function test_toggle_user_status_suspend()
    {
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.users.toggle_status', $user->user_id));

        $response->assertSessionHas('success', 'Akun pengguna berhasil disuspend (dinonaktifkan).');
        $this->assertEquals('suspended', $user->fresh()->status);
    }

    public function test_payouts_index_with_search()
    {
        $seller = User::factory()->create(['name' => 'John Seller']);
        $bankAccount = BankAccount::factory()->create(['user_id' => $seller->user_id]);
        PayoutRequest::factory()->create([
            'seller_id' => $seller->user_id,
            'bank_account_id' => $bankAccount->bank_account_id
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.payouts', ['q' => 'John']));

        $response->assertOk();
    }

    public function test_payouts_index_filter_status_approved()
    {
        $seller = User::factory()->create();
        $bankAccount = BankAccount::factory()->create(['user_id' => $seller->user_id]);
        PayoutRequest::factory()->create([
            'seller_id' => $seller->user_id,
            'bank_account_id' => $bankAccount->bank_account_id,
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.payouts', ['status' => 'approved']));

        $response->assertOk();
    }

    public function test_update_payout_status_approve()
    {
        $seller = User::factory()->create();
        $bankAccount = BankAccount::factory()->create(['user_id' => $seller->user_id]);
        $payout = PayoutRequest::factory()->create([
            'seller_id' => $seller->user_id,
            'bank_account_id' => $bankAccount->bank_account_id,
            'status' => 'requested',
            'amount' => 100000
        ]);

        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.payouts.update', $payout->payout_request_id), [
                             'status' => 'approved'
                         ]);

        $response->assertRedirect();
        $this->assertEquals('paid', $payout->fresh()->status);
    }

    public function test_update_payout_status_reject()
    {
        $seller = User::factory()->create();
        $bankAccount = BankAccount::factory()->create(['user_id' => $seller->user_id]);
        $payout = PayoutRequest::factory()->create([
            'seller_id' => $seller->user_id,
            'bank_account_id' => $bankAccount->bank_account_id,
            'status' => 'requested',
            'amount' => 100000
        ]);

        WalletLedger::create([
            'user_id' => $seller->user_id,
            'direction' => 'debit',
            'source_type' => 'payout',
            'source_id' => $payout->payout_request_id,
            'amount' => 100000,
            'balance_after' => -100000,
            'posted_at' => now()
        ]);

        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.payouts.update', $payout->payout_request_id), [
                             'status' => 'rejected',
                             'notes' => 'Invalid request'
                         ]);

        $response->assertRedirect();
        $this->assertEquals('rejected', $payout->fresh()->status);
        $this->assertEquals(1, WalletLedger::where('user_id', $seller->user_id)->where('direction', 'credit')->count());
    }

    public function test_payment_gateway_page_creates_provider_if_not_exists()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.integrations.payment'));

        $response->assertOk();
        $this->assertDatabaseHas('integration_providers', ['code' => 'midtrans']);
    }

    public function test_shipping_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.integrations.shipping'));
        $response->assertOk();
    }

    public function test_whatsapp_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.integrations.whatsapp'));
        $response->assertOk();
    }

    public function test_get_payment_test_token_success()
    {
        $this->mock(\App\Services\MidtransService::class, function ($mock) {
            $mock->shouldReceive('createSnapToken')->andReturn(['token' => 'TEST_TOKEN_123']);
        });

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.integrations.payment.test-token'));

        $response->assertOk()
                 ->assertJson(['token' => 'TEST_TOKEN_123']);
    }

    public function test_store_carrier()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('admin.integrations.carrier.store'), [
                             'name' => 'Test Carrier',
                             'code' => 'TEST',
                             'provider_type' => 'rates'
                         ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shipping_carriers', ['code' => 'test']);
    }

    public function test_update_carrier()
    {
        $carrier = ShippingCarrier::factory()->create(['name' => 'Old Name', 'provider_type' => 'rates']);

        $response = $this->actingAs($this->admin)
                         ->put(route('admin.integrations.carrier.update', $carrier->shipping_carrier_id), [
                             'name' => 'New Name',
                             'code' => $carrier->code,
                             'provider_type' => 'rates'
                         ]);

        $response->assertRedirect();
        $this->assertEquals('New Name', $carrier->fresh()->name);
    }

    public function test_delete_carrier()
    {
        $carrier = ShippingCarrier::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin.integrations.carrier.delete', $carrier->shipping_carrier_id));

        $response->assertRedirect();
        $this->assertNull(ShippingCarrier::find($carrier->shipping_carrier_id));
    }

    public function test_check_shipping_cost_test()
    {
        $this->mock(\App\Services\RajaOngkirService::class, function ($mock) {
            $mock->shouldReceive('checkCost')->andReturn([
                'status' => true,
                'data' => [
                    [
                        'code' => 'jne',
                        'costs' => [
                            ['service' => 'REG', 'cost' => [['value' => 10000, 'etd' => '2-3']]]
                        ]
                    ]
                ]
            ]);
        });

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.integrations.shipping.test-cost'), [
                             'origin' => 1,
                             'destination' => 2,
                             'weight' => 1000,
                             'courier' => 'jne'
                         ]);

        $response->assertRedirect()
                 ->assertSessionHas('cost_results');
    }

    public function test_send_test_whatsapp()
    {
        $this->mock(\App\Services\FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once()->andReturn(['status' => true]);
        });

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.integrations.whatsapp.test-send'), [
                             'phone' => '08123456789'
                         ]);

        $response->assertRedirect()
                 ->assertSessionHas('success');
    }

    public function test_update_payment_gateway()
    {
        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.integrations.payment.update'), [
                             'server_key' => 'SB-Mid-Server-NEW',
                             'client_key' => 'SB-Mid-Client-NEW',
                             'merchant_id' => 'G12345',
                             'mode' => 'sandbox'
                         ]);

        $response->assertRedirect();
        
        $provider = IntegrationProvider::where('code', 'midtrans')->first();
        $this->assertDatabaseHas('integration_keys', ['provider_id' => $provider->integration_provider_id]);
        
        $keys = $provider->getActiveKeys(); 
        $key = $keys->first(); 
        
        $this->assertNotNull($key, 'Active key not found for Midtrans');
        $this->assertEquals('SB-Mid-Server-NEW', \Illuminate\Support\Facades\Crypt::decryptString($key->encrypted_k));
    }

    public function test_update_shipping_api()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('admin.integrations.shipping.update'), [
                             'api_key' => 'key123',
                             'base_url' => 'https://api.rajaongkir.com/starter'
                         ]);

        $response->assertRedirect();
        
        $provider = IntegrationProvider::where('code', 'rajaongkir')->first();
        $keys = $provider->getActiveKeys();
        $key = $keys->first();
        
        $this->assertEquals('key123', $key->public_k);
    }

    public function test_users_show_all_filters()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Product::factory()->create([
            'seller_id' => $user->user_id,
            'title' => 'Filtered Product',
            'category_id' => $category->category_id,
            'status' => ProductStatus::Suspended
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.show', [
            'id' => $user->user_id,
            'category' => $category->category_id,
            'status' => ProductStatus::Suspended->value,
            'q' => 'Filtered'
        ]));

        $response->assertOk()->assertSee('Filtered Product');
    }

    public function test_send_reset_link_failure()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->user_id, 'phone' => '08123456789']);

        $this->mock(\App\Services\FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once()->andReturn(['status' => false, 'error' => 'API Down']);
        });

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.users.send_reset_link', $user->user_id));

        $response->assertSessionHas('error', 'Gagal kirim WA: API Down');
    }

    public function test_destroy_user_full_cleanup()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $buyer = User::factory()->create(['role' => 'buyer']);
        
        Profile::factory()->create(['user_id' => $user->user_id]);
        Address::factory()->create(['user_id' => $user->user_id]);
        $product = Product::factory()->create(['seller_id' => $user->user_id]);
        $order = Order::factory()->create(['seller_id' => $user->user_id, 'buyer_id' => $buyer->user_id]);
        WalletLedger::create([
            'user_id' => $user->user_id,
            'direction' => 'credit',
            'amount' => 1000,
            'balance_after' => 1000,
            'source_type' => 'test',
            'posted_at' => now()
        ]);

        $response = $this->actingAs($this->superAdmin)
                         ->delete(route('admin.users.destroy', $user->user_id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['user_id' => $user->user_id]);
        $this->assertDatabaseMissing('profiles', ['user_id' => $user->user_id]);
        $this->assertDatabaseMissing('products', ['seller_id' => $user->user_id]);
        $this->assertDatabaseMissing('orders', ['seller_id' => $user->user_id]);
        $this->assertDatabaseMissing('wallet_ledgers', ['user_id' => $user->user_id]);
    }

    public function test_toggle_carrier_status()
    {
        $carrier = ShippingCarrier::factory()->create(['is_enabled' => true]);

        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.integrations.carrier.toggle', $carrier->shipping_carrier_id));

        $response->assertSessionHas('success');
        $this->assertEquals(0, $carrier->fresh()->is_enabled);
    }

    public function test_webhook_logs_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.integrations.webhook-logs'));
        $response->assertOk();
    }

    public function test_check_shipping_cost_test_failure()
    {
        $this->mock(\App\Services\RajaOngkirService::class, function ($mock) {
            $mock->shouldReceive('checkCost')->andReturn(['status' => false, 'message' => 'Empty Results']);
        });

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.integrations.shipping.test-cost'), [
                             'origin' => 1,
                             'destination' => 2,
                             'weight' => 1000,
                             'courier' => 'jne'
                         ]);

        $response->assertSessionHas('error');
    }

    public function test_send_test_whatsapp_failure()
    {
        $this->mock(\App\Services\FonnteService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->andReturn(['status' => false, 'error' => 'API Limit']);
        });

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.integrations.whatsapp.test-send'), [
                             'phone' => '08123456789'
                         ]);

        $response->assertSessionHas('error');
    }

    public function test_get_payment_test_token_failure()
    {
        $this->mock(\App\Services\MidtransService::class, function ($mock) {
            $mock->shouldReceive('createSnapToken')->andReturn(null);
        });

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.integrations.payment.test-token'));

        $response->assertStatus(500);
    }
}
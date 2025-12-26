<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Address;
use App\Models\Category;
use App\Models\WalletLedger;
use App\Models\BankAccount;
use App\Models\PayoutRequest;
use App\Enums\ProductStatus;
use Illuminate\Support\Facades\DB;
use App\Models\ProductImage;

class ShopControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seller;
    protected $buyer;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seller = User::factory()->create(['role' => 'seller', 'username' => 'sellershop']);
        $this->buyer = User::factory()->create(['role' => 'buyer']);
        $this->category = Category::factory()->create();

        
        Address::factory()->create([
            'user_id' => $this->seller->user_id,
            'is_shop_default' => true,
            'is_default' => true
        ]);
    }

    

    public function test_public_shop_page_accessible()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->seller->user_id,
            'status' => ProductStatus::Active
        ]);

        $response = $this->get(route('shop.show', $this->seller->username));

        $response->assertOk();
        $response->assertViewHas('seller');
        $response->assertViewHas('products');
        $response->assertSee($this->seller->username);
        $response->assertSee($product->title);
    }

    public function test_public_shop_page_search()
    {
        Product::factory()->create(['seller_id' => $this->seller->user_id, 'title' => 'Apple iPhone', 'status' => ProductStatus::Active]);
        Product::factory()->create(['seller_id' => $this->seller->user_id, 'title' => 'Samsung Galaxy', 'status' => ProductStatus::Active]);

        $response = $this->get(route('shop.show', ['id' => $this->seller->username, 'search' => 'Apple']));

        $response->assertOk();
        $response->assertSee('Apple iPhone');
        $response->assertDontSee('Samsung Galaxy');
    }

    

    public function test_seller_dashboard_accessible()
    {
        $response = $this->actingAs($this->seller)->get(route('shop.index'));
        $response->assertOk();
        $response->assertViewIs('shop.index');
    }

    public function test_buyer_redirected_to_onboarding()
    {
        $response = $this->actingAs($this->buyer)->get(route('shop.index'));
        $response->assertViewIs('shop.onboarding');
    }

    public function test_register_store_converts_buyer_to_seller()
    {
        $response = $this->actingAs($this->buyer)->post(route('shop.register'));
        
        $response->assertRedirect(route('shop.index'));
        $this->assertEquals('seller', $this->buyer->fresh()->role);
    }

    

    public function test_create_product_page_accessible()
    {
        $response = $this->actingAs($this->seller)->get(route('shop.products.create'));
        $response->assertOk();
    }

    public function test_store_product_success()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('prod.jpg');

        $data = [
            'title' => 'Test Product',
            'category_id' => $this->category->category_id,
            'price' => '50.000', 
            'weight' => '1.000',
            'stock' => '10',
            'condition' => 'new',
            'description' => 'Test description',
            'status_input' => 'active',
            'images' => [$image]
        ];

        $response = $this->actingAs($this->seller)->post(route('shop.products.store'), $data);

        $response->assertRedirect(route('shop.products.index'));
        $this->assertDatabaseHas('products', [
            'title' => 'Test Product',
            'price' => 50000,
            'stock' => 10
        ]);
    }

    public function test_store_product_validation_error()
    {
        $response = $this->actingAs($this->seller)->post(route('shop.products.store'), [
            'title' => '', 
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_edit_product_page_accessible()
    {
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id]);
        
        $response = $this->actingAs($this->seller)->get(route('shop.products.edit', $product));
        $response->assertOk();
    }

    public function test_update_product_success()
    {
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id]);

        $data = [
            'title' => 'Updated Title',
            'category_id' => $this->category->category_id,
            'price' => '60.000',
            'weight' => '2.000',
            'stock' => '20',
            'condition' => 'used',
            'description' => 'Updated desc',
            'status_input' => 'active'
        ];

        $response = $this->actingAs($this->seller)
            ->put(route('shop.products.update', $product), $data);

        $response->assertRedirect(route('shop.products.index'));
        $this->assertEquals('Updated Title', $product->fresh()->title);
        $this->assertEquals(60000, $product->fresh()->price);
    }

    

    public function test_orders_list_accessible()
    {
        Order::factory()->create(['seller_id' => $this->seller->user_id, 'status' => 'pending']);
        
        $response = $this->actingAs($this->seller)->get(route('shop.orders'));
        $response->assertOk();
        $response->assertSee('pending');
    }

    public function test_update_order_status_logic()
    {
        $order = Order::factory()->create([
            'seller_id' => $this->seller->user_id, 
            'status' => 'paid',
            'payment_status' => 'settlement',
            'seller_earnings' => 90000
        ]);

        
        $response = $this->actingAs($this->seller)
            ->patch(route('shop.orders.update_status', $order->order_id), ['status' => 'processed']);
        
        $this->assertEquals('shipped', $order->fresh()->status);

        $response = $this->actingAs($this->seller)
            ->patch(route('shop.orders.update_status', $order->order_id), ['status' => 'completed']);

        $this->assertEquals('completed', $order->fresh()->status);
        
        
        $this->assertDatabaseHas('wallet_ledgers', [
            'user_id' => $this->seller->user_id,
            'direction' => 'credit',
            'amount' => 90000,
            'source_type' => 'order'
        ]);
    }

    

    public function test_payouts_page_accessible()
    {
        $response = $this->actingAs($this->seller)->get(route('shop.payouts'));
        $response->assertOk();
    }

    public function test_store_bank_account()
    {
        $data = [
            'bank_name' => 'BCA',
            'account_no' => '1234567890',
            'account_name' => 'Seller Name'
        ];

        $response = $this->actingAs($this->seller)
            ->post(route('shop.banks.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('bank_accounts', ['account_no' => '1234567890']);
    }

    public function test_payout_request_success()
    {
        
        WalletLedger::create([
            'user_id' => $this->seller->user_id,
            'direction' => 'credit',
            'amount' => 500000,
            'balance_after' => 500000,
            'source_type' => 'deposit', 
            'posted_at' => now()
        ]);

        $bank = BankAccount::create([
            'user_id' => $this->seller->user_id,
            'bank_name' => 'BCA', 
            'account_no' => '123', 
            'account_name' => 'Seller'
        ]);

        $response = $this->actingAs($this->seller)->post(route('shop.payouts.store'), [
            'amount' => '100.000',
            'bank_account_id' => $bank->bank_account_id
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payout_requests', ['amount' => 100000]);
        
        
        $this->assertEquals(400000, WalletLedger::where('user_id', $this->seller->user_id)->latest('wallet_ledger_id')->value('balance_after'));
    }

    public function test_payout_insufficient_balance()
    {
        $bank = BankAccount::create([
            'user_id' => $this->seller->user_id,
            'bank_name' => 'BCA', 
            'account_no' => '123', 
            'account_name' => 'Seller'
        ]);

        $response = $this->actingAs($this->seller)->post(route('shop.payouts.store'), [
            'amount' => '100.000', 
            'bank_account_id' => $bank->bank_account_id
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('payout_requests', ['amount' => 100000]);
    }

    

    public function test_shop_address_index()
    {
        $response = $this->actingAs($this->seller)->get(route('shop.address.index'));
        $response->assertOk();
    }

    public function test_shop_address_create()
    {
        $data = [
            'label' => 'Warehouse',
            'recipient' => 'Manager',
            'phone' => '08123',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'district' => 'Coblong',
            'village' => 'Dago',
            'postal_code' => '40135',
            'detail_address' => 'Jl Dago'
        ];

        $response = $this->actingAs($this->seller)->post(route('shop.address.store'), $data);
        $response->assertRedirect(route('shop.address.index'));
        $this->assertDatabaseHas('addresses', ['label' => 'Warehouse']);
    }

    public function test_cannot_delete_shop_default_address()
    {
        $address = $this->seller->addresses()->where('is_shop_default', true)->first();
        
        $response = $this->actingAs($this->seller)->delete(route('shop.address.delete', $address->address_id));
        
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('addresses', ['address_id' => $address->address_id]);
    }

    public function test_address_delete_prevents_last_address()
    {

    }

    public function test_product_filters_logic()
    {
         $seller = $this->createSeller();
         
         $active = Product::factory()->create(['seller_id' => $seller->user_id, 'title' => 'ActiveProd', 'status' => 'active', 'stock' => 10]);
         $draft = Product::factory()->create(['seller_id' => $seller->user_id, 'title' => 'DraftProd', 'status' => 'archived', 'stock' => 5]);
         $suspended = Product::factory()->create(['seller_id' => $seller->user_id, 'title' => 'SuspendedProd', 'status' => 'suspended', 'stock' => 5]);
         $empty = Product::factory()->create(['seller_id' => $seller->user_id, 'title' => 'EmptyProd', 'status' => 'active', 'stock' => 0]);

         
         $this->actingAs($seller)
              ->get(route('shop.products.index', ['tab' => 'active']))
              ->assertOk()
              ->assertSee('ActiveProd')
              ->assertDontSee('DraftProd');

         
         $this->actingAs($seller)
              ->get(route('shop.products.index', ['tab' => 'draft']))
              ->assertOk()
              ->assertSee('DraftProd');
              
         
         $this->actingAs($seller)
              ->get(route('shop.products.index', ['tab' => 'suspended']))
              ->assertOk()
              ->assertSee('SuspendedProd');
              
         
         $this->actingAs($seller)
              ->get(route('shop.products.index', ['tab' => 'empty']))
              ->assertOk()
              ->assertSee('EmptyProd');
    }

    public function test_order_filters_logic()
    {
        $seller = $this->createSeller();
        $buyer = User::factory()->create();
        
        $pending = Order::factory()->create(['seller_id' => $seller->user_id, 'status' => 'pending', 'code' => 'ORD-PENDING']);
        $completed = Order::factory()->create(['seller_id' => $seller->user_id, 'status' => 'completed', 'code' => 'ORD-COMPLETED']);
        
        
        $this->actingAs($seller)
             ->get(route('shop.orders', ['tab' => 'pending']))
             ->assertOk()
             ->assertSee('ORD-PENDING')
             ->assertDontSee('ORD-COMPLETED');
             
        
        $this->actingAs($seller)
             ->get(route('shop.orders', ['tab' => 'completed']))
             ->assertOk()
             ->assertSee('ORD-COMPLETED');
             
        
        $this->actingAs($seller)
             ->get(route('shop.orders', ['q' => 'ORD-PENDING']))
             ->assertOk()
             ->assertSee('ORD-PENDING');
    }
    
    public function test_payout_search_filter()
    {
        $seller = $this->createSeller();
        $bank = \App\Models\BankAccount::factory()->create(['user_id' => $seller->user_id, 'bank_name' => 'BCA UTAMA']);
        $payout1 = \App\Models\PayoutRequest::factory()->create(['seller_id' => $seller->user_id, 'amount' => 50000, 'bank_account_id' => $bank->bank_account_id, 'requested_at' => now(), 'processed_at' => now()]);
        $payout2 = \App\Models\PayoutRequest::factory()->create(['seller_id' => $seller->user_id, 'amount' => 100000, 'requested_at' => now(), 'processed_at' => now()]);
        
        
        $this->actingAs($seller)
             ->get(route('shop.payouts', ['q' => 'BCA']))
             ->assertOk()
             ->assertSee('BCA UTAMA');
    }
    
    public function test_check_bank_deletion_logic()
    {
        $seller = $this->createSeller();
        $bank = \App\Models\BankAccount::factory()->create(['user_id' => $seller->user_id]);
        
        
        \App\Models\PayoutRequest::factory()->create([
            'seller_id' => $seller->user_id,
            'bank_account_id' => $bank->bank_account_id,
            'status' => 'requested'
        ]);
        
        $response = $this->actingAs($seller)
             ->getJson(route('shop.banks.check_deletion', $bank->bank_account_id));
             
        $response->assertOk()
                 ->assertJson(['pending_count' => 1]);
    }
    
    protected function createSeller()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        Address::factory()->create([
            'user_id' => $seller->user_id,
            'is_shop_default' => true,
            'is_default' => true
        ]);
        return $seller;
    }

    

    public function test_reports_accessible()
    {
        $response = $this->actingAs($this->seller)->get(route('shop.reports'));
        $response->assertOk();
        $response->assertViewHas('dailySales');
    }

    public function test_reports_redirect_if_no_address()
    {
        $newSeller = User::factory()->create(['role' => 'seller']); 
        
        
        $response = $this->actingAs($newSeller)->get(route('shop.reports'));
        $response->assertRedirect(route('shop.address.index'));
        $response->assertSessionHas('error');
    }

    public function test_reports_date_filtering()
    {
        Order::factory()->create([
            'seller_id' => $this->seller->user_id, 
            'status' => 'completed',
            'created_at' => now()->subYear(),
            'subtotal_amount' => 100000
        ]);
        
        $currentOrder = Order::factory()->create([
            'seller_id' => $this->seller->user_id, 
            'status' => 'completed',
            'created_at' => now(),
            'subtotal_amount' => 50000
        ]);

        $response = $this->actingAs($this->seller)->get(route('shop.reports', [
            'month' => now()->format('m'),
            'year' => now()->format('Y')
        ]));
        
        $response->assertOk();
        $response->assertViewHas('totalRevenue', 50000); 
    }

    

    public function test_address_create_view_accessible()
    {
        $response = $this->actingAs($this->seller)->get(route('shop.address.create'));
        $response->assertOk();
    }

    public function test_address_set_default_logic()
    {
        $address2 = Address::factory()->create(['user_id' => $this->seller->user_id, 'is_shop_default' => false]);
        
        $response = $this->actingAs($this->seller)->patch(route('shop.address.setdefault', $address2->address_id));
        
        $response->assertRedirect();
        $this->assertTrue((bool)$address2->fresh()->is_shop_default);
    }

    public function test_address_edit_update_logic()
    {
        $address = Address::factory()->create(['user_id' => $this->seller->user_id]);
        
        
        $response = $this->actingAs($this->seller)->get(route('shop.address.edit', $address->address_id));
        $response->assertOk();

        
        $data = [
            'label' => 'Updated Label',
            'recipient' => 'Updated Recipient',
            'phone' => '089999',
            'city' => 'New City',
            'province' => 'New Prov',
            'district' => 'New Dist',
            'village' => 'New Vill',
            'postal_code' => '12345',
            'detail_address' => 'New Detail',
            'is_shop_default' => '1' 
        ];

        $response = $this->actingAs($this->seller)->put(route('shop.address.update', $address->address_id), $data);
        
        $response->assertRedirect(route('shop.address.index'));
        $this->assertEquals('Updated Label', $address->fresh()->label);
        $this->assertTrue((bool)$address->fresh()->is_shop_default);
    }

    public function test_address_delete_logic()
    {
        $address = Address::factory()->create(['user_id' => $this->seller->user_id, 'is_shop_default' => false]);
        
        $response = $this->actingAs($this->seller)->delete(route('shop.address.delete', $address->address_id));
        
        $response->assertRedirect();
        $this->assertNull(Address::find($address->address_id));
    }

    

    public function test_bank_update_logic()
    {
        $bank = BankAccount::factory()->create(['user_id' => $this->seller->user_id]);
        
        $response = $this->actingAs($this->seller)->put(route('shop.banks.update', $bank->bank_account_id), [
            'bank_name' => 'UPDATED BANK',
            'account_no' => '999',
            'account_name' => 'UPDATED NAME'
        ]);
        
        $response->assertRedirect();
        $this->assertEquals('UPDATED BANK', $bank->fresh()->bank_name);
    }

    public function test_bank_delete_with_transfer()
    {
        $bank1 = BankAccount::factory()->create(['user_id' => $this->seller->user_id]);
        $bank2 = BankAccount::factory()->create(['user_id' => $this->seller->user_id]);
        
        $payout = PayoutRequest::factory()->create([
            'seller_id' => $this->seller->user_id,
            'bank_account_id' => $bank1->bank_account_id,
            'status' => 'requested'
        ]);
        
        
        $response = $this->actingAs($this->seller)->delete(route('shop.banks.delete', $bank1->bank_account_id), [
            'transfer_to' => $bank2->bank_account_id
        ]);
        
        $response->assertRedirect();
        $this->assertNull(BankAccount::find($bank1->bank_account_id));
        $this->assertEquals($bank2->bank_account_id, $payout->fresh()->bank_account_id);
    }

    

    public function test_update_order_status_unauthorized()
    {
        $order = Order::factory()->create(['seller_id' => $this->seller->user_id]);
        $stranger = User::factory()->create(); 
        
        $response = $this->actingAs($stranger)->patch(route('shop.orders.update_status', $order->order_id), ['status' => 'processed']);
        
        $response->assertForbidden(); 
    }

    public function test_admin_can_update_status()
    {
        $order = Order::factory()->create(['seller_id' => $this->seller->user_id]);
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->patch(route('shop.orders.update_status', $order->order_id), ['status' => 'processed']);
        
        $this->assertEquals('shipped', $order->fresh()->status);
    }

    public function test_update_order_status_branches()
    {
        $order = Order::factory()->create(['seller_id' => $this->seller->user_id, 'status' => 'processed']);
        
        
        $this->actingAs($this->seller)->patch(route('shop.orders.update_status', $order->order_id), ['status' => 'delivered']);
        $this->assertEquals('delivered', $order->fresh()->status);
        
        
        $this->actingAs($this->seller)->patch(route('shop.orders.update_status', $order->order_id), ['status' => 'paid']);
        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals('settlement', $order->fresh()->payment_status);
    }

    
    
    public function test_product_store_ajax_response()
    {
        $response = $this->actingAs($this->seller)->postJson(route('shop.products.store'), [
            'title' => 'Ajax Product',
            'category_id' => $this->category->category_id,
            'price' => '10000',
            'weight' => '1000',
            'stock' => '5',
            'condition' => 'new',
            'description' => 'Desc',
            'status_input' => 'active',
            'images' => [UploadedFile::fake()->image('prod.jpg')]
        ]);
        
        $response->assertOk()
                 ->assertJson(['status' => 'success', 'message' => 'Produk berhasil diterbitkan!']);
    }

    public function test_product_validation_rules()
    {
        
        $response = $this->actingAs($this->seller)->post(route('shop.products.store'), [
            'title' => 'Invalid Price',
            'category_id' => $this->category->category_id,
            'price' => '10500', 
            'weight' => '1000',
            'stock' => '1',
            'condition' => 'new',
            'description' => 'Desc',
            'status_input' => 'active',
            'images' => [UploadedFile::fake()->image('prod.jpg')]
        ]);
        $response->assertSessionHasErrors('price');

        
        $response = $this->actingAs($this->seller)->post(route('shop.products.store'), [
            'title' => 'Invalid Weight',
            'category_id' => $this->category->category_id,
            'price' => '10000',
            'weight' => '1200', 
            'stock' => '1',
            'condition' => 'new',
            'description' => 'Desc',
            'status_input' => 'active',
            'images' => [UploadedFile::fake()->image('prod.jpg')]
        ]);
        $response->assertSessionHasErrors('weight');
    }

    public function test_product_image_upload_logic()
    {
        Storage::fake('public');
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id]);
        $image1 = UploadedFile::fake()->image('img1.jpg');
        $image2 = UploadedFile::fake()->image('img2.jpg');
        
        $response = $this->actingAs($this->seller)
            ->withoutExceptionHandling()
            ->put(route('shop.products.update', $product), [
            'title' => $product->title,
            'category_id' => $this->category->category_id,
            'price' => '10000',
            'weight' => '1000',
            'stock' => '10',
            'condition' => 'new',
            'description' => 'Desc',
            'status_input' => 'active',
            'images' => [$image1, $image2]
        ]);
        
        
        $this->assertEquals(2, $product->images()->count());
        
        $this->assertNotNull($product->fresh()->main_image);
        $this->assertTrue((bool)$product->images()->first()->is_primary);
    }
    
    public function test_check_product_deletion()
    {
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id]);
        
        
        $response = $this->actingAs($this->seller)
            ->withoutExceptionHandling()
            ->getJson(route('shop.products.check_deletion', $product));
        $response->assertOk()->assertJson(['message' => 'Aman untuk dihapus.']);
        
        
        $order = Order::factory()->create(['seller_id' => $this->seller->user_id, 'status' => 'pending']);
        \App\Models\OrderItem::factory()->create(['order_id' => $order->order_id, 'product_id' => $product->product_id]);
        
        $response = $this->actingAs($this->seller)->getJson(route('shop.products.check_deletion', $product));
        $response->assertOk();
        $this->assertStringContainsString('Terdapat 1 transaksi berjalan', $response->json('message'));
    }

    public function test_delete_product_image()
    {
        Storage::fake('public');
        $product = Product::factory()->create(['seller_id' => $this->seller->user_id]);
        $image = ProductImage::create([
            'product_id' => $product->product_id,
            'url' => 'test.jpg',
            'is_primary' => false,
            'sort_order' => 1
        ]);
        
        $response = $this->actingAs($this->seller)->deleteJson(route('shop.products.image.delete', $image->product_image_id));
        
        $response->assertOk()->assertJson(['status' => 'success']);
        $this->assertNull(ProductImage::find($image->product_image_id));
    }
}


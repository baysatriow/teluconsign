<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| 1. GUEST & PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// --- Public View ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/shop/{id}', [ShopController::class, 'show'])->name('shop.show');

// --- Authentication (Guest Only) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // OTP Verification
    Route::get('/otp-verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('/otp-verify', [OtpController::class, 'verify'])->name('otp.verify.submit');
    Route::post('/otp-resend', [OtpController::class, 'resend'])->name('otp.resend');

    // Forgot Password Flow
    Route::prefix('forgot-password')->name('password.')->group(function () {
        Route::get('/', [ForgotPasswordController::class, 'showSearchForm'])->name('request');
        Route::post('/search', [ForgotPasswordController::class, 'search'])->name('search');
        Route::get('/verify', [ForgotPasswordController::class, 'showVerifyForm'])->name('verify.show');
        Route::post('/verify', [ForgotPasswordController::class, 'verify'])->name('verify.submit');
        Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('reset');
        Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('update');
    });
});

/*
|--------------------------------------------------------------------------
| 2. AUTHENTICATED ROUTES (Verified OTP)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified_otp'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- User Profile & Settings ---
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        
        // Address Management
        Route::post('/address', [ProfileController::class, 'addAddress'])->name('address.add');
        Route::put('/address/{id}', [ProfileController::class, 'updateAddress'])->name('address.update');
        Route::delete('/address/{id}', [ProfileController::class, 'deleteAddress'])->name('address.delete');
        Route::patch('/address/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('address.default');

        // Bank Account Management
        Route::post('/bank', [ProfileController::class, 'addBank'])->name('bank.add');
        Route::put('/bank/{id}', [ProfileController::class, 'updateBank'])->name('bank.update');
        Route::delete('/bank/{id}', [ProfileController::class, 'deleteBank'])->name('bank.delete');

        // Phone Update via OTP
        Route::post('/phone/request', [ProfileController::class, 'requestPhoneUpdate'])->name('phone.request');
        Route::post('/phone/verify', [ProfileController::class, 'verifyPhoneUpdate'])->name('phone.verify');
    });

    // --- Shopping Cart ---
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'addToCart'])->name('add');
        Route::post('/update/{itemId}', [CartController::class, 'updateItem'])->name('update');
        Route::delete('/item/{itemId}', [CartController::class, 'deleteItem'])->name('deleteItem');
        Route::delete('/store/{sellerId}', [CartController::class, 'deleteStoreItems'])->name('deleteStore');
    });

    // --- Order & Transaction ---
    Route::post('/product/{product}/buy', [ProductController::class, 'buyNow'])->name('product.buy');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrdersController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/pay', [OrdersController::class, 'pay'])->name('orders.pay');

    // --- Checkout Flow ---
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/check-shipping', [CheckoutController::class, 'checkShippingCost'])->name('check_shipping');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    });

    // --- Custom Payment Page ---
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/charge', [PaymentController::class, 'createCharge'])->name('charge');
        Route::get('/{payment}/status', [PaymentController::class, 'checkStatus'])->name('status');
        Route::post('/{payment}/demo', [PaymentController::class, 'demoPayment'])->name('demo');
    });

    // --- Utilities ---
    Route::get('/location/search', [LocationController::class, 'search'])->name('location.search');

    /*
    |----------------------------------------------------------------------
    | 3. SELLER / SHOP ROUTES
    |----------------------------------------------------------------------
    */
    Route::prefix('myshop')->name('shop.')->group(function () {
        Route::get('/', [ShopController::class, 'index'])->name('index');
        Route::post('/register', [ShopController::class, 'registerStore'])->name('shop.register');

        // Shop Address Management
        Route::prefix('address')->name('address.')->group(function () {
            Route::get('/', [ShopController::class, 'addressIndex'])->name('index');
            Route::get('/create', [ShopController::class, 'addressCreate'])->name('create');
            Route::post('/', [ShopController::class, 'addressStore'])->name('store');
            Route::get('/{id}/edit', [ShopController::class, 'addressEdit'])->name('edit');
            Route::put('/{id}', [ShopController::class, 'addressUpdate'])->name('update');
            Route::delete('/{id}', [ShopController::class, 'addressDestroy'])->name('delete');
            Route::patch('/{id}/default', [ShopController::class, 'addressSetDefault'])->name('setdefault');
        });

        // Product Management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ShopController::class, 'products'])->name('index');
            Route::get('/create', [ShopController::class, 'createProduct'])->name('create');
            Route::post('/', [ShopController::class, 'storeProduct'])->name('store');
            Route::get('/{product}/edit', [ShopController::class, 'editProduct'])->name('edit');
            Route::put('/{product}', [ShopController::class, 'updateProduct'])->name('update');
            Route::get('/{product}/check-deletion', [ShopController::class, 'checkProductDeletion'])->name('check_deletion');
            Route::delete('/{id}', [ShopController::class, 'deleteProduct'])->name('delete');
            Route::delete('/image/{product}', [ShopController::class, 'deleteProductImage'])->name('image.delete');
        });

        // Order Management
        Route::get('/orders', [ShopController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [ShopController::class, 'orderDetail'])->name('orders.show');
        Route::patch('/orders/{order}/status', [ShopController::class, 'updateOrderStatus'])->name('orders.update_status');

        // Finance & Reports
        Route::get('/reports', [ShopController::class, 'reports'])->name('reports');
        Route::get('/payouts', [ShopController::class, 'payouts'])->name('payouts');
        Route::post('/payouts', [ShopController::class, 'storePayout'])->name('payouts.store');
        
        // Shop Bank Management
        Route::prefix('banks')->name('banks.')->group(function () {
            Route::post('/', [ShopController::class, 'storeBank'])->name('store');
            Route::put('/{id}', [ShopController::class, 'updateBank'])->name('update');
            Route::delete('/{id}', [ShopController::class, 'deleteBank'])->name('delete');
            Route::get('/{id}/check', [ShopController::class, 'checkBankDeletion'])->name('check_deletion');
        });
    });

    /*
    |----------------------------------------------------------------------
    | 4. ADMIN ROUTES
    |----------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Product Moderation
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [AdminController::class, 'products'])->name('index'); // Changed from 'products' for consistency
            Route::get('/{id}', [AdminController::class, 'showProduct'])->name('show');
            Route::patch('/{id}/toggle-status', [AdminController::class, 'toggleProductStatus'])->name('toggle_status');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::get('/create', [AdminController::class, 'usersCreate'])->name('create');
            Route::post('/', [AdminController::class, 'storeAdmin'])->name('store_admin');
            Route::get('/{id}', [AdminController::class, 'usersShow'])->name('show');
            Route::get('/{id}/edit', [AdminController::class, 'usersEdit'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'updateAdmin'])->name('update_admin');
            Route::delete('/{id}', [AdminController::class, 'destroyUser'])->name('destroy');
            Route::patch('/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('toggle_status');
            Route::post('/{id}/reset-link', [AdminController::class, 'sendResetLink'])->name('send_reset_link');
        });

        // Category & Payouts
        Route::get('/categories/check/{id}', [AdminCategoryController::class, 'checkDeletion'])->name('categories.check');
        Route::resource('categories', AdminCategoryController::class);
        
        Route::get('/payouts', [AdminController::class, 'payouts'])->name('payouts');
        Route::patch('/payouts/{id}', [AdminController::class, 'updatePayoutStatus'])->name('payouts.update');

        // Integration Settings
        Route::prefix('integrations')->name('integrations.')->group(function () {
            // Payment
            Route::get('/payment', [AdminController::class, 'paymentGateway'])->name('payment');
            Route::patch('/payment-gateway', [AdminController::class, 'updatePaymentGateway'])->name('payment.update');
            Route::get('/payment/test-token', [AdminController::class, 'getPaymentTestToken'])->name('payment.test-token');

            // Logistics
            Route::get('/logistics', [AdminController::class, 'shipping'])->name('shipping');
            Route::post('/shipping', [AdminController::class, 'updateShippingApi'])->name('shipping.update');
            Route::post('/shipping-carrier', [AdminController::class, 'storeCarrier'])->name('carrier.store');
            Route::put('/shipping-carrier/{id}', [AdminController::class, 'updateCarrier'])->name('carrier.update');
            Route::patch('/shipping-carrier/{id}/toggle', [AdminController::class, 'toggleCarrierStatus'])->name('carrier.toggle');
            Route::delete('/shipping-carrier/{id}', [AdminController::class, 'deleteCarrier'])->name('carrier.delete');
            Route::post('/shipping/test-cost', [AdminController::class, 'checkShippingCostTest'])->name('shipping.test-cost');

            // WhatsApp & Logs
            Route::get('/whatsapp', [AdminController::class, 'whatsapp'])->name('whatsapp');
            Route::post('/whatsapp', [AdminController::class, 'updateWhatsappApi'])->name('whatsapp.update');
            Route::post('/whatsapp/test-send', [AdminController::class, 'sendTestWhatsapp'])->name('whatsapp.test-send');
            Route::get('/webhook-logs', [AdminController::class, 'webhookLogs'])->name('webhook-logs');
        });
    });
});

/*
|--------------------------------------------------------------------------
| 5. WEBHOOKS & FALLBACK
|--------------------------------------------------------------------------
*/

Route::post('/webhook/midtrans', [WebhookController::class, 'midtransNotification'])->name('webhook.midtrans');

Route::fallback(function () {
    abort(404);
});
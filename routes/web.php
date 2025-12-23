<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminCategoryController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==============================================================================
// 1. PUBLIC ROUTES (Bisa diakses siapa saja)
// ==============================================================================

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/shop/{id}', [ShopController::class, 'show'])->name('shop.show');

// ==============================================================================
// 2. GUEST ROUTES (Hanya untuk yang BELUM login)
// ==============================================================================

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // OTP (Sekarang OTP diakses sebagai guest karena user belum Auth::login)
    Route::get('/otp-verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('/otp-verify', [OtpController::class, 'verify'])->name('otp.verify.submit');
    Route::post('/otp-resend', [OtpController::class, 'resend'])->name('otp.resend');

    // --- LUPA PASSWORD ---
    // 1. Cari Akun
    Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showSearchForm'])->name('password.request');
    Route::post('/forgot-password/search', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'search'])->name('password.search');

    // 2. Verifikasi Nomor HP
    Route::get('/forgot-password/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify.show');
    Route::post('/forgot-password/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verify'])->name('password.verify.submit');

    // 3. Reset Password (Link dari WA)
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.update');

});

// ==============================================================================
// 3. AUTHENTICATED ROUTES (Harus Login & Verified)
// ==============================================================================

Route::middleware(['auth', 'verified_otp'])->group(function () {

    // --- LOGOUT ---
    // Logout dikecualikan dari verified_otp di middleware class, tapi aman ditaruh disini
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- FITUR KERANJANG & BELI ---
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update/{itemId}', [CartController::class, 'updateItem'])->name('cart.update');
    Route::delete('/cart/item/{itemId}', [CartController::class, 'deleteItem'])->name('cart.deleteItem');
    Route::delete('/cart/store/{sellerId}', [CartController::class, 'deleteStoreItems'])->name('cart.deleteStore');
    Route::post('/product/{product}/buy', [ProductController::class, 'buyNow'])->name('product.buy');
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // --- FITUR PROFIL (USER BIASA) ---
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');


    // --- FITUR AUTOCOMPLETE LOKASI ---
    Route::get('/location/search', [App\Http\Controllers\LocationController::class, 'search'])->name('location.search');

    // Kelola Alamat (CRUD)
    Route::post('/profile/address', [ProfileController::class, 'addAddress'])->name('profile.address.add');
    Route::put('/profile/address/{id}', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
    Route::delete('/profile/address/{id}', [ProfileController::class, 'deleteAddress'])->name('profile.address.delete');
    Route::patch('/profile/address/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.address.default');

    // Kelola Bank (CRUD)
    Route::post('/profile/bank', [ProfileController::class, 'addBank'])->name('profile.bank.add');
    Route::put('/profile/bank/{id}', [ProfileController::class, 'updateBank'])->name('profile.bank.update');
    Route::delete('/profile/bank/{id}', [ProfileController::class, 'deleteBank'])->name('profile.bank.delete');
    
    // Update Phone Number (OTP)
    Route::post('/profile/phone/request', [ProfileController::class, 'requestPhoneUpdate'])->name('profile.phone.request');
    Route::post('/profile/phone/verify', [ProfileController::class, 'verifyPhoneUpdate'])->name('profile.phone.verify');

    // --- FITUR TOKO (PENJUAL) ---
    // 1. Dashboard & Core
    Route::get('/myshop', [ShopController::class, 'index'])->name('shop.index');
    Route::post('/myshop/register', [ShopController::class, 'registerStore'])->name('shop.register');

    // Alamat Toko
    Route::get('/myshop/address', [ShopController::class, 'addressIndex'])->name('shop.address.index');
    Route::get('/myshop/address/create', [ShopController::class, 'addressCreate'])->name('shop.address.create'); // NEW
    Route::post('/myshop/address', [ShopController::class, 'addressStore'])->name('shop.address.store'); // NEW
    Route::get('/myshop/address/{id}/edit', [ShopController::class, 'addressEdit'])->name('shop.address.edit'); // NEW
    Route::put('/myshop/address/{id}', [ShopController::class, 'addressUpdate'])->name('shop.address.update'); // NEW
    Route::delete('/myshop/address/{id}', [ShopController::class, 'addressDestroy'])->name('shop.address.delete'); // NEW
    Route::patch('/myshop/address/{id}/default', [ShopController::class, 'addressSetDefault'])->name('shop.address.setdefault');

    // 2. Produk (CRUD & List)
    Route::get('/myshop/products', [ShopController::class, 'products'])->name('shop.products.index');
    Route::get('/myshop/products/create', [ShopController::class, 'createProduct'])->name('shop.products.create');
    Route::post('/myshop/products', [ShopController::class, 'storeProduct'])->name('shop.products.store');
    Route::get('/myshop/products/{product}/edit', [ShopController::class, 'editProduct'])->name('shop.products.edit');
    Route::put('/myshop/products/{product}', [ShopController::class, 'updateProduct'])->name('shop.products.update');
    Route::get('/myshop/products/{product}/check-deletion', [ShopController::class, 'checkProductDeletion'])->name('shop.products.check_deletion');
    Route::delete('/myshop/products/{id}', [ShopController::class, 'deleteProduct'])->name('shop.products.delete');
    Route::delete('/myshop/products/image/{product}', [ShopController::class, 'deleteProductImage'])->name('shop.products.image.delete');

    // 3. Pesanan
    Route::get('/myshop/orders', [ShopController::class, 'orders'])->name('shop.orders');
    Route::get('/myshop/orders/{order}', [ShopController::class, 'orderDetail'])->name('shop.orders.show');
    Route::patch('/myshop/orders/{order}/status', [ShopController::class, 'updateOrderStatus'])->name('shop.orders.update_status');

    // 4. Laporan
    Route::get('/myshop/reports', [ShopController::class, 'reports'])->name('shop.reports');

    // 5. Saldo & Penarikan
    Route::get('/myshop/payouts', [ShopController::class, 'payouts'])->name('shop.payouts');
    Route::post('/myshop/payouts', [ShopController::class, 'storePayout'])->name('shop.payouts.store');
    Route::post('/myshop/banks', [ShopController::class, 'storeBank'])->name('shop.banks.store');
    Route::put('/myshop/banks/{id}', [ShopController::class, 'updateBank'])->name('shop.banks.update');
    Route::delete('/myshop/banks/{id}', [ShopController::class, 'deleteBank'])->name('shop.banks.delete');
    Route::get('/myshop/banks/{id}/check', [ShopController::class, 'checkBankDeletion'])->name('shop.banks.check_deletion');

    // Halaman Orders Pembeli
    Route::get('/orders', [App\Http\Controllers\OrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\OrdersController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/pay', [App\Http\Controllers\OrdersController::class, 'pay'])->name('orders.pay');
    // --- CHECKOUT ---
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/check-shipping', [CheckoutController::class, 'checkShippingCost'])->name('checkout.check_shipping'); // NEW ROUTE
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // --- PAYMENT (Custom Payment Page) ---
    Route::get('/payment/{payment}', [App\Http\Controllers\PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{payment}/charge', [App\Http\Controllers\PaymentController::class, 'createCharge'])->name('payment.charge');
    Route::get('/payment/{payment}/status', [App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('payment.status');
    Route::post('/payment/{payment}/demo', [App\Http\Controllers\PaymentController::class, 'demoPayment'])->name('payment.demo');

    // --- ADMIN ROUTES (Nested Middleware: auth + verified_otp + admin) ---
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Manajemen Produk
        Route::get('/products', [AdminController::class, 'products'])->name('products');
        Route::get('/products/{id}', [AdminController::class, 'showProduct'])->name('products.show');
        Route::patch('/products/{id}/toggle-status', [AdminController::class, 'toggleProductStatus'])->name('products.toggle_status');

        // Manajemen User
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('users.create'); // New Create Page must be before {id}
        Route::get('/users/{id}/edit', [AdminController::class, 'usersEdit'])->name('users.edit'); // New Edit Page
        Route::get('/users/{id}', [AdminController::class, 'usersShow'])->name('users.show');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy'); // Delete User
        Route::post('/users', [AdminController::class, 'storeAdmin'])->name('users.store_admin');
        Route::put('/users/{id}', [AdminController::class, 'updateAdmin'])->name('users.update_admin'); // Update Admin Data
        Route::patch('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle_status');
        Route::patch('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle_status');
        Route::post('/users/{id}/reset-link', [AdminController::class, 'sendResetLink'])->name('users.send_reset_link'); // Reset Link Action

        // Manajemen Kategori
        Route::get('/categories/check/{id}', [AdminCategoryController::class, 'checkDeletion'])->name('categories.check');
        Route::resource('categories', AdminCategoryController::class);

        // Manajemen Payouts
        Route::get('/payouts', [AdminController::class, 'payouts'])->name('payouts');
        Route::patch('/payouts/{id}', [AdminController::class, 'updatePayoutStatus'])->name('payouts.update');

        // --- INTEGRASI & SETTINGS ---
    // --- INTEGRASI & SETTINGS ---
    
    // 1. Payment Gateway
    Route::get('/integrations/payment', [AdminController::class, 'paymentGateway'])->name('integrations.payment');
    Route::patch('/integrations/payment-gateway', [AdminController::class, 'updatePaymentGateway'])->name('integrations.payment.update');

    // Test Routes
    Route::get('/integrations/payment/test-token', [AdminController::class, 'getPaymentTestToken'])->name('integrations.payment.test-token'); // JSON for JS Popup

    // 2. Logistik
    Route::get('/integrations/logistics', [AdminController::class, 'shipping'])->name('integrations.shipping');
    Route::post('/integrations/shipping', [AdminController::class, 'updateShippingApi'])->name('integrations.shipping.update');
    Route::post('/integrations/shipping-carrier', [AdminController::class, 'storeCarrier'])->name('integrations.carrier.store');
    Route::put('/integrations/shipping-carrier/{id}', [AdminController::class, 'updateCarrier'])->name('integrations.carrier.update');
    Route::patch('/integrations/shipping-carrier/{id}/toggle', [AdminController::class, 'toggleCarrierStatus'])->name('integrations.carrier.toggle');
    Route::delete('/integrations/shipping-carrier/{id}', [AdminController::class, 'deleteCarrier'])->name('integrations.carrier.delete');
    Route::post('/integrations/shipping/test-cost', [AdminController::class, 'checkShippingCostTest'])->name('integrations.shipping.test-cost');

    // 3. WhatsApp
    Route::get('/integrations/whatsapp', [AdminController::class, 'whatsapp'])->name('integrations.whatsapp');
    Route::post('/integrations/whatsapp', [AdminController::class, 'updateWhatsappApi'])->name('integrations.whatsapp.update');
    Route::post('/integrations/whatsapp/test-send', [AdminController::class, 'sendTestWhatsapp'])->name('integrations.whatsapp.test-send');
    
    // 4. Webhook Logs
    Route::get('/integrations/webhook-logs', [AdminController::class, 'webhookLogs'])->name('integrations.webhook-logs');
    });
});

// ==============================================================================
// WEBHOOK ENDPOINTS (No Auth - Called by external services)
// ==============================================================================

Route::post('/webhook/midtrans', [App\Http\Controllers\WebhookController::class, 'midtransNotification'])->name('webhook.midtrans');

// Fallback jika url ngawur
Route::fallback(function () {
    abort(404);
});

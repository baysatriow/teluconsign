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
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

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
    Route::post('/product/{id}/buy', [ProductController::class, 'buyNow'])->name('product.buy');

    // --- FITUR PROFIL (USER BIASA) ---
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Kelola Alamat (CRUD)
    Route::post('/profile/address', [ProfileController::class, 'addAddress'])->name('profile.address.add');
    Route::put('/profile/address/{id}', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
    Route::delete('/profile/address/{id}', [ProfileController::class, 'deleteAddress'])->name('profile.address.delete');
    Route::patch('/profile/address/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.address.default');

    // Kelola Bank (CRUD)
    Route::post('/profile/bank', [ProfileController::class, 'addBank'])->name('profile.bank.add');
    Route::put('/profile/bank/{id}', [ProfileController::class, 'updateBank'])->name('profile.bank.update');
    Route::delete('/profile/bank/{id}', [ProfileController::class, 'deleteBank'])->name('profile.bank.delete');

    // --- FITUR TOKO (PENJUAL) ---
    Route::get('/myshop', [ShopController::class, 'index'])->name('shop.index');
    Route::post('/myshop/register', [ShopController::class, 'registerStore'])->name('shop.register'); // Daftar jadi seller

    // Kelola Produk (CRUD)
    Route::get('/myshop/products/create', [ShopController::class, 'createProduct'])->name('shop.products.create');
    Route::post('/myshop/products', [ShopController::class, 'storeProduct'])->name('shop.products.store');
    Route::get('/myshop/products/{id}/edit', [ShopController::class, 'editProduct'])->name('shop.products.edit');
    Route::put('/myshop/products/{id}', [ShopController::class, 'updateProduct'])->name('shop.products.update');
    Route::delete('/myshop/products/{id}', [ShopController::class, 'deleteProduct'])->name('shop.products.delete');
    Route::delete('/myshop/products/image/{id}', [ShopController::class, 'deleteProductImage'])->name('shop.products.image.delete');

    // Kelola Pesanan Toko
    Route::get('/myshop/orders', [ShopController::class, 'orders'])->name('shop.orders');

    // Placeholder Halaman Orders Pembeli
    Route::get('/orders', function () {
        $orders = \App\Models\Order::where('buyer_id', auth()->id())
                    ->with(['items', 'seller'])
                    ->latest()
                    ->get();
        return view('orders.index', compact('orders'));
    })->name('orders.index');
    // --- CHECKOUT ---
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/check-shipping', [CheckoutController::class, 'checkShippingCost'])->name('checkout.check_shipping'); // NEW ROUTE
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // --- ADMIN ROUTES (Nested Middleware: auth + verified_otp + admin) ---
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Manajemen Produk
        Route::get('/products', [AdminController::class, 'products'])->name('products');
        Route::get('/products/{id}', [AdminController::class, 'showProduct'])->name('products.show');
        Route::patch('/products/{id}/toggle-status', [AdminController::class, 'toggleProductStatus'])->name('products.toggle_status');

        // Manajemen User
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeAdmin'])->name('users.store_admin');
        Route::patch('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle_status');

        // Manajemen Payouts
        Route::get('/payouts', [AdminController::class, 'payouts'])->name('payouts');
        Route::patch('/payouts/{id}', [AdminController::class, 'updatePayoutStatus'])->name('payouts.update');

        // Manajemen Integrasi
        Route::get('/integrations', [AdminController::class, 'integrations'])->name('integrations');
        Route::patch('/integrations/payment-gateway', [AdminController::class, 'updatePaymentGateway'])->name('integrations.payment.update');
        Route::post('/integrations/shipping', [AdminController::class, 'updateShippingApi'])->name('integrations.shipping.update');
        Route::post('/integrations/whatsapp', [AdminController::class, 'updateWhatsappApi'])->name('integrations.whatsapp.update');
    });
});

// Fallback jika url ngawur
Route::fallback(function () {
    return redirect()->route('home');
});

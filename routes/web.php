<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProductController;  // <-- PERBAIKAN 1: Huruf 'A' Besar
use App\Http\Controllers\CategoryController;

// ====================================================
// AREA PUBLIK
// ====================================================

Route::get('/', function () {
    return view('landing');
})->name('home');

// Auth Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// ====================================================
// AREA PROTECTED (Middleware Auth)
// ====================================================

Route::middleware(['auth'])->group(function () {

    // Dashboard & Logout
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- SETTINGS: PROFILE & PASSWORD ---
    Route::get('/profile', [SettingsController::class, 'profile'])->name('profile.index');
    Route::put('/profile', [SettingsController::class, 'update'])->name('profile.update');
    Route::put('/password', [SettingsController::class, 'updatePassword'])->name('password.update');
    Route::post('/password/check', [SettingsController::class, 'checkPassword'])->name('password.check');

    // --- SETTINGS: BANK ACCOUNT ---
    Route::get('/bank-accounts', [SettingsController::class, 'bank'])->name('bank.index');
    Route::post('/bank-accounts', [SettingsController::class, 'storeBank'])->name('bank.store');
    Route::put('/bank-accounts/{id}', [SettingsController::class, 'updateBank'])->name('bank.update');
    Route::delete('/bank-accounts/{id}', [SettingsController::class, 'destroyBank'])->name('bank.destroy');
    Route::get('/bank-accounts/default/{id}', [SettingsController::class, 'setDefaultBank'])->name('bank.setDefault');

    // --- SETTINGS: ADDRESS ---
    Route::get('/addresses', [SettingsController::class, 'address'])->name('address.index');
    Route::post('/addresses', [SettingsController::class, 'storeAddress'])->name('address.store');
    Route::put('/addresses/{id}', [SettingsController::class, 'updateAddress'])->name('address.update');
    Route::delete('/addresses/{id}', [SettingsController::class, 'destroyAddress'])->name('address.destroy');
    Route::get('/addresses/default/{id}', [SettingsController::class, 'setDefaultAddress'])->name('address.setDefault');

    // --- MANAJEMEN PRODUK ---
    // PERBAIKAN 2: Hapus tanda slash '/' di depan 'products'
    Route::resource('products', ProductController::class);

    // Route custom untuk update status produk (diluar resource standar)
    Route::post('/products/{product}/status', [ProductController::class, 'changeStatus'])->name('products.changeStatus');

    // --- MANAJEMEN KATEGORI ---
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit', 'update']);
    Route::get('categories/all', [CategoryController::class, 'allCategories'])->name('categories.all');
    Route::post('categories/quick', [CategoryController::class, 'store'])->name('categories.quickStore');

});

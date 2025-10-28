<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Halaman publik utama
Route::get('/', function () {
    return view('landing');
})->name('home');

// Register
Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->name('register.form');
Route::post('/register', [AuthController::class, 'register'])
    ->name('register.submit');

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login.form');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

// Dashboard (hanya login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KudaController;
use App\Http\Controllers\PeternakanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KawinSilangController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\LisensiController;
use App\Http\Controllers\UserController;

// Landing Page sebelum login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('landing');
})->name('landing');

// Auth (tidak perlu login)
Route::middleware('guest')->group(function () {
    Route::get('/testapi', [AuthController::class, 'testApi']);

    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman yang butuh login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Marketplace dipisah dari data kuda agar data kuda tetap menjadi data master
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/marketplace/terjual', [MarketplaceController::class, 'terjual'])->name('marketplace.terjual');

    // Resource route
    Route::resource('kuda', KudaController::class);
    Route::resource('peternakan', PeternakanController::class);
    Route::resource('transaksi', TransaksiController::class);
    Route::resource('kawin-silang', KawinSilangController::class);
    Route::resource('lisensi', LisensiController::class);
    Route::resource('users', UserController::class);

    // Lisensi
    Route::post('/lisensi/{id}/approve', [LisensiController::class, 'approve'])->name('lisensi.approve');
    Route::post('/lisensi/{id}/decline', [LisensiController::class, 'decline'])->name('lisensi.decline');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KudaController;
use App\Http\Controllers\PeternakanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KawinSilangController;
use App\Http\Controllers\LisensiController;
use App\Http\Controllers\UserController;

// -------------------------------------------------------
// Auth (tidak perlu login)
// -------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------------------------------------------
// Halaman yang butuh login
// -------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile',   fn() => view('admin.dashboard'))->name('profile');

    Route::resource('kuda',         KudaController::class);
    Route::resource('peternakan',   PeternakanController::class);
    Route::resource('transaksi',    TransaksiController::class);
    Route::resource('kawin-silang', KawinSilangController::class);
    Route::resource('lisensi',      LisensiController::class);
    Route::resource('users',        UserController::class);
});

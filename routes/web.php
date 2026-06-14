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

    // Custom route kuda harus di atas resource
    Route::get('/kuda/tersedia', [KudaController::class, 'tersedia'])->name('kuda.tersedia');
    Route::get('/kuda/terjual', [KudaController::class, 'terjual'])->name('kuda.terjual');
    Route::get('/kuda/breeding', [KudaController::class, 'breeding'])->name('kuda.breeding');

    // Resource route
    Route::resource('kuda', KudaController::class);
    Route::resource('peternakan', PeternakanController::class);
    Route::resource('transaksi', TransaksiController::class);
    Route::resource('kawin-silang', KawinSilangController::class);
    Route::resource('lisensi', LisensiController::class);
    Route::resource('users', UserController::class);

    // Route untuk approve/decline lisensi (hanya admin)
    Route::post('/lisensi/{id}/approve', [LisensiController::class, 'approve'])->name('lisensi.approve');
    Route::post('/lisensi/{id}/decline', [LisensiController::class, 'decline'])->name('lisensi.decline');
});
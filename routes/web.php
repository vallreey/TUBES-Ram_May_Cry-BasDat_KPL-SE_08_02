<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KudaController;
use App\Http\Controllers\PeternakanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KawinSilangController;
use App\Http\Controllers\LisensiController;
use App\Http\Controllers\UserController;

// Redirect root ke dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Resource routes (CRUD otomatis: index, create, store, show, edit, update, destroy)
Route::resource('kuda',         KudaController::class);
Route::resource('peternakan',   PeternakanController::class);
Route::resource('transaksi',    TransaksiController::class);
Route::resource('kawin-silang', KawinSilangController::class);
Route::resource('lisensi',      LisensiController::class);
Route::resource('users',        UserController::class);

// Profile
Route::get('/profile', fn() => view('admin.dashboard'))->name('profile');

// Auth placeholder
Route::get('/login',   fn() => view('welcome'))->name('login');
Route::post('/logout', fn() => redirect('/'))->name('logout');

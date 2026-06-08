<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\KudaApiController;
use App\Http\Controllers\Api\PeternakanApiController;
use App\Http\Controllers\Api\TransaksiApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;


Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Ram May Cry aktif',
        'data' => [
            'app' => config('app.name'),
            'environment' => config('app.env'),
            'timestamp' => now()->toDateTimeString(),
        ],
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
});

Route::get('/dashboard-summary', [DashboardApiController::class, 'summary']);

Route::apiResource('users', UserApiController::class);
Route::apiResource('peternakan', PeternakanApiController::class);
Route::apiResource('kuda', KudaApiController::class);
Route::apiResource('transaksi', TransaksiApiController::class);

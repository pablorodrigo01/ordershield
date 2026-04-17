<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MetricsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);

        Route::get('/{id}/analysis', [OrderController::class, 'analysis']);
        Route::get('/{id}/audit-logs', [OrderController::class, 'auditLogs']);

        Route::post('/{id}/approve', [OrderController::class, 'approve']);
        Route::post('/{id}/block', [OrderController::class, 'block']);
        Route::post('/{id}/under-review', [OrderController::class, 'underReview']);
    });

    Route::get('/metrics', [MetricsController::class, 'index']);
});

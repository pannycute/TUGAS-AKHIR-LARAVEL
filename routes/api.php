<?php

// use Illuminate\Http\Request;
use App\Http\Controllers\API\PaymentConfirmationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderItemController;
use App\Http\Controllers\API\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('paymentmethods', PaymentMethodController::class);
    });

    // Public for logged-in users
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('orderitems', OrderItemController::class);
    Route::apiResource('paymentconfirmations', PaymentConfirmationController::class);
});

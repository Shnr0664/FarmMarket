<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FarmerController; // Add this import
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

// Public routes

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('products', [ProductController::class, 'index']);
Route::get('/products/search', [ProductController::class, 'searchAndFilter'])->name('products.search'); // Search, filter, sort
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('user', [UserController::class, 'show']);
    Route::put('users/{user}/personal-info', [UserController::class, 'updatePersonalInfo']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/cart/remove', [CartController::class, 'removeFromCart']);
    Route::get('/cart/view', [CartController::class, 'viewCart']);

    Route::post('/order/create', [OrderController::class, 'createOrder']);
    Route::post('/order/process/{orderId}', [OrderController::class, 'processOrder']);
    Route::post('/order/cancel/{orderId}', [OrderController::class, 'cancelOrder']);
    Route::post('/order/complete/{orderId}', [OrderController::class, 'completeOrder']);
    Route::get('/orders', [OrderController::class, 'listOrders']);
    Route::middleware('farmer.approved')->group(function () {
        Route::apiResource('farms', FarmController::class);
        Route::apiResource('farms.products', ProductController::class);
    });
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::patch('farmers/{farmer}/approve', [FarmerController::class, 'approve']);
    Route::patch('farmers/{farmer}/reject', [FarmerController::class, 'reject']);
});

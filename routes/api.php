<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FarmManagementController;

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


    Route::post('/farms', [FarmController::class, 'store']); // Create farm
    Route::put('/farms/{farm}', [FarmController::class, 'update']); // Update farm
    Route::delete('/farms/{farm}', [FarmController::class, 'destroy']); // Delete farm

    Route::post('/farms/{farm}/products', [ProductController::class, 'store']);
    Route::post('/products/{product}/update', [ProductController::class, 'update']);
    Route::post('/products/{product}/delete', [ProductController::class, 'destroy']);

    Route::get('/farmers', [FarmerController::class, 'index']);
    Route::get('/farmers/{farmer}', [FarmerController::class, 'show']);

    Route::get('/farms', [FarmManagementController::class, 'index']);
    Route::get('/farms/{farm}', [FarmManagementController::class, 'show']);

    Route::get('/farms/{farm}/products', [ProductController::class, 'getProductsByFarm']);
    Route::get('/farmer/products', [ProductController::class, 'getProductsByFarmer']);
    Route::post('/products/add', [ProductController::class, 'store']);
    Route::put('/products/{product}/update', [ProductController::class, 'update']);

});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('farmers/{farmer}/approve', [FarmerController::class, 'approve']);
    Route::post('farmers/{farmer}/reject', [FarmerController::class, 'reject']);
});

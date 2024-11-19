<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get('products', [ProductController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::put('users/{user}/personal-info', [UserController::class, 'updatePersonalInfo']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/cart/remove', [CartController::class, 'removeFromCart']);
    Route::get('/cart/view', [CartController::class, 'viewCart']);

    Route::post('/order/create', [OrderController::class, 'createOrder']);
    Route::post('/order/process/{orderId}', [OrderController::class, 'processOrder']);
    Route::post('/order/cancel/{orderId}', [OrderController::class, 'cancelOrder']);
    Route::post('/order/complete/{orderId}', [OrderController::class, 'completeOrder']);
    Route::get('/orders', [OrderController::class, 'listOrders']);

});
Route::get('/products', [ProductController::class, 'index'])->name('products.index'); //Main page with products
Route::get('/products/search', [ProductController::class, 'searchAndFilter'])->name('products.search'); // Search, filter, sort
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

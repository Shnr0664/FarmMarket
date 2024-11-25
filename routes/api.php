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
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Mail;

Route::get('/send-test-email', function () {
    Mail::raw('This is a test email sent using Gmail SMTP with App Password.', function ($message) {
        $message->to('shynaray.sagidullayeva@nu.edu.kz') // Replace with your email
            ->subject('Test Email');
    });

    return 'Test email sent!';
});


// Public routes

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('products', [ProductController::class, 'index']);
Route::get('/products/search', [ProductController::class, 'searchAndFilter'])->name('products.search'); // Search, filter, sort
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

Route::middleware('auth:sanctum')->group(function () {

    // Email verification routes
    Route::post('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent.']);
    })->name('verification.resend');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json(['message' => 'Email verified successfully.']);
    })->middleware('signed')->name('verification.verify');

    Route::get('/email/verify', function (Request $request) {
        return $request->user()->hasVerifiedEmail()
            ? response()->json(['message' => 'Email is already verified.'])
            : response()->json(['message' => 'Email is not verified.'], 403);
    })->name('verification.status');
});

// Protected routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
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


    Route::post('/farms', [FarmController::class, 'store']);
    Route::post('/farms/{farm}/update', [FarmController::class, 'update']);
    Route::post('/farms/{farm}/delete', [FarmController::class, 'destroy']);

    Route::post('/farms/{farm}/products', [ProductController::class, 'store']);
    Route::post('/products/{product}/update', [ProductController::class, 'update']);
    Route::post('/products/{product}/delete', [ProductController::class, 'destroy']);

    Route::get('/farmers', [FarmerController::class, 'index']);
    Route::get('/farmers/{farmer}', [FarmerController::class, 'show']);

    Route::get('/farms', [FarmManagementController::class, 'index']);
    Route::get('/farms/{farm}', [FarmManagementController::class, 'show']);


});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('farmers/{farmer}/approve', [FarmerController::class, 'approve']);
    Route::post('farmers/{farmer}/reject', [FarmerController::class, 'reject']);
});

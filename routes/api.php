<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FarmerController; // Add this import

// Public routes
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/user', [AuthController::class, 'user']);
    
});

Route::prefix('v1')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::patch('farmers/{farmer}/approve', [FarmerController::class, 'approve']);
    Route::patch('farmers/{farmer}/reject', [FarmerController::class, 'reject']);
});
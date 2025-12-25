<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ServiceDurationController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/send-verification-code', [AuthController::class, 'sendVerificationCode']);
Route::post('/verify-registration-code', [AuthController::class, 'verifyRegistrationCode']); // Step 1: Verify code
Route::post('/complete-registration', [AuthController::class, 'completeRegistration']); // Step 2: Complete registration
Route::post('/login', [AuthController::class, 'login']);


// Protected routes - Customer only
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Customer routes
    Route::prefix('customer')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Api\CustomerController::class, 'profile']);
        Route::put('/profile', [\App\Http\Controllers\Api\CustomerController::class, 'updateProfile']);
        Route::get('/dashboard', [\App\Http\Controllers\Api\CustomerController::class, 'dashboard']);
        
        // Bookings
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/bookings/available-time-slots', [BookingController::class, 'availableTimeSlots']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/bookings/{booking}', [BookingController::class, 'show']);
        Route::put('/bookings/{booking}', [BookingController::class, 'update']);
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
        Route::post('/bookings/{booking}/payment', [BookingController::class, 'payment']);
    });
    
});

// Public Services API routes (for customers)
Route::prefix('services')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    
    Route::get('/sub-categories', [SubCategoryController::class, 'index']);
    Route::get('/sub-categories/{subCategory}', [SubCategoryController::class, 'show']);
    
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
    
    Route::get('/durations', [ServiceDurationController::class, 'index']);
    Route::get('/durations/{serviceDuration}', [ServiceDurationController::class, 'show']);
});


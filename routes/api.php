<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\RatingController;
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
        Route::get('/reports', [\App\Http\Controllers\Api\Customer\ReportsController::class, 'index']);

        // Help Guides
        Route::get('/help-guide', [\App\Http\Controllers\Api\HelpGuideController::class, 'index']);
        Route::get('/help-guide/{id}', [\App\Http\Controllers\Api\HelpGuideController::class, 'show']);

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/bookings/available-dates', [BookingController::class, 'availableDates']);
        Route::get('/bookings/available-time-slots', [BookingController::class, 'availableTimeSlots']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/bookings/{booking}', [BookingController::class, 'show']);
        Route::put('/bookings/{booking}', [BookingController::class, 'update']);
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
        Route::post('/bookings/payment', [BookingController::class, 'payment']);
        Route::post('/bookings/initiate-online-payment', [BookingController::class, 'initiateOnlinePayment']);
        // PayMob Online Payment (Legacy - for old bookings)
        Route::post('/bookings/{bookingId}/pay-online', [\App\Http\Controllers\Api\PaymentController::class, 'initiatePayment']);
        
        // Consultation Bookings
        Route::get('/bookings/consultation/available-dates', [BookingController::class, 'availableConsultationDates']);
        Route::get('/bookings/consultation/available-time-slots', [BookingController::class, 'availableConsultationTimeSlots']);
        Route::post('/bookings/consultation', [BookingController::class, 'storeConsultation']);

        // Ratings
        Route::post('/ratings', [RatingController::class, 'store']);
        Route::get('/ratings/my-ratings', [RatingController::class, 'myRatings']);
    });

    // Employee routes
    Route::prefix('employee')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\EmployeeController::class, 'dashboard']);
        Route::get('/bookings', [\App\Http\Controllers\Api\EmployeeController::class, 'bookings']);
    });

    // Notifications routes (for all authenticated users)
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::get('/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);
    });

    // Tickets routes (for all authenticated users)
    Route::prefix('tickets')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\TicketController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\TicketController::class, 'store']);
        Route::get('/{ticket}', [\App\Http\Controllers\Api\TicketController::class, 'show']);
        Route::post('/{ticketId}/messages', [\App\Http\Controllers\Api\TicketController::class, 'addMessage']);
        Route::put('/{ticket}/status', [\App\Http\Controllers\Api\TicketController::class, 'updateStatus']);
    });

    // Subscriptions routes
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::get('/active', [SubscriptionController::class, 'active']);
        Route::get('/requests', [SubscriptionController::class, 'requests']);
        Route::get('/{subscription}', [SubscriptionController::class, 'show']);
        Route::post('/', [SubscriptionController::class, 'store']);
    });

});

// Payment Callback (Public)
Route::any('/payment/callback', [\App\Http\Controllers\Api\PaymentController::class, 'callback']);

// Public Services API routes (for customers)
Route::prefix('services')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    Route::get('/sub-categories', [SubCategoryController::class, 'index']);
    Route::get('/sub-categories/{subCategory}', [SubCategoryController::class, 'show']);

    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
});

// Public Consultations API routes (for customers)
Route::prefix('consultations')->group(function () {
    Route::get('/', [ConsultationController::class, 'index']);
    Route::get('/{id}', [ConsultationController::class, 'show']);
    Route::get('/category/{categoryId}', [ConsultationController::class, 'byCategory']);
    
    // Available dates and time slots for consultations
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/available-dates', [BookingController::class, 'availableConsultationDates']);
        Route::get('/available-time-slots', [BookingController::class, 'availableConsultationTimeSlots']);
    });
});

// FAQ API routes
Route::prefix('faqs')->group(function () {
    Route::get('/', [FaqController::class, 'index']);
    Route::get('/category/{category}', [FaqController::class, 'getByCategory']);
});


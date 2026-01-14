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
        Route::post('/profile/avatar', [\App\Http\Controllers\Api\CustomerController::class, 'updateAvatar']);
        Route::get('/dashboard', [\App\Http\Controllers\Api\CustomerController::class, 'dashboard']);
        Route::get('/reports', [\App\Http\Controllers\Api\Customer\ReportsController::class, 'index']);
        Route::get('/activity-log', [\App\Http\Controllers\Api\Customer\ReportsController::class, 'activityLog']);

        // Help Guides
        Route::get('/help-guide', [\App\Http\Controllers\Api\HelpGuideController::class, 'index']);
        Route::get('/help-guide/{id}', [\App\Http\Controllers\Api\HelpGuideController::class, 'show']);

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/bookings/past', [BookingController::class, 'pastBookings']);
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

        // Invoices
        Route::get('/invoices', [\App\Http\Controllers\Api\InvoiceController::class, 'index']);
        Route::get('/invoices/{booking}', [\App\Http\Controllers\Api\InvoiceController::class, 'show']);
        Route::get('/invoices/{booking}/download', [\App\Http\Controllers\Api\InvoiceController::class, 'download']);

        // Points & Wallet
        Route::prefix('points')->group(function () {
            Route::get('/wallet', [\App\Http\Controllers\Customer\PointsController::class, 'index']);
            Route::post('/purchase', [\App\Http\Controllers\Customer\PointsController::class, 'purchase']);
            Route::get('/transactions', [\App\Http\Controllers\Customer\PointsController::class, 'transactions']);
        });

        // Ready Apps
        Route::prefix('ready-apps')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'index']);
            Route::get('/favorites', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'favorites']);
            Route::get('/orders', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'orders']);
            Route::get('/{id}', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'show']);
            Route::post('/{id}/purchase', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'purchase']);
            Route::post('/{id}/inquiry', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'inquiry']);
            Route::post('/{id}/favorite', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'toggleFavorite']);
            Route::delete('/{id}/favorite', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'toggleFavorite']);
        });

        // AI Services (Ready Services)
        Route::prefix('ai-services')->group(function () {
            Route::get('/favorites', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'favorites']);
            Route::get('/orders', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'orders']);
            Route::post('/{id}/purchase', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'purchase']);
            Route::post('/{id}/inquiry', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'inquiry']);
            Route::post('/{id}/favorite', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'toggleFavorite']);
            Route::delete('/{id}/favorite', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'toggleFavorite']);
        });

        // AI Service Requests (Custom Requests)
        Route::prefix('ai-service-requests')->group(function () {
            Route::get('/categories', [\App\Http\Controllers\Api\Customer\AiServiceRequestsController::class, 'categories']);
            Route::get('/', [\App\Http\Controllers\Api\Customer\AiServiceRequestsController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\Customer\AiServiceRequestsController::class, 'store']);
            Route::get('/{id}', [\App\Http\Controllers\Api\Customer\AiServiceRequestsController::class, 'show']);
            Route::put('/{id}', [\App\Http\Controllers\Api\Customer\AiServiceRequestsController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\Customer\AiServiceRequestsController::class, 'destroy']);
            Route::post('/{id}/accept-quote', [\App\Http\Controllers\Api\Customer\AiServiceRequestsController::class, 'acceptQuote']);
            Route::post('/{id}/reject-quote', [\App\Http\Controllers\Api\Customer\AiServiceRequestsController::class, 'rejectQuote']);
        });
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
        Route::get('/active', [SubscriptionController::class, 'active']);
        Route::get('/requests', [SubscriptionController::class, 'requests']);
        Route::get('/{subscription}', [SubscriptionController::class, 'show']);
        Route::post('/', [SubscriptionController::class, 'store']);
    });
});
        // AI Services (Ready Services)
        Route::prefix('customer/ai-services')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'index']);

            Route::get('/{id}', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'show']);

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

// Navigation API routes (Public - for frontend)
Route::prefix('navigation')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\NavigationController::class, 'index']);
});

// Hero API routes (Public - for frontend)
Route::prefix('hero')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\HeroController::class, 'index']);
});

// Public Ratings API routes
Route::prefix('ratings')->group(function () {
    Route::get('/', [RatingController::class, 'index']);
    Route::get('/statistics', [RatingController::class, 'statistics']);
});

// Public Ready Apps API routes (for browsing without authentication)
Route::prefix('ready-apps')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'index']);
    Route::get('/{id}', [\App\Http\Controllers\Api\Customer\ReadyAppsController::class, 'show']);
});

// Public AI Services API routes (for browsing without authentication)
Route::prefix('ai-services')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'index']);
    Route::get('/{id}', [\App\Http\Controllers\Api\Customer\AiServicesController::class, 'show']);
});

// Public Customer Facing API routes
Route::get('/navigation', [\App\Http\Controllers\Api\NavigationController::class, 'index']);
Route::get('/hero', [\App\Http\Controllers\Api\HeroController::class, 'index']);
Route::get('/company-logo', [\App\Http\Controllers\Api\CompanyLogoController::class, 'index']);
Route::get('/footer', [\App\Http\Controllers\Api\FooterController::class, 'index']);
Route::get('/consultation-booking-section', [\App\Http\Controllers\Api\ConsultationBookingSectionController::class, 'index']);
Route::get('/technologies-section', [\App\Http\Controllers\Api\TechnologiesSectionController::class, 'index']);
Route::get('/services-section', [\App\Http\Controllers\Api\HomeServicesSectionController::class, 'index']);
Route::get('/ready-apps-section', [\App\Http\Controllers\Api\HomeReadyAppsSectionController::class, 'index']);

Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index']);
   });
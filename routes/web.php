<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\StaffController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionRequestController;
use App\Http\Controllers\Web\FaqController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Switch Language Route
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('switch-language');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes (requires admin role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users management - Main page redirects to admins
    Route::get('/users', [AdminController::class, 'users'])->name('users');

    // Admins management
    Route::get('/users/admins', [AdminController::class, 'admins'])->name('users.admins');
    Route::get('/users/admins/create', [AdminController::class, 'createAdmin'])->name('users.admins.create');
    Route::post('/users/admins', [AdminController::class, 'storeAdmin'])->name('users.admins.store');
    Route::get('/users/admins/{user}', [AdminController::class, 'showAdmin'])->name('users.admins.show');
    Route::get('/users/admins/{user}/edit', [AdminController::class, 'editAdmin'])->name('users.admins.edit');
    Route::put('/users/admins/{user}', [AdminController::class, 'updateAdmin'])->name('users.admins.update');
    Route::delete('/users/admins/{user}', [AdminController::class, 'deleteAdmin'])->name('users.admins.delete');

    // Staff management
    Route::get('/users/staff', [AdminController::class, 'staff'])->name('users.staff');
    Route::get('/users/staff/create', [AdminController::class, 'createStaff'])->name('users.staff.create');
    Route::post('/users/staff', [AdminController::class, 'storeStaff'])->name('users.staff.store');
    Route::get('/users/staff/{user}', [AdminController::class, 'showStaff'])->name('users.staff.show');
    Route::get('/users/staff/{user}/edit', [AdminController::class, 'editStaff'])->name('users.staff.edit');
    Route::put('/users/staff/{user}', [AdminController::class, 'updateStaff'])->name('users.staff.update');
    Route::delete('/users/staff/{user}', [AdminController::class, 'deleteStaff'])->name('users.staff.delete');

    // Customers management
    Route::get('/users/customers', [AdminController::class, 'customers'])->name('users.customers');
    Route::get('/users/customers/create', [AdminController::class, 'createCustomer'])->name('users.customers.create');
    Route::post('/users/customers', [AdminController::class, 'storeCustomer'])->name('users.customers.store');
    Route::get('/users/customers/{user}', [AdminController::class, 'showCustomer'])->name('users.customers.show');
    Route::get('/users/customers/{user}/edit', [AdminController::class, 'editCustomer'])->name('users.customers.edit');
    Route::put('/users/customers/{user}', [AdminController::class, 'updateCustomer'])->name('users.customers.update');
    Route::delete('/users/customers/{user}', [AdminController::class, 'deleteCustomer'])->name('users.customers.delete');

    // Bookings management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [AdminController::class, 'showBooking'])->name('bookings.show');
    Route::put('/bookings/{booking}/status', [AdminController::class, 'updateBookingStatus'])->name('bookings.update-status');
    Route::put('/bookings/{booking}/payment-status', [AdminController::class, 'updateBookingPaymentStatus'])->name('bookings.update-payment-status');

    // Time Slots management
    Route::get('/time-slots', [AdminController::class, 'timeSlots'])->name('time-slots');
    Route::get('/time-slots/create', [AdminController::class, 'createTimeSlot'])->name('time-slots.create');
    Route::post('/time-slots', [AdminController::class, 'storeTimeSlot'])->name('time-slots.store');
    Route::get('/time-slots/{timeSlot}/edit', [AdminController::class, 'editTimeSlot'])->name('time-slots.edit');
    Route::put('/time-slots/{timeSlot}', [AdminController::class, 'updateTimeSlot'])->name('time-slots.update');
    Route::delete('/time-slots/{timeSlot}', [AdminController::class, 'deleteTimeSlot'])->name('time-slots.delete');
    Route::post('/time-slots/bulk-create', [AdminController::class, 'bulkCreateTimeSlots'])->name('time-slots.bulk-create');

    // Employee Schedules (Recurring Time Slots)
    Route::get('/time-slots/schedules', [AdminController::class, 'employeeSchedules'])->name('time-slots.schedules');
    Route::get('/time-slots/schedules/create', [AdminController::class, 'createSchedule'])->name('time-slots.schedules.create');
    Route::post('/time-slots/schedules', [AdminController::class, 'storeSchedule'])->name('time-slots.schedules.store');
    Route::get('/time-slots/schedules/{schedule}/edit', [AdminController::class, 'editSchedule'])->name('time-slots.schedules.edit');
    Route::put('/time-slots/schedules/{schedule}', [AdminController::class, 'updateSchedule'])->name('time-slots.schedules.update');
    Route::delete('/time-slots/schedules/{schedule}', [AdminController::class, 'deleteSchedule'])->name('time-slots.schedules.delete');

    // Categories management (used as specializations)
    Route::resource('categories', CategoryController::class)->parameters([
        'categories' => 'category'
    ]);

    // Sub Categories management
    Route::resource('sub-categories', SubCategoryController::class)->parameters([
        'sub-categories' => 'subCategory'
    ]);

    // API routes for dynamic loading
    Route::get('/api/categories/{category}/subcategories', [AdminController::class, 'getSubCategories'])->name('api.categories.subcategories');
    Route::get('/api/subcategories/{subcategory}', [AdminController::class, 'getSubCategory'])->name('api.subcategories.show');

    // Services management
    Route::resource('services', ServiceController::class)->parameters([
        'services' => 'service'
    ]);

    // Consultations management
    Route::resource('consultations', ConsultationController::class)->parameters([
        'consultations' => 'consultation'
    ]);

    // Tickets management
    Route::get('/tickets', [\App\Http\Controllers\Web\AdminController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Web\AdminController::class, 'showTicket'])->name('tickets.show');
    Route::put('/tickets/{ticket}/status', [\App\Http\Controllers\Web\AdminController::class, 'updateTicketStatus'])->name('tickets.update-status');

    // FAQs management
    Route::resource('faqs', AdminFaqController::class);

    // Help Guides management
    Route::resource('help-guides', \App\Http\Controllers\Admin\HelpGuideController::class);

    // Subscriptions management
    Route::resource('subscriptions', SubscriptionController::class)->parameters([
        'subscriptions' => 'subscription'
    ]);

    // Subscription requests management
    Route::prefix('subscription-requests')->name('subscription-requests.')->group(function () {
        Route::get('/', [SubscriptionRequestController::class, 'index'])->name('index');
        Route::get('/{request}', [SubscriptionRequestController::class, 'show'])->name('show');
        Route::post('/{request}/approve', [SubscriptionRequestController::class, 'approve'])->name('approve');
        Route::post('/{request}/reject', [SubscriptionRequestController::class, 'reject'])->name('reject');
    });

    // Invoices management
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\InvoiceController::class, 'statistics'])->name('statistics');
        Route::get('/export', [\App\Http\Controllers\Admin\InvoiceController::class, 'export'])->name('export');
        Route::get('/{booking}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('show');
        Route::get('/{booking}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('download');
    });
});

// Staff routes (requires staff or admin role)
Route::middleware(['auth', 'role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');

    // My Bookings
    Route::get('/my-bookings', [StaffController::class, 'myBookings'])->name('my-bookings');
    Route::get('/my-bookings/{booking}', [StaffController::class, 'showBooking'])->name('my-bookings.show');
    // Note: Booking status updates automatically based on time - employees cannot manually change status

    // My Schedule
    Route::get('/my-schedule', [StaffController::class, 'mySchedule'])->name('my-schedule');

    // Tickets (AJAX)
    Route::get('/tickets', [StaffController::class, 'getTickets'])->name('tickets');
});

// Notifications routes (for all authenticated users)
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('index');
    Route::post('/{notification}/read', [\App\Http\Controllers\Web\NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [\App\Http\Controllers\Web\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{notification}', [\App\Http\Controllers\Web\NotificationController::class, 'destroy'])->name('destroy');
});

// Tickets routes (for all authenticated users)
Route::middleware('auth')->prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\TicketController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Web\TicketController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Web\TicketController::class, 'store'])->name('store');
    Route::get('/{ticket}', [\App\Http\Controllers\Web\TicketController::class, 'show'])->name('show');
    Route::post('/{ticket}/messages', [\App\Http\Controllers\Web\TicketController::class, 'addMessage'])->name('add-message');
    Route::put('/{ticket}/status', [\App\Http\Controllers\Web\TicketController::class, 'updateStatus'])->name('update-status');
});

// Settings routes (for all authenticated users)
Route::middleware('auth')->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\SettingsController::class, 'index'])->name('index');
    Route::put('/profile', [\App\Http\Controllers\Web\SettingsController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [\App\Http\Controllers\Web\SettingsController::class, 'updatePassword'])->name('password.update');
});

// FAQ route for all authenticated users
Route::middleware('auth')->get('/faqs', [FaqController::class, 'index'])->name('faqs.index');

// Help & Guide routes (for all authenticated users)
Route::middleware('auth')->prefix('help-guide')->name('help-guide.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\HelpGuideController::class, 'index'])->name('index');
});

// Customer routes (public and authenticated)
Route::prefix('customer')->name('customer.')->group(function () {
    // Authenticated routes
    Route::middleware('auth')->group(function () {
        // Customer routes here
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('/bookings', [CustomerController::class, 'bookings'])->name('bookings');
        Route::get('/bookings/{booking}', [CustomerController::class, 'showBooking'])->name('bookings.show');
    });
});

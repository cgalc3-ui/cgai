<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\StaffController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceDurationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
    
    // Specializations management
    Route::get('/specializations', [AdminController::class, 'specializations'])->name('specializations');
    Route::get('/specializations/create', [AdminController::class, 'createSpecialization'])->name('specializations.create');
    Route::post('/specializations', [AdminController::class, 'storeSpecialization'])->name('specializations.store');
    Route::get('/specializations/{specialization}/edit', [AdminController::class, 'editSpecialization'])->name('specializations.edit');
    Route::put('/specializations/{specialization}', [AdminController::class, 'updateSpecialization'])->name('specializations.update');
    Route::delete('/specializations/{specialization}', [AdminController::class, 'deleteSpecialization'])->name('specializations.delete');
    
    // Categories management
    Route::resource('categories', CategoryController::class)->parameters([
        'categories' => 'category'
    ]);
    
    // Sub Categories management
    Route::resource('sub-categories', SubCategoryController::class)->parameters([
        'sub-categories' => 'subCategory'
    ]);
    
    // Services management
    Route::resource('services', ServiceController::class)->parameters([
        'services' => 'service'
    ]);
    
    // Service Durations management
    Route::resource('service-durations', ServiceDurationController::class)->parameters([
        'service-durations' => 'serviceDuration'
    ]);
});

// Staff routes (requires staff or admin role)
Route::middleware(['auth', 'role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
    
    // Customers management
    Route::get('/customers', [StaffController::class, 'customers'])->name('customers');
    Route::get('/customers/{customer}', [StaffController::class, 'showCustomer'])->name('customers.show');
});

// Customer routes (public and authenticated)
Route::prefix('customer')->name('customer.')->group(function () {
    // Authenticated routes
    Route::middleware('auth')->group(function () {
        // Customer routes here
    });
});

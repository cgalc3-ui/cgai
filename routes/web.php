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

// Public subscriptions page (allow GET /subscriptions without API token)
Route::get('/subscriptions', [\App\Http\Controllers\Api\SubscriptionController::class, 'index']);

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

    // Ready Apps management
    Route::prefix('ready-apps')->name('ready-apps.')->group(function () {
        // Categories
        Route::resource('categories', \App\Http\Controllers\Admin\ReadyAppCategoryController::class)->parameters([
            'categories' => 'category'
        ]);

        // Apps
        Route::resource('apps', \App\Http\Controllers\Admin\ReadyAppController::class)->parameters([
            'apps' => 'app'
        ]);

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReadyAppOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [\App\Http\Controllers\Admin\ReadyAppOrderController::class, 'show'])->name('show');
            Route::put('/{order}/status', [\App\Http\Controllers\Admin\ReadyAppOrderController::class, 'updateStatus'])->name('update-status');
        });
    });

    // AI Services management
    Route::prefix('ai-services')->name('ai-services.')->group(function () {
        // Categories
        Route::resource('categories', \App\Http\Controllers\Admin\AiServiceCategoryController::class)->parameters([
            'categories' => 'category'
        ]);

        // Tags (Technologies)
        Route::resource('tags', \App\Http\Controllers\Admin\AiServiceTagController::class)->parameters([
            'tags' => 'tag'
        ]);

        // Services
        Route::resource('services', \App\Http\Controllers\Admin\AiServiceController::class)->parameters([
            'services' => 'service'
        ]);

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AiServiceOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [\App\Http\Controllers\Admin\AiServiceOrderController::class, 'show'])->name('show');
            Route::put('/{order}/status', [\App\Http\Controllers\Admin\AiServiceOrderController::class, 'updateStatus'])->name('update-status');
        });

        // Custom Requests
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AiServiceRequestController::class, 'index'])->name('index');
            Route::get('/{request}', [\App\Http\Controllers\Admin\AiServiceRequestController::class, 'show'])->name('show');
            Route::put('/{request}/status', [\App\Http\Controllers\Admin\AiServiceRequestController::class, 'updateStatus'])->name('update-status');
            Route::put('/{request}/quote', [\App\Http\Controllers\Admin\AiServiceRequestController::class, 'updateQuote'])->name('update-quote');
        });
    });

    // Tickets management
    Route::get('/tickets', [\App\Http\Controllers\Web\AdminController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Web\AdminController::class, 'showTicket'])->name('tickets.show');
    Route::put('/tickets/{ticket}/status', [\App\Http\Controllers\Web\AdminController::class, 'updateTicketStatus'])->name('tickets.update-status');

    // FAQs management
    Route::resource('faqs', AdminFaqController::class);

    // Help Guides management
    Route::resource('help-guides', \App\Http\Controllers\Admin\HelpGuideController::class);

    // Customer-facing content management (وجهة العميل)
    Route::prefix('customer-facing')->name('customer-facing.')->group(function () {
        // Main customer facing page
        Route::get('/', [\App\Http\Controllers\Admin\CustomerFacingController::class, 'index'])->name('index');
        
        // Load section content (AJAX endpoint)
        Route::get('/load-section/{section}', [\App\Http\Controllers\Admin\CustomerFacingController::class, 'loadSection'])->name('load-section');
        
        // Navigation management
        Route::prefix('navigation')->name('navigation.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\NavigationController::class, 'index'])->name('index');

            // Logo
            Route::get('/logo/create', [\App\Http\Controllers\Admin\NavigationController::class, 'createLogo'])->name('logo.create');
            Route::post('/logo', [\App\Http\Controllers\Admin\NavigationController::class, 'storeLogo'])->name('logo.store');

            // Menu Items
            Route::get('/menu-items/create', [\App\Http\Controllers\Admin\NavigationController::class, 'createMenuItem'])->name('menu-items.create');
            Route::post('/menu-items', [\App\Http\Controllers\Admin\NavigationController::class, 'storeMenuItem'])->name('menu-items.store');
            Route::get('/menu-items/{navigationItem}/edit', [\App\Http\Controllers\Admin\NavigationController::class, 'editMenuItem'])->name('menu-items.edit');
            Route::put('/menu-items/{navigationItem}', [\App\Http\Controllers\Admin\NavigationController::class, 'updateMenuItem'])->name('menu-items.update');

            // Buttons
            Route::get('/buttons/create', [\App\Http\Controllers\Admin\NavigationController::class, 'createButton'])->name('buttons.create');
            Route::post('/buttons', [\App\Http\Controllers\Admin\NavigationController::class, 'storeButton'])->name('buttons.store');
            Route::get('/buttons/{navigationItem}/edit', [\App\Http\Controllers\Admin\NavigationController::class, 'editButton'])->name('buttons.edit');
            Route::put('/buttons/{navigationItem}', [\App\Http\Controllers\Admin\NavigationController::class, 'updateButton'])->name('buttons.update');

            // Delete
            Route::delete('/{navigationItem}', [\App\Http\Controllers\Admin\NavigationController::class, 'destroy'])->name('destroy');
        });

        // Hero management
        Route::prefix('hero')->name('hero.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\HeroController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\HeroController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\HeroController::class, 'store'])->name('store');
            Route::get('/{hero}/edit', [\App\Http\Controllers\Admin\HeroController::class, 'edit'])->name('edit');
            Route::put('/{hero}', [\App\Http\Controllers\Admin\HeroController::class, 'update'])->name('update');
            Route::delete('/{hero}', [\App\Http\Controllers\Admin\HeroController::class, 'destroy'])->name('destroy');
        });

        // Company Logo management
        Route::prefix('company-logo')->name('company-logo.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CompanyLogoController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\CompanyLogoController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CompanyLogoController::class, 'store'])->name('store');
            Route::get('/{companyLogo}/edit', [\App\Http\Controllers\Admin\CompanyLogoController::class, 'edit'])->name('edit');
            Route::put('/{companyLogo}', [\App\Http\Controllers\Admin\CompanyLogoController::class, 'update'])->name('update');
            Route::delete('/{companyLogo}', [\App\Http\Controllers\Admin\CompanyLogoController::class, 'destroy'])->name('destroy');
        });

        // Footer management
        Route::prefix('footer')->name('footer.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\FooterController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\FooterController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\FooterController::class, 'store'])->name('store');
            Route::get('/{footer}/edit', [\App\Http\Controllers\Admin\FooterController::class, 'edit'])->name('edit');
            Route::put('/{footer}', [\App\Http\Controllers\Admin\FooterController::class, 'update'])->name('update');
            Route::delete('/{footer}', [\App\Http\Controllers\Admin\FooterController::class, 'destroy'])->name('destroy');
        });

        // Consultation Booking Section management
        Route::prefix('consultation-booking-section')->name('consultation-booking-section.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ConsultationBookingSectionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ConsultationBookingSectionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ConsultationBookingSectionController::class, 'store'])->name('store');
            Route::get('/{consultationBookingSection}/edit', [\App\Http\Controllers\Admin\ConsultationBookingSectionController::class, 'edit'])->name('edit');
            Route::put('/{consultationBookingSection}', [\App\Http\Controllers\Admin\ConsultationBookingSectionController::class, 'update'])->name('update');
            Route::delete('/{consultationBookingSection}', [\App\Http\Controllers\Admin\ConsultationBookingSectionController::class, 'destroy'])->name('destroy');
        });

        // Technologies Section management
        Route::prefix('technologies-section')->name('technologies-section.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\TechnologiesSectionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\TechnologiesSectionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\TechnologiesSectionController::class, 'store'])->name('store');
            Route::get('/{technologiesSection}/edit', [\App\Http\Controllers\Admin\TechnologiesSectionController::class, 'edit'])->name('edit');
            Route::put('/{technologiesSection}', [\App\Http\Controllers\Admin\TechnologiesSectionController::class, 'update'])->name('update');
            Route::delete('/{technologiesSection}', [\App\Http\Controllers\Admin\TechnologiesSectionController::class, 'destroy'])->name('destroy');
        });

        // Services Section management
        Route::prefix('services-section')->name('services-section.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\HomeServicesSectionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\HomeServicesSectionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\HomeServicesSectionController::class, 'store'])->name('store');
            Route::get('/{homeServicesSection}/edit', [\App\Http\Controllers\Admin\HomeServicesSectionController::class, 'edit'])->name('edit');
            Route::put('/{homeServicesSection}', [\App\Http\Controllers\Admin\HomeServicesSectionController::class, 'update'])->name('update');
            Route::delete('/{homeServicesSection}', [\App\Http\Controllers\Admin\HomeServicesSectionController::class, 'destroy'])->name('destroy');
            
            // Category management
            Route::get('/category/{category}/edit', [\App\Http\Controllers\Admin\HomeServicesSectionController::class, 'editCategory'])->name('category.edit');
            Route::put('/category/{category}', [\App\Http\Controllers\Admin\HomeServicesSectionController::class, 'updateCategory'])->name('category.update');
        });

        // Ready Apps Section management
        Route::prefix('ready-apps-section')->name('ready-apps-section.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\HomeReadyAppsSectionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\HomeReadyAppsSectionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\HomeReadyAppsSectionController::class, 'store'])->name('store');
            Route::get('/{homeReadyAppsSection}/edit', [\App\Http\Controllers\Admin\HomeReadyAppsSectionController::class, 'edit'])->name('edit');
            Route::put('/{homeReadyAppsSection}', [\App\Http\Controllers\Admin\HomeReadyAppsSectionController::class, 'update'])->name('update');
            Route::delete('/{homeReadyAppsSection}', [\App\Http\Controllers\Admin\HomeReadyAppsSectionController::class, 'destroy'])->name('destroy');
            
            // Category management
            Route::get('/category/{category}/edit', [\App\Http\Controllers\Admin\HomeReadyAppsSectionController::class, 'editCategory'])->name('category.edit');
            Route::put('/category/{category}', [\App\Http\Controllers\Admin\HomeReadyAppsSectionController::class, 'updateCategory'])->name('category.update');
        });
    });

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

    // Points & Wallet management
    Route::prefix('points')->name('points.')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\Admin\PointsSettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\PointsSettingsController::class, 'updateSettings'])->name('settings.update');
        Route::post('/services/{serviceId}/pricing', [\App\Http\Controllers\Admin\PointsSettingsController::class, 'updateServicePricing'])->name('services.pricing');
        Route::post('/consultations/{consultationId}/pricing', [\App\Http\Controllers\Admin\PointsSettingsController::class, 'updateConsultationPricing'])->name('consultations.pricing');
        Route::post('/subscriptions/{subscriptionId}/pricing', [\App\Http\Controllers\Admin\PointsSettingsController::class, 'updateSubscriptionPricing'])->name('subscriptions.pricing');
        Route::get('/transactions', [\App\Http\Controllers\Admin\PointsSettingsController::class, 'transactions'])->name('transactions');
        Route::get('/wallets', [\App\Http\Controllers\Admin\PointsSettingsController::class, 'wallets'])->name('wallets');
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

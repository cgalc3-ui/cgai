<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/confirm.css') }}">
    @stack('styles')
    <script>
        // Apply theme BEFORE page renders to prevent flickering
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Check for saved sidebar state before page renders
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.documentElement.classList.add('sidebar-collapsed-init');
            }
        })();
    </script>
</head>

<body class="">
    <div class="dashboard-container" id="dashboardContainer">
        <script>
            if (document.documentElement.classList.contains('sidebar-collapsed-init')) {
                document.getElementById('dashboardContainer').classList.add('sidebar-collapsed');
            }
        </script>
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2 class="logo">
                    <i class="fas fa-layer-group"></i>
                    <span class="logo-full">{{ config('app.name') }}</span>
                    <span class="logo-mini">{{ substr(config('app.name'), 0, 2) }}</span>
                </h2>
            </div>

            <div class="sidebar-user">
                <div class="user-avatar-wrapper">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff' }}"
                        alt="{{ auth()->user()->name }}" class="user-avatar">
                </div>
                <div class="user-info">
                    <h4 class="user-name">{{ auth()->user()->name }}</h4>
                    <p class="user-role">
                        @if(auth()->user()->isAdmin())
                            {{ __('messages.main_supervisor') }}
                        @elseif(auth()->user()->isStaff())
                            {{ __('messages.employee') }}
                        @endif
                    </p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-label">{{ __('messages.navigation') ?? 'NAVIGATION' }}</div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" <a href="{{ route('admin.dashboard') }}"
                        class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>{{ __('messages.dashboard') }}</span>
                    </a>
                    <div class="nav-group">
                        <div class="nav-group-header {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                            onclick="toggleNavGroup(this)">
                            <i class="fas fa-users"></i>
                            <span>{{ __('messages.users') }}</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div class="nav-group-items {{ request()->routeIs('admin.users*') ? 'expanded' : '' }}">
                            <a href="{{ route('admin.users.admins') }}"
                                class="nav-item {{ request()->routeIs('admin.users.admins*') ? 'active' : '' }}">
                                <i class="fas fa-user-shield"></i>
                                <span>{{ __('messages.admins') }}</span>
                            </a>
                            <a href="{{ route('admin.users.staff') }}"
                                class="nav-item {{ request()->routeIs('admin.users.staff*') ? 'active' : '' }}">
                                <i class="fas fa-user-tie"></i>
                                <span>{{ __('messages.staff') }}</span>
                            </a>
                            <a href="{{ route('admin.users.customers') }}"
                                class="nav-item {{ request()->routeIs('admin.users.customers*') ? 'active' : '' }}">
                                <i class="fas fa-user"></i>
                                <span>{{ __('messages.customers') }}</span>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.time-slots') }}"
                        class="nav-item {{ request()->routeIs('admin.time-slots') && !request()->routeIs('admin.time-slots.schedules*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>{{ __('messages.time_slots') }}</span>
                    </a>
                    <a href="{{ route('admin.time-slots.schedules') }}"
                        class="nav-item {{ request()->routeIs('admin.time-slots.schedules*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ __('messages.recurring_appointments') }}</span>
                    </a>
                    <div class="nav-group">
                        <div class="nav-group-header {{ request()->routeIs('admin.categories*') || request()->routeIs('admin.sub-categories*') || request()->routeIs('admin.services*') || request()->routeIs('admin.consultations*') ? 'active' : '' }}"
                            onclick="toggleNavGroup(this)">
                            <i class="fas fa-concierge-bell"></i>
                            <span>{{ __('messages.services_menu') }}</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div
                            class="nav-group-items {{ request()->routeIs('admin.categories*') || request()->routeIs('admin.sub-categories*') || request()->routeIs('admin.services*') || request()->routeIs('admin.consultations*') ? 'expanded' : '' }}">
                            <a href="{{ route('admin.categories.index') }}"
                                class="nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                                <i class="fas fa-folder"></i>
                                <span>{{ __('messages.categories') }}</span>
                            </a>
                            <a href="{{ route('admin.sub-categories.index') }}"
                                class="nav-item {{ request()->routeIs('admin.sub-categories*') ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i>
                                <span>{{ __('messages.sub_categories') }}</span>
                            </a>
                            <a href="{{ route('admin.services.index') }}"
                                class="nav-item {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
                                <i class="fas fa-cog"></i>
                                <span>{{ __('messages.services') }}</span>
                            </a>
                            <a href="{{ route('admin.consultations.index') }}"
                                class="nav-item {{ request()->routeIs('admin.consultations*') ? 'active' : '' }}">
                                <i class="fas fa-comments"></i>
                                <span>{{ __('messages.consultations') }}</span>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.bookings') }}"
                        class="nav-item {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>{{ __('messages.bookings') }}</span>
                    </a>
                    <div class="nav-group">
                        <div class="nav-group-header {{ request()->routeIs('admin.subscriptions*') || request()->routeIs('admin.subscription-requests*') ? 'active' : '' }}"
                            onclick="toggleNavGroup(this)">
                            <i class="fas fa-crown"></i>
                            <span>{{ __('messages.subscriptions') }}</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div
                            class="nav-group-items {{ request()->routeIs('admin.subscriptions*') || request()->routeIs('admin.subscription-requests*') ? 'expanded' : '' }}">
                            <a href="{{ route('admin.subscriptions.index') }}"
                                class="nav-item {{ request()->routeIs('admin.subscriptions*') && !request()->routeIs('admin.subscription-requests*') ? 'active' : '' }}">
                                <i class="fas fa-box"></i>
                                <span>{{ __('messages.packages') }}</span>
                            </a>
                            <a href="{{ route('admin.subscription-requests.index') }}"
                                class="nav-item {{ request()->routeIs('admin.subscription-requests*') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice"></i>
                                <span>{{ __('messages.subscription_requests') }}</span>
                                @php
                                    $pendingRequestsCount = \App\Models\SubscriptionRequest::where('status', 'pending')->count();
                                @endphp
                                @if($pendingRequestsCount > 0)
                                    <span class="nav-badge">{{ $pendingRequestsCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.invoices.index') }}"
                        class="nav-item {{ request()->routeIs('admin.invoices*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i>
                        <span>{{ __('messages.invoices') }}</span>
                    </a>
                    <div class="nav-group">
                        <div class="nav-group-header {{ request()->routeIs('admin.ready-apps*') ? 'active' : '' }}"
                            onclick="toggleNavGroup(this)">
                            <i class="fas fa-mobile-alt"></i>
                            <span>{{ __('messages.ready_apps') }}</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div class="nav-group-items {{ request()->routeIs('admin.ready-apps*') ? 'expanded' : '' }}">
                            <a href="{{ route('admin.ready-apps.categories.index') }}"
                                class="nav-item {{ request()->routeIs('admin.ready-apps.categories*') ? 'active' : '' }}">
                                <i class="fas fa-folder"></i>
                                <span>{{ __('messages.ready_app_categories') }}</span>
                            </a>
                            <a href="{{ route('admin.ready-apps.apps.index') }}"
                                class="nav-item {{ request()->routeIs('admin.ready-apps.apps*') ? 'active' : '' }}">
                                <i class="fas fa-mobile-alt"></i>
                                <span>{{ __('messages.ready_apps') }}</span>
                            </a>
                            <a href="{{ route('admin.ready-apps.orders.index') }}"
                                class="nav-item {{ request()->routeIs('admin.ready-apps.orders*') ? 'active' : '' }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span>{{ __('messages.ready_app_orders') }}</span>
                                @php
                                    try {
                                        $pendingOrdersCount = \App\Models\ReadyAppOrder::where('status', 'pending')->count();
                                    } catch (\Exception $e) {
                                        $pendingOrdersCount = 0;
                                    }
                                @endphp
                                @if($pendingOrdersCount > 0)
                                    <span class="nav-badge">{{ $pendingOrdersCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="nav-group">
                        <div class="nav-group-header {{ request()->routeIs('admin.ai-services*') ? 'active' : '' }}"
                            onclick="toggleNavGroup(this)">
                            <i class="fas fa-robot"></i>
                            <span>{{ __('messages.ai_services') }}</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div class="nav-group-items {{ request()->routeIs('admin.ai-services*') ? 'expanded' : '' }}">
                            <a href="{{ route('admin.ai-services.categories.index') }}"
                                class="nav-item {{ request()->routeIs('admin.ai-services.categories*') ? 'active' : '' }}">
                                <i class="fas fa-folder"></i>
                                <span>{{ __('messages.ai_service_categories') }}</span>
                            </a>
                            <a href="{{ route('admin.ai-services.services.index') }}"
                                class="nav-item {{ request()->routeIs('admin.ai-services.services*') ? 'active' : '' }}">
                                <i class="fas fa-robot"></i>
                                <span>{{ __('messages.ai_services_list') }}</span>
                            </a>
                            <a href="{{ route('admin.ai-services.orders.index') }}"
                                class="nav-item {{ request()->routeIs('admin.ai-services.orders*') ? 'active' : '' }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span>{{ __('messages.ai_service_orders') }}</span>
                                @php
                                    try {
                                        $pendingAiServiceOrdersCount = \App\Models\AiServiceOrder::where('status', 'pending')->count();
                                    } catch (\Exception $e) {
                                        $pendingAiServiceOrdersCount = 0;
                                    }
                                @endphp
                                @if($pendingAiServiceOrdersCount > 0)
                                    <span class="nav-badge">{{ $pendingAiServiceOrdersCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.ai-services.requests.index') }}"
                                class="nav-item {{ request()->routeIs('admin.ai-services.requests*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i>
                                <span>{{ __('messages.ai_service_requests') }}</span>
                                @php
                                    try {
                                        $pendingAiServiceRequestsCount = \App\Models\AiServiceRequest::where('status', 'pending')->count();
                                    } catch (\Exception $e) {
                                        $pendingAiServiceRequestsCount = 0;
                                    }
                                @endphp
                                @if($pendingAiServiceRequestsCount > 0)
                                    <span class="nav-badge">{{ $pendingAiServiceRequestsCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.points.settings') }}"
                        class="nav-item {{ request()->routeIs('admin.points*') ? 'active' : '' }}">
                        <i class="fas fa-coins"></i>
                        <span>{{ __('messages.points_system') ?? 'نظام النقاط' }}</span>
                    </a>
                    <a href="{{ route('admin.customer-facing.index') }}"
                        class="nav-item {{ request()->routeIs('admin.customer-facing*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>{{ __('messages.customer_facing') ?? 'وجهة العميل' }}</span>
                    </a>

                    <div class="nav-section-label">{{ __('messages.more_menu') ?? 'MORE' }}</div>
                    <a href="{{ route('admin.tickets') }}"
                        class="nav-item {{ request()->routeIs('admin.tickets*') ? 'active' : '' }}">
                        <i class="fas fa-headset"></i>
                        <span>{{ __('messages.tickets_support') }}</span>
                        @php
                            $adminTicketsCount = \App\Models\Ticket::whereIn('status', ['open', 'in_progress'])->count();
                        @endphp
                        @if($adminTicketsCount > 0)
                            <span class="nav-badge">{{ $adminTicketsCount }}</span>
                        @endif
                    </a>
                @elseif(auth()->user()->isStaff())
                    <a href="{{ route('staff.dashboard') }}"
                        class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>{{ __('messages.dashboard') }}</span>
                    </a>
                    <a href="{{ route('staff.my-bookings') }}"
                        class="nav-item {{ request()->routeIs('staff.my-bookings') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>{{ __('messages.my_bookings') }}</span>
                    </a>
                    <a href="{{ route('staff.my-schedule') }}"
                        class="nav-item {{ request()->routeIs('staff.my-schedule') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ __('messages.work_days') }}</span>
                    </a>
                    <a href="{{ route('tickets.index') }}"
                        class="nav-item {{ request()->routeIs('tickets*') ? 'active' : '' }}">
                        <i class="fas fa-headset"></i>
                        <span>{{ __('messages.support_tickets') }}</span>
                        @php
                            $userTicketsCount = \App\Models\Ticket::where('user_id', auth()->id())
                                ->whereIn('status', ['open', 'in_progress'])->count();
                        @endphp
                        @if($userTicketsCount > 0)
                            <span class="nav-badge">{{ $userTicketsCount }}</span>
                        @endif
                    </a>
                @endif

                <!-- Notifications link for all users -->
                <a href="{{ route('notifications.index') }}"
                    class="nav-item {{ request()->routeIs('notifications*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span>{{ __('messages.notifications') }}</span>
                    @php
                        $unreadNotificationsCount = auth()->user()->unreadNotificationsCount();
                    @endphp
                    @if($unreadNotificationsCount > 0)
                        <span class="nav-badge">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.faqs.index') }}"
                        class="nav-item {{ request()->routeIs('admin.faqs*') ? 'active' : '' }}">
                        <i class="fas fa-question-circle"></i>
                        <span>{{ __('messages.faqs_management') }}</span>
                    </a>
                    <a href="{{ route('admin.help-guides.index') }}"
                        class="nav-item {{ request()->routeIs('admin.help-guides*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        <span>{{ __('messages.help_guides_management') }}</span>
                    </a>
                @else
                    <a href="{{ route('faqs.index') }}"
                        class="nav-item {{ request()->routeIs('faqs.index') ? 'active' : '' }}">
                        <i class="fas fa-question-circle"></i>
                        <span>{{ __('messages.faqs') }}</span>
                    </a>
                    <!-- Help & Guide link for non-admin users -->
                    <a href="{{ route('help-guide.index') }}"
                        class="nav-item {{ request()->routeIs('help-guide*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        <span>{{ __('messages.help_and_guide') }}</span>
                    </a>
                @endif

                <!-- Tickets link for all users (if not admin/staff) -->
                @if(auth()->user()->isCustomer())
                    <a href="{{ route('tickets.index') }}"
                        class="nav-item {{ request()->routeIs('tickets*') ? 'active' : '' }}">
                        <i class="fas fa-headset"></i>
                        <span>{{ __('messages.support_tickets') }}</span>
                        @php
                            $userTicketsCount = \App\Models\Ticket::where('user_id', auth()->id())
                                ->whereIn('status', ['open', 'in_progress'])->count();
                        @endphp
                        @if($userTicketsCount > 0)
                            <span class="nav-badge">{{ $userTicketsCount }}</span>
                        @endif
                    </a>
                @endif

                <a href="{{ route('settings.index') }}"
                    class="nav-item {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>{{ __('messages.settings') }}</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>{{ __('messages.logout') }}</span>
                    </button>
                </form>
            </div>
        </aside>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="top-bar-left">
                    <button id="sidebarToggle" class="icon-btn sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="breadcrumb-container">
                        <h4 class="page-title">@yield('page-title', 'لوحة التحكم')</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                @yield('breadcrumbs')
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="top-bar-search d-none-mobile">
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text"
                            placeholder="{{ __('messages.search_placeholder') ?? 'Search something...' }}"
                            class="search-field">
                    </div>
                </div>

                <div class="top-bar-right">
                    <div class="top-bar-actions">
                        <div class="language-dropdown" style="position: relative;">
                            <button class="icon-btn language-btn" id="languageToggle"
                                title="{{ __('messages.change_language') }}">
                                <i class="fas fa-globe globe-icon"></i>
                            </button>
                            <div class="language-menu" id="languageMenu">
                                <a href="{{ route('switch-language', 'ar') }}"
                                    class="language-option {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                    <span class="language-flag">🇸🇦</span>
                                    <span class="language-name">{{ __('messages.arabic') }}</span>
                                    @if(app()->getLocale() == 'ar')
                                        <i class="fas fa-check"></i>
                                    @endif
                                </a>
                                <a href="{{ route('switch-language', 'en') }}"
                                    class="language-option {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                    <span class="language-flag">🇬🇧</span>
                                    <span class="language-name">{{ __('messages.english') }}</span>
                                    @if(app()->getLocale() == 'en')
                                        <i class="fas fa-check"></i>
                                    @endif
                                </a>
                            </div>
                        </div>

                        <button class="icon-btn theme-toggle-btn" id="themeToggle"
                            title="{{ __('messages.toggle_theme') ?? 'Toggle Theme' }}">
                            <i class="fas fa-moon" id="themeIcon"></i>
                        </button>

                        <a href="{{ route('notifications.index') }}" class="icon-btn notification-btn"
                            title="{{ __('messages.notifications') }}">
                            <i class="far fa-bell"></i>
                            @php
                                $unreadCount = auth()->user()->unreadNotificationsCount();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="badge-notification">{{ $unreadCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('settings.index') }}" class="icon-btn d-none-mobile"
                            title="{{ __('messages.settings') }}">
                            <i class="fas fa-cog"></i>
                        </a>

                        <div class="user-profile-nav" id="userProfileNav" style="position: relative; cursor: pointer;">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff' }}"
                                alt="{{ auth()->user()->name }}" class="nav-user-avatar">
                            <span class="nav-user-name">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down nav-user-arrow"></i>
                            <div class="user-profile-menu" id="userProfileMenu" style="display: none;">
                                <a href="{{ route('settings.index') }}" class="user-profile-menu-item">
                                    <i class="fas fa-user-cog"></i>
                                    <span>{{ __('messages.settings') }}</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="user-profile-menu-item-form">
                                    @csrf
                                    <button type="submit" class="user-profile-menu-item logout-menu-item">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>{{ __('messages.logout') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/toast.js') }}"></script>
    <script src="{{ asset('js/confirm.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        // Set translations for JavaScript
        window.translations = {
            confirm: '{{ __('messages.confirm') }}',
            cancel: '{{ __('messages.cancel') }}',
            ok: '{{ __('messages.ok') }}',
            delete: '{{ __('messages.delete') }}',
            confirm_delete: '{{ __('messages.confirm_delete') }}',
            confirm_delete_title: '{{ __('messages.confirm_delete_title') }}',
            warning: '{{ __('messages.warning') }}',
            info: '{{ __('messages.info') }}'
        };

        // Show toast notifications from session
        @if(session('success'))
            Toast.success('{{ session('success') }}');
        @endif

        @if(session('error'))
            Toast.error('{{ session('error') }}');
        @endif

        @if(session('warning'))
            Toast.warning('{{ session('warning') }}');
        @endif

        @if(session('info'))
            Toast.info('{{ session('info') }}');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                Toast.error('{{ $error }}');
            @endforeach
        @endif
    </script>
    <script>
        // Notifications polling
        (function () {
            const notificationBtn = document.querySelector('.notification-btn');
            if (!notificationBtn) return;

            function updateNotificationCount() {
                fetch('{{ route("notifications.index") }}?read=0', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const badge = notificationBtn.querySelector('.badge-notification');
                        const unreadCount = data.unread_count || 0;

                        if (unreadCount > 0) {
                            if (!badge) {
                                const span = document.createElement('span');
                                span.className = 'badge-notification';
                                notificationBtn.appendChild(span);
                            }
                            notificationBtn.querySelector('.badge-notification').textContent = unreadCount;
                        } else {
                            const badge = notificationBtn.querySelector('.badge-notification');
                            if (badge) {
                                badge.remove();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching notification count:', error);
                    });
            }

            // Update every 30 seconds
            setInterval(updateNotificationCount, 30000);

            // Update on page load
            updateNotificationCount();
        })();
    </script>
    <script>
        // Language dropdown toggle
        (function () {
            const languageToggle = document.getElementById('languageToggle');
            const languageMenu = document.getElementById('languageMenu');
            const languageDropdown = document.querySelector('.language-dropdown');

            if (languageToggle && languageMenu) {
                // Toggle dropdown
                languageToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    languageDropdown.classList.toggle('active');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function (e) {
                    if (!languageDropdown.contains(e.target)) {
                        languageDropdown.classList.remove('active');
                    }
                });

                // Close dropdown when selecting a language
                const languageOptions = languageMenu.querySelectorAll('.language-option');
                languageOptions.forEach(option => {
                    option.addEventListener('click', function () {
                        // The page will reload after language change, so no need to manually close
                    });
                });
            }
        })();

        // User Profile Dropdown
        (function () {
            const userProfileNav = document.getElementById('userProfileNav');
            const userProfileMenu = document.getElementById('userProfileMenu');

            if (userProfileNav && userProfileMenu) {
                // Toggle dropdown
                userProfileNav.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = userProfileMenu.style.display === 'block';
                    userProfileMenu.style.display = isOpen ? 'none' : 'block';
                    userProfileNav.classList.toggle('active', !isOpen);
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function (e) {
                    if (!userProfileNav.contains(e.target)) {
                        userProfileMenu.style.display = 'none';
                    }
                });
            }
        })();

        // Theme Toggle
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const html = document.documentElement;

            if (!themeToggle || !themeIcon) {
                console.error('Theme toggle elements not found');
                return;
            }

            // Get current theme - it should already be set by the script in <head>
            const currentTheme = html.getAttribute('data-theme') || localStorage.getItem('theme') || 'light';

            // Ensure theme is set (fallback)
            if (!html.getAttribute('data-theme')) {
                html.setAttribute('data-theme', currentTheme);
            }

            // Update icon based on current theme
            if (currentTheme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }

            // Add click event listener
            themeToggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const currentTheme = html.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);

                // Update icon
                if (newTheme === 'dark') {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                } else {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
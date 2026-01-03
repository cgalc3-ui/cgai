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
    @stack('styles')
    <script>
        // Check for saved sidebar state before page renders
        (function () {
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
                    <a href="{{ route('admin.bookings') }}"
                        class="nav-item {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>{{ __('messages.bookings') }}</span>
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
                    </a>

                    <div class="nav-section-label">{{ __('messages.more_menu') ?? 'MORE' }}</div>
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
                                <li class="breadcrumb-item"><a href="#">{{ config('app.name') }}</a></li>
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

                        <a href="{{ route('notifications.index') }}" class="icon-btn notification-btn" title="{{ __('messages.notifications') }}">
                            <i class="far fa-bell"></i>
                            @php
                                $unreadCount = auth()->user()->unreadNotificationsCount();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="badge-notification">{{ $unreadCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('settings.index') }}" class="icon-btn d-none-mobile" title="{{ __('messages.settings') }}">
                            <i class="fas fa-cog"></i>
                        </a>

                        <button class="icon-btn theme-toggle" title="{{ __('messages.theme') ?? 'Theme' }}">
                            <i class="far fa-moon"></i>
                        </button>

                        <div class="user-profile-nav">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff' }}"
                                alt="{{ auth()->user()->name }}" class="nav-user-avatar">
                            <span class="nav-user-name">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down nav-user-arrow"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/dashboard.js') }}"></script>
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
    </script>
    @stack('scripts')
</body>

</html>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

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
                    <span class="logo-full">{{ config('app.name') }}</span>
                    <span class="logo-mini">{{ substr(config('app.name'), 0, 2) }}</span>
                </h2>
            </div>

            <nav class="sidebar-nav">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>لوحة التحكم</span>
                    </a>
                    <div class="nav-group">
                        <div class="nav-group-header {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                            onclick="toggleNavGroup(this)">
                            <i class="fas fa-users"></i>
                            <span>المستخدمين</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div class="nav-group-items {{ request()->routeIs('admin.users*') ? 'expanded' : '' }}">
                            <a href="{{ route('admin.users.admins') }}"
                                class="nav-item {{ request()->routeIs('admin.users.admins*') ? 'active' : '' }}">
                                <i class="fas fa-user-shield"></i>
                                <span>الأدمن</span>
                            </a>
                            <a href="{{ route('admin.users.staff') }}"
                                class="nav-item {{ request()->routeIs('admin.users.staff*') ? 'active' : '' }}">
                                <i class="fas fa-user-tie"></i>
                                <span>الموظفين</span>
                            </a>
                            <a href="{{ route('admin.users.customers') }}"
                                class="nav-item {{ request()->routeIs('admin.users.customers*') ? 'active' : '' }}">
                                <i class="fas fa-user"></i>
                                <span>العملاء</span>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.time-slots') }}"
                        class="nav-item {{ request()->routeIs('admin.time-slots') && !request()->routeIs('admin.time-slots.schedules*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>الأوقات المتاحة</span>
                    </a>
                    <a href="{{ route('admin.time-slots.schedules') }}"
                        class="nav-item {{ request()->routeIs('admin.time-slots.schedules*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>المواعيد المتكررة</span>
                    </a>
                    <a href="{{ route('admin.bookings') }}"
                        class="nav-item {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>الحجوزات</span>
                    </a>
                    <div class="nav-group">
                        <div class="nav-group-header {{ request()->routeIs('admin.categories*') || request()->routeIs('admin.sub-categories*') || request()->routeIs('admin.services*') || request()->routeIs('admin.consultations*') ? 'active' : '' }}"
                            onclick="toggleNavGroup(this)">
                            <i class="fas fa-concierge-bell"></i>
                            <span>الخدمات</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </div>
                        <div
                            class="nav-group-items {{ request()->routeIs('admin.categories*') || request()->routeIs('admin.sub-categories*') || request()->routeIs('admin.services*') || request()->routeIs('admin.consultations*') ? 'expanded' : '' }}">
                            <a href="{{ route('admin.categories.index') }}"
                                class="nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                                <i class="fas fa-folder"></i>
                                <span>الفئات</span>
                            </a>
                            <a href="{{ route('admin.sub-categories.index') }}"
                                class="nav-item {{ request()->routeIs('admin.sub-categories*') ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i>
                                <span>الفئات الفرعية</span>
                            </a>
                            <a href="{{ route('admin.services.index') }}"
                                class="nav-item {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
                                <i class="fas fa-cog"></i>
                                <span>الخدمات</span>
                            </a>
                            <a href="{{ route('admin.consultations.index') }}"
                                class="nav-item {{ request()->routeIs('admin.consultations*') ? 'active' : '' }}">
                                <i class="fas fa-comments"></i>
                                <span>الاستشارات</span>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.tickets') }}"
                        class="nav-item {{ request()->routeIs('admin.tickets*') ? 'active' : '' }}">
                        <i class="fas fa-headset"></i>
                        <span>الدعم والتذاكر</span>
                        @php
                            $openTicketsCount = \App\Models\Ticket::whereIn('status', ['open', 'in_progress'])->count();
                        @endphp
                        @if($openTicketsCount > 0)
                            <span class="nav-badge">{{ $openTicketsCount }}</span>
                        @endif
                    </a>
                @elseif(auth()->user()->isStaff())
                    <a href="{{ route('staff.dashboard') }}"
                        class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>لوحة التحكم</span>
                    </a>
                    <a href="{{ route('staff.my-bookings') }}"
                        class="nav-item {{ request()->routeIs('staff.my-bookings') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>حجوزاتي</span>
                    </a>
                    <a href="{{ route('staff.my-schedule') }}"
                        class="nav-item {{ request()->routeIs('staff.my-schedule') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>أيام العمل</span>
                    </a>
                    <a href="{{ route('tickets.index') }}"
                        class="nav-item {{ request()->routeIs('tickets*') ? 'active' : '' }}">
                        <i class="fas fa-headset"></i>
                        <span>الدعم والتذاكر</span>
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
                    <span>الإشعارات</span>
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
                        <span>إدارة الأسئلة الشائعة</span>
                    </a>
                @else
                    <a href="{{ route('faqs.index') }}"
                        class="nav-item {{ request()->routeIs('faqs.index') ? 'active' : '' }}">
                        <i class="fas fa-question-circle"></i>
                        <span>الأسئلة الشائعة</span>
                    </a>
                @endif

                <!-- Tickets link for all users (if not admin/staff) -->
                @if(auth()->user()->isCustomer())
                    <a href="{{ route('tickets.index') }}"
                        class="nav-item {{ request()->routeIs('tickets*') ? 'active' : '' }}">
                        <i class="fas fa-headset"></i>
                        <span>الدعم والتذاكر</span>
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
                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="top-bar-left">
                    <button id="sidebarToggle" class="icon-btn sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'لوحة التحكم')</h1>
                </div>
                <div class="top-bar-right">
                    <div class="top-bar-actions">
                        <a href="{{ route('notifications.index') }}" class="icon-btn notification-btn"
                            title="الإشعارات">
                            <i class="fas fa-bell"></i>
                            @php
                                $unreadCount = auth()->user()->unreadNotificationsCount();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="badge-notification">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <button class="icon-btn settings-btn" title="الإعدادات">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="user-profile">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-details">
                                <div class="user-name">{{ auth()->user()->name }}</div>
                                <div class="user-title">
                                    @if(auth()->user()->isAdmin())
                                        مشرف رئيسي
                                    @elseif(auth()->user()->isStaff())
                                        موظف
                                    @endif
                                </div>
                            </div>
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
    @stack('scripts')
</body>

</html>
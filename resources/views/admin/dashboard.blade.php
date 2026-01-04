@extends('layouts.dashboard')

@section('title', __('messages.admin_dashboard'))
@section('page-title', __('messages.admin_dashboard'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.system_overview') }}</h2>
            <p>{{ __('messages.welcome_back') }}, {{ auth()->user()->name }}</p>
        </div>
    </div>

    <!-- Main Statistics Cards (KPIs) -->
    <div class="stats-grid">
        <!-- Total Bookings -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_bookings') }}</h3>
                <i class="fas fa-ellipsis-v stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle teal">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ number_format($stats['total_bookings'] ?? 0) }}</h2>
                        <span class="stat-card-trend {{ ($stats['bookings_change'] ?? 0) >= 0 ? 'up' : 'down' }}">
                            <i class="fas fa-arrow-{{ ($stats['bookings_change'] ?? 0) >= 0 ? 'up' : 'down' }}"></i> 
                            {{ number_format(abs($stats['bookings_change'] ?? 0), 2) }}%
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ __('messages.since_last_month') }}</span>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_revenue') }}</h3>
                <i class="fas fa-ellipsis-v stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle teal">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ number_format($stats['total_revenue'] ?? 0, 0) }} {{ __('messages.sar') }}</h2>
                        <span class="stat-card-trend {{ ($stats['revenue_change'] ?? 0) >= 0 ? 'up' : 'down' }}">
                            <i class="fas fa-arrow-{{ ($stats['revenue_change'] ?? 0) >= 0 ? 'up' : 'down' }}"></i> 
                            {{ number_format(abs($stats['revenue_change'] ?? 0), 2) }}%
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ __('messages.since_last_month') }}</span>
                </div>
            </div>
        </div>

        <!-- New Customers -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_customers') }}</h3>
                <i class="fas fa-ellipsis-v stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle yellow">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ number_format($stats['total_customers'] ?? 0) }}</h2>
                        <span class="stat-card-trend {{ ($stats['customers_change'] ?? 0) >= 0 ? 'up' : 'down' }}">
                            <i class="fas fa-arrow-{{ ($stats['customers_change'] ?? 0) >= 0 ? 'up' : 'down' }}"></i> 
                            {{ number_format(abs($stats['customers_change'] ?? 0), 2) }}%
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ __('messages.since_last_month') }}</span>
                </div>
            </div>
        </div>

        <!-- Total Services -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_services') }}</h3>
                <i class="fas fa-ellipsis-v stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle light-blue">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ number_format($stats['total_services'] ?? 0) }}</h2>
                        <span class="stat-card-trend up">
                            <i class="fas fa-arrow-up"></i> {{ number_format($stats['active_services'] ?? 0) }}
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ __('messages.active_services') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Row: Statistics & Total Revenue -->
    <div class="row" style="margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <!-- Statistics Card with Bar Chart -->
        <div class="card dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.statistics') }}</h3>
                <i class="fas fa-ellipsis-v card-more"></i>
        </div>
            <div class="card-body">
                <div class="stats-metrics">
                    <div class="metric-item">
                        <span class="metric-label">{{ __('messages.total_bookings') }}</span>
                        <span class="metric-value up">{{ number_format($stats['total_bookings'] ?? 0) }}</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">{{ __('messages.completed_bookings') }}</span>
                        <span class="metric-value up">{{ number_format($stats['completed_bookings'] ?? 0) }}</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">{{ __('messages.pending_bookings') }}</span>
                        <span class="metric-value down">{{ number_format($stats['pending_bookings'] ?? 0) }}</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">{{ __('messages.confirmed_bookings') }}</span>
                        <span class="metric-value">{{ number_format($stats['confirmed_bookings'] ?? 0) }}</span>
                    </div>
                    </div>
                <div class="chart-container">
                    <canvas id="statisticsChart"></canvas>
                    </div>
            </div>
        </div>

        <!-- Total Revenue Card with Area Chart -->
        <div class="card dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.total_revenue') }}</h3>
                <i class="fas fa-ellipsis-v card-more"></i>
                    </div>
            <div class="card-body">
                <div class="stats-metrics">
                    <div class="metric-item">
                        <span class="metric-label">{{ __('messages.total_revenue') }}</span>
                        <span class="metric-value up">{{ number_format($stats['total_revenue'] ?? 0, 0) }} {{ __('messages.sar') }}</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">{{ __('messages.paid_bookings') }}</span>
                        <span class="metric-value up">{{ number_format($stats['paid_bookings'] ?? 0) }}</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">{{ __('messages.unpaid_bookings') }}</span>
                        <span class="metric-value down">{{ number_format($stats['unpaid_bookings'] ?? 0) }}</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card dashboard-card" style="margin-top: 30px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bolt" style="color: #f59e0b; margin-left: 8px;"></i>
                {{ __('messages.quick_actions') }}
                </h3>
            </div>
        <div class="card-body">
            <div class="actions-grid-custom">
                <a href="{{ route('admin.users.staff') }}" class="action-tile tile-blue">
                    <div class="action-icon-box">
                        <i class="fas fa-user-plus"></i>
                            </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.add_staff') }}</span>
                        <span class="action-desc">{{ __('messages.manage_staff_team') }}</span>
                            </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>

                <a href="{{ route('admin.services.index') }}" class="action-tile tile-emerald">
                    <div class="action-icon-box">
                        <i class="fas fa-plus"></i>
                            </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.add_service') }}</span>
                        <span class="action-desc">{{ __('messages.expand_services_list') }}</span>
                        </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>

                <a href="{{ route('admin.time-slots') }}" class="action-tile tile-amber">
                    <div class="action-icon-box">
                        <i class="fas fa-calendar-plus"></i>
                </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.open_appointments') }}</span>
                        <span class="action-desc">{{ __('messages.manage_schedule') }}</span>
            </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>

                <a href="{{ route('admin.bookings') }}" class="action-tile tile-indigo">
                    <div class="action-icon-box">
                        <i class="fas fa-list"></i>
            </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.bookings') }}</span>
                        <span class="action-desc">{{ __('messages.track_all_requests') }}</span>
                            </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>

                <a href="{{ route('admin.tickets') }}" class="action-tile tile-pink">
                    <div class="action-icon-box">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.tickets_support') }}</span>
                        <span class="action-desc">{{ __('messages.manage_tickets_desc') }}</span>
                    </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Transactions, Recent Users, Transactions Uses -->
    <div class="row" style="margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 25px;">
        <!-- Transactions Table -->
        <div class="card dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.transactions') }}</h3>
                <button class="btn-add-new">{{ __('messages.add_new') }}</button>
            </div>
            <div class="card-body">
                <p class="card-summary">{{ $stats['total_bookings'] ?? 0 }} {{ __('messages.total_bookings') }} - {{ $stats['completed_bookings'] ?? 0 }} {{ __('messages.completed') }}</p>
                <div class="table-container-inline">
                    <table class="data-table-inline">
                        <thead>
                            <tr>
                                <th>{{ __('messages.booking_id') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings->take(5) as $booking)
                                <tr>
                                    <td>#{{ $booking->id }}</td>
                                    <td>{{ $booking->created_at ? $booking->created_at->format('Y-m-d') : '-' }}</td>
                                    <td>{{ number_format($booking->total_price ?? 0, 2) }} {{ __('messages.sar') }}</td>
                                    <td>
                                        <span class="status-dot {{ $booking->status === 'completed' ? 'completed' : ($booking->status === 'pending' ? 'pending' : ($booking->status === 'cancelled' ? 'failed' : 'pending')) }}"></span>
                                        @if($booking->status === 'completed') {{ __('messages.completed') }}
                                        @elseif($booking->status === 'pending') {{ __('messages.pending') }}
                                        @elseif($booking->status === 'confirmed') {{ __('messages.confirmed') }}
                                        @else {{ __('messages.cancelled') }} @endif
                                    </td>
                                    <td><i class="fas fa-ellipsis-v"></i></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                                            </div>
                <div class="pagination-inline">
                    <span>{{ __('messages.showing') }} 5 {{ __('messages.of') }} {{ $stats['total_bookings'] ?? 0 }} {{ __('messages.bookings') }}</span>
                    <div class="pagination-controls">
                        <button><i class="fas fa-chevron-right"></i></button>
                        <button class="active">1</button>
                        <button>2</button>
                        <button>3</button>
                        <button><i class="fas fa-chevron-left"></i></button>
                                            </div>
                                        </div>
            </div>
        </div>

        <!-- Recent New Users -->
        <div class="card dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.recent_new_users') }}</h3>
                <div class="card-actions">
                    <button class="btn-import">{{ __('messages.import') }}</button>
                    <button class="btn-export">{{ __('messages.export') }}</button>
                </div>
            </div>
            <div class="card-body">
                <p class="card-summary">{{ $stats['total_customers'] ?? 0 }} {{ __('messages.total_customers') }} - {{ $recentCustomers->count() }} {{ __('messages.recent_customers') }}</p>
                <div class="table-container-inline">
                    <table class="data-table-inline">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.role') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCustomers->take(5) as $customer)
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar-small">{{ mb_substr($customer->name, 0, 1) }}</div>
                                            <span>{{ $customer->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ __('messages.customer') }}</td>
                                    <td>
                                        <span class="status-dot active"></span>
                                        {{ __('messages.active') }}
                                    </td>
                                    <td><i class="fas fa-ellipsis-v"></i></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination-inline">
                    <span>{{ __('messages.showing') }} 5 {{ __('messages.of') }} {{ $recentCustomers->count() }} {{ __('messages.results') }}</span>
                    <div class="pagination-controls">
                        <button><i class="fas fa-chevron-right"></i></button>
                        <button class="active">1</button>
                        <button>2</button>
                        <button><i class="fas fa-chevron-left"></i></button>
            </div>
        </div>
                </div>
            </div>

        <!-- Transactions Uses (Donut Chart) -->
        <div class="card dashboard-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.transactions_uses') }}</h3>
                <button class="btn-refresh"><i class="fas fa-sync-alt"></i> {{ __('messages.refresh') }}</button>
                    </div>
            <div class="card-body">
                <div class="donut-chart-wrapper">
                    <canvas id="bookingsStatusChart" width="200" height="200"></canvas>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-dot" style="background: #10b981;"></span>
                        <span class="legend-label">{{ __('messages.completed') }}</span>
                        <span class="legend-value">{{ $stats['completed_bookings'] ?? 0 }}</span>
                        </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background: #3b82f6;"></span>
                        <span class="legend-label">{{ __('messages.confirmed') }}</span>
                        <span class="legend-value">{{ $stats['confirmed_bookings'] ?? 0 }}</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background: #f59e0b;"></span>
                        <span class="legend-label">{{ __('messages.pending') }}</span>
                        <span class="legend-value">{{ $stats['pending_bookings'] ?? 0 }}</span>
                        </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background: #ef4444;"></span>
                        <span class="legend-label">{{ __('messages.cancelled') }}</span>
                        <span class="legend-value">{{ $stats['cancelled_bookings'] ?? 0 }}</span>
                    </div>
                        </div>
                    </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Dashboard Cards */
            .dashboard-card {
                background: white;
                border-radius: 16px;
                border: 1px solid #f1f5f9;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            [data-theme="dark"] .dashboard-card {
                background: var(--card-bg);
                border-color: var(--border-color);
            }

            .dashboard-card .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 25px;
                border-bottom: 1px solid #f1f5f9;
            }

            [data-theme="dark"] .dashboard-card .card-header {
                border-bottom-color: var(--border-color);
            }

            .dashboard-card .card-title {
                font-size: 18px;
                font-weight: 700;
                color: #1e293b;
                margin: 0;
            }

            [data-theme="dark"] .dashboard-card .card-title {
                color: var(--text-primary);
            }

            .card-more {
                color: #94a3b8;
                cursor: pointer;
                font-size: 14px;
            }

            .card-more:hover {
                color: #64748b;
            }

            .dashboard-card .card-body {
                padding: 25px;
            }

            /* Stats Metrics */
            .stats-metrics {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
                margin-bottom: 25px;
            }

            .metric-item {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .metric-label {
                font-size: 12px;
                color: #94a3b8;
                font-weight: 600;
            }

            [data-theme="dark"] .metric-label {
                color: var(--text-secondary);
            }

            .metric-value {
                font-size: 16px;
                font-weight: 700;
                color: #1e293b;
            }

            [data-theme="dark"] .metric-value {
                color: var(--text-primary);
            }

            .metric-value.up {
                color: #10b981;
            }

            .metric-value.down {
                color: #ef4444;
            }

            .metric-value::before {
                content: '';
                display: inline-block;
                width: 0;
                height: 0;
                margin-left: 5px;
            }

            .metric-value.up::before {
                content: '↑';
                color: #10b981;
            }

            .metric-value.down::before {
                content: '↓';
                color: #ef4444;
            }

            /* Chart Container */
            .chart-container {
                height: 250px !important;
                min-height: 250px;
                position: relative;
                width: 100%;
            }
            
            .chart-container canvas {
                width: 100% !important;
                height: 100% !important;
            }

            /* Card Summary */
            .card-summary {
                font-size: 13px;
                color: #64748b;
                margin-bottom: 20px;
            }

            [data-theme="dark"] .card-summary {
                color: var(--text-secondary);
            }

            /* Inline Table */
            .table-container-inline {
                overflow-x: auto;
            }

            .data-table-inline {
                width: 100%;
                border-collapse: collapse;
            }

            .data-table-inline thead {
                border-bottom: 1px solid #f1f5f9;
            }

            [data-theme="dark"] .data-table-inline thead {
                border-bottom-color: var(--border-color);
            }

            .data-table-inline th {
                padding: 12px 8px;
                text-align: right;
                font-size: 12px;
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
            }

            [data-theme="dark"] .data-table-inline th {
                color: var(--text-secondary);
            }

            .data-table-inline td {
                padding: 12px 8px;
                font-size: 13px;
                color: #1e293b;
                border-bottom: 1px solid #f8fafc;
            }

            [data-theme="dark"] .data-table-inline td {
                color: var(--text-primary);
                border-bottom-color: var(--border-color);
            }

            .data-table-inline tbody tr:hover {
                background: #f8fafc;
            }

            [data-theme="dark"] .data-table-inline tbody tr:hover {
                background: var(--sidebar-active-bg);
            }

            /* Status Dot */
            .status-dot {
                display: inline-block;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                margin-left: 6px;
            }

            .status-dot.completed {
                background: #10b981;
            }

            .status-dot.pending {
                background: #f59e0b;
            }

            .status-dot.failed {
                background: #ef4444;
            }

            .status-dot.active {
                background: #10b981;
            }

            /* User Cell */
            .user-cell {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .user-avatar-small {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: #4fc6e1;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
            }

            /* Buttons */
            .btn-add-new,
            .btn-import,
            .btn-export,
            .btn-refresh {
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                background: #4fc6e1;
                color: white;
                transition: all 0.2s;
            }

            .btn-add-new:hover,
            .btn-import:hover,
            .btn-export:hover,
            .btn-refresh:hover {
                background: #3ba8c1;
                transform: translateY(-1px);
            }

            .card-actions {
                display: flex;
                gap: 8px;
            }

            /* Pagination Inline */
            .pagination-inline {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #f1f5f9;
            }

            [data-theme="dark"] .pagination-inline {
                border-top-color: var(--border-color);
            }

            .pagination-inline span {
                font-size: 12px;
                color: #64748b;
            }

            [data-theme="dark"] .pagination-inline span {
                color: var(--text-secondary);
            }

            .pagination-controls {
                display: flex;
                gap: 5px;
            }

            .pagination-controls button {
                width: 28px;
                height: 28px;
                border-radius: 6px;
                border: 1px solid #e2e8f0;
                background: white;
                color: #64748b;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                transition: all 0.2s;
            }

            [data-theme="dark"] .pagination-controls button {
                background: var(--sidebar-active-bg);
                border-color: var(--border-color);
                color: var(--text-secondary);
            }

            .pagination-controls button:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
            }

            [data-theme="dark"] .pagination-controls button:hover {
                background: var(--bg-light);
                border-color: var(--primary-color);
            }

            .pagination-controls button.active {
                background: #4fc6e1;
                color: white;
                border-color: #4fc6e1;
            }

            /* Donut Chart */
            .donut-chart-wrapper {
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 20px 0;
            }

            .chart-legend {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .legend-item {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .legend-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
            }

            .legend-label {
                flex: 1;
                font-size: 13px;
                color: #64748b;
                font-weight: 600;
            }

            [data-theme="dark"] .legend-label {
                color: var(--text-secondary);
            }

            .legend-value {
                font-size: 13px;
                font-weight: 700;
                color: #1e293b;
            }

            [data-theme="dark"] .legend-value {
                color: var(--text-primary);
            }

            /* Stat Card Icon Colors */
            .stat-card-icon-circle.teal {
                background: rgba(20, 184, 166, 0.1);
                color: #14b8a6;
            }

            .stat-card-icon-circle.yellow {
                background: rgba(234, 179, 8, 0.1);
                color: #eab308;
            }

            .stat-card-icon-circle.light-blue {
                background: rgba(59, 130, 246, 0.1);
                color: #3b82f6;
            }

            .stat-card-trend.down {
                color: #ef4444;
            }

            /* Quick Actions Grid */
            .actions-grid-custom {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 20px;
            }

            .action-tile {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 20px;
                background: #fff;
                border: 1px solid #f1f5f9;
                border-radius: 16px;
                text-decoration: none;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            [data-theme="dark"] .action-tile {
                background: var(--card-bg);
                border-color: var(--border-color);
            }

            .action-tile:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.05);
                border-color: #e2e8f0;
            }

            [data-theme="dark"] .action-tile:hover {
                border-color: var(--primary-color);
            }

            .action-icon-box {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                transition: all 0.3s;
            }

            .tile-blue .action-icon-box {
                background: rgba(59, 130, 246, 0.1);
                color: #3b82f6;
            }

            .tile-emerald .action-icon-box {
                background: rgba(16, 185, 129, 0.1);
                color: #10b981;
            }

            .tile-amber .action-icon-box {
                background: rgba(245, 158, 11, 0.1);
                color: #f59e0b;
            }

            .tile-indigo .action-icon-box {
                background: rgba(99, 102, 241, 0.1);
                color: #6366f1;
            }

            .tile-pink .action-icon-box {
                background: rgba(236, 72, 153, 0.1);
                color: #ec4899;
            }

            .action-tile:hover .action-icon-box {
                transform: scale(1.1);
            }

            .action-info {
                display: flex;
                flex-direction: column;
                gap: 2px;
            }

            .action-name {
                font-size: 15px;
                font-weight: 700;
                color: #1e293b;
            }

            [data-theme="dark"] .action-name {
                color: var(--text-primary);
            }

            .action-desc {
                font-size: 12px;
                color: #94a3b8;
                font-weight: 500;
            }

            [data-theme="dark"] .action-desc {
                color: var(--text-secondary);
            }

            .action-arrow {
                margin-right: auto;
                color: #cbd5e1;
                font-size: 14px;
                transition: transform 0.3s;
            }

            .action-tile:hover .action-arrow {
                transform: translateX(-5px);
                color: #64748b;
            }

            @media (max-width: 1200px) {
                .row {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            // Wait for Chart.js to load
            function initCharts() {
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js is not loaded');
                    setTimeout(initCharts, 100);
                    return;
                }
                
                // Statistics Bar Chart - Bookings by Month
                const statisticsCtx = document.getElementById('statisticsChart');
                if (statisticsCtx) {
                    const bookingsChartData = {!! json_encode($stats['bookings_chart_data'] ?? ['labels' => [], 'open_campaign' => [], 'marketing_cost' => []]) !!};
                    
                    console.log('Bookings Chart Data:', bookingsChartData);
                    
                    try {
                        // Ensure we have data
                        const labels = bookingsChartData.labels && bookingsChartData.labels.length > 0 
                            ? bookingsChartData.labels 
                            : ['Jan', 'Feb', 'Mar', 'Apr', 'May'];
                        const openCampaign = bookingsChartData.open_campaign && bookingsChartData.open_campaign.length > 0 
                            ? bookingsChartData.open_campaign 
                            : [80, 90, 100, 85, 95];
                        const marketingCost = bookingsChartData.marketing_cost && bookingsChartData.marketing_cost.length > 0 
                            ? bookingsChartData.marketing_cost 
                            : [40, 45, 50, 42, 48];
                        
                        new Chart(statisticsCtx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: '{{ __('messages.open_campaign') }}',
                                    data: openCampaign,
                                    backgroundColor: '#14b8a6',
                                    borderRadius: 4
                                }, {
                                    label: '{{ __('messages.marketing_cost') }}',
                                    data: marketingCost,
                                    backgroundColor: '#8b5cf6',
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 5,
                                    font: {
                                        size: 10
                                    },
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches || document.documentElement.getAttribute('data-theme') === 'dark' ? '#9ca3af' : '#6b7280'
                                },
                                grid: {
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches || document.documentElement.getAttribute('data-theme') === 'dark' ? '#374151' : '#f1f5f9'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches || document.documentElement.getAttribute('data-theme') === 'dark' ? '#9ca3af' : '#6b7280'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                    });
                    console.log('Statistics Chart created successfully');
                    } catch (error) {
                        console.error('Error creating Statistics Chart:', error);
                    }
                } else {
                    console.error('Statistics Chart: Canvas not found');
                }

                // Revenue Area Chart - Revenue by Month
                const revenueCtx = document.getElementById('revenueChart');
                if (revenueCtx) {
                    const revenueData = {!! json_encode($stats['revenue_by_month_12'] ?? []) !!};
                    const expensesData = {!! json_encode($stats['expenses_by_month_12'] ?? []) !!};
                    const monthLabels = {!! json_encode($stats['month_labels'] ?? []) !!};
                    
                    console.log('Revenue Chart Data:', { revenueData, expensesData, monthLabels });
                    
                    try {
                        // Ensure we have data
                        const labels = monthLabels && monthLabels.length > 0 
                            ? monthLabels 
                            : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        const expenses = expensesData && expensesData.length > 0 
                            ? expensesData 
                            : [20, 25, 30, 28, 35, 32, 40, 38, 45, 42, 50, 48];
                        const revenue = revenueData && revenueData.length > 0 
                            ? revenueData 
                            : [60, 65, 70, 68, 75, 72, 80, 78, 85, 82, 90, 88];
                        
                        new Chart(revenueCtx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: '{{ __('messages.total_expenses') }}',
                                    data: expenses,
                                    backgroundColor: 'rgba(20, 184, 166, 0.6)',
                                    borderColor: '#14b8a6',
                                    borderWidth: 0,
                                    fill: true,
                                    tension: 0.4,
                                    order: 1
                                }, {
                                    label: '{{ __('messages.total_income') }}',
                                    data: revenue,
                                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                    borderColor: '#10b981',
                                    borderWidth: 0,
                                    fill: true,
                                    tension: 0.4,
                                    order: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                stacked: false,
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches || document.documentElement.getAttribute('data-theme') === 'dark' ? '#9ca3af' : '#6b7280'
                                },
                                grid: {
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches || document.documentElement.getAttribute('data-theme') === 'dark' ? '#374151' : '#f1f5f9'
                                }
                            },
                            x: {
                                stacked: false,
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches || document.documentElement.getAttribute('data-theme') === 'dark' ? '#9ca3af' : '#6b7280'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                    });
                    console.log('Revenue Chart created successfully');
                    } catch (error) {
                        console.error('Error creating Revenue Chart:', error);
                    }
                } else {
                    console.error('Revenue Chart: Canvas not found');
                }

                // Bookings Status Donut Chart
                const bookingsStatusCtx = document.getElementById('bookingsStatusChart');
                if (bookingsStatusCtx) {
                    const statusData = {!! json_encode($stats['bookings_status_data'] ?? []) !!};
                    const total = (statusData.completed || 0) + (statusData.confirmed || 0) + (statusData.pending || 0) + (statusData.cancelled || 0);
                    
                    console.log('Status Chart Data:', statusData, 'Total:', total);
                    
                    if (total > 0) {
                        try {
                            new Chart(bookingsStatusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: [
                                '{{ __('messages.completed') }}',
                                '{{ __('messages.confirmed') }}',
                                '{{ __('messages.pending') }}',
                                '{{ __('messages.cancelled') }}'
                            ],
                            datasets: [{
                                data: [
                                    statusData.completed || 0,
                                    statusData.confirmed || 0,
                                    statusData.pending || 0,
                                    statusData.cancelled || 0
                                ],
                                backgroundColor: [
                                    '#10b981',
                                    '#3b82f6',
                                    '#f59e0b',
                                    '#ef4444'
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            cutout: '70%'
                        }
                        });
                        console.log('Status Chart created successfully');
                        } catch (error) {
                            console.error('Error creating Status Chart:', error);
                        }
                    } else {
                        console.log('Status Chart: No data to display (total is 0)');
                    }
                } else {
                    console.error('Status Chart: Canvas not found');
                }
            }
            
            // Initialize charts when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCharts);
            } else {
                initCharts();
            }
        </script>
    @endpush
@endsection

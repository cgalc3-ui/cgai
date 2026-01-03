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

    <!-- Main Statistics Cards -->
    <!-- Main Statistics Cards -->
    <div class="stats-grid">
        <!-- Total Revenue -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_revenue') }}</h3>
                <i class="fas fa-ellipsis-h stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle pink">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ number_format($stats['total_revenue'] ?? 0, 0) }}</h2>
                        <span class="stat-card-trend up">
                            <i class="fas fa-arrow-up"></i> +{{ number_format(15.5, 2) }}%
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ __('messages.compared_to_last_month') }}</span>
                </div>
            </div>
            <div class="stat-card-chart">
                <svg viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="M0,25 C10,20 20,28 30,22 C40,16 50,18 60,12 C70,6 80,10 90,8 C100,6 100,5 100,5" fill="none"
                        stroke="#f1556c" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_bookings') }}</h3>
                <i class="fas fa-ellipsis-h stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle blue">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ $stats['total_bookings'] ?? 0 }}</h2>
                        <span class="stat-card-trend up">
                            <i class="fas fa-arrow-up"></i> +{{ number_format(8.2, 2) }}%
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ $stats['today_bookings'] ?? 0 }}
                        {{ __('messages.today_bookings') }}</span>
                </div>
            </div>
            <div class="stat-card-chart">
                <svg viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="M0,20 C10,25 20,15 30,18 C40,21 50,10 60,15 C70,20 80,5 90,10 C100,15 100,10 100,10"
                        fill="none" stroke="#4fc6e1" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_customers') }}</h3>
                <i class="fas fa-ellipsis-h stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle orange">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ $stats['total_customers'] ?? 0 }}</h2>
                        <span class="stat-card-trend up">
                            <i class="fas fa-arrow-up"></i> +{{ number_format(12.1, 2) }}%
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ $recentCustomers->count() }}
                        {{ __('messages.new_customers') }}</span>
                </div>
            </div>
            <div class="stat-card-chart">
                <svg viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="M0,28 C10,22 20,25 30,20 C40,15 50,22 60,18 C70,14 80,18 90,12 C100,6 100,5 100,5" fill="none"
                        stroke="#f7b84b" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
        </div>

        <!-- Total Employees -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_staff') }}</h3>
                <i class="fas fa-ellipsis-h stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle purple">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ $stats['total_employees'] ?? 0 }}</h2>
                        <span class="stat-card-trend {{ ($stats['total_employees'] ?? 0) > 0 ? 'up' : 'down' }}">
                            <i class="fas fa-users"></i>
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ $stats['total_categories'] ?? 0 }}
                        {{ __('messages.different_categories') }}</span>
                </div>
            </div>
            <div class="stat-card-chart">
                <svg viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="M0,15 C10,12 20,18 30,15 C40,12 50,20 60,15 C70,10 80,12 90,10 C100,8 100,10 100,10"
                        fill="none" stroke="#6658dd" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
        </div>
    </div>



    <!-- Quick Actions -->
    <div class="card"
        style="margin-top: 30px; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div class="card-header" style="padding: 20px 25px; border-bottom: 1px solid #f1f5f9;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-bolt" style="color: #f59e0b;"></i> {{ __('messages.quick_actions') }}
            </h3>
        </div>
        <div class="card-body" style="padding: 25px;">
            <div class="actions-grid-custom">
                <a href="{{ route('admin.users.staff.create') }}" class="action-tile tile-blue">
                    <div class="action-icon-box">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.add_staff') }}</span>
                        <span class="action-desc">{{ __('messages.manage_staff_team') }}</span>
                    </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>

                <a href="{{ route('admin.services.create') }}" class="action-tile tile-emerald">
                    <div class="action-icon-box">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.add_service') }}</span>
                        <span class="action-desc">{{ __('messages.expand_services_list') }}</span>
                    </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>

                <a href="{{ route('admin.time-slots.create') }}" class="action-tile tile-amber">
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
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 30px; display: grid; grid-template-columns: 2fr 1.2fr; gap: 25px;">
        <!-- Recent Bookings Table -->
        <div class="card">
            <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b;"><i class="fas fa-history color-emerald"></i>
                    {{ __('messages.recent_bookings') }}</h3>
                <a href="{{ route('admin.bookings') }}" class="btn btn-sm btn-info"
                    style="border-radius: 8px;">{{ __('messages.view_all') }}</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container" style="box-shadow: none; border-radius: 0; border: none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="padding-right: 25px;">{{ __('messages.client') }}</th>
                                <th>{{ __('messages.service') }}</th>
                                <th>{{ __('messages.time') }}</th>
                                <th style="text-align: center;">{{ __('messages.status') }}</th>
                                <th style="text-align: center;">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings->take(6) as $booking)
                                <tr class="table-row-hover">
                                    <td style="padding-right: 25px;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div class="user-avatar-mini" style="background: #f1f5f9; color: #475569;">
                                                {{ mb_substr($booking->customer->name ?? '?', 0, 1) }}
                                            </div>
                                            <div style="display: flex; flex-direction: column;">
                                                <span
                                                    style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ $booking->customer->name ?? __('messages.unknown') }}</span>
                                                <span style="font-size: 11px; color: #94a3b8;">#{{ $booking->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            style="display: flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; font-size: 13px;">
                                            <i class="fas fa-magic" style="font-size: 12px; opacity: 0.7;"></i>
                                            @if($booking->booking_type === 'consultation')
                                                <i class="fas fa-comments" style="margin-left: 5px;"></i>
                                                {{ $booking->consultation->name ?? '-' }}
                                            @else
                                                {{ $booking->service->name ?? '-' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">
                                            <i class="far fa-clock" style="margin-left: 4px;"></i>
                                            {{ $booking->created_at ? $booking->created_at->diffForHumans() : '-' }}
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="status-pill {{ $booking->status }}">
                                            @if($booking->status == 'pending') {{ __('messages.pending') }}
                                            @elseif($booking->status == 'confirmed')
                                            {{ __('messages.confirmed') }} @elseif($booking->status == 'completed')
                                            {{ __('messages.completed') }} @else {{ __('messages.cancelled') }} @endif
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="calm-action-btn"
                                            title="{{ __('messages.view') }}">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Booking Status Summary (Donut Chart) -->
        <div class="card donut-report-card">
            <div class="report-header">
                <div class="report-title-group">
                    <h3 class="report-title">{{ __('messages.status_summary') }}</h3>
                    <div class="report-date">
                        <i class="far fa-calendar-alt"></i>
                        <span>{{ now()->startOfMonth()->translatedFormat('d M') }} -
                            {{ now()->endOfMonth()->translatedFormat('d M') }}</span>
                    </div>
                </div>
                <div class="report-total">
                    @php
                        $total_bookings = ($stats['pending_bookings'] ?? 0) + ($stats['confirmed_bookings'] ?? 0) + ($stats['completed_bookings'] ?? 0) + ($stats['cancelled_bookings'] ?? 0);
                    @endphp
                    <span class="total-value">{{ $total_bookings }}</span>
                    <span class="total-label">حجز</span>
                </div>
            </div>

            <div class="report-body" style="padding: 20px;">
                @php
                    $safe_total = $total_bookings > 0 ? $total_bookings : 1;
                    $p_pending = (($stats['pending_bookings'] ?? 0) / $safe_total) * 100;
                    $p_confirmed = (($stats['confirmed_bookings'] ?? 0) / $safe_total) * 100;
                    $p_completed = (($stats['completed_bookings'] ?? 0) / $safe_total) * 100;
                    $p_cancelled = (($stats['cancelled_bookings'] ?? 0) / $safe_total) * 100;
                    $circumference = 2 * pi() * 35;
                    $offset = 0;
                @endphp

                <div class="donut-chart-container" style="width: 140px; height: 140px;">
                    <svg viewBox="0 0 100 100" class="donut-svg">
                        <circle cx="50" cy="50" r="35" fill="transparent" stroke="#f1f5f9" stroke-width="10" />
                        @if($total_bookings > 0)
                            <circle cx="50" cy="50" r="35" fill="transparent" stroke="#6366f1" stroke-width="10"
                                stroke-dasharray="{{ ($p_completed / 100) * $circumference }} {{ $circumference }}"
                                stroke-dashoffset="0" transform="rotate(-90 50 50)" />
                            @php $offset -= ($p_completed / 100) * $circumference; @endphp
                            <circle cx="50" cy="50" r="35" fill="transparent" stroke="#06b6d4" stroke-width="10"
                                stroke-dasharray="{{ ($p_confirmed / 100) * $circumference }} {{ $circumference }}"
                                stroke-dashoffset="{{ $offset }}" transform="rotate(-90 50 50)" />
                            @php $offset -= ($p_confirmed / 100) * $circumference; @endphp
                            <circle cx="50" cy="50" r="35" fill="transparent" stroke="#f59e0b" stroke-width="10"
                                stroke-dasharray="{{ ($p_pending / 100) * $circumference }} {{ $circumference }}"
                                stroke-dashoffset="{{ $offset }}" transform="rotate(-90 50 50)" />
                            @php $offset -= ($p_pending / 100) * $circumference; @endphp
                            <circle cx="50" cy="50" r="35" fill="transparent" stroke="#ef4444" stroke-width="10"
                                stroke-dasharray="{{ ($p_cancelled / 100) * $circumference }} {{ $circumference }}"
                                stroke-dashoffset="{{ $offset }}" transform="rotate(-90 50 50)" />
                        @endif
                    </svg>
                    <div class="donut-inner-text">
                        <span class="inner-percent"
                            style="font-size: 20px;">{{ round(($p_completed + $p_confirmed)) }}%</span>
                        <span class="inner-label" style="font-size: 10px;">نشط</span>
                    </div>
                </div>

                <div class="report-legend" style="gap: 8px;">
                    <div class="legend-item" style="padding-bottom: 5px;">
                        <div class="legend-info">
                            <span class="legend-badge"
                                style="background: #6366f1; min-width: 35px; padding: 1px 5px; font-size: 10px;">Com</span>
                            <span class="legend-name" style="font-size: 12px;">{{ __('messages.completed') }}</span>
                        </div>
                        <span class="legend-value" style="font-size: 12px;">{{ round($p_completed, 1) }}%</span>
                    </div>
                    <div class="legend-item" style="padding-bottom: 5px;">
                        <div class="legend-info">
                            <span class="legend-badge"
                                style="background: #06b6d4; min-width: 35px; padding: 1px 5px; font-size: 10px;">Con</span>
                            <span class="legend-name" style="font-size: 12px;">{{ __('messages.confirmed') }}</span>
                        </div>
                        <span class="legend-value" style="font-size: 12px;">{{ round($p_confirmed, 1) }}%</span>
                    </div>
                    <div class="legend-item" style="padding-bottom: 5px;">
                        <div class="legend-info">
                            <span class="legend-badge"
                                style="background: #f59e0b; min-width: 35px; padding: 1px 5px; font-size: 10px;">Pen</span>
                            <span class="legend-name" style="font-size: 12px;">{{ __('messages.pending') }}</span>
                        </div>
                        <span class="legend-value" style="font-size: 12px;">{{ round($p_pending, 1) }}%</span>
                    </div>
                    <div class="legend-item" style="padding-bottom: 5px;">
                        <div class="legend-info">
                            <span class="legend-badge"
                                style="background: #ef4444; min-width: 35px; padding: 1px 5px; font-size: 10px;">Can</span>
                            <span class="legend-name" style="font-size: 12px;">{{ __('messages.cancelled') }}</span>
                        </div>
                        <span class="legend-value" style="font-size: 12px;">{{ round($p_cancelled, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Staff & Customers Row -->
    <div class="row" style="margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <!-- Recent Staff Section -->
        <div class="card premium-list-card">
            <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b;">
                    <i class="fas fa-user-tie color-indigo" style="margin-left: 8px;"></i>
                    {{ __('messages.recent_staff_added') }}
                </h3>
                <a href="{{ route('admin.users.staff') }}" class="btn-view-all">{{ __('messages.view_all') }}</a>
            </div>
            <div class="card-body" style="padding: 0 25px 25px 25px;">
                <div class="premium-list">
                    @foreach($recentStaff->take(4) as $staff)
                        <div class="premium-list-item">
                            <div class="item-avatar" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                {{ mb_substr($staff->name, 0, 1) }}
                            </div>
                            <div class="item-details">
                                <span class="item-title">{{ $staff->name }}</span>
                                <span
                                    class="item-subtitle">{{ $staff->employee?->specialization ?? __('messages.staff_in_system') }}</span>
                            </div>
                            <div class="item-action" style="display: flex; align-items: center; gap: 10px;">
                                <div class="status-pill confirmed" style="font-size: 10px; padding: 2px 8px;">
                                    {{ __('messages.active') }}
                                </div>
                                <a href="{{ route('admin.users.staff.edit', $staff->id) }}" class="calm-action-btn sm">
                                    <i class="far fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Customers Section -->
        <div class="card premium-list-card">
            <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b;">
                    <i class="fas fa-users color-emerald" style="margin-left: 8px;"></i>
                    {{ __('messages.recent_customers_joined') }}
                </h3>
                <a href="{{ route('admin.users.customers') }}" class="btn-view-all">{{ __('messages.view_all') }}</a>
            </div>
            <div class="card-body" style="padding: 0 25px 25px 25px;">
                <div class="premium-list">
                    @foreach($recentCustomers->take(4) as $customer)
                        <div class="premium-list-item">
                            <div class="item-avatar" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                {{ mb_substr($customer->name, 0, 1) }}
                            </div>
                            <div class="item-details">
                                <span class="item-title">{{ $customer->name }}</span>
                                <span class="item-subtitle">{{ __('messages.joined') }}
                                    {{ $customer->created_at ? $customer->created_at->diffForHumans() : __('messages.recently') }}</span>
                            </div>
                            <div class="item-action" style="display: flex; align-items: center; gap: 10px;">
                                <div class="status-pill pending"
                                    style="font-size: 10px; padding: 2px 8px; background: #f0fdf4; color: #16a34a;">
                                    {{ __('messages.customer_role') }}
                                </div>
                                <a href="{{ route('admin.users.customers.show', $customer->id) }}" class="calm-action-btn sm">
                                    <i class="far fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Donut Report Card */
            .donut-report-card {
                border-radius: 16px;
                border: 1px solid #f1f5f9;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                padding: 0 !important;
                overflow: hidden;
            }

            .report-header {
                padding: 25px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #f1f5f9;
            }

            .report-title {
                font-size: 18px;
                font-weight: 700;
                color: #1e293b;
                margin: 0 0 5px 0;
            }

            .report-date {
                font-size: 13px;
                color: #94a3b8;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .report-total {
                text-align: left;
                display: flex;
                flex-direction: column;
            }

            .total-value {
                font-size: 22px;
                font-weight: 800;
                color: #1e293b;
                line-height: 1;
            }

            .total-label {
                font-size: 12px;
                color: #94a3b8;
                font-weight: 600;
            }

            .report-body {
                padding: 30px 25px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 30px;
            }

            .donut-chart-container {
                position: relative;
                width: 180px;
                height: 180px;
            }

            .donut-svg {
                width: 100%;
                height: 100%;
            }

            .donut-inner-text {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .inner-percent {
                font-size: 24px;
                font-weight: 800;
                color: #1e293b;
            }

            .inner-label {
                font-size: 12px;
                color: #94a3b8;
                font-weight: 700;
            }

            .report-legend {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .legend-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-bottom: 10px;
                border-bottom: 1px solid #f8fafc;
            }

            .legend-info {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .legend-badge {
                padding: 2px 8px;
                border-radius: 6px;
                color: white;
                font-size: 11px;
                font-weight: 700;
                min-width: 45px;
                text-align: center;
            }

            .legend-name {
                font-size: 14px;
                font-weight: 600;
                color: #64748b;
            }

            .legend-value {
                font-size: 14px;
                font-weight: 700;
                color: #1e293b;
            }

            /* Premium Table Enhancements */
            .user-avatar-mini {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                font-weight: 700;
                flex-shrink: 0;
            }

            .table-row-hover {
                transition: all 0.2s ease;
            }

            .table-row-hover:hover {
                background-color: #f8fafc !important;
            }

            .status-badge {
                padding: 4px 10px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 700;
                display: inline-block;
            }

            .status-badge.completed {
                background: #d1fae5;
                color: #065f46;
            }

            .status-badge.cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            /* Calm Design System */
            .status-pill {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .status-pill.pending {
                background: #fffbeb;
                color: #d97706;
            }

            .status-pill.confirmed {
                background: #eff6ff;
                color: #3b82f6;
            }

            .status-pill.completed {
                background: #ecfdf5;
                color: #059669;
            }

            .status-pill.cancelled {
                background: #fff1f2;
                color: #e11d48;
            }

            .calm-action-btn {
                width: 34px;
                height: 34px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                background: #f8fafc;
                color: #64748b;
                border: 1px solid #e2e8f0;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                text-decoration: none;
            }

            .calm-action-btn i {
                font-size: 14px;
            }

            .calm-action-btn:hover {
                background: #eff6ff;
                color: #3b82f6;
                border-color: #bfdbfe;
                transform: translateY(-2px);
                box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
            }

            .calm-action-btn.sm {
                width: 28px;
                height: 28px;
                border-radius: 8px;
            }

            .calm-action-btn.sm i {
                font-size: 12px;
            }

            /* Premium List Cards */
            .premium-list-card {
                border-radius: 16px;
                border: 1px solid #f1f5f9;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            .btn-view-all {
                font-size: 12px;
                font-weight: 700;
                color: #6366f1;
                text-decoration: none;
                padding: 6px 12px;
                background: rgba(99, 102, 241, 0.05);
                border-radius: 8px;
                transition: all 0.2s;
            }

            .btn-view-all:hover {
                background: rgba(99, 102, 241, 0.1);
                transform: translateY(-1px);
            }

            .premium-list {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .premium-list-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px;
                border-radius: 12px;
                transition: all 0.2s;
            }

            .premium-list-item:hover {
                background: #f8fafc;
            }

            .item-avatar {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                font-weight: 800;
                flex-shrink: 0;
            }

            .item-details {
                display: flex;
                flex-direction: column;
                flex-grow: 1;
            }

            .item-title {
                font-size: 14px;
                font-weight: 700;
                color: #1e293b;
            }

            .item-subtitle {
                font-size: 12px;
                color: #94a3b8;
                font-weight: 500;
            }

            .item-action {
                flex-shrink: 0;
            }

            .item-label {
                font-size: 10px;
                font-weight: 800;
                padding: 3px 8px;
                border-radius: 6px;
                text-transform: uppercase;
            }

            .item-label.indigo {
                background: rgba(99, 102, 241, 0.1);
                color: #6366f1;
            }

            .item-label.emerald {
                background: rgba(16, 185, 129, 0.1);
                color: #10b981;
            }

            /* Premium Quick Actions */
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

            .action-tile:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.05);
                border-color: #e2e8f0;
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

            .tile-purple .action-icon-box {
                background: rgba(139, 92, 246, 0.1);
                color: #8b5cf6;
            }

            .action-tile:hover .action-icon-box {
                transform: scale(1.1);
            }

            .tile-blue:hover {
                background: linear-gradient(135deg, #fff 60%, rgba(59, 130, 246, 0.05) 100%);
            }

            .tile-emerald:hover {
                background: linear-gradient(135deg, #fff 60%, rgba(16, 185, 129, 0.05) 100%);
            }

            .tile-amber:hover {
                background: linear-gradient(135deg, #fff 60%, rgba(245, 158, 11, 0.05) 100%);
            }

            .tile-indigo:hover {
                background: linear-gradient(135deg, #fff 60%, rgba(99, 102, 241, 0.05) 100%);
            }

            .tile-purple:hover {
                background: linear-gradient(135deg, #fff 60%, rgba(139, 92, 246, 0.05) 100%);
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

            .action-desc {
                font-size: 12px;
                color: #94a3b8;
                font-weight: 500;
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

            .color-blue {
                color: #3b82f6;
            }

            .color-amber {
                color: #f59e0b;
            }

            .color-emerald {
                color: #10b981;
            }

            .color-indigo {
                color: #6366f1;
            }

            @media (max-width: 900px) {
                .row {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    @endpush
@endsection
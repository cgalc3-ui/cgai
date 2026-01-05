@extends('layouts.dashboard')

@section('title', __('messages.invoice_details'))
@section('page-title', __('messages.invoice_details'))

@section('content')
    <div class="booking-modern-container">
        <!-- Header -->
        <header class="booking-m-header">
            <div class="m-header-main">
                <h1 class="m-title">{{ __('messages.invoice') }} #INV-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h1>
                <p class="m-subtitle">{{ __('messages.issued_date') }} 
                    @php
                        $issuedDate = null;
                        if ($booking->paid_at) {
                            $issuedDate = $booking->paid_at instanceof \Carbon\Carbon 
                                ? $booking->paid_at 
                                : \Carbon\Carbon::parse($booking->paid_at);
                        } elseif ($booking->created_at) {
                            $issuedDate = $booking->created_at instanceof \Carbon\Carbon 
                                ? $booking->created_at 
                                : \Carbon\Carbon::parse($booking->created_at);
                        }
                    @endphp
                    {{ $issuedDate ? $issuedDate->format('Y-m-d H:i') : '---' }}
                </p>
            </div>
            <div class="m-header-actions">
                <a href="{{ route('admin.invoices.download', $booking->id) }}" target="_blank" class="m-btn-back" style="background: #3b82f6; color: white; border-color: #3b82f6;">
                    <i class="fas fa-download"></i> {{ __('messages.download_pdf') }}
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="m-btn-back">
                    <i class="fas fa-long-arrow-alt-right"></i> {{ __('messages.back_to_invoices') }}
                </a>
            </div>
        </header>

        <!-- Stats Row -->
        <div class="m-stats-row">
            <div class="m-stat-card">
                <div class="m-stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
                <div class="m-stat-info">
                    <span class="m-stat-label">{{ __('messages.payment_status') }}</span>
                    <span class="m-stat-status payment-paid">{{ __('messages.paid_successfully') }}</span>
                </div>
            </div>
            <div class="m-stat-card">
                <div class="m-stat-icon icon-purple"><i class="fas fa-tag"></i></div>
                <div class="m-stat-info">
                    <span class="m-stat-label">{{ __('messages.total_amount') }}</span>
                    <span class="m-stat-value">{{ number_format($booking->total_price, 2) }} {{ __('messages.sar') }}</span>
                </div>
            </div>
            <div class="m-stat-card">
                <div class="m-stat-icon icon-blue"><i class="fas fa-calendar-check"></i></div>
                <div class="m-stat-info">
                    <span class="m-stat-label">{{ __('messages.booking_date') }}</span>
                    <span class="m-stat-status">{{ $booking->booking_date->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        <div class="m-content-grid">
            <!-- Main Info -->
            <div class="m-main-column">
                <!-- Service Section -->
                <div class="m-card">
                    <div class="m-card-header">
                        <i class="fas fa-concierge-bell"></i> {{ $booking->booking_type === 'consultation' ? __('messages.consultation_details') : __('messages.service_details') }}
                    </div>
                    <div class="m-service-box">
                        <div class="m-service-info">
                            <h3>{{ $booking->bookable ? $booking->bookable->trans('name') : '---' }}</h3>
                            <div class="m-service-meta">
                                @if($booking->booking_type === 'consultation')
                                    <span><i class="far fa-folder"></i> {{ $booking->consultation->category->trans('name') ?? '' }}</span>
                                    <span class="m-dot"></span>
                                    <span><i class="far fa-clock"></i> {{ $booking->formatted_duration }}</span>
                                @else
                                    <span><i class="far fa-folder"></i> {{ $booking->service->subCategory->trans('name') ?? '' }}</span>
                                    <span class="m-dot"></span>
                                    <span><i class="far fa-clock"></i> {{ $booking->formatted_duration }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="m-info-split">
                    <!-- Customer Card -->
                    <div class="m-card">
                        <div class="m-card-header">
                            <i class="fas fa-user-circle"></i> {{ __('messages.customer_data') }}
                        </div>
                        <div class="m-contact-list">
                            <div class="m-contact-item">
                                <span class="label">{{ __('messages.customer_name') }}</span>
                                <span class="value">{{ $booking->customer->name ?? '---' }}</span>
                            </div>
                            <div class="m-contact-item">
                                <span class="label">{{ __('messages.phone_number') }}</span>
                                <a href="tel:{{ $booking->customer->phone }}" class="value link">{{ $booking->customer->phone ?? '---' }}</a>
                            </div>
                            <div class="m-contact-item">
                                <span class="label">{{ __('messages.email') }}</span>
                                <span class="value">{{ $booking->customer->email ?? '---' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Card -->
                    <div class="m-card">
                        <div class="m-card-header">
                            <i class="fas fa-id-badge"></i> {{ __('messages.employee_in_charge') }}
                        </div>
                        <div class="m-contact-list">
                            <div class="m-contact-item">
                                <span class="label">{{ __('messages.employee_name') }}</span>
                                <span class="value">{{ $booking->employee && $booking->employee->user ? $booking->employee->user->name : '---' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="m-card">
                    <div class="m-card-header">
                        <i class="fas fa-credit-card"></i> {{ __('messages.payment_details') }}
                    </div>
                    <div class="m-contact-list">
                        <div class="m-contact-item">
                            <span class="label">{{ __('messages.payment_status') }}</span>
                            <span class="value" style="color: #22c55e; font-weight: 700;">{{ __('messages.paid') }}</span>
                        </div>
                        <div class="m-contact-item">
                            <span class="label">{{ __('messages.paid_at') }}</span>
                            <span class="value">
                                @if($booking->paid_at)
                                    @php
                                        $paidAt = $booking->paid_at;
                                        if (!($paidAt instanceof \Carbon\Carbon)) {
                                            $paidAt = \Carbon\Carbon::parse($paidAt);
                                        }
                                    @endphp
                                    {{ $paidAt->format('Y-m-d H:i:s') }}
                                @else
                                    ---
                                @endif
                            </span>
                        </div>
                        @if($booking->payment_id)
                            <div class="m-contact-item">
                                <span class="label">{{ __('messages.transaction_id') }}</span>
                                <span class="value">{{ $booking->payment_id }}</span>
                            </div>
                        @endif
                        @if($booking->payment_data && is_array($booking->payment_data))
                            @if(isset($booking->payment_data['transaction_id']))
                                <div class="m-contact-item">
                                    <span class="label">{{ __('messages.payment_transaction_id') }}</span>
                                    <span class="value">{{ $booking->payment_data['transaction_id'] }}</span>
                                </div>
                            @endif
                            @if(isset($booking->payment_data['payment_method']))
                                <div class="m-contact-item">
                                    <span class="label">{{ __('messages.payment_method') }}</span>
                                    <span class="value">{{ $booking->payment_data['payment_method'] }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="m-card">
                    <div class="m-card-header">
                        <i class="fas fa-calendar-alt"></i> {{ __('messages.booking_schedule') }}
                    </div>
                    <div class="m-time-display">
                        <div class="m-time-item">
                            <label>{{ __('messages.date') }}</label>
                            <div class="val">{{ $booking->booking_date->format('Y-m-d') }}</div>
                            <div class="day">{{ $booking->booking_date->locale(app()->getLocale())->dayName }}</div>
                        </div>
                        <div class="m-time-line"></div>
                        <div class="m-time-item">
                            <label>{{ __('messages.attendance_time') }}</label>
                            <div class="val">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</div>
                            <div class="day">{{ __('messages.local_time') }}</div>
                        </div>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="m-card m-notes-area">
                        <div class="m-card-header">
                            <i class="fas fa-sticky-note"></i> {{ __('messages.notes') }}
                        </div>
                        <div class="notes-content">{{ $booking->notes }}</div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="m-sidebar-column">
                <div class="m-sticky-card">
                    <div class="m-status-info">
                        <div class="status-badge completed">
                            <i class="fas fa-check-circle"></i>
                            {{ __('messages.payment_completed') }}
                        </div>
                        <div class="time-display-info">
                            <i class="fas fa-calendar-check"></i>
                            <div>
                                <strong>{{ __('messages.booking_date') }}</strong><br>
                                {{ $booking->booking_date->format('Y-m-d') }}<br>
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </div>
                        </div>
                        <div class="time-display-info">
                            <i class="fas fa-money-bill-wave"></i>
                            <div>
                                <strong>{{ __('messages.total_amount') }}</strong><br>
                                <span style="font-size: 20px; font-weight: 800; color: #22c55e;">
                                    {{ number_format($booking->total_price, 2) }} {{ __('messages.sar') }}
                                </span>
                            </div>
                        </div>
                        @if($booking->paid_at)
                            <div class="time-display-info">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>{{ __('messages.paid_at') }}</strong><br>
                                    @php
                                        $paidAt = $booking->paid_at;
                                        if (!($paidAt instanceof \Carbon\Carbon)) {
                                            $paidAt = \Carbon\Carbon::parse($paidAt);
                                        }
                                    @endphp
                                    {{ $paidAt->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </div>

    @push('styles')
        <style>
            /* Modern Harmonious Theme */
            :root {
                --m-bg: #f8fafc;
                --m-card: #ffffff;
                --m-border: #edf2f7;
                --m-text-p: #2d3748;
                --m-text-s: #718096;
                --m-indigo: #5a67d8;
                --m-green: #38a169;
                --m-header-bg: #fdfdfd;
            }

            .booking-modern-container {
                font-family: 'Cairo', sans-serif;
                color: var(--m-text-p);
                padding: 0 10px;
            }

            /* Soft Header */
            .booking-m-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                padding-bottom: 25px;
                margin-bottom: 25px;
                border-bottom: 2px solid var(--m-border);
            }

            .m-title {
                font-size: 26px;
                font-weight: 800;
                margin: 0;
                color: #1a202c;
            }

            .m-subtitle {
                margin: 5px 0 0;
                color: var(--m-text-s);
                font-size: 14px;
            }

            .m-btn-back {
                text-decoration: none;
                color: var(--m-text-s);
                font-size: 14px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 18px;
                background: white;
                border: 1px solid var(--m-border);
                border-radius: 10px;
                transition: all 0.2s;
            }

            .m-btn-back:hover {
                background: #f7fafc;
                color: var(--m-text-p);
                transform: translateX(5px);
            }

            /* Stats Row */
            .m-stats-row {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            .m-stat-card {
                background: white;
                padding: 20px;
                border-radius: 16px;
                border: 1px solid var(--m-border);
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            }

            .m-stat-icon {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
            }

            .icon-blue {
                background: #eff6ff;
                color: #3b82f6;
            }

            .icon-green {
                background: #f0fdf4;
                color: #22c55e;
            }

            .icon-purple {
                background: #faf5ff;
                color: #a855f7;
            }

            .m-stat-label {
                display: block;
                font-size: 12px;
                color: var(--m-text-s);
                font-weight: 700;
            }

            .m-stat-status {
                font-size: 15px;
                font-weight: 800;
            }

            .payment-paid {
                color: #22c55e;
            }

            .m-stat-value {
                font-size: 20px;
                font-weight: 800;
                color: #1a202c;
            }

            /* Column Grid */
            .m-content-grid {
                display: grid;
                grid-template-columns: 1fr 340px;
                gap: 25px;
                align-items: start;
            }

            /* Modern Card */
            .m-card {
                background: white;
                border-radius: 20px;
                border: 1px solid var(--m-border);
                padding: 25px;
                margin-bottom: 25px;
            }

            .m-card-header {
                font-size: 15px;
                font-weight: 800;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
                color: var(--m-indigo);
            }

            .m-card-header i {
                color: #bbbdd6;
            }

            /* Service Info */
            .m-service-box {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .m-service-info h3 {
                font-size: 22px;
                font-weight: 800;
                margin: 0 0 10px 0;
            }

            .m-service-meta {
                display: flex;
                align-items: center;
                gap: 15px;
                font-size: 14px;
                color: var(--m-text-s);
            }

            .m-service-meta i {
                margin-left: 5px;
            }

            .m-dot {
                width: 4px;
                height: 4px;
                background: #cbd5e1;
                border-radius: 50%;
            }

            /* Info Split Row */
            .m-info-split {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            .m-contact-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .m-contact-item {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 15px;
            }

            .m-contact-item .label {
                font-weight: 700;
                color: var(--m-text-s);
                font-size: 13px;
                min-width: 120px;
            }

            .m-contact-item .value {
                color: var(--m-text-p);
                font-weight: 600;
                text-align: left;
                flex: 1;
            }

            .m-contact-item .value.link {
                color: var(--m-indigo);
                text-decoration: none;
            }

            .m-contact-item .value.link:hover {
                text-decoration: underline;
            }

            /* Time Display */
            .m-time-display {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 20px;
                background: #f8fafc;
                border-radius: 12px;
            }

            .m-time-item {
                flex: 1;
                text-align: center;
            }

            .m-time-item label {
                display: block;
                font-size: 12px;
                color: var(--m-text-s);
                font-weight: 700;
                margin-bottom: 8px;
            }

            .m-time-item .val {
                font-size: 20px;
                font-weight: 800;
                color: var(--m-text-p);
                margin-bottom: 5px;
            }

            .m-time-item .day {
                font-size: 13px;
                color: var(--m-text-s);
            }

            .m-time-line {
                width: 1px;
                height: 60px;
                background: var(--m-border);
            }

            /* Notes */
            .m-notes-area {
                background: #fffdf7;
                border-color: #fceec7;
            }

            .notes-content {
                line-height: 1.8;
                font-size: 15px;
                color: #78716c;
            }

            /* Sidebar Sticky */
            .m-sticky-card {
                background: white;
                border-radius: 20px;
                border: 1px solid var(--m-border);
                padding: 25px;
                margin-bottom: 20px;
                position: sticky;
                top: 20px;
            }

            .m-status-info {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .status-badge {
                padding: 15px;
                border-radius: 12px;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 15px;
            }

            .status-badge.completed {
                background: #d1fae5;
                color: #059669;
            }

            .time-display-info {
                padding: 12px;
                background: #f8fafc;
                border-radius: 10px;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 14px;
                color: var(--m-text-s);
            }

            .time-display-info i {
                color: var(--m-indigo);
            }

            .time-display-info strong {
                color: var(--m-text-p);
                font-weight: 700;
            }

            @media (max-width: 1024px) {
                .m-content-grid {
                    grid-template-columns: 1fr;
                }

                .m-info-split {
                    grid-template-columns: 1fr;
                }

                .m-stats-row {
                    grid-template-columns: 1fr;
                }
            }

            /* Dark Mode Styles */
            [data-theme="dark"] .booking-modern-container {
                color: var(--text-primary);
            }

            [data-theme="dark"] .booking-m-header {
                border-bottom-color: var(--border-color);
            }

            [data-theme="dark"] .m-title {
                color: var(--text-primary);
            }

            [data-theme="dark"] .m-subtitle {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .m-btn-back {
                background: var(--card-bg);
                border-color: var(--border-color);
                color: var(--text-primary);
            }

            [data-theme="dark"] .m-btn-back:hover {
                background: var(--sidebar-active-bg);
                color: var(--text-primary);
                border-color: var(--primary-color);
            }

            [data-theme="dark"] .m-stat-card {
                background: var(--card-bg);
                border-color: var(--border-color);
            }

            [data-theme="dark"] .m-stat-label {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .m-stat-status {
                color: var(--text-primary);
            }

            [data-theme="dark"] .m-stat-value {
                color: var(--text-primary);
            }

            [data-theme="dark"] .m-card {
                background: var(--card-bg);
                border-color: var(--border-color);
            }

            [data-theme="dark"] .m-card-header {
                color: var(--text-primary);
            }

            [data-theme="dark"] .m-card-header i {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .m-contact-item .label {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .m-contact-item .value {
                color: var(--text-primary);
            }

            [data-theme="dark"] .m-time-display {
                background: var(--sidebar-active-bg);
            }

            [data-theme="dark"] .m-time-line {
                background: var(--border-color);
            }

            [data-theme="dark"] .m-sticky-card {
                background: var(--card-bg);
                border-color: var(--border-color);
            }

            [data-theme="dark"] .time-display-info {
                background: var(--sidebar-active-bg);
            }

            [data-theme="dark"] .time-display-info strong {
                color: var(--text-primary);
            }

            [data-theme="dark"] .notes-content {
                color: var(--text-secondary);
            }
        </style>
    @endpush
@endsection


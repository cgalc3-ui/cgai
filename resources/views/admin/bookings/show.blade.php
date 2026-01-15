@extends('layouts.dashboard')

@section('title', __('messages.booking_details'))
@section('page-title', __('messages.booking_details'))

@section('content')
    <div class="booking-modern-container">
        <!-- Header: Soft & Cohesive -->
        <header class="booking-m-header">
            <div class="m-header-main">
                <h1 class="m-title">{{ __('messages.booking_review') }}</h1>
                <p class="m-subtitle">{{ __('messages.registration_date') }}: {{ $booking->created_at ? $booking->created_at->format('Y-m-d') : '---' }}</p>
            </div>
            <div class="m-header-actions">
                <a href="{{ route('admin.bookings') }}" class="m-btn-back">
                    <i class="fas fa-long-arrow-alt-right"></i> {{ __('messages.back_to_bookings_list') }}
                </a>
            </div>
        </header>

        <!-- Essential Stats: Integrated Style -->
        <div class="m-stats-row">
            <div class="m-stat-card">
                <div class="m-stat-icon icon-blue"><i class="fas fa-tasks"></i></div>
                <div class="m-stat-info">
                    <span class="m-stat-label">{{ __('messages.booking_status') }}</span>
                    <span class="m-stat-status status-{{ $booking->status }}">
                        @if($booking->status === 'pending') {{ __('messages.pending_wait') }}
                        @elseif($booking->status === 'confirmed') {{ __('messages.confirmed_booking') }}
                        @elseif($booking->status === 'completed') {{ __('messages.completed_booking') }}
                        @else {{ __('messages.cancelled_booking') }} @endif
                    </span>
                </div>
            </div>
            <div class="m-stat-card">
                <div class="m-stat-icon icon-green"><i class="fas fa-wallet"></i></div>
                <div class="m-stat-info">
                    <span class="m-stat-label">{{ __('messages.payment_status') }}</span>
                    <span class="m-stat-status payment-{{ $booking->payment_status }}">
                        @if($booking->payment_status === 'paid') {{ __('messages.paid_successfully') }}
                        @elseif($booking->payment_status === 'unpaid') {{ __('messages.waiting_payment') }}
                        @else {{ __('messages.refunded') }} @endif
                    </span>
                </div>
            </div>
            <div class="m-stat-card">
                <div class="m-stat-icon icon-purple"><i class="fas fa-tag"></i></div>
                <div class="m-stat-info">
                    <span class="m-stat-label">{{ __('messages.total_cost') }}</span>
                    <span class="m-stat-value">{{ number_format($booking->total_price, 2) }} {{ __('messages.sar') }}</span>
                </div>
            </div>
        </div>

        <div class="m-content-grid">
            <!-- Main Info Side -->
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
                                    <span class="m-dot"></span>
                                    <span><i class="fas fa-tag"></i> {{ __('messages.fixed_price') }}: {{ number_format($booking->consultation->fixed_price ?? 0, 2) }} {{ __('messages.sar') }}</span>
                                @else
                                    <span><i class="far fa-folder"></i> {{ $booking->service->subCategory->trans('name') ?? '' }}</span>
                                    <span class="m-dot"></span>
                                    <span><i class="far fa-clock"></i>
                                        {{ $booking->formatted_duration }}</span>
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
                                <span class="label">{{ __('messages.customer_name') }}:</span>
                                <span class="value">{{ $booking->customer->name ?? '---' }}</span>
                            </div>
                            <div class="m-contact-item">
                                <span class="label">{{ __('messages.phone_number') }}:</span>
                                <a href="tel:{{ $booking->customer->phone }}"
                                    class="value link">{{ $booking->customer->phone ?? '---' }}</a>
                            </div>
                            <div class="m-contact-item">
                                <span class="label">{{ __('messages.email') }}:</span>
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
                                <span class="label">{{ __('messages.employee_name') }}:</span>
                                <span class="value">{{ $booking->employee->user->name ?? '---' }}</span>
                            </div>
                            <div class="m-contact-item">
                                <span class="label">{{ __('messages.specialization') }}:</span>
                                <span class="value">{{ __('messages.direct_service_manager') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date & Time Card -->
                <div class="m-card">
                    <div class="m-card-header">
                        <i class="fas fa-calendar-alt"></i> {{ __('messages.booking_schedule') }}
                    </div>
                    <div class="m-time-display">
                        <div class="m-time-item">
                            <label>{{ __('messages.date') }}</label>
                            <div class="val">{{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '---' }}</div>
                            <div class="day">{{ $booking->booking_date ? $booking->booking_date->locale(app()->getLocale())->dayName : '---' }}</div>
                        </div>
                        <div class="m-time-line"></div>
                        <div class="m-time-item">
                            <label>{{ __('messages.attendance_time') }}</label>
                            <div class="val">
                                @if($booking->start_time && $booking->end_time)
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                @else
                                    ---
                                @endif
                            </div>
                            <div class="day">{{ __('messages.local_time') }}</div>
                        </div>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="m-card m-notes-area">
                        <div class="m-card-header"><i class="fas fa-comment-alt"></i> {{ __('messages.additional_notes') }}</div>
                        <div class="notes-content">{{ $booking->notes }}</div>
                    </div>
                @endif
            </div>

            <!-- Sticky Controls Side -->
            <aside class="m-side-column">
                <div class="m-sticky-card">
                    <div class="m-card-header">{{ __('messages.administrative_actions') }}</div>

                    <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}" class="m-admin-form">
                        @csrf @method('PUT')
                        <div class="m-form-group">
                            <label>{{ __('messages.update_general_status') }}</label>
                            <div class="m-select-wrapper">
                                <select name="status">
                                    <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>⏳ {{ __('messages.pending_wait') }}</option>
                                    <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>✅ {{ __('messages.confirmed_booking') }}</option>
                                    <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>✨ {{ __('messages.completed_booking') }}</option>
                                    <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>❌ {{ __('messages.cancelled_booking') }}</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="m-btn-save primary">{{ __('messages.save_changes') }}</button>
                    </form>

                    <div class="m-card-divider"></div>

                    <form method="POST" action="{{ route('admin.bookings.update-payment-status', $booking) }}"
                        class="m-admin-form">
                        @csrf @method('PUT')
                        <div class="m-form-group">
                            <label>{{ __('messages.update_payment_status') }}</label>
                            <div class="m-select-wrapper">
                                <select name="payment_status">
                                    <option value="unpaid" {{ $booking->payment_status === 'unpaid' ? 'selected' : '' }}>🚩
                                        {{ __('messages.unpaid') }}</option>
                                    <option value="paid" {{ $booking->payment_status === 'paid' ? 'selected' : '' }}>💰
                                        {{ __('messages.paid') }}</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="m-btn-save secondary">{{ __('messages.update_payment') }}</button>
                    </form>
                </div>

                <div class="m-sticky-card m-quick-actions">
                    <h4 class="m-side-title">{{ __('messages.additional_tools') }}</h4>
                    <button type="button" class="tool-btn" onclick="window.print()">
                        <i class="fas fa-print"></i> {{ __('messages.print_invoice') }}
                    </button>
                    <form method="POST" action="{{ route('admin.bookings.send-reminder', $booking) }}" class="tool-btn-form" style="display: inline;" 
                        onsubmit="event.preventDefault(); sendReminderConfirm(this); return false;">
                        @csrf
                        <button type="submit" class="tool-btn">
                            <i class="fas fa-paper-plane"></i> {{ __('messages.send_reminder') }}
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </div>

    @push('styles')
        <style>
            /* Modern Harmonious Theme: "Soft Indigo & Slate" */
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

            .m-badge-id {
                display: inline-block;
                padding: 3px 12px;
                background: #ebf4ff;
                color: #3182ce;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 800;
                margin-bottom: 10px;
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

            /* Stats Row - Cohesive Cards */
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

            /* Service Info Styles */
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
                font-size: 14px;
                padding-bottom: 8px;
                border-bottom: 1px dashed #f1f1f1;
            }

            .m-contact-item:last-child {
                border: none;
            }

            .m-contact-item .label {
                color: var(--m-text-s);
                font-weight: 600;
            }

            .m-contact-item .value {
                font-weight: 700;
                color: var(--m-text-p);
            }

            .m-contact-item .value.link {
                color: var(--m-indigo);
                text-decoration: none;
            }

            /* Time Display Grid */
            .m-time-display {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 0;
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
            }

            .m-time-item .day {
                font-size: 14px;
                color: var(--m-text-s);
                margin-top: 4px;
            }

            .m-time-line {
                width: 1px;
                height: 40px;
                background: var(--m-border);
                margin: 0 40px;
            }

            /* Notes Area */
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

            .m-form-group {
                margin-bottom: 15px;
            }

            .m-form-group label {
                display: block;
                font-size: 12px;
                font-weight: 800;
                color: var(--m-text-s);
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .m-select-wrapper select {
                width: 100%;
                padding: 12px;
                border: 1px solid var(--m-border);
                border-radius: 10px;
                background: #fafafa;
                font-size: 14px;
                font-family: inherit;
                outline: none;
                cursor: pointer;
            }

            .m-btn-save {
                width: 100%;
                padding: 14px;
                border-radius: 12px;
                border: none;
                font-size: 14px;
                font-weight: 800;
                cursor: pointer;
                transition: all 0.2s;
            }

            .m-btn-save.primary {
                background: var(--m-indigo);
                color: white;
                margin-top: 5px;
            }

            .m-btn-save.primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(90, 103, 216, 0.3);
            }

            .m-btn-save.secondary {
                background: #f1f1f1;
                color: var(--m-text-p);
            }

            .m-card-divider {
                height: 1px;
                background: var(--m-border);
                margin: 25px 0;
            }

            .tool-btn {
                width: 100%;
                padding: 12px;
                background: white;
                border: 1px solid var(--m-border);
                border-radius: 10px;
                color: var(--m-text-p);
                font-size: 13px;
                font-weight: 700;
                margin-bottom: 10px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .tool-btn:hover {
                background: #fafafa;
                border-color: var(--m-text-s);
            }

            .m-side-title {
                font-size: 14px;
                font-weight: 800;
                margin: 0 0 15px 0;
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
                border-bottom-color: var(--border-color) !important;
            }

            [data-theme="dark"] .m-title {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .m-subtitle {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .m-btn-back {
                background: var(--sidebar-active-bg) !important;
                color: var(--text-secondary) !important;
                border-color: var(--border-color) !important;
            }

            [data-theme="dark"] .m-btn-back:hover {
                background: var(--bg-light) !important;
                color: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
            }

            [data-theme="dark"] .m-stat-card {
                background: var(--card-bg) !important;
                border-color: var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .m-stat-label {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .m-stat-status,
            [data-theme="dark"] .m-stat-value {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .m-card,
            [data-theme="dark"] .m-sticky-card {
                background: var(--card-bg) !important;
                border-color: var(--border-color) !important;
            }

            [data-theme="dark"] .m-card-header {
                color: var(--primary-color) !important;
            }

            [data-theme="dark"] .m-card-header i {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .m-service-info h3 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .m-service-meta {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .m-dot {
                background: var(--border-color) !important;
            }

            [data-theme="dark"] .m-contact-item {
                border-bottom-color: var(--border-color) !important;
            }

            [data-theme="dark"] .m-contact-item .label {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .m-contact-item .value {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .m-contact-item .value.link {
                color: var(--primary-color) !important;
            }

            [data-theme="dark"] .m-time-item label {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .m-time-item .val {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .m-time-item .day {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .m-time-line {
                background: var(--border-color) !important;
            }

            [data-theme="dark"] .m-notes-area {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
            }

            [data-theme="dark"] .notes-content {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .m-form-group label {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .m-select-wrapper select {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .m-select-wrapper select:focus {
                border-color: var(--primary-color) !important;
            }

            [data-theme="dark"] .m-btn-save.primary {
                background: var(--primary-color) !important;
            }

            [data-theme="dark"] .m-btn-save.primary:hover {
                background: var(--primary-dark) !important;
            }

            [data-theme="dark"] .m-btn-save.secondary {
                background: var(--sidebar-active-bg) !important;
                color: var(--text-primary) !important;
                border: 1px solid var(--border-color) !important;
            }

            [data-theme="dark"] .m-btn-save.secondary:hover {
                background: var(--bg-light) !important;
                border-color: var(--primary-color) !important;
            }

            [data-theme="dark"] .m-card-divider {
                background: var(--border-color) !important;
            }

            [data-theme="dark"] .tool-btn {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .tool-btn:hover {
                background: var(--bg-light) !important;
                border-color: var(--primary-color) !important;
            }

            [data-theme="dark"] .m-side-title {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .icon-blue {
                background: rgba(59, 130, 246, 0.2) !important;
                color: #3b82f6 !important;
            }

            [data-theme="dark"] .icon-green {
                background: rgba(34, 197, 94, 0.2) !important;
                color: #22c55e !important;
            }

            [data-theme="dark"] .icon-purple {
                background: rgba(168, 85, 247, 0.2) !important;
                color: #a855f7 !important;
            }

            [data-theme="dark"] .status-pending {
                color: var(--warning-color) !important;
            }

            [data-theme="dark"] .status-confirmed {
                color: var(--secondary-color) !important;
            }

            [data-theme="dark"] .status-completed {
                color: var(--success-color) !important;
            }

            [data-theme="dark"] .status-cancelled {
                color: var(--danger-color) !important;
            }

            [data-theme="dark"] .payment-paid {
                color: var(--success-color) !important;
            }

            [data-theme="dark"] .payment-unpaid {
                color: var(--warning-color) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function sendReminderConfirm(form) {
                const message = {!! json_encode(__('messages.send_reminder_confirm') ?? 'هل تريد إرسال تذكرة للعميل؟') !!};
                const title = {!! json_encode(__('messages.send_reminder') ?? 'إرسال تذكرة') !!};
                
                Confirm.confirm(message, title).then(confirmed => {
                    if (confirmed) {
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
@extends('layouts.dashboard')

@section('title', 'تفاصيل الحجز')
@section('page-title', 'تفاصيل الحجز')

@section('content')
    <div class="booking-modern-container">
        <!-- Header: Soft & Cohesive -->
        <header class="booking-m-header">
            <div class="m-header-main">
                <h1 class="m-title">تفاصيل الحجز</h1>
                <p class="m-subtitle">تاريخ التسجيل: {{ $booking->created_at->format('Y-m-d') }}</p>
            </div>
            <div class="m-header-actions">
                <a href="{{ route('staff.my-bookings') }}" class="m-btn-back">
                    <i class="fas fa-long-arrow-alt-right"></i> الرجوع لقائمة الحجوزات
                </a>
            </div>
        </header>

        <!-- Essential Stats: Integrated Style -->
        <div class="m-stats-row">
            <div class="m-stat-card">
                <div class="m-stat-icon icon-blue"><i class="fas fa-tasks"></i></div>
                <div class="m-stat-info">
                    <span class="m-stat-label">حالة الحجز</span>
                    <span class="m-stat-status status-{{ $booking->actual_status }}">
                        @php
                            $actualStatus = $booking->actual_status;
                        @endphp
                        @if($actualStatus === 'pending') ⏳ قيد الانتظار
                        @elseif($actualStatus === 'in_progress') 🔄 قيد التنفيذ
                        @elseif($actualStatus === 'completed') ✅ حجز مكتمل
                        @else ❌ حجز ملغي @endif
                    </span>
                </div>
            </div>
            <div class="m-stat-card">
                <div class="m-stat-icon icon-green"><i class="fas fa-wallet"></i></div>
                <div class="m-stat-info">
                    <span class="m-stat-label">حالة الدفع</span>
                    <span class="m-stat-status payment-{{ $booking->payment_status }}">
                        @if($booking->payment_status === 'paid') ✅ تم الدفع بنجاح
                        @elseif($booking->payment_status === 'unpaid') ⏳ في انتظار الدفع
                        @else 🔄 تم الاسترداد @endif
                    </span>
                </div>
            </div>
            <div class="m-stat-card">
                <div class="m-stat-icon icon-purple"><i class="fas fa-tag"></i></div>
                <div class="m-stat-info">
                    <span class="m-stat-label">التكلفة الإجمالية</span>
                    <span class="m-stat-value">{{ number_format($booking->total_price, 2) }} ر.س</span>
                </div>
            </div>
        </div>

        <div class="m-content-grid">
            <!-- Main Info Side -->
            <div class="m-main-column">
                <!-- Service Section -->
                <div class="m-card">
                    <div class="m-card-header">
                        <i class="fas fa-concierge-bell"></i> تفاصيل الخدمة
                    </div>
                    <div class="m-service-box">
                        <div class="m-service-info">
                            <h3>{{ $booking->service->name ?? '---' }}</h3>
                            <div class="m-service-meta">
                                <span><i class="far fa-folder"></i> {{ $booking->service->subCategory->name ?? '' }}</span>
                                <span class="m-dot"></span>
                                <span><i class="far fa-clock"></i>
                                    {{ $booking->formatted_duration }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="m-info-split">
                    <!-- Customer Card -->
                    <div class="m-card">
                        <div class="m-card-header">
                            <i class="fas fa-user-circle"></i> بيانات العميل
                        </div>
                        <div class="m-contact-list">
                            <div class="m-contact-item">
                                <span class="label">اسم العميل:</span>
                                <span class="value">{{ $booking->customer->name ?? '---' }}</span>
                            </div>
                            <div class="m-contact-item">
                                <span class="label">رقم الجوال:</span>
                                <a href="tel:{{ $booking->customer->phone }}" class="value link">{{ $booking->customer->phone ?? '---' }}</a>
                            </div>
                            <div class="m-contact-item">
                                <span class="label">البريد الالكتروني:</span>
                                <span class="value">{{ $booking->customer->email ?? '---' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Card -->
                    <div class="m-card">
                        <div class="m-card-header">
                            <i class="fas fa-id-badge"></i> الموظف المسؤول
                        </div>
                        <div class="m-contact-list">
                            <div class="m-contact-item">
                                <span class="label">اسم الموظف:</span>
                                <span class="value">{{ $booking->employee->user->name ?? '---' }}</span>
                            </div>
                            <div class="m-contact-item">
                                <span class="label">التخصص:</span>
                                <span class="value">مسؤول الخدمة المباشرة</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date & Time Card -->
                <div class="m-card">
                    <div class="m-card-header">
                        <i class="fas fa-calendar-alt"></i> جدول الحجز
                    </div>
                    <div class="m-time-display">
                        <div class="m-time-item">
                            <label>تاريخ الحجز</label>
                            <div class="val">{{ $booking->booking_date->format('Y-m-d') }}</div>
                            <div class="day">{{ $booking->booking_date->locale('ar')->dayName }}</div>
                        </div>
                        <div class="m-time-line"></div>
                        <div class="m-time-item">
                            <label>وقت الحضور والبدء</label>
                            <div class="val">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </div>
                            <div class="day">توقيت محلي</div>
                        </div>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="m-card m-notes-area">
                        <div class="m-card-header"><i class="fas fa-comment-alt"></i> ملاحظات إضافية</div>
                        <div class="notes-content">{{ $booking->notes }}</div>
                    </div>
                @endif
            </div>

            <!-- Sticky Info Side -->
            <aside class="m-side-column">
                <div class="m-sticky-card">
                    <div class="m-card-header">معلومات الحالة</div>
                    
                    @php
                        $actualStatus = $booking->actual_status;
                        $timeDisplay = $booking->time_display;
                    @endphp

                    <div class="m-status-info">
                        @if($actualStatus === 'pending')
                            <div class="status-badge pending">
                                <i class="fas fa-clock"></i> قيد الانتظار
                            </div>
                            @if($timeDisplay && isset($timeDisplay['formatted']))
                                <div class="time-display-info">
                                    <i class="fas fa-hourglass-start"></i>
                                    <span>يبدأ بعد: <strong>{{ $timeDisplay['formatted'] }}</strong></span>
                                </div>
                            @endif
                        @elseif($actualStatus === 'in_progress')
                            <div class="status-badge in-progress">
                                <i class="fas fa-play-circle"></i> قيد التنفيذ
                            </div>
                            @if($timeDisplay)
                                @if(isset($timeDisplay['elapsed_formatted']))
                                    <div class="time-display-info">
                                        <i class="fas fa-clock"></i>
                                        <span>منقضي: <strong>{{ $timeDisplay['elapsed_formatted'] }}</strong></span>
                                    </div>
                                @endif
                                @if(isset($timeDisplay['remaining_formatted']))
                                    <div class="time-display-info">
                                        <i class="fas fa-hourglass-end"></i>
                                        <span>متبقي: <strong>{{ $timeDisplay['remaining_formatted'] }}</strong></span>
                                    </div>
                                @endif
                            @endif
                        @elseif($actualStatus === 'completed')
                            <div class="status-badge completed">
                                <i class="fas fa-check-double"></i> مكتمل
                            </div>
                        @else
                            <div class="status-badge cancelled">
                                <i class="fas fa-times-circle"></i> ملغي
                            </div>
                        @endif
                    </div>
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

            .status-badge.pending {
                background: #fef3c7;
                color: #d97706;
            }

            .status-badge.in-progress {
                background: #dbeafe;
                color: #2563eb;
            }

            .status-badge.completed {
                background: #d1fae5;
                color: #059669;
            }

            .status-badge.cancelled {
                background: #fee2e2;
                color: #dc2626;
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
        </style>
    @endpush
@endsection


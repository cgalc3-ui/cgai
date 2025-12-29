@extends('layouts.dashboard')

@section('title', 'لوحة تحكم المشرف الرئيسي')
@section('page-title', 'لوحة تحكم المشرف الرئيسي')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>نظرة عامة على النظام</h2>
            <p>مرحباً بك مجدداً، {{ auth()->user()->name }}</p>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon blue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-value">{{ number_format($stats['total_revenue'] ?? 0, 2) }} ر.س</div>
                <div class="stat-card-label">إجمالي الإيرادات</div>
                <div class="stat-card-sub">
                    <span class="text-success">
                        <i class="fas fa-arrow-up"></i> {{ number_format($stats['month_revenue'] ?? 0, 2) }} ر.س هذا الشهر
                    </span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon green">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-value">{{ $stats['total_bookings'] ?? 0 }}</div>
                <div class="stat-card-label">إجمالي الحجوزات</div>
                <div class="stat-card-sub">
                    <span class="text-info">{{ $stats['today_bookings'] ?? 0 }} حجز اليوم</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon orange">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-value">{{ $stats['total_customers'] ?? 0 }}</div>
                <div class="stat-card-label">إجمالي العملاء</div>
                <div class="stat-card-sub">
                    <span class="text-muted">{{ $recentCustomers->count() }} عميل جديد</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon purple">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-value">{{ $stats['total_employees'] ?? 0 }}</div>
                <div class="stat-card-label">إجمالي الموظفين</div>
                <div class="stat-card-sub">
                    <span class="text-muted">{{ $stats['total_specializations'] ?? 0 }} تخصص مختلف</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Status Summary -->
    <div class="card" style="margin-top: 30px;">
        <div class="card-header">
            <h3>
                <i class="fas fa-chart-pie color-blue"></i> ملخص حالات الحجوزات
            </h3>
        </div>
        <div class="card-body">
            <div class="status-summary-grid">
                <div class="status-item-minimal">
                    <span class="dot warning"></span>
                    <span class="label">قيد الانتظار</span>
                    <span class="value">{{ $stats['pending_bookings'] ?? 0 }}</span>
                </div>
                <div class="status-item-minimal">
                    <span class="dot info"></span>
                    <span class="label">مؤكد</span>
                    <span class="value">{{ $stats['confirmed_bookings'] ?? 0 }}</span>
                </div>
                <div class="status-item-minimal">
                    <span class="dot success"></span>
                    <span class="label">مكتمل</span>
                    <span class="value">{{ $stats['completed_bookings'] ?? 0 }}</span>
                </div>
                <div class="status-item-minimal">
                    <span class="dot danger"></span>
                    <span class="label">ملغي</span>
                    <span class="value">{{ $stats['cancelled_bookings'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="margin-top: 30px;">
        <div class="card-header">
            <h3>
                <i class="fas fa-bolt color-amber"></i> إجراءات سريعة
            </h3>
        </div>
        <div class="card-body">
            <div class="actions-grid-custom">
                <a href="{{ route('admin.users.staff.create') }}" class="action-btn-custom">
                    <i class="fas fa-user-plus"></i> إضافة موظف
                </a>
                <a href="{{ route('admin.services.create') }}" class="action-btn-custom">
                    <i class="fas fa-plus"></i> إضافة خدمة
                </a>
                <a href="{{ route('admin.time-slots.create') }}" class="action-btn-custom">
                    <i class="fas fa-calendar-plus"></i> فتح مواعيد
                </a>
                <a href="{{ route('admin.bookings') }}" class="action-btn-custom">
                    <i class="fas fa-list"></i> الحجوزات
                </a>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <!-- Recent Bookings Table -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3><i class="fas fa-history color-emerald"></i> آخر الحجوزات</h3>
                <a href="{{ route('admin.bookings') }}" class="btn btn-sm btn-primary">عرض الكل</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container" style="box-shadow: none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>العميل</th>
                                <th>الخدمة</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings->take(6) as $booking)
                                <tr>
                                    <td><strong>{{ $booking->customer->name ?? 'مجهول' }}</strong></td>
                                    <td>{{ $booking->service->name ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $booking->status === 'pending' ? 'badge-warning' : ($booking->status === 'confirmed' ? 'badge-info' : ($booking->status === 'completed' ? 'badge-success' : 'badge-danger')) }}">
                                            @if($booking->status == 'pending') معلق @elseif($booking->status == 'confirmed')
                                            مؤكد @elseif($booking->status == 'completed') مكتمل @else ملغي @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Staff & Customers -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-tie color-indigo"></i> آخر الموظفين المضافين</h3>
                </div>
                <div class="card-body" style="padding: 10px 20px;">
                    @foreach($recentStaff->take(3) as $staff)
                        <div
                            style="display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f1f1;">
                            <div
                                style="width: 35px; height: 35px; border-radius: 8px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                {{ substr($staff->name, 0, 1) }}</div>
                            <div>
                                <div style="font-size: 13px; font-weight: bold;">{{ $staff->name }}</div>
                                <div style="font-size: 11px; color: #666;">{{ $staff->employee?->specialization ?? 'موظف' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-circle color-emerald"></i> آخر العملاء</h3>
                </div>
                <div class="card-body" style="padding: 10px 20px;">
                    @foreach($recentCustomers->take(3) as $customer)
                        <div
                            style="display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f1f1;">
                            <div
                                style="width: 35px; height: 35px; border-radius: 8px; background: #f0fdf4; color: #10b981; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                {{ substr($customer->name, 0, 1) }}</div>
                            <div>
                                <div style="font-size: 13px; font-weight: bold;">{{ $customer->name }}</div>
                                <div style="font-size: 11px; color: #666;">{{ $customer->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Safe overrides that don't break existing layout */
            .status-summary-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
            }

            .status-item-minimal {
                padding: 15px;
                background: #f8fafc;
                border-radius: 10px;
                display: flex;
                align-items: center;
                gap: 10px;
                border: 1px solid #f1f5f9;
            }

            .dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
            }

            .dot.warning {
                background: #f59e0b;
            }

            .dot.info {
                background: #3b82f6;
            }

            .dot.success {
                background: #10b981;
            }

            .dot.danger {
                background: #ef4444;
            }

            .status-item-minimal .label {
                flex: 1;
                font-size: 13px;
                font-weight: 600;
                color: #64748b;
            }

            .status-item-minimal .value {
                font-size: 16px;
                font-weight: 800;
                color: #1e293b;
            }

            .actions-grid-custom {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 15px;
            }

            .action-btn-custom {
                padding: 15px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                text-decoration: none;
                color: #334155;
                font-weight: 600;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 10px;
                transition: all 0.2s;
            }

            .action-btn-custom:hover {
                background: #3b82f6;
                color: white;
                border-color: #3b82f6;
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
@extends('layouts.dashboard')

@section('title', 'لوحة تحكم المشرف الرئيسي')
@section('page-title', 'لوحة تحكم المشرف الرئيسي')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>لوحة تحكم المشرف الرئيسي</h2>
        <p>إدارة النظام بالكامل: المستخدمين، الحجوزات، الخدمات، والموظفين</p>
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
                <span class="text-muted">{{ $stats['total_specializations'] ?? 0 }} تخصص</span>
            </div>
        </div>
    </div>
</div>

<!-- Booking Status Summary -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3>
            <i class="fas fa-chart-pie"></i> ملخص حالة الحجوزات
        </h3>
    </div>
    <div class="card-body">
        <div class="status-summary">
            <div class="status-item">
                <div class="status-icon warning">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['pending_bookings'] ?? 0 }}</div>
                    <div class="status-label">قيد الانتظار</div>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon info">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['confirmed_bookings'] ?? 0 }}</div>
                    <div class="status-label">مؤكد</div>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon success">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['completed_bookings'] ?? 0 }}</div>
                    <div class="status-label">مكتمل</div>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['cancelled_bookings'] ?? 0 }}</div>
                    <div class="status-label">ملغي</div>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon success">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['paid_bookings'] ?? 0 }}</div>
                    <div class="status-label">مدفوع</div>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="status-content">
                    <div class="status-value">{{ $stats['unpaid_bookings'] ?? 0 }}</div>
                    <div class="status-label">غير مدفوع</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3>
            <i class="fas fa-bolt"></i> إجراءات سريعة
        </h3>
    </div>
    <div class="card-body">
        <div class="actions-grid">
            <a href="{{ route('admin.users.staff.create') }}" class="action-btn">
                <i class="fas fa-user-tie"></i>
                <span>إضافة موظف</span>
            </a>
            <a href="{{ route('admin.users.customers.create') }}" class="action-btn">
                <i class="fas fa-user"></i>
                <span>إضافة عميل</span>
            </a>
            <a href="{{ route('admin.services.create') }}" class="action-btn">
                <i class="fas fa-concierge-bell"></i>
                <span>إضافة خدمة</span>
            </a>
            <a href="{{ route('admin.categories.create') }}" class="action-btn">
                <i class="fas fa-folder-plus"></i>
                <span>إضافة فئة</span>
            </a>
            <a href="{{ route('admin.specializations.create') }}" class="action-btn">
                <i class="fas fa-tags"></i>
                <span>إضافة تخصص</span>
            </a>
            <a href="{{ route('admin.time-slots.create') }}" class="action-btn">
                <i class="fas fa-calendar-plus"></i>
                <span>إضافة وقت متاح</span>
            </a>
        </div>
    </div>
</div>

<!-- Recent Data Tables -->
<div class="dashboard-tables" style="margin-top: 30px;">
    <div class="row">
        <!-- Recent Bookings -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-calendar-check"></i> آخر الحجوزات
                    </h3>
                    <a href="{{ route('admin.bookings') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @if($recentBookings->count() > 0)
                        <div class="recent-list">
                            @foreach($recentBookings->take(5) as $booking)
                                <div class="recent-item">
                                    <div class="recent-item-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="recent-item-content">
                                        <div class="recent-item-title">{{ $booking->customer->name ?? 'غير محدد' }}</div>
                                        <div class="recent-item-subtitle">
                                            {{ $booking->service->name ?? 'غير محدد' }} - {{ $booking->booking_date->format('Y-m-d') }}
                                        </div>
                                    </div>
                                    <div class="recent-item-badge">
                                        <span class="badge badge-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'info' : ($booking->status === 'completed' ? 'success' : 'danger')) }}">
                                            @if($booking->status === 'pending') قيد الانتظار
                                            @elseif($booking->status === 'confirmed') مؤكد
                                            @elseif($booking->status === 'completed') مكتمل
                                            @else ملغي
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-small">
                            <i class="fas fa-calendar-times"></i>
                            <p>لا توجد حجوزات حديثة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Customers -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-users"></i> آخر العملاء
                    </h3>
                    <a href="{{ route('admin.users.customers') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @if($recentCustomers->count() > 0)
                        <div class="recent-list">
                            @foreach($recentCustomers->take(5) as $customer)
                                <div class="recent-item">
                                    <div class="recent-item-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="recent-item-content">
                                        <div class="recent-item-title">{{ $customer->name }}</div>
                                        <div class="recent-item-subtitle">
                                            @if($customer->phone)
                                                <i class="fas fa-phone"></i> {{ $customer->phone }}
                                            @endif
                                            @if($customer->email)
                                                <span style="margin-left: 10px;">
                                                    <i class="fas fa-envelope"></i> {{ $customer->email }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="recent-item-date">
                                        {{ $customer->created_at->format('Y-m-d') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-small">
                            <i class="fas fa-user-slash"></i>
                            <p>لا يوجد عملاء جدد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stat-card-content {
        flex: 1;
    }
    .stat-card-sub {
        margin-top: 8px;
        font-size: 13px;
    }
    .status-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }
    .status-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: transform 0.2s;
    }
    .status-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .status-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }
    .status-icon.warning { background: #ffc107; }
    .status-icon.info { background: #17a2b8; }
    .status-icon.success { background: #28a745; }
    .status-icon.danger { background: #dc3545; }
    .status-content {
        flex: 1;
    }
    .status-value {
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }
    .status-label {
        font-size: 13px;
        color: #666;
        margin-top: 4px;
    }
    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }
    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
        gap: 10px;
    }
    .action-btn:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }
    .action-btn i {
        font-size: 28px;
    }
    .action-btn span {
        font-weight: 600;
        font-size: 14px;
    }
    .recent-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .recent-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .recent-item:hover {
        background: #e9ecef;
        transform: translateX(-5px);
    }
    .recent-item-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .recent-item-content {
        flex: 1;
    }
    .recent-item-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }
    .recent-item-subtitle {
        font-size: 12px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .recent-item-badge {
        display: flex;
        align-items: center;
    }
    .recent-item-date {
        font-size: 12px;
        color: #999;
    }
    .empty-state-small {
        text-align: center;
        padding: 30px;
        color: #999;
    }
    .empty-state-small i {
        font-size: 36px;
        margin-bottom: 10px;
        opacity: 0.5;
    }
    .dashboard-tables .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    @media (max-width: 768px) {
        .status-summary {
            grid-template-columns: repeat(2, 1fr);
        }
        .actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush
@endsection

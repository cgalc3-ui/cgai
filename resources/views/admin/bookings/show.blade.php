@extends('layouts.dashboard')

@section('title', 'تفاصيل الحجز')
@section('page-title', 'تفاصيل الحجز #' . $booking->id)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>تفاصيل الحجز #{{ $booking->id }}</h2>
        <p>عرض وتعديل معلومات الحجز</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.bookings') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Booking Details Card -->
        <div class="card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-info-circle"></i> معلومات الحجز
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i> العميل
                        </div>
                        <div class="info-value">
                            <div class="user-name">{{ $booking->customer->name ?? 'غير محدد' }}</div>
                            @if($booking->customer->phone)
                                <div class="user-detail">
                                    <i class="fas fa-phone"></i> {{ $booking->customer->phone }}
                                </div>
                            @endif
                            @if($booking->customer->email)
                                <div class="user-detail">
                                    <i class="fas fa-envelope"></i> {{ $booking->customer->email }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user-tie"></i> الموظف
                        </div>
                        <div class="info-value">
                            {{ $booking->employee->user->name ?? 'غير محدد' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-concierge-bell"></i> الخدمة
                        </div>
                        <div class="info-value">
                            <div class="service-name">{{ $booking->service->name ?? 'غير محدد' }}</div>
                            @if($booking->service->subCategory)
                                <div class="service-category">
                                    <i class="fas fa-folder"></i> {{ $booking->service->subCategory->name }}
                                    @if($booking->service->subCategory->category)
                                        <span class="text-muted">/ {{ $booking->service->subCategory->category->name }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($booking->serviceDuration)
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-clock"></i> مدة الخدمة
                        </div>
                        <div class="info-value">
                            {{ $booking->serviceDuration->formatted_duration }}
                        </div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar"></i> التاريخ
                        </div>
                        <div class="info-value">
                            {{ $booking->booking_date->format('Y-m-d') }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-clock"></i> الوقت
                        </div>
                        <div class="info-value">
                            {{ $booking->start_time }} - {{ $booking->end_time }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-money-bill-wave"></i> السعر
                        </div>
                        <div class="info-value price">
                            {{ number_format($booking->total_price, 2) }} ر.س
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-info-circle"></i> حالة الحجز
                        </div>
                        <div class="info-value">
                            @if($booking->status === 'pending')
                                <span class="badge badge-warning">
                                    <i class="fas fa-hourglass-half"></i> قيد الانتظار
                                </span>
                            @elseif($booking->status === 'confirmed')
                                <span class="badge badge-info">
                                    <i class="fas fa-check-circle"></i> مؤكد
                                </span>
                            @elseif($booking->status === 'completed')
                                <span class="badge badge-success">
                                    <i class="fas fa-check-double"></i> مكتمل
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i> ملغي
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-credit-card"></i> حالة الدفع
                        </div>
                        <div class="info-value">
                            @if($booking->payment_status === 'paid')
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> مدفوع
                                </span>
                            @elseif($booking->payment_status === 'unpaid')
                                <span class="badge badge-warning">
                                    <i class="fas fa-exclamation-triangle"></i> غير مدفوع
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-undo"></i> مسترد
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($booking->notes)
                    <div class="info-item full-width">
                        <div class="info-label">
                            <i class="fas fa-sticky-note"></i> الملاحظات
                        </div>
                        <div class="info-value">
                            <div class="notes-box">{{ $booking->notes }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar-plus"></i> تاريخ الإنشاء
                        </div>
                        <div class="info-value">
                            {{ $booking->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-edit"></i> آخر تحديث
                        </div>
                        <div class="info-value">
                            {{ $booking->updated_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Update Status Card -->
        <div class="card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-edit"></i> تحديث الحالة
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="status">حالة الحجز</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                            <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> تحديث الحالة
                    </button>
                </form>
            </div>
        </div>

        <!-- Update Payment Status Card -->
        <div class="card" style="margin-top: 20px;">
            <div class="card-header">
                <h3>
                    <i class="fas fa-money-check-alt"></i> تحديث حالة الدفع
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.bookings.update-payment-status', $booking) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="payment_status">حالة الدفع</label>
                        <select name="payment_status" id="payment_status" class="form-control" required>
                            <option value="paid" {{ $booking->payment_status === 'paid' ? 'selected' : '' }}>مدفوع</option>
                            <option value="unpaid" {{ $booking->payment_status === 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                            <option value="refunded" {{ $booking->payment_status === 'refunded' ? 'selected' : '' }}>مسترد</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> تحديث حالة الدفع
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .info-item.full-width {
        grid-column: 1 / -1;
    }
    .info-label {
        font-weight: 600;
        color: #666;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-value {
        color: #333;
        font-size: 15px;
    }
    .user-name {
        font-weight: 600;
        margin-bottom: 4px;
    }
    .user-detail {
        font-size: 13px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
    }
    .service-name {
        font-weight: 600;
        margin-bottom: 4px;
    }
    .service-category {
        font-size: 13px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .price {
        color: #28a745;
        font-weight: 600;
        font-size: 18px;
    }
    .notes-box {
        background-color: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        border-right: 3px solid #007bff;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection

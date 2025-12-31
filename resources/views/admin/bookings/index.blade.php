@extends('layouts.dashboard')

@section('title', 'إدارة الحجوزات')
@section('page-title', 'قائمة الحجوزات')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة الحجوزات</h2>
            <p>إدارة وعرض جميع الحجوزات في مكان واحد</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
            </a>
            <span class="total-count">إجمالي الحجوزات: {{ $bookings->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('admin.bookings') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="status"><i class="fas fa-tasks"></i> حالة الحجز:</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="payment_status"><i class="fas fa-credit-card"></i> حالة الدفع:</label>
                    <select name="payment_status" id="payment_status" class="filter-input">
                        <option value="">الكل</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>مدفوع</option>
                        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>غير مدفوع
                        </option>
                        <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>مسترد
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date"><i class="fas fa-calendar-alt"></i> التاريخ:</label>
                    <input type="date" name="date" id="date" class="filter-input" value="{{ request('date') }}">
                </div>
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> بحث:</label>
                    <input type="text" name="search" id="search" class="filter-input" placeholder="اسم العميل أو الهاتف"
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> تطبيق الفلتر
                </button>
                <a href="{{ route('admin.bookings') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> مسح
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>العميل</th>
                    <th>الموظف</th>
                    <th>الخدمة</th>
                    <th>التاريخ والوقت</th>
                    <th>السعر</th>
                    <th>حالة الحجز</th>
                    <th>حالة الدفع</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-name">{{ $booking->customer->name ?? 'غير محدد' }}</div>
                                <div class="user-details">
                                    @if($booking->customer->phone)
                                        <span><i class="fas fa-phone"></i> {{ $booking->customer->phone }}</span>
                                    @endif
                                    @if($booking->customer->email)
                                        <span><i class="fas fa-envelope"></i> {{ $booking->customer->email }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-name">{{ $booking->employee->user->name ?? 'غير محدد' }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="service-info">
                                <div class="service-name">
                                    @if($booking->booking_type === 'consultation')
                                        <i class="fas fa-comments" style="margin-left: 5px;"></i>
                                        {{ $booking->consultation->name ?? 'غير محدد' }}
                                    @else
                                        {{ $booking->service->name ?? 'غير محدد' }}
                                    @endif
                                </div>
                                <div class="service-duration">
                                    <i class="fas fa-clock"></i>
                                    {{ $booking->formatted_duration }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="datetime-info">
                                <div class="date">
                                    <i class="fas fa-calendar"></i> {{ $booking->booking_date->format('Y-m-d') }}
                                </div>
                                <div class="time-slots-list">
                                    @php
                                        $timeRanges = $booking->formatted_time_slots;
                                    @endphp
                                    @foreach($timeRanges as $range)
                                        <div class="time-slot-item">
                                            <i class="fas fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($range['start'])->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($range['end'])->format('h:i A') }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong class="price">{{ number_format($booking->total_price, 2) }} ر.س</strong>
                        </td>
                        <td>
                            @if($booking->status === 'pending')
                                <span class="status-pill pending">معلق</span>
                            @elseif($booking->status === 'confirmed')
                                <span class="status-pill confirmed">مؤكد</span>
                            @elseif($booking->status === 'completed')
                                <span class="status-pill completed">مكتمل</span>
                            @else
                                <span class="status-pill cancelled">ملغي</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->payment_status === 'paid')
                                <span class="status-pill completed">مدفوع</span>
                            @elseif($booking->payment_status === 'unpaid')
                                <span class="status-pill pending">غير مدفوع</span>
                            @else
                                <span class="status-pill cancelled">مسترد</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="calm-action-btn" title="عرض">
                                <i class="far fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>لا توجد حجوزات</h3>
                                <p>لم يتم العثور على أي حجوزات</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $bookings->links() }}
        </div>
    </div>

    @push('styles')
        <style>
            .user-info {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .user-name {
                font-weight: 600;
                color: #333;
            }

            .user-details {
                display: flex;
                flex-direction: column;
                gap: 2px;
                font-size: 12px;
                color: #666;
            }

            .user-details span {
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .service-info {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .service-name {
                font-weight: 600;
                color: #333;
            }

            .service-duration {
                font-size: 12px;
                color: #666;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .datetime-info {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .datetime-info .date,
            .datetime-info .time {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 13px;
            }

            .datetime-info .date {
                font-weight: 600;
                color: #333;
            }

            .datetime-info .time {
                color: #666;
            }

            .time-slots-list {
                display: flex;
                flex-direction: column;
                gap: 4px;
                margin-top: 4px;
            }

            .time-slot-item {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                color: #666;
                padding: 2px 0;
            }

            .time-slot-item i {
                font-size: 11px;
            }

            .price {
                color: #28a745;
                font-size: 15px;
            }

            .empty-state {
                padding: 40px 20px;
                text-align: center;
            }

            .empty-state i {
                font-size: 48px;
                color: #ccc;
                margin-bottom: 16px;
            }

            .empty-state h3 {
                color: #666;
                margin-bottom: 8px;
            }

            .empty-state p {
                color: #999;
            }
        </style>
    @endpush
@endsection
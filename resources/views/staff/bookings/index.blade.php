@extends('layouts.dashboard')

@section('title', 'حجوزاتي')
@section('page-title', 'حجوزاتي')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h2>حجوزاتي</h2>
            <p>قائمة بجميع الحجوزات المخصصة لك</p>
        </div>
        <div class="page-header-right">
            <span class="total-count">{{ $bookings->total() }} حجز</span>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="section-container">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="15%">العميل</th>
                        <th width="15%">الخدمة</th>
                        <th width="20%">التاريخ والوقت</th>
                        <th width="10%">السعر</th>
                        <th width="15%">حالة الحجز</th>
                        <th width="15%">حالة الدفع</th>
                        <th width="10%">الإجراءات</th>
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
                                    </div>
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
                                        <i class="fas fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d') }}
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
                                @php
                                    $actualStatus = $booking->actual_status;
                                    $timeDisplay = $booking->time_display;
                                @endphp

                                @if($actualStatus == 'completed')
                                    <span class="status-pill completed">مكتمل</span>
                                @elseif($actualStatus == 'in_progress')
                                    <span class="status-pill confirmed" style="background: #e0f2fe; color: #0369a1;">قيد
                                        التنفيذ</span>
                                    @if($timeDisplay && isset($timeDisplay['elapsed_formatted']))
                                        <div class="time-info" style="font-size: 10px; color: #64748b; margin-top: 4px;">
                                            <i class="far fa-clock"></i> {{ $timeDisplay['elapsed_formatted'] }}
                                        </div>
                                    @endif
                                @elseif($actualStatus == 'cancelled')
                                    <span class="status-pill cancelled">ملغي</span>
                                @else
                                    <span class="status-pill pending">قيد الانتظار</span>
                                    @if($timeDisplay && isset($timeDisplay['formatted']))
                                        <div class="time-info" style="font-size: 10px; color: #64748b; margin-top: 4px;">
                                            <i class="far fa-hourglass"></i> {{ $timeDisplay['formatted'] }}
                                        </div>
                                    @endif
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
                                <a href="{{ route('staff.my-bookings.show', $booking) }}" class="calm-action-btn"
                                    title="عرض التفاصيل">
                                    <i class="far fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <h3>لا توجد حجوزات</h3>
                                    <p>لم يتم العثور على أي حجوزات مخصصة لك حالياً</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="pagination-wrapper">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-refresh page every minute to update booking statuses and times
        setInterval(function () {
            // Only refresh if page is visible
            if (!document.hidden) {
                location.reload();
            }
        }, 60000); // 60 seconds
    </script>
@endpush

@push('styles')
    <style>
        .section-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

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
            color: #10b981;
            font-size: 15px;
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 16px;
        }

        .empty-state h3 {
            color: #4b5563;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .empty-state p {
            color: #9ca3af;
        }

        /* Ensure table container has no shadow if section container has it */
        .section-container .table-container {
            box-shadow: none;
            border-radius: 0;
        }

        .status-select {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background-color: white;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
        }

        .status-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .payment-status {
            font-size: 11px;
            margin-top: 4px;
        }

        .text-success {
            color: #10b981;
        }

        .text-warning {
            color: #f59e0b;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-view-details {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: #3b82f6;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-view-details:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .btn-view-details i {
            font-size: 12px;
        }
    </style>
@endpush
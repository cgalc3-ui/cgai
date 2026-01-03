@extends('layouts.dashboard')

@section('title', __('messages.bookings'))
@section('page-title', __('messages.bookings_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.bookings_list') }}</h2>
            <p>{{ __('messages.manage_bookings_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.back_to_dashboard') }}
            </a>
            <span class="total-count">{{ __('messages.total_bookings') }}: {{ $bookings->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.bookings') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="status"><i class="fas fa-tasks"></i> {{ __('messages.booking_status') }}:</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                            {{ __('messages.pending') }}</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>
                            {{ __('messages.confirmed') }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                            {{ __('messages.completed') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                            {{ __('messages.cancelled') }}</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="payment_status"><i class="fas fa-credit-card"></i>
                        {{ __('messages.payment_status') }}:</label>
                    <select name="payment_status" id="payment_status" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>
                            {{ __('messages.paid') }}</option>
                        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>
                            {{ __('messages.unpaid') }}
                        </option>
                        <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>
                            {{ __('messages.refunded') }}
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date"><i class="fas fa-calendar-alt"></i> {{ __('messages.date') }}:</label>
                    <input type="date" name="date" id="date" class="filter-input" value="{{ request('date') }}">
                </div>
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search') }}:</label>
                    <input type="text" name="search" id="search" class="filter-input"
                        placeholder="{{ __('messages.search_customer_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> {{ __('messages.apply_filter') }}
                </button>
                <a href="{{ route('admin.bookings') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.client') }}</th>
                    <th>{{ __('messages.employee') }}</th>
                    <th>{{ __('messages.service') }}</th>
                    <th>{{ __('messages.datetime') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th class="text-center">{{ __('messages.booking_status') }}</th>
                    <th class="text-center">{{ __('messages.payment_status') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-name">{{ $booking->customer->name ?? __('messages.not_specified') }}</div>
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
                                <div class="user-name">{{ $booking->employee->user->name ?? __('messages.not_specified') }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="service-info">
                                <div class="service-name">
                                    @if($booking->booking_type === 'consultation')
                                        <i class="fas fa-comments" style="margin-left: 5px;"></i>
                                        {{ $booking->consultation ? $booking->consultation->trans('name') : __('messages.not_specified') }}
                                    @else
                                        {{ $booking->service ? $booking->service->trans('name') : __('messages.not_specified') }}
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
                            <strong class="price">{{ number_format($booking->total_price, 2) }}
                                {{ __('messages.sar') }}</strong>
                        </td>
                        <td class="text-center">
                            @if($booking->status === 'pending')
                                <span class="status-pill pending">{{ __('messages.pending') }}</span>
                            @elseif($booking->status === 'confirmed')
                                <span class="status-pill confirmed">{{ __('messages.confirmed') }}</span>
                            @elseif($booking->status === 'completed')
                                <span class="status-pill completed">{{ __('messages.completed') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.cancelled') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($booking->payment_status === 'paid')
                                <span class="status-pill completed">{{ __('messages.paid') }}</span>
                            @elseif($booking->payment_status === 'unpaid')
                                <span class="status-pill pending">{{ __('messages.unpaid') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.refunded') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="calm-action-btn" title="{{ __('messages.view') }}">
                                <i class="far fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>{{ __('messages.no_bookings') }}</h3>
                                <p>{{ __('messages.no_bookings_desc') }}</p>
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
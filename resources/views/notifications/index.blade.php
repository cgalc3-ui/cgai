@extends('layouts.dashboard')

@section('title', __('messages.notifications'))
@section('page-title', __('messages.notifications'))

@section('content')
    <div class="notifications-container">
        <!-- Header -->
        <div class="notifications-header">
            <div class="header-left">
                <h2>{{ __('messages.notifications') }}</h2>
                <p>{{ __('messages.all_notifications_in_one_place') }}</p>
            </div>
            <div class="header-right">
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-mark-all-read">
                            <i class="fas fa-check-double"></i>
                            {{ __('messages.mark_all_as_read') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="notifications-list">
            @forelse($notifications as $notification)
                <div class="notification-item {{ $notification->read ? 'read' : 'unread' }}"
                    data-notification-id="{{ $notification->id }}">
                    <div class="notification-icon">
                        @if($notification->type === 'booking_created' || $notification->type === 'booking_assigned')
                            <i class="fas fa-calendar-check"></i>
                        @elseif($notification->type === 'booking_status_updated')
                            <i class="fas fa-sync-alt"></i>
                        @elseif($notification->type === 'payment_received')
                            <i class="fas fa-money-bill-wave"></i>
                        @elseif($notification->type === 'new_booking')
                            <i class="fas fa-bell"></i>
                        @else
                            <i class="fas fa-info-circle"></i>
                        @endif
                    </div>
                    <div class="notification-content">
                        <div class="notification-header">
                            <h3 class="notification-title">{{ $notification->translated_title }}</h3>
                            <span class="notification-time">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="notification-message">{{ $notification->translated_message }}</p>
                        @if($notification->data)
                            <div class="notification-data">
                                @php
                                    $actionRoute = null;
                                    $actionLabel = null;
                                    $data = $notification->data;

                                    if (isset($data['booking_id'])) {
                                        $actionLabel = __('messages.view_booking');
                                        if (auth()->user()->isAdmin()) {
                                            $actionRoute = route('admin.bookings.show', $data['booking_id']);
                                        } elseif (auth()->user()->isStaff()) {
                                            $actionRoute = route('staff.my-bookings.show', $data['booking_id']);
                                        } else {
                                            $actionRoute = route('customer.bookings.show', $data['booking_id']);
                                        }
                                    } elseif (isset($data['subscription_request_id'])) {
                                        $actionLabel = __('messages.view_subscription_request');
                                        if (auth()->user()->isAdmin()) {
                                            $actionRoute = route('admin.subscription-requests.show', $data['subscription_request_id']);
                                        }
                                    } elseif (isset($data['subscription_id']) || isset($data['package_id'])) {
                                        $subId = $data['subscription_id'] ?? $data['package_id'];
                                        $actionLabel = __('messages.view_subscription');
                                        if (auth()->user()->isAdmin()) {
                                            $actionRoute = route('admin.subscriptions.show', $subId);
                                        }
                                    } elseif (isset($data['ticket_id'])) {
                                        $actionLabel = __('messages.view_details');
                                        if (auth()->user()->isAdmin()) {
                                            $actionRoute = route('admin.tickets.show', $data['ticket_id']);
                                        } else {
                                            $actionRoute = route('tickets.show', $data['ticket_id']);
                                        }
                                    }
                                @endphp
                                @if($actionRoute)
                                    <a href="{{ $actionRoute }}" class="notification-link">
                                        <i class="fas fa-external-link-alt"></i>
                                        {{ $actionLabel }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="notification-actions">
                        @if(!$notification->read)
                            <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn-mark-read" title="{{ __('messages.mark_as_read') }}">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" title="{{ __('messages.delete') }}"
                                onclick="return confirm('{{ __('messages.delete_notification_confirm') }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h3>{{ __('messages.no_notifications') }}</h3>
                    <p>{{ __('messages.no_notifications_found') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="pagination-wrapper">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .notifications-container {
                background: white;
                border-radius: 12px;
                box-shadow: var(--card-shadow);
                padding: 24px;
            }

            .notifications-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
                padding-bottom: 16px;
                border-bottom: 2px solid #e5e7eb;
            }

            .header-left h2 {
                font-size: 24px;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 4px 0;
            }

            .header-left p {
                color: #6b7280;
                font-size: 14px;
                margin: 0;
            }

            .btn-mark-all-read {
                padding: 10px 20px;
                background: #3b82f6;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
            }

            .btn-mark-all-read:hover {
                background: #2563eb;
                transform: translateY(-1px);
            }

            .notifications-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .notification-item {
                display: flex;
                gap: 16px;
                padding: 16px;
                background: #f9fafb;
                border-radius: 12px;
                border: 2px solid transparent;
                transition: all 0.2s;
            }

            .notification-item.unread {
                background: #eff6ff;
                border-color: #3b82f6;
            }

            .notification-item:hover {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .notification-icon {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                flex-shrink: 0;
            }

            .notification-item.unread .notification-icon {
                background: #dbeafe;
                color: #2563eb;
            }

            .notification-item.read .notification-icon {
                background: #e5e7eb;
                color: #6b7280;
            }

            .notification-content {
                flex: 1;
            }

            .notification-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 8px;
            }

            .notification-title {
                font-size: 16px;
                font-weight: 700;
                color: #1f2937;
                margin: 0;
            }

            .notification-item.read .notification-title {
                color: #6b7280;
            }

            .notification-time {
                font-size: 12px;
                color: #9ca3af;
            }

            .notification-message {
                font-size: 14px;
                color: #4b5563;
                margin: 0 0 8px 0;
                line-height: 1.5;
            }

            .notification-item.read .notification-message {
                color: #9ca3af;
            }

            .notification-data {
                margin-top: 8px;
            }

            .notification-link {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: #3b82f6;
                color: white;
                border-radius: 6px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 600;
                transition: all 0.2s;
            }

            .notification-link:hover {
                background: #2563eb;
            }

            .notification-actions {
                display: flex;
                gap: 8px;
                align-items: flex-start;
            }

            .btn-mark-read,
            .btn-delete {
                width: 36px;
                height: 36px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
            }

            .btn-mark-read {
                background: #10b981;
                color: white;
            }

            .btn-mark-read:hover {
                background: #059669;
            }

            .btn-delete {
                background: #ef4444;
                color: white;
            }

            .btn-delete:hover {
                background: #dc2626;
            }

            .empty-state {
                text-align: center;
                padding: 60px 20px;
            }

            .empty-state i {
                font-size: 64px;
                color: #d1d5db;
                margin-bottom: 16px;
            }

            .empty-state h3 {
                font-size: 20px;
                font-weight: 700;
                color: #4b5563;
                margin: 0 0 8px 0;
            }

            .empty-state p {
                color: #9ca3af;
                font-size: 14px;
                margin: 0;
            }

            .pagination-wrapper {
                margin-top: 24px;
                display: flex;
                justify-content: center;
            }
        </style>
    @endpush
@endsection
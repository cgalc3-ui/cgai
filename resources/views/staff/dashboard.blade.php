@extends('layouts.dashboard')

@section('title', __('messages.staff_dashboard'))
@section('page-title', __('messages.staff_dashboard'))

@section('content')
    <div class="dashboard-description">
        <h2>{{ __('messages.staff_dashboard') }}</h2>
        <p>{{ __('messages.staff_dashboard_desc') }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <!-- Total Bookings -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_bookings') }}</h3>
                <i class="fas fa-ellipsis-v stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle teal">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ number_format($stats['total_bookings'] ?? 0) }}</h2>
                        <span class="stat-card-trend up">
                            <i class="fas fa-arrow-up"></i> {{ number_format($stats['today_bookings'] ?? 0) }}
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ __('messages.booked_today') }}</span>
                </div>
            </div>
        </div>

        <!-- Today Bookings -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.today_bookings') }}</h3>
                <i class="fas fa-ellipsis-v stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle teal">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ number_format($stats['today_bookings'] ?? 0) }}</h2>
                        <span class="stat-card-trend up">
                            <i class="fas fa-arrow-up"></i> {{ number_format($stats['upcoming_bookings'] ?? 0) }}
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ __('messages.upcoming_bookings') }}</span>
                </div>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.pending') }}</h3>
                <i class="fas fa-ellipsis-v stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle yellow">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ number_format($stats['pending_bookings'] ?? 0) }}</h2>
                        <span class="stat-card-trend down">
                            <i class="fas fa-clock"></i>
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ __('messages.bookings_needing_review') }}</span>
                </div>
            </div>
        </div>

        <!-- Completed Bookings -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.completed_bookings') }}</h3>
                <i class="fas fa-ellipsis-v stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle light-blue">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ number_format($stats['completed_bookings'] ?? 0) }}</h2>
                        <span class="stat-card-trend up">
                            <i class="fas fa-arrow-up"></i> {{ number_format($stats['completed_bookings'] ?? 0) }}
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ __('messages.completed') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Section -->
    <div class="section-container">
        <div class="section-header">
            <h3>{{ __('messages.my_bookings') }}</h3>
            <p>{{ __('messages.latest_my_bookings') }}</p>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.client') }}</th>
                        <th>{{ __('messages.service') }}</th>
                        <th>{{ __('messages.datetime') }}</th>
                        <th>{{ __('messages.price') }}</th>
                        <th>{{ __('messages.booking_status') }}</th>
                        <th>{{ __('messages.payment_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings ?? [] as $booking)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">
                                        {{ optional($booking->customer)->name ?? __('messages.unspecified') }}</div>
                                    <div class="user-details">
                                        @if(optional($booking->customer)->phone)
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
                                            {{ optional($booking->consultation)->trans ? $booking->consultation->trans('name') : (optional($booking->consultation)->name ?? __('messages.unspecified')) }}
                                        @else
                                            {{ optional($booking->service)->trans ? $booking->service->trans('name') : (optional($booking->service)->name ?? __('messages.unspecified')) }}
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
                                    <div class="time">
                                        <i class="fas fa-clock"></i> {{ $booking->start_time }} - {{ $booking->end_time }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong class="price">{{ number_format($booking->total_price, 2) }}
                                    {{ __('messages.sar') }}</strong>
                            </td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-hourglass-half"></i> {{ __('messages.pending') }}
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge badge-info">
                                        <i class="fas fa-check-circle"></i> {{ __('messages.confirmed') }}
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-double"></i> {{ __('messages.completed') }}
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times-circle"></i> {{ __('messages.rejected') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($booking->payment_status === 'paid')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> {{ __('messages.paid') }}
                                    </span>
                                @elseif($booking->payment_status === 'unpaid')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-exclamation-triangle"></i> {{ __('messages.unpaid') }}
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-undo"></i> {{ __('messages.refunded') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
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
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card dashboard-card" style="margin-top: 30px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bolt" style="color: #f59e0b; margin-left: 8px;"></i>
                {{ __('messages.quick_actions') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="actions-grid-custom">
                <a href="{{ route('staff.my-bookings') }}" class="action-tile tile-indigo">
                    <div class="action-icon-box">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.my_bookings') }}</span>
                        <span class="action-desc">{{ __('messages.view_all_my_bookings') }}</span>
                    </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>

                <a href="{{ route('staff.my-schedule') }}" class="action-tile tile-amber">
                    <div class="action-icon-box">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.work_days') }}</span>
                        <span class="action-desc">{{ __('messages.manage_schedule') }}</span>
                    </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>

                <a href="javascript:void(0)" onclick="openTicketsModal()" class="action-tile tile-pink">
                    <div class="action-icon-box">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-name">{{ __('messages.tickets_support') }}</span>
                        <span class="action-desc">{{ __('messages.manage_tickets_desc') }}</span>
                    </div>
                    <i class="fas fa-chevron-left action-arrow"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Tickets Modal -->
    <div id="ticketsModal" class="modal-overlay" style="display: none;">
        <div class="modal-container tickets-modal">
            <div class="modal-header">
                <h2><i class="fas fa-headset"></i> {{ __('messages.tickets_support') }}</h2>
                <button class="modal-close" onclick="closeTicketsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="tickets-filters">
                    <select id="ticketStatusFilter" onchange="loadTickets()">
                        <option value="">{{ __('messages.all_statuses') }}</option>
                        <option value="open">{{ __('messages.open') }}</option>
                        <option value="in_progress">{{ __('messages.in_progress') }}</option>
                        <option value="resolved">{{ __('messages.resolved') }}</option>
                        <option value="closed">{{ __('messages.closed') }}</option>
                    </select>
                    <select id="ticketPriorityFilter" onchange="loadTickets()">
                        <option value="">{{ __('messages.all_priorities') }}</option>
                        <option value="low">{{ __('messages.low') }}</option>
                        <option value="medium">{{ __('messages.medium') }}</option>
                        <option value="high">{{ __('messages.high') }}</option>
                        <option value="urgent">{{ __('messages.urgent') }}</option>
                    </select>
                    <button class="btn btn-primary" onclick="openCreateTicketModal()">
                        <i class="fas fa-plus"></i> {{ __('messages.new_ticket') }}
                    </button>
                </div>
                <div id="ticketsList" class="tickets-list-modal">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Ticket Modal -->
    <div id="createTicketModal" class="modal-overlay" style="display: none;">
        <div class="modal-container create-ticket-modal">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> {{ __('messages.create_new_ticket') }}</h2>
                <button class="modal-close" onclick="closeCreateTicketModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="createTicketForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="ticketSubject">{{ __('messages.subject') }} <span class="required">*</span></label>
                        <input type="text" name="subject" id="ticketSubject" class="form-input" required>
                        <span class="error-message" id="subjectError"></span>
                    </div>

                    <div class="form-group">
                        <label for="ticketPriority">{{ __('messages.priority') }}</label>
                        <select name="priority" id="ticketPriority" class="form-select">
                            <option value="low">{{ __('messages.low') }}</option>
                            <option value="medium" selected>{{ __('messages.medium') }}</option>
                            <option value="high">{{ __('messages.high') }}</option>
                            <option value="urgent">{{ __('messages.urgent') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ticketDescription">{{ __('messages.description') }} <span
                                class="required">*</span></label>
                        <textarea name="description" id="ticketDescription" class="form-textarea" rows="6"
                            required></textarea>
                        <span class="error-message" id="descriptionError"></span>
                    </div>

                    <div class="form-group">
                        <label for="ticketAttachments">{{ __('messages.attach_images') }}
                            ({{ __('messages.optional') }})</label>
                        <input type="file" name="attachments[]" id="ticketAttachments" class="form-input" multiple
                            accept="image/*">
                        <small class="form-help">{{ __('messages.attachment_help') }}</small>
                        <span class="error-message" id="attachmentsError"></span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="submitTicketBtn">
                            <i class="fas fa-paper-plane"></i>
                            {{ __('messages.send_ticket') }}
                        </button>
                        <button type="button" class="btn-cancel" onclick="closeCreateTicketModal()">
                            {{ __('messages.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Stat Card Icon Colors - Matching Admin Dashboard */
            .stat-card-icon-circle.teal {
                background: rgba(20, 184, 166, 0.1);
                color: #14b8a6;
            }

            .stat-card-icon-circle.yellow {
                background: rgba(234, 179, 8, 0.1);
                color: #eab308;
            }

            .stat-card-icon-circle.light-blue {
                background: rgba(59, 130, 246, 0.1);
                color: #3b82f6;
            }

            .stat-card-trend.down {
                color: #ef4444;
            }

            /* Dashboard Cards */
            .dashboard-card {
                background: white;
                border-radius: 16px;
                border: 1px solid #f1f5f9;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            [data-theme="dark"] .dashboard-card {
                background: var(--card-bg);
                border-color: var(--border-color);
            }

            .dashboard-card .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 25px;
                border-bottom: 1px solid #f1f5f9;
            }

            [data-theme="dark"] .dashboard-card .card-header {
                border-bottom-color: var(--border-color);
            }

            .dashboard-card .card-title {
                font-size: 18px;
                font-weight: 700;
                color: #1e293b;
                margin: 0;
            }

            [data-theme="dark"] .dashboard-card .card-title {
                color: var(--text-primary);
            }

            .dashboard-card .card-body {
                padding: 25px;
            }

            /* Quick Actions Grid */
            .actions-grid-custom {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 20px;
            }

            .action-tile {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 20px;
                background: #fff;
                border: 1px solid #f1f5f9;
                border-radius: 16px;
                text-decoration: none;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            [data-theme="dark"] .action-tile {
                background: var(--card-bg);
                border-color: var(--border-color);
            }

            .action-tile:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.05);
                border-color: #e2e8f0;
            }

            [data-theme="dark"] .action-tile:hover {
                border-color: var(--primary-color);
                box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.3);
            }

            .action-icon-box {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                transition: all 0.3s;
            }

            .tile-indigo .action-icon-box {
                background: rgba(99, 102, 241, 0.1);
                color: #6366f1;
            }

            [data-theme="dark"] .tile-indigo .action-icon-box {
                background: rgba(99, 102, 241, 0.2);
                color: #818cf8;
            }

            .tile-amber .action-icon-box {
                background: rgba(245, 158, 11, 0.1);
                color: #f59e0b;
            }

            [data-theme="dark"] .tile-amber .action-icon-box {
                background: rgba(245, 158, 11, 0.2);
                color: #fbbf24;
            }

            .tile-pink .action-icon-box {
                background: rgba(236, 72, 153, 0.1);
                color: #ec4899;
            }

            [data-theme="dark"] .tile-pink .action-icon-box {
                background: rgba(236, 72, 153, 0.2);
                color: #f472b6;
            }

            .action-tile:hover .action-icon-box {
                transform: scale(1.1);
            }

            .action-info {
                display: flex;
                flex-direction: column;
                gap: 2px;
            }

            .action-name {
                font-size: 15px;
                font-weight: 700;
                color: #1e293b;
            }

            [data-theme="dark"] .action-name {
                color: var(--text-primary);
            }

            .action-desc {
                font-size: 12px;
                color: #94a3b8;
                font-weight: 500;
            }

            [data-theme="dark"] .action-desc {
                color: var(--text-secondary);
            }

            .action-arrow {
                margin-right: auto;
                color: #cbd5e1;
                font-size: 14px;
                transition: transform 0.3s;
            }

            [data-theme="dark"] .action-arrow {
                color: #6b7280;
            }

            .action-tile:hover .action-arrow {
                transform: translateX(-5px);
                color: #64748b;
            }

            [data-theme="dark"] .action-tile:hover .action-arrow {
                color: var(--text-primary);
            }

            /* Modal Styles */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.6);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .modal-container {
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                max-width: 900px;
                width: 100%;
                max-height: 90vh;
                display: flex;
                flex-direction: column;
            }

            .modal-header {
                padding: 20px 24px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-header h2 {
                margin: 0;
                font-size: 20px;
                color: #1f2937;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 24px;
                color: #6b7280;
                cursor: pointer;
                padding: 0;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 6px;
                transition: all 0.2s;
            }

            .modal-close:hover {
                background: #f3f4f6;
                color: #1f2937;
            }

            .modal-body {
                padding: 24px;
                overflow-y: auto;
                flex: 1;
            }

            .tickets-filters {
                display: flex;
                gap: 12px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }

            .tickets-filters select {
                padding: 10px 16px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 14px;
                background: white;
                min-width: 150px;
            }

            .tickets-filters .btn {
                padding: 10px 20px;
                border-radius: 8px;
                border: none;
                cursor: pointer;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .tickets-list-modal {
                min-height: 300px;
            }

            .ticket-item-modal {
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 16px;
                margin-bottom: 12px;
                cursor: pointer;
                transition: all 0.2s;
            }

            .ticket-item-modal:hover {
                background: #f3f4f6;
                border-color: #d1d5db;
            }

            .ticket-item-header {
                display: flex;
                justify-content: space-between;
                align-items: start;
                margin-bottom: 12px;
            }

            .ticket-item-title {
                font-weight: 600;
                color: #1f2937;
                font-size: 16px;
                margin: 0 0 4px 0;
            }

            .ticket-item-meta {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                font-size: 12px;
                color: #6b7280;
            }

            .ticket-item-description {
                color: #4b5563;
                font-size: 14px;
                margin-top: 8px;
                line-height: 1.5;
            }

            .badge {
                padding: 4px 12px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 500;
            }

            .badge-open {
                background: #fef3c7;
                color: #92400e;
            }

            .badge-in_progress {
                background: #dbeafe;
                color: #1e40af;
            }

            .badge-resolved {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-closed {
                background: #e5e7eb;
                color: #374151;
            }

            .badge-urgent {
                background: #fee2e2;
                color: #991b1b;
            }

            .badge-high {
                background: #fed7aa;
                color: #9a3412;
            }

            .badge-medium {
                background: #dbeafe;
                color: #1e40af;
            }

            .badge-low {
                background: #e5e7eb;
                color: #6b7280;
            }

            .loading-spinner {
                text-align: center;
                padding: 40px;
                color: #6b7280;
            }

            .empty-state-modal {
                text-align: center;
                padding: 60px 20px;
                color: #6b7280;
            }

            .empty-state-modal i {
                font-size: 48px;
                margin-bottom: 16px;
                color: #d1d5db;
            }

            /* Create Ticket Modal Styles */
            .create-ticket-modal {
                max-width: 700px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
            }

            .required {
                color: #ef4444;
            }

            .form-input,
            .form-select,
            .form-textarea {
                width: 100%;
                padding: 12px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 14px;
                font-family: inherit;
                transition: all 0.2s;
                box-sizing: border-box;
            }

            .form-input:focus,
            .form-select:focus,
            .form-textarea:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .form-textarea {
                resize: vertical;
            }

            .form-help {
                display: block;
                font-size: 12px;
                color: #6b7280;
                margin-top: 4px;
            }

            .error-message {
                display: block;
                font-size: 12px;
                color: #ef4444;
                margin-top: 4px;
                min-height: 16px;
            }

            .form-actions {
                display: flex;
                gap: 12px;
                margin-top: 24px;
            }

            .btn-submit {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                background: #3b82f6;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-submit:hover:not(:disabled) {
                background: #2563eb;
            }

            .btn-submit:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            .btn-cancel {
                display: inline-flex;
                align-items: center;
                padding: 12px 24px;
                background: #f3f4f6;
                color: #4b5563;
                border: none;
                border-radius: 8px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-cancel:hover {
                background: #e5e7eb;
            }

            /* Dark Mode Styles for Modals */
            [data-theme="dark"] .modal-overlay {
                background: rgba(0, 0, 0, 0.8);
            }

            [data-theme="dark"] .modal-container {
                background: var(--card-bg);
                border: 1px solid var(--border-color);
            }

            [data-theme="dark"] .modal-header {
                border-bottom-color: var(--border-color);
            }

            [data-theme="dark"] .modal-header h2 {
                color: var(--text-primary);
            }

            [data-theme="dark"] .modal-close {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .modal-close:hover {
                background: var(--sidebar-active-bg);
                color: var(--text-primary);
            }

            [data-theme="dark"] .tickets-filters select {
                background: var(--card-bg);
                border-color: var(--border-color);
                color: var(--text-primary);
            }

            [data-theme="dark"] .tickets-filters select:focus {
                border-color: var(--primary-color);
                outline: none;
            }

            [data-theme="dark"] .tickets-filters .btn {
                background: var(--primary-color);
                color: white;
            }

            [data-theme="dark"] .tickets-filters .btn:hover {
                opacity: 0.9;
            }

            [data-theme="dark"] .ticket-item-modal {
                background: var(--sidebar-active-bg);
                border-color: var(--border-color);
            }

            [data-theme="dark"] .ticket-item-modal:hover {
                background: var(--card-bg);
                border-color: var(--primary-color);
            }

            [data-theme="dark"] .ticket-item-title {
                color: var(--text-primary);
            }

            [data-theme="dark"] .ticket-item-meta {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .ticket-item-description {
                color: var(--text-primary);
            }

            [data-theme="dark"] .loading-spinner {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .empty-state-modal {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .empty-state-modal i {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .form-group label {
                color: var(--text-primary);
            }

            [data-theme="dark"] .form-input,
            [data-theme="dark"] .form-select,
            [data-theme="dark"] .form-textarea {
                background: var(--card-bg);
                border-color: var(--border-color);
                color: var(--text-primary);
            }

            [data-theme="dark"] .form-input:focus,
            [data-theme="dark"] .form-select:focus,
            [data-theme="dark"] .form-textarea:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            }

            [data-theme="dark"] .form-help {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .btn-cancel {
                background: var(--sidebar-active-bg);
                color: var(--text-primary);
            }

            [data-theme="dark"] .btn-cancel:hover {
                background: var(--border-color);
            }

            /* Badge colors in dark mode */
            [data-theme="dark"] .badge-open {
                background: rgba(234, 179, 8, 0.2);
                color: #fbbf24;
            }

            [data-theme="dark"] .badge-in_progress {
                background: rgba(59, 130, 246, 0.2);
                color: #60a5fa;
            }

            [data-theme="dark"] .badge-resolved {
                background: rgba(34, 197, 94, 0.2);
                color: #4ade80;
            }

            [data-theme="dark"] .badge-closed {
                background: rgba(107, 114, 128, 0.2);
                color: #9ca3af;
            }

            [data-theme="dark"] .badge-urgent {
                background: rgba(239, 68, 68, 0.2);
                color: #f87171;
            }

            [data-theme="dark"] .badge-high {
                background: rgba(249, 115, 22, 0.2);
                color: #fb923c;
            }

            [data-theme="dark"] .badge-medium {
                background: rgba(59, 130, 246, 0.2);
                color: #60a5fa;
            }

            [data-theme="dark"] .badge-low {
                background: rgba(107, 114, 128, 0.2);
                color: #9ca3af;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function openTicketsModal() {
                document.getElementById('ticketsModal').style.display = 'flex';
                loadTickets();
            }

            function closeTicketsModal() {
                document.getElementById('ticketsModal').style.display = 'none';
            }

            function loadTickets() {
                const ticketsList = document.getElementById('ticketsList');
                const status = document.getElementById('ticketStatusFilter').value;
                const priority = document.getElementById('ticketPriorityFilter').value;

                ticketsList.innerHTML = `<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}</div>`;

                let url = '{{ route("staff.tickets") }}?';
                if (status) url += 'status=' + status + '&';
                if (priority) url += 'priority=' + priority + '&';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.data && data.data.data.length > 0) {
                            let html = '';
                            data.data.data.forEach(ticket => {
                                const statusBadge = getStatusBadge(ticket.status);
                                const priorityBadge = getPriorityBadge(ticket.priority);
                                const locale = '{{ app()->getLocale() === "ar" ? "ar-SA" : "en-US" }}';
                                const createdAt = new Date(ticket.created_at).toLocaleDateString(locale);

                                const ticketUrl = '{{ url("/tickets") }}/' + ticket.id;
                                html += `
                                                                                <div class="ticket-item-modal" onclick="window.location.href='${ticketUrl}'">
                                                                                    <div class="ticket-item-header">
                                                                                        <div>
                                                                                            <h3 class="ticket-item-title">${ticket.subject}</h3>
                                                                                            <div class="ticket-item-meta">
                                                                                                <span>#${ticket.id}</span>
                                                                                                <span><i class="fas fa-calendar"></i> ${createdAt}</span>
                                                                                                ${statusBadge}
                                                                                                ${priorityBadge}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <p class="ticket-item-description">${ticket.description ? ticket.description.substring(0, 100) + '...' : ''}</p>
                                                                                </div>
                                                                            `;
                            });
                            ticketsList.innerHTML = html;
                        } else {
                            ticketsList.innerHTML = `
                                                                            <div class="empty-state-modal">
                                                                                <i class="fas fa-inbox"></i>
                                                                                <h3>{{ __('messages.no_tickets') }}</h3>
                                                                                <p>{{ __('messages.no_tickets_found') }}</p>
                                                                            </div>
                                                                        `;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading tickets:', error);
                        ticketsList.innerHTML = `
                                                                        <div class="empty-state-modal">
                                                                            <i class="fas fa-exclamation-triangle"></i>
                                                                            <h3>{{ __('messages.error') }}</h3>
                                                                            <p>{{ __('messages.error_loading_tickets') }}</p>
                                                                        </div>
                                                                    `;
                    });
            }

            function getStatusBadge(status) {
                const badges = {
                    'open': `<span class="badge badge-open">{{ __('messages.open') }}</span>`,
                    'in_progress': `<span class="badge badge-in_progress">{{ __('messages.in_progress') }}</span>`,
                    'resolved': `<span class="badge badge-resolved">{{ __('messages.resolved') }}</span>`,
                    'closed': `<span class="badge badge-closed">{{ __('messages.closed') }}</span>`
                };
                return badges[status] || '';
            }

            function getPriorityBadge(priority) {
                const badges = {
                    'urgent': `<span class="badge badge-urgent">{{ __('messages.urgent') }}</span>`,
                    'high': `<span class="badge badge-high">{{ __('messages.high') }}</span>`,
                    'medium': `<span class="badge badge-medium">{{ __('messages.medium') }}</span>`,
                    'low': `<span class="badge badge-low">{{ __('messages.low') }}</span>`
                };
                return badges[priority] || '';
            }

            // Close modal when clicking outside
            document.getElementById('ticketsModal')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeTicketsModal();
                }
            });

            // Close modal on ESC key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const ticketsModal = document.getElementById('ticketsModal');
                    const createModal = document.getElementById('createTicketModal');
                    if (createModal && createModal.style.display !== 'none') {
                        closeCreateTicketModal();
                    } else if (ticketsModal && ticketsModal.style.display !== 'none') {
                        closeTicketsModal();
                    }
                }
            });

            // Create Ticket Modal Functions
            function openCreateTicketModal() {
                document.getElementById('createTicketModal').style.display = 'flex';
                // Clear form
                document.getElementById('createTicketForm').reset();
                // Clear errors
                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            }

            function closeCreateTicketModal() {
                document.getElementById('createTicketModal').style.display = 'none';
                // Clear form
                document.getElementById('createTicketForm').reset();
                // Clear errors
                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            }

            // Handle create ticket form submission
            document.getElementById('createTicketForm')?.addEventListener('submit', function (e) {
                e.preventDefault();

                const form = this;
                const submitBtn = document.getElementById('submitTicketBtn');
                const formData = new FormData(form);

                // Clear previous errors
                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}`;

                fetch('{{ route("tickets.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw { errors: data.errors || {}, message: data.message || '{{ __('messages.error') }}' };
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success || !data.errors) {
                            // Success - close modal and reload tickets
                            closeCreateTicketModal();
                            if (document.getElementById('ticketsModal').style.display !== 'none') {
                                loadTickets();
                            } else {
                                // If tickets modal is closed, open it and show the new ticket
                                openTicketsModal();
                                setTimeout(() => loadTickets(), 300);
                            }
                            // Show success message
                            alert('{{ __('messages.success') }}');
                        } else {
                            // Show validation errors
                            if (data.errors) {
                                if (data.errors.subject) {
                                    document.getElementById('subjectError').textContent = data.errors.subject[0];
                                }
                                if (data.errors.description) {
                                    document.getElementById('descriptionError').textContent = data.errors.description[0];
                                }
                                if (data.errors.attachments) {
                                    document.getElementById('attachmentsError').textContent = Array.isArray(data.errors.attachments)
                                        ? data.errors.attachments[0]
                                        : data.errors.attachments;
                                }
                                if (data.errors['attachments.*']) {
                                    document.getElementById('attachmentsError').textContent = Array.isArray(data.errors['attachments.*'])
                                        ? data.errors['attachments.*'][0]
                                        : data.errors['attachments.*'];
                                }
                            } else {
                                alert('{{ __('messages.error') }}: ' + (data.message || '{{ __('messages.error') }}'));
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('{{ __('messages.error') }}');
                    })
                    .finally(() => {
                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> {{ __('messages.send_ticket') }}';
                    });
            });

            // Close create modal when clicking outside
            document.getElementById('createTicketModal')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeCreateTicketModal();
                }
            });
        </script>
    @endpush
@endsection
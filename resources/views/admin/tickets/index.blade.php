@extends('layouts.dashboard')

@section('title', __('messages.tickets'))
@section('page-title', __('messages.tickets_list'))

@section('content')
    <div class="tickets-admin-container">
        <!-- Header -->
        <div class="tickets-header">
            <div class="header-left">
                <h2>{{ __('messages.tickets_list') }}</h2>
                <p>{{ __('messages.manage_tickets_desc') }}</p>
            </div>
            <div class="header-right">
                <span class="total-count">{{ __('messages.total_tickets') }}: {{ $tickets->total() }}</span>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
            <form method="GET" action="{{ route('admin.tickets') }}" class="filter-form">
                <div class="filter-inputs">
                    <div class="filter-group">
                        <label for="status"><i class="fas fa-tasks"></i> {{ __('messages.status') }}:</label>
                        <select name="status" id="status" class="filter-select">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('messages.open') }}
                            </option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>
                                {{ __('messages.in_progress') }}</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>
                                {{ __('messages.resolved') }}</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>
                                {{ __('messages.closed') }}</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="priority"><i class="fas fa-exclamation-circle"></i> {{ __('messages.priority') }}:</label>
                        <select name="priority" id="priority" class="filter-select">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('messages.low') }}
                            </option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>
                                {{ __('messages.medium') }}</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('messages.high') }}
                            </option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>
                                {{ __('messages.urgent') }}</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="search"><i class="fas fa-search"></i> {{ __('messages.search') }}:</label>
                        <input type="text" name="search" id="search" class="filter-input"
                            placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> {{ __('messages.search') }}
                    </button>
                    <a href="{{ route('admin.tickets') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.client') }}</th>
                        <th>{{ __('messages.subject') }}</th>
                        <th class="text-center">{{ __('messages.status') }}</th>
                        <th class="text-center">{{ __('messages.priority') }}</th>
                        <th>{{ __('messages.assigned_to') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">{{ $ticket->user->name }}</div>
                                    <div class="user-email">{{ $ticket->user->email }}</div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="ticket-link">
                                    {{ \Illuminate\Support\Str::limit($ticket->trans('subject'), 50) }}
                                </a>
                            </td>
                            <td class="text-center">
                                @if($ticket->status === 'open')
                                    <span class="status-pill pending">{{ __('messages.open') }}</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="status-pill active">{{ __('messages.in_progress') }}</span>
                                @elseif($ticket->status === 'resolved')
                                    <span class="status-pill confirmed">{{ __('messages.resolved') }}</span>
                                @else
                                    <span class="status-pill cancelled">{{ __('messages.closed') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($ticket->priority === 'urgent')
                                    <span class="status-pill cancelled">{{ __('messages.urgent') }}</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="status-pill pending">{{ __('messages.high') }}</span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="status-pill active">{{ __('messages.medium') }}</span>
                                @else
                                    <span class="status-pill completed">{{ __('messages.low') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $ticket->assignedUser->name ?? __('messages.unassigned') }}
                            </td>
                            <td>
                                {{ $ticket->created_at->format('Y-m-d') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="calm-action-btn"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-headset"></i>
                                    <h3>{{ __('messages.no_tickets') }}</h3>
                                    <p>{{ __('messages.no_tickets_desc') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
            <div class="pagination-wrapper">
                {{ $tickets->links('vendor.pagination.custom', ['itemName' => 'tickets']) }}
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .tickets-admin-container {
                background: white;
                border-radius: 12px;
                box-shadow: var(--card-shadow);
                padding: 24px;
            }

            .tickets-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
                padding-bottom: 16px;
                border-bottom: 2px solid #e5e7eb;
            }

            .tickets-header h2 {
                font-size: 24px;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 4px 0;
            }

            .tickets-header p {
                color: #6b7280;
                font-size: 14px;
                margin: 0;
            }


            .ticket-link {
                color: #3b82f6;
                text-decoration: none;
                font-weight: 600;
            }

            .ticket-link:hover {
                text-decoration: underline;
            }

            .btn-view {
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
            }

            .btn-view:hover {
                opacity: 0.9;
            }

            .empty-state {
                text-align: center;
                padding: 40px 20px;
            }

            .empty-state i {
                font-size: 48px;
                color: #d1d5db;
                margin-bottom: 16px;
            }

            .empty-state h3 {
                color: #4b5563;
                margin-bottom: 8px;
            }

            .empty-state p {
                color: #9ca3af;
            }

            /* Filter spacing - Add margin between search input and buttons */
            .tickets-admin-container .filter-form {
                gap: 40px;
            }

            /* Dark Mode Styles */
            [data-theme="dark"] .tickets-admin-container {
                background: var(--card-bg) !important;
                border: 1px solid var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .tickets-header {
                border-bottom-color: var(--border-color) !important;
            }

            [data-theme="dark"] .tickets-header h2 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .tickets-header p {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .total-count {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .filter-container {
                background: var(--card-bg) !important;
                border: 1px solid var(--border-color) !important;
            }

            [data-theme="dark"] .filter-group label {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .filter-select,
            [data-theme="dark"] .filter-input {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .filter-select:focus,
            [data-theme="dark"] .filter-input:focus {
                border-color: var(--primary-color) !important;
                box-shadow: 0 0 0 0.2rem rgba(102, 88, 221, 0.2) !important;
            }

            [data-theme="dark"] .btn-primary {
                background: var(--primary-color) !important;
                color: white !important;
                border-color: var(--primary-color) !important;
            }

            [data-theme="dark"] .btn-primary:hover {
                opacity: 0.9;
            }

            [data-theme="dark"] .btn-secondary {
                background: var(--sidebar-active-bg) !important;
                color: var(--text-primary) !important;
                border-color: var(--border-color) !important;
            }

            [data-theme="dark"] .btn-secondary:hover {
                opacity: 0.9;
            }

            [data-theme="dark"] .data-table {
                background: var(--card-bg) !important;
            }

            [data-theme="dark"] .data-table thead {
                background: var(--sidebar-active-bg) !important;
            }

            [data-theme="dark"] .data-table thead th {
                color: var(--text-primary) !important;
                border-bottom-color: var(--border-color) !important;
            }

            [data-theme="dark"] .data-table tbody tr {
                border-bottom-color: var(--border-color) !important;
            }

            [data-theme="dark"] .data-table tbody tr:hover {
                background: var(--sidebar-active-bg) !important;
            }

            [data-theme="dark"] .data-table tbody td {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .user-name {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .user-email {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .ticket-link {
                color: var(--primary-color) !important;
            }

            [data-theme="dark"] .ticket-link:hover {
                color: var(--primary-dark) !important;
            }

            [data-theme="dark"] .empty-state i {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .empty-state h3 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .empty-state p {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .pagination-wrapper {
                color: var(--text-primary);
            }

            [data-theme="dark"] .pagination-wrapper .pagination a,
            [data-theme="dark"] .pagination-wrapper .pagination span {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .pagination-wrapper .pagination .active span {
                background: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
                color: white !important;
            }
        </style>
    @endpush
@endsection
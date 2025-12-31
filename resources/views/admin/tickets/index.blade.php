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

        <!-- Filters -->
        <div class="filters-container">
            <form method="GET" action="{{ route('admin.tickets') }}" class="filters-form">
                <div class="filter-group">
                    <label>{{ __('messages.status') }}:</label>
                    <select name="status" class="filter-select">
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
                    <label>{{ __('messages.priority') }}:</label>
                    <select name="priority" class="filter-select">
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
                    <label>{{ __('messages.search') }}:</label>
                    <input type="text" name="search" class="filter-input"
                        placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn-filter">{{ __('messages.apply') }}</button>
                <a href="{{ route('admin.tickets') }}" class="btn-clear">{{ __('messages.clear') }}</a>
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
                                    {{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}
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
                {{ $tickets->links() }}
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

            .filters-container {
                background: #f9fafb;
                padding: 16px;
                border-radius: 8px;
                margin-bottom: 24px;
            }

            .filters-form {
                display: flex;
                gap: 16px;
                align-items: flex-end;
            }

            .filter-group {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .filter-group label {
                font-size: 12px;
                font-weight: 600;
                color: #4b5563;
            }

            .filter-select,
            .filter-input {
                padding: 8px 12px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                font-size: 14px;
                background: white;
            }

            .btn-filter,
            .btn-clear {
                padding: 8px 16px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                border: none;
                text-decoration: none;
                display: inline-block;
            }

            .btn-filter {
                background: #3b82f6;
                color: white;
            }

            .btn-clear {
                background: #e5e7eb;
                color: #4b5563;
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
                background: #2563eb;
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
        </style>
    @endpush
@endsection
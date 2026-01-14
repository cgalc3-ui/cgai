@extends('layouts.dashboard')

@section('title', __('messages.invoices'))
@section('page-title', __('messages.invoices_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.invoices_list') }}</h2>
            <p>{{ __('messages.manage_invoices_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.back_to_dashboard') }}
            </a>
            <a href="{{ route('admin.invoices.export', request()->all()) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> {{ __('messages.export') }}
            </a>
            <span class="total-count">{{ __('messages.total_invoices') }}: {{ $invoices->total() }}</span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid" style="margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_invoices') }}</h3>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle teal">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="stat-card-info">
                    <h2 class="stat-card-value">{{ number_format($stats['total_invoices']) }}</h2>
                    <span class="stat-card-subtitle">{{ __('messages.all_time') }}</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_revenue') }}</h3>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle green">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-card-info">
                    <h2 class="stat-card-value">{{ number_format($stats['total_revenue'], 2) }}</h2>
                    <span class="stat-card-subtitle">{{ __('messages.sar') }}</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.today_invoices') }}</h3>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle blue">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-card-info">
                    <h2 class="stat-card-value">{{ number_format($stats['today_invoices']) }}</h2>
                    <span class="stat-card-subtitle">{{ number_format($stats['today_revenue'], 2) }} {{ __('messages.sar') }}</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.month_invoices') }}</h3>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle purple">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-card-info">
                    <h2 class="stat-card-value">{{ number_format($stats['month_invoices']) }}</h2>
                    <span class="stat-card-subtitle">{{ number_format($stats['month_revenue'], 2) }} {{ __('messages.sar') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="customer_id"><i class="fas fa-user"></i> {{ __('messages.customer') }}:</label>
                    <select name="customer_id" id="customer_id" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="employee_id"><i class="fas fa-user-tie"></i> {{ __('messages.employee') }}:</label>
                    <select name="employee_id" id="employee_id" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->user->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="status"><i class="fas fa-tasks"></i> {{ __('messages.booking_status') }}:</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('messages.confirmed') }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date_from"><i class="fas fa-calendar-alt"></i> {{ __('messages.date_from') }}:</label>
                    <input type="date" name="date_from" id="date_from" class="filter-input" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label for="date_to"><i class="fas fa-calendar-alt"></i> {{ __('messages.date_to') }}:</label>
                    <input type="date" name="date_to" id="date_to" class="filter-input" value="{{ request('date_to') }}">
                </div>
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search') }}:</label>
                    <input type="text" name="search" id="search" class="filter-input"
                        placeholder="{{ __('messages.search_invoice_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> {{ __('messages.apply_filter') }}
                </button>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.invoice_number') }}</th>
                    <th>{{ __('messages.customer') }}</th>
                    <th>{{ __('messages.service') }}</th>
                    <th>{{ __('messages.employee') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td data-label="{{ __('messages.invoice_number') }}">
                            <strong>INV-{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</strong>
                        </td>
                        <td data-label="{{ __('messages.customer') }}">
                            <div class="user-info">
                                <div class="user-name">{{ $invoice->customer->name ?? __('messages.not_specified') }}</div>
                                <div class="user-details">
                                    @if($invoice->customer->phone)
                                        <span><i class="fas fa-phone"></i> {{ $invoice->customer->phone }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td data-label="{{ __('messages.service') }}">
                            <div class="service-info">
                                <div class="service-name">
                                    @if($invoice->booking_type === 'consultation')
                                        <i class="fas fa-comments" style="margin-left: 5px;"></i>
                                        {{ $invoice->consultation ? $invoice->consultation->trans('name') : __('messages.not_specified') }}
                                    @else
                                        {{ $invoice->service ? $invoice->service->trans('name') : __('messages.not_specified') }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td data-label="{{ __('messages.employee') }}">
                            {{ $invoice->employee && $invoice->employee->user ? $invoice->employee->user->name : __('messages.not_specified') }}
                        </td>
                        <td data-label="{{ __('messages.date') }}">
                            <div class="datetime-info">
                                <div class="date">
                                    <i class="fas fa-calendar"></i> {{ $invoice->booking_date->format('Y-m-d') }}
                                </div>
                                @if($invoice->paid_at)
                                    <div class="time" style="font-size: 11px; color: #718096;">
                                        <i class="fas fa-clock"></i> {{ __('messages.paid_at') }}: {{ $invoice->paid_at->format('Y-m-d H:i') }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td data-label="{{ __('messages.amount') }}">
                            <strong class="price">{{ number_format($invoice->total_price, 2) }} {{ __('messages.sar') }}</strong>
                        </td>
                        <td class="text-center" data-label="{{ __('messages.status') }}">
                            @if($invoice->status === 'pending')
                                <span class="badge badge-warning">
                                    <i class="fas fa-hourglass-half"></i> {{ __('messages.pending') }}
                                </span>
                            @elseif($invoice->status === 'confirmed')
                                <span class="badge badge-info">
                                    <i class="fas fa-check-circle"></i> {{ __('messages.confirmed') }}
                                </span>
                            @elseif($invoice->status === 'completed')
                                <span class="badge badge-success">
                                    <i class="fas fa-check-double"></i> {{ __('messages.completed') }}
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i> {{ __('messages.cancelled') }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center" data-label="{{ __('messages.actions') }}">
                            <div class="action-buttons" style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="calm-action-btn" title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.invoices.download', $invoice->id) }}" target="_blank" class="calm-action-btn" title="{{ __('messages.download') }}">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-file-invoice"></i>
                                <h3>{{ __('messages.no_invoices') }}</h3>
                                <p>{{ __('messages.no_invoices_desc') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($invoices->hasPages())
        <div class="pagination-wrapper">
            {{ $invoices->links('vendor.pagination.custom', ['itemName' => 'invoices']) }}
        </div>
    @endif

    @push('styles')
        <style>
            .stat-card-icon-circle.teal {
                background: rgba(20, 184, 166, 0.1);
                color: #14b8a6;
            }

            .stat-card-icon-circle.green {
                background: rgba(34, 197, 94, 0.1);
                color: #22c55e;
            }

            .stat-card-icon-circle.blue {
                background: rgba(59, 130, 246, 0.1);
                color: #3b82f6;
            }

            .stat-card-icon-circle.purple {
                background: rgba(168, 85, 247, 0.1);
                color: #a855f7;
            }

            [data-theme="dark"] .stat-card-icon-circle {
                opacity: 0.9;
            }

            /* Responsive Styles for Invoices Page */
            @media (min-width: 1200px) {
                .filter-form {
                    display: flex;
                    flex-direction: row;
                    align-items: flex-end;
                    gap: 24px;
                    width: 100%;
                }

                .filter-inputs {
                    display: flex;
                    flex: 1;
                    gap: 20px;
                    flex-wrap: wrap;
                    min-width: 0;
                }

                .filter-group {
                    min-width: 180px;
                    max-width: 250px;
                    flex: 1 1 auto;
                }

                .filter-actions {
                    display: flex;
                    flex-shrink: 0;
                    gap: 12px;
                    align-items: center;
                }
            }

            @media (min-width: 768px) and (max-width: 1199px) {
                .filter-form {
                    display: flex;
                    flex-direction: row;
                    align-items: flex-end;
                    gap: 20px;
                    width: 100%;
                }

                .filter-inputs {
                    display: flex;
                    flex: 1;
                    gap: 15px;
                    flex-wrap: wrap;
                    min-width: 0;
                }

                .filter-group {
                    min-width: 160px;
                    max-width: 220px;
                    flex: 1 1 auto;
                }

                .filter-actions {
                    display: flex;
                    flex-shrink: 0;
                    gap: 12px;
                    align-items: center;
                }
            }

            @media (max-width: 767px) {
                .filter-container {
                    padding: 20px;
                    margin-bottom: 20px;
                }

                .filter-container::before {
                    font-size: 14px;
                    margin-bottom: 15px;
                    padding-bottom: 10px;
                }

                .filter-form {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 20px;
                }

                .filter-inputs {
                    flex-direction: column;
                    gap: 15px;
                    width: 100%;
                    flex-wrap: nowrap;
                }

                .filter-group {
                    width: 100%;
                    min-width: 100%;
                    margin: 0;
                    flex: 1;
                }

                .filter-group label {
                    font-size: 13px;
                    margin-bottom: 6px;
                }

                .filter-input,
                .filter-select {
                    height: 44px;
                    font-size: 14px;
                    width: 100%;
                }

                .filter-actions {
                    width: 100%;
                    flex-direction: row;
                    gap: 10px;
                    margin-top: 0;
                    height: auto;
                    align-self: stretch;
                }

                .filter-actions .btn {
                    flex: 1;
                    width: auto;
                    height: 44px;
                    min-width: 0;
                }

                .page-header {
                    flex-direction: column;
                    gap: 15px;
                    align-items: flex-start;
                }

                .page-header-right {
                    flex-direction: column;
                    gap: 10px;
                    width: 100%;
                }

                .page-header-right .btn {
                    width: 100%;
                    justify-content: center;
                }

                .page-header-right .total-count {
                    width: 100%;
                    text-align: center;
                }

                /* Table to Cards Transformation */
                .data-table {
                    width: 100% !important;
                    font-size: 13px !important;
                    min-width: auto !important;
                }

                .data-table thead {
                    display: none !important;
                }

                .data-table tbody,
                .data-table tr,
                .data-table td {
                    display: block !important;
                    width: 100% !important;
                }

                .data-table tr {
                    margin-bottom: 20px !important;
                    background: white !important;
                    border-radius: 20px !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
                    padding: 15px !important;
                    border: none !important;
                }

                [data-theme="dark"] .data-table tr {
                    background: var(--card-bg, #1e1f27) !important;
                    border: none !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4) !important;
                }

                .data-table td {
                    display: grid !important;
                    grid-template-columns: 110px 1fr !important;
                    gap: 12px !important;
                    padding: 14px 20px !important;
                    border: none !important;
                    border-bottom: none !important;
                    text-align: start !important;
                    align-items: center !important;
                    font-size: 14px !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    color: var(--text-primary) !important;
                    font-weight: 500 !important;
                    word-wrap: break-word !important;
                    overflow-wrap: break-word !important;
                }

                html[dir='rtl'] .data-table td {
                    grid-template-columns: 1fr 110px !important;
                    text-align: end !important;
                }

                [data-theme="dark"] .data-table td {
                    border: none !important;
                    border-bottom: none !important;
                    color: var(--text-primary, #f1f5f9) !important;
                }

                .data-table td:last-child {
                    border: none !important;
                    border-bottom: none !important;
                }

                .data-table .badge {
                    padding: 4px 12px !important;
                    font-size: 11px !important;
                    font-weight: 800 !important;
                    border-radius: 30px !important;
                    display: inline-flex !important;
                    width: fit-content !important;
                    margin: 0 !important;
                }

                .data-table td::before {
                    content: attr(data-label) !important;
                    font-weight: 800 !important;
                    color: var(--primary-color) !important;
                    font-size: 10px !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.5px !important;
                    opacity: 0.6 !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }

                html[dir='rtl'] .data-table td::before {
                    order: 2 !important;
                    text-align: right !important;
                }

                .data-table td > * {
                    order: 1 !important;
                }

                .data-table td.text-center {
                    text-align: start !important;
                }

                html[dir='rtl'] .data-table td.text-center {
                    text-align: end !important;
                }

                /* Actions column - First in card */
                .data-table td:last-child {
                    display: flex !important;
                    justify-content: flex-start !important;
                    background: #f8fafc !important;
                    padding: 14px 20px !important;
                    margin: 0 !important;
                    border-bottom: none !important;
                    grid-template-columns: 1fr !important;
                    order: -1 !important;
                }

                [data-theme="dark"] .data-table td:last-child {
                    background: var(--sidebar-active-bg, #15171d) !important;
                }

                html[dir='rtl'] .data-table td:last-child {
                    justify-content: flex-end !important;
                }

                .data-table td:last-child::before {
                    display: none !important;
                }

                .data-table td:last-child > div {
                    display: flex !important;
                    gap: 8px !important;
                    flex-wrap: nowrap !important;
                    justify-content: flex-start !important;
                    width: 100% !important;
                    align-items: center !important;
                }

                html[dir='rtl'] .data-table td:last-child > div {
                    justify-content: flex-end !important;
                }

                .data-table td:last-child > div form {
                    display: inline-block !important;
                    margin: 0 !important;
                }

                /* Smaller action buttons */
                .data-table .calm-action-btn {
                    width: 36px !important;
                    height: 36px !important;
                    min-width: 36px !important;
                    padding: 0 !important;
                    border-radius: 10px !important;
                    font-size: 14px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }

                .data-table .calm-action-btn i {
                    font-size: 14px !important;
                    margin: 0 !important;
                }
            }

            @media (max-width: 575px) {
                .filter-container {
                    padding: 15px;
                    margin-bottom: 15px;
                }

                .filter-container::before {
                    font-size: 13px;
                    margin-bottom: 12px;
                    padding-bottom: 8px;
                }

                .filter-form {
                    gap: 15px;
                }

                .filter-inputs {
                    gap: 12px;
                }

                .filter-group {
                    gap: 6px;
                }

                .filter-group label {
                    font-size: 12px;
                }

                .filter-group label i {
                    width: 20px;
                    height: 20px;
                    font-size: 12px;
                }

                .filter-input,
                .filter-select {
                    height: 42px;
                    font-size: 13px;
                    padding: 0 14px;
                }

                .filter-actions {
                    flex-direction: column;
                    gap: 10px;
                    height: auto;
                }

                .filter-actions .btn {
                    width: 100%;
                    height: 42px;
                    font-size: 13px;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                    gap: 15px;
                    margin-bottom: 20px;
                }

                .stat-card {
                    padding: 18px;
                }

                .stat-card-value {
                    font-size: 22px;
                }

                .stat-card-icon-circle {
                    width: 45px;
                    height: 45px;
                    font-size: 20px;
                }

                .page-header {
                    padding: 15px;
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 15px;
                }

                .page-header-left {
                    width: 100%;
                }

                .page-header-left h2 {
                    font-size: 18px;
                }

                .page-header-left p {
                    font-size: 12px;
                }

                .page-header-right {
                    margin-top: 0;
                    width: 100%;
                    flex-direction: column;
                    gap: 10px;
                }

                .page-header-right .btn {
                    width: 100%;
                    justify-content: center;
                }

                .page-header-right .total-count {
                    width: 100%;
                    text-align: center;
                    padding: 8px 12px;
                    font-size: 12px;
                }

                .table-container {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    margin-bottom: 15px;
                }

                .data-table {
                    font-size: 13px !important;
                }

                .data-table tr {
                    padding: 12px !important;
                    border-radius: 16px !important;
                }

                .data-table td {
                    grid-template-columns: 90px 1fr !important;
                    padding: 12px 16px !important;
                    font-size: 13px !important;
                    gap: 10px !important;
                }

                html[dir='rtl'] .data-table td {
                    grid-template-columns: 1fr 90px !important;
                }

                .data-table td::before {
                    font-size: 9px !important;
                }

                .data-table .badge {
                    padding: 4px 10px !important;
                    font-size: 10px !important;
                    font-weight: 800 !important;
                    border-radius: 30px !important;
                    display: inline-flex !important;
                    width: fit-content !important;
                    margin: 0 !important;
                }

                .action-buttons {
                    display: flex !important;
                    flex-direction: row !important;
                    gap: 8px !important;
                    flex-wrap: nowrap !important;
                    justify-content: flex-start !important;
                    align-items: center !important;
                    width: 100% !important;
                }

                html[dir='rtl'] .action-buttons {
                    justify-content: flex-end !important;
                }

                .data-table .calm-action-btn {
                    width: 32px !important;
                    height: 32px !important;
                    min-width: 32px !important;
                    font-size: 13px !important;
                }

                .data-table .calm-action-btn i {
                    font-size: 13px !important;
                }

                .data-table td:last-child {
                    padding: 12px 20px !important;
                }

                .data-table td:last-child > div {
                    gap: 6px !important;
                    flex-wrap: nowrap !important;
                }

                .user-info .user-name {
                    font-size: 12px;
                }

                .user-info .user-details {
                    font-size: 11px;
                }

                .service-name {
                    font-size: 12px;
                }

                .datetime-info .date {
                    font-size: 11px;
                }

                .datetime-info .time {
                    font-size: 10px;
                }

                .price {
                    font-size: 13px;
                }

                .badge {
                    font-size: 10px;
                    padding: 4px 8px;
                }

                .pagination-wrapper {
                    padding: 15px 10px !important;
                }
            }
        </style>
    @endpush
@endsection


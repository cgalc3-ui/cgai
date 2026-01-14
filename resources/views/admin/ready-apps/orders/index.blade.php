@extends('layouts.dashboard')

@section('title', __('messages.ready_app_orders'))
@section('page-title', __('messages.ready_app_orders'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.ready_app_orders') }}</h2>
            <p>{{ __('messages.manage_ready_apps_desc') }}</p>
        </div>
        <div class="page-header-right">
            <span class="total-count">{{ __('messages.total') }}: {{ $orders->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.ready-apps.orders.index') }}" class="filter-form">
            <div class="filter-inputs" style="gap: 30px;">
                <div class="filter-group">
                    <label for="status"><i class="fas fa-toggle-on"></i> {{ __('messages.status') }}:</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                            {{ __('messages.pending') }}
                        </option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>
                            {{ __('messages.processing') }}
                        </option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                            {{ __('messages.approved') }}
                        </option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                            {{ __('messages.rejected') }}
                        </option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                            {{ __('messages.completed') }}
                        </option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                            {{ __('messages.cancelled') }}
                        </option>
                    </select>
                </div>
                <div class="filter-group" style="margin-inline-start: 40px;">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search') }}:</label>
                    <input type="text" name="search" id="search" class="filter-input"
                        placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="filter-actions" style="margin-inline-start: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> {{ __('messages.apply_filter') }}
                </button>
                <a href="{{ route('admin.ready-apps.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.ready_app_name') }}</th>
                    <th>{{ __('messages.customer') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->app->trans('name') }}</td>
                        <td>
                            <div>
                                <strong>{{ $order->user->name }}</strong><br>
                                <small class="text-muted">{{ $order->user->email }}</small>
                            </div>
                        </td>
                        <td>
                            <span style="color: #28a745; font-weight: 600;">{{ number_format($order->price, 2) }}
                                {{ $order->currency }}</span>
                        </td>
                        <td class="text-center">
                            @if($order->status === 'pending')
                                <span class="status-pill pending">{{ __('messages.pending') }}</span>
                            @elseif($order->status === 'processing')
                                <span class="status-pill confirmed">{{ __('messages.processing') }}</span>
                            @elseif($order->status === 'approved')
                                <span class="status-pill completed">{{ __('messages.approved') }}</span>
                            @elseif($order->status === 'rejected')
                                <span class="status-pill cancelled">{{ __('messages.rejected') }}</span>
                            @elseif($order->status === 'completed')
                                <span class="status-pill completed">{{ __('messages.completed') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.cancelled') }}</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.ready-apps.orders.show', $order) }}" class="calm-action-btn primary"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $orders->links('vendor.pagination.custom', ['itemName' => 'orders']) }}
        </div>
    </div>
@endsection


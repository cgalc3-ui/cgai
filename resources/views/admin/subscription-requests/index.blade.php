@extends('layouts.dashboard')

@section('title', __('messages.subscription_requests'))
@section('page-title', __('messages.subscription_requests_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.subscription_requests_list') }}</h2>
            <p>{{ __('messages.manage_subscription_requests_desc') }}</p>
        </div>
        <div class="page-header-right">
            <span class="total-count">{{ __('messages.total_requests') }}: {{ $requests->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.subscription-requests.index') }}" class="filter-form">
            <div class="filter-inputs" style="gap: 30px;">
                <div class="filter-group">
                    <label for="status"><i class="fas fa-tasks"></i> {{ __('messages.request_status') }}:</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                            {{ __('messages.pending') }}
                        </option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                            {{ __('messages.approved') }}
                        </option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                            {{ __('messages.rejected') }}
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="subscription_id"><i class="fas fa-box"></i> {{ __('messages.package') }}:</label>
                    <select name="subscription_id" id="subscription_id" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($subscriptions as $subscription)
                            <option value="{{ $subscription->id }}" {{ request('subscription_id') == $subscription->id ? 'selected' : '' }}>
                                {{ $subscription->trans('name') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date"><i class="fas fa-calendar-alt"></i> {{ __('messages.date') }}:</label>
                    <input type="date" name="date" id="date" class="filter-input" value="{{ request('date') }}">
                </div>
                <div class="filter-group" style="margin-inline-start: 40px;">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search') }}:</label>
                    <input type="text" name="search" id="search" class="filter-input"
                        placeholder="{{ __('messages.search_customer_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="filter-actions" style="margin-inline-start: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> {{ __('messages.apply_filter') }}
                </button>
                <a href="{{ route('admin.subscription-requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.user') }}</th>
                    <th>{{ __('messages.package') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.request_date') }}</th>
                    <th>{{ __('messages.processed_by') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->subscription->name }}</td>
                        <td class="text-center">
                            @if($request->status == 'pending')
                                <span class="status-pill pending">{{ __('messages.pending') }}</span>
                            @elseif($request->status == 'approved')
                                <span class="status-pill completed">{{ __('messages.approved') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.rejected') }}</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $request->approver ? $request->approver->name : '-' }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.subscription-requests.show', $request) }}" class="calm-action-btn info"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('messages.no_subscription_requests') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
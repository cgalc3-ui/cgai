@extends('layouts.dashboard')

@section('title', __('messages.ai_service_requests'))
@section('page-title', __('messages.ai_service_requests_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.ai_service_requests_list') }}</h2>
            <p>{{ __('messages.manage_ai_service_requests_desc') }}</p>
        </div>
        <div class="page-header-right">
            <span class="total-count">{{ __('messages.total') }}: {{ $requests->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.ai-services.requests.index') }}" class="filter-form">
            <div class="filter-inputs" style="gap: 30px;">
                <div class="filter-group">
                    <label for="status"><i class="fas fa-toggle-on"></i> {{ __('messages.status') }}:</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                            {{ __('messages.pending') }}
                        </option>
                        <option value="reviewing" {{ request('status') === 'reviewing' ? 'selected' : '' }}>
                            {{ __('messages.reviewing') }}
                        </option>
                        <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>
                            {{ __('messages.quoted') }}
                        </option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                            {{ __('messages.approved') }}
                        </option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>
                            {{ __('messages.in_progress') }}
                        </option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                            {{ __('messages.completed') }}
                        </option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                            {{ __('messages.cancelled') }}
                        </option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                            {{ __('messages.rejected') }}
                        </option>
                    </select>
                </div>
                @if(isset($categories) && $categories->count() > 0)
                <div class="filter-group">
                    <label for="category_id"><i class="fas fa-tags"></i> {{ __('messages.ai_service_category') }}:</label>
                    <select name="category_id" id="category_id" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->trans('name') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
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
                <a href="{{ route('admin.ai-services.requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.ai_service_request_title') }}</th>
                    <th>{{ __('messages.ai_service_category') }}</th>
                    <th>{{ __('messages.customer') }}</th>
                    <th>{{ __('messages.ai_service_request_budget_range') }}</th>
                    <th>{{ __('messages.ai_service_request_estimated_price') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>
                            <strong>{{ $request->title }}</strong>
                            @if($request->urgency === 'high')
                                <span class="badge badge-danger" style="margin-inline-start: 8px;">
                                    <i class="fas fa-exclamation-triangle"></i> {{ __('messages.high') }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $request->category->trans('name') }}</td>
                        <td>
                            <div>
                                <strong>{{ $request->user->name }}</strong><br>
                                <small class="text-muted">{{ $request->user->email }}</small>
                            </div>
                        </td>
                        <td>
                            @if($request->budget_range === 'custom' && $request->custom_budget)
                                <span style="color: #28a745; font-weight: 600;">{{ number_format($request->custom_budget, 2) }} {{ $request->currency }}</span>
                            @else
                                <span class="badge badge-info">{{ __('messages.' . $request->budget_range) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($request->estimated_price)
                                <span style="color: #3b82f6; font-weight: 600;">{{ number_format($request->estimated_price, 2) }} {{ $request->currency }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($request->status === 'pending')
                                <span class="status-pill pending">{{ __('messages.pending') }}</span>
                            @elseif($request->status === 'reviewing')
                                <span class="status-pill confirmed">{{ __('messages.reviewing') }}</span>
                            @elseif($request->status === 'quoted')
                                <span class="status-pill info">{{ __('messages.quoted') }}</span>
                            @elseif($request->status === 'approved')
                                <span class="status-pill completed">{{ __('messages.approved') }}</span>
                            @elseif($request->status === 'in_progress')
                                <span class="status-pill confirmed">{{ __('messages.in_progress') }}</span>
                            @elseif($request->status === 'completed')
                                <span class="status-pill completed">{{ __('messages.completed') }}</span>
                            @elseif($request->status === 'cancelled')
                                <span class="status-pill cancelled">{{ __('messages.cancelled') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.rejected') }}</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.ai-services.requests.show', $request) }}" class="calm-action-btn primary"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $requests->links('vendor.pagination.custom', ['itemName' => 'requests']) }}
        </div>
    </div>
@endsection


@extends('layouts.dashboard')

@section('title', __('messages.subscriptions'))
@section('page-title', __('messages.subscriptions_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.subscriptions_list') }}</h2>
            <p>{{ __('messages.manage_subscriptions_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_subscription') }}
            </a>
            <span class="total-count">{{ __('messages.total_subscriptions') }}: {{ $subscriptions->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="filter-form">
            <div class="filter-inputs" style="gap: 30px;">
                <div class="filter-group">
                    <label for="status"><i class="fas fa-toggle-on"></i> {{ __('messages.status') }}:</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                            {{ __('messages.active') }}
                        </option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            {{ __('messages.inactive') }}
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="duration_type"><i class="fas fa-calendar-alt"></i> {{ __('messages.duration_type') }}:</label>
                    <select name="duration_type" id="duration_type" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="monthly" {{ request('duration_type') === 'monthly' ? 'selected' : '' }}>
                            {{ __('messages.monthly') }}
                        </option>
                        <option value="3months" {{ request('duration_type') === '3months' ? 'selected' : '' }}>
                            {{ __('messages.3months') }}
                        </option>
                        <option value="6months" {{ request('duration_type') === '6months' ? 'selected' : '' }}>
                            {{ __('messages.6months') }}
                        </option>
                        <option value="yearly" {{ request('duration_type') === 'yearly' ? 'selected' : '' }}>
                            {{ __('messages.yearly') }}
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="ai_enabled"><i class="fas fa-robot"></i> {{ __('messages.ai_enabled') }}:</label>
                    <select name="ai_enabled" id="ai_enabled" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="enabled" {{ request('ai_enabled') === 'enabled' ? 'selected' : '' }}>
                            {{ __('messages.enabled') }}
                        </option>
                        <option value="disabled" {{ request('ai_enabled') === 'disabled' ? 'selected' : '' }}>
                            {{ __('messages.disabled') }}
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
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th>{{ __('messages.duration_type') }}</th>
                    <th>{{ __('messages.max_debtors') }}</th>
                    <th>{{ __('messages.max_messages') }}</th>
                    <th class="text-center">{{ __('messages.ai_enabled') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->trans('name') }}</td>
                        <td>{{ number_format($subscription->price, 2) }} {{ __('messages.sar') }}</td>
                        <td>{{ $subscription->trans('duration_text') ?? $subscription->duration_text }}</td>
                        <td>{{ $subscription->max_debtors == 0 ? __('messages.unlimited') : $subscription->max_debtors }}</td>
                        <td>{{ $subscription->max_messages == 0 ? __('messages.unlimited') : $subscription->max_messages }}</td>
                        <td class="text-center">
                            @if($subscription->ai_enabled)
                                <span class="status-pill completed">{{ __('messages.enabled') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.disabled') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($subscription->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="calm-action-btn info"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_subscription_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="calm-action-btn danger" title="{{ __('messages.delete') }}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">{{ __('messages.no_subscriptions') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $subscriptions->links() }}
        </div>
    </div>
@endsection
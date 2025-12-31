@extends('layouts.dashboard')

@section('title', __('messages.subscription_details'))
@section('page-title', __('messages.subscription_details'))

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>{{ __('messages.subscription_details') }}</h2>
        <p>{{ __('messages.view_subscription_details_desc') }}</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('messages.back') }}
        </a>
        <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
        </a>
    </div>
</div>

<div class="details-container">
    <div class="details-card">
        <h3>{{ __('messages.subscription_info') }}</h3>
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.name') }}:</span>
                <span class="detail-value">{{ $subscription->name }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.description') }}:</span>
                <span class="detail-value">{{ $subscription->description ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.price') }}:</span>
                <span class="detail-value">{{ number_format($subscription->price, 2) }} {{ __('messages.sar') }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.duration_type') }}:</span>
                <span class="detail-value">{{ $subscription->duration_text }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.max_debtors') }}:</span>
                <span class="detail-value">{{ $subscription->max_debtors == 0 ? __('messages.unlimited') : $subscription->max_debtors }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.max_messages') }}:</span>
                <span class="detail-value">{{ $subscription->max_messages == 0 ? __('messages.unlimited') : $subscription->max_messages }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.ai_enabled') }}:</span>
                <span class="detail-value">
                    @if($subscription->ai_enabled)
                        <span class="status-pill completed">{{ __('messages.enabled') }}</span>
                    @else
                        <span class="status-pill cancelled">{{ __('messages.disabled') }}</span>
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.status') }}:</span>
                <span class="detail-value">
                    @if($subscription->is_active)
                        <span class="status-pill completed">{{ __('messages.active') }}</span>
                    @else
                        <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.created_at') }}:</span>
                <span class="detail-value">{{ $subscription->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>

    <div class="details-card">
        <h3>{{ __('messages.statistics') }}</h3>
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.subscription_requests_count') }}:</span>
                <span class="detail-value">{{ $subscription->requests->count() }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.active_subscriptions_count') }}:</span>
                <span class="detail-value">{{ $subscription->userSubscriptions->where('status', 'active')->count() }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{ __('messages.total_subscriptions_count') }}:</span>
                <span class="detail-value">{{ $subscription->userSubscriptions->count() }}</span>
            </div>
        </div>
    </div>
</div>
@endsection


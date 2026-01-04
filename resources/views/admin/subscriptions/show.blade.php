@extends('layouts.dashboard')

@section('title', __('messages.subscription_details'))
@section('page-title', __('messages.subscription_details'))

@section('content')
<div class="subscription-modern-container">
    <!-- Header: Soft & Cohesive -->
    <header class="subscription-m-header">
        <div class="m-header-main">
            <h1 class="m-title">{{ $subscription->trans('name') }}</h1>
            <p class="m-subtitle">{{ __('messages.created_at') }}: {{ $subscription->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <div class="m-header-actions">
            <a href="{{ route('admin.subscriptions.index') }}" class="m-btn-back">
                <i class="fas fa-long-arrow-alt-right"></i> {{ __('messages.back') }}
            </a>
            <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="m-btn-edit">
                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
            </a>
        </div>
    </header>

    <!-- Essential Stats: Integrated Style -->
    <div class="m-stats-row">
        <div class="m-stat-card">
            <div class="m-stat-icon icon-purple"><i class="fas fa-tag"></i></div>
            <div class="m-stat-info">
                <span class="m-stat-label">{{ __('messages.price') }}</span>
                <span class="m-stat-value">{{ number_format($subscription->price, 2) }} {{ __('messages.sar') }}</span>
            </div>
        </div>
        <div class="m-stat-card">
            <div class="m-stat-icon icon-blue"><i class="fas fa-calendar-alt"></i></div>
            <div class="m-stat-info">
                <span class="m-stat-label">{{ __('messages.duration_type') }}</span>
                <span class="m-stat-value">{{ $subscription->duration_text }}</span>
            </div>
        </div>
        <div class="m-stat-card">
            <div class="m-stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
            <div class="m-stat-info">
                <span class="m-stat-label">{{ __('messages.status') }}</span>
                <span class="m-stat-status status-{{ $subscription->is_active ? 'active' : 'inactive' }}">
                    @if($subscription->is_active)
                        {{ __('messages.active') }}
                    @else
                        {{ __('messages.inactive') }}
                    @endif
                </span>
            </div>
        </div>
        <div class="m-stat-card">
            <div class="m-stat-icon icon-orange"><i class="fas fa-users"></i></div>
            <div class="m-stat-info">
                <span class="m-stat-label">{{ __('messages.active_subscriptions_count') }}</span>
                <span class="m-stat-value">{{ $subscription->userSubscriptions->where('status', 'active')->count() }}</span>
            </div>
        </div>
    </div>

    <div class="m-content-grid">
        <!-- Main Info Side -->
        <div class="m-main-column">
            <!-- Subscription Info Card -->
            <div class="m-card">
                <div class="m-card-header">
                    <i class="fas fa-info-circle"></i> {{ __('messages.subscription_info') }}
                </div>
                <div class="m-details-list">
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-signature"></i> {{ __('messages.name') }}
                        </span>
                        <span class="m-detail-value">{{ $subscription->trans('name') }}</span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-align-right"></i> {{ __('messages.description') }}
                        </span>
                        <span class="m-detail-value">{{ $subscription->trans('description') ?? '-' }}</span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-money-bill-wave"></i> {{ __('messages.price') }}
                        </span>
                        <span class="m-detail-value highlight">{{ number_format($subscription->price, 2) }} {{ __('messages.sar') }}</span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-clock"></i> {{ __('messages.duration_type') }}
                        </span>
                        <span class="m-detail-value">{{ $subscription->duration_text }}</span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-user-friends"></i> {{ __('messages.max_debtors') }}
                        </span>
                        <span class="m-detail-value">
                            @if($subscription->max_debtors == 0)
                                <span class="badge badge-info">{{ __('messages.unlimited') }}</span>
                            @else
                                {{ $subscription->max_debtors }}
                            @endif
                        </span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-envelope"></i> {{ __('messages.max_messages') }}
                        </span>
                        <span class="m-detail-value">
                            @if($subscription->max_messages == 0)
                                <span class="badge badge-info">{{ __('messages.unlimited') }}</span>
                            @else
                                {{ $subscription->max_messages }}
                            @endif
                        </span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-robot"></i> {{ __('messages.ai_enabled') }}
                        </span>
                        <span class="m-detail-value">
                            @if($subscription->ai_enabled)
                                <span class="badge badge-success">{{ __('messages.enabled') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('messages.disabled') }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-toggle-on"></i> {{ __('messages.status') }}
                        </span>
                        <span class="m-detail-value">
                            @if($subscription->is_active)
                                <span class="badge badge-success">{{ __('messages.active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('messages.inactive') }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Features Card -->
            @php
                $features = $subscription->features ?? null;
                if (is_string($features)) {
                    $features = json_decode($features, true) ?? [];
                }
                if (!is_array($features)) {
                    $features = [];
                }
            @endphp
            @if(!empty($features) && count($features) > 0)
            <div class="m-card">
                <div class="m-card-header">
                    <i class="fas fa-list-check"></i> {{ __('messages.features') }}
                </div>
                <div class="m-features-list">
                    @foreach($features as $feature)
                        <div class="m-feature-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>{{ is_array($feature) ? ($feature['name'] ?? $feature['title'] ?? '') : $feature }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Side Column -->
        <aside class="m-side-column">
            <!-- Statistics Card -->
            <div class="m-sticky-card">
                <div class="m-card-header">
                    <i class="fas fa-chart-bar"></i> {{ __('messages.statistics') }}
                </div>
                <div class="m-stats-list">
                    <div class="m-stat-item">
                        <div class="m-stat-icon-small icon-blue">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="m-stat-content">
                            <span class="m-stat-title">{{ __('messages.subscription_requests_count') }}</span>
                            <span class="m-stat-number">{{ $subscription->requests->count() }}</span>
                        </div>
                    </div>
                    <div class="m-stat-item">
                        <div class="m-stat-icon-small icon-green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="m-stat-content">
                            <span class="m-stat-title">{{ __('messages.active_subscriptions_count') }}</span>
                            <span class="m-stat-number">{{ $subscription->userSubscriptions->where('status', 'active')->count() }}</span>
                        </div>
                    </div>
                    <div class="m-stat-item">
                        <div class="m-stat-icon-small icon-purple">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="m-stat-content">
                            <span class="m-stat-title">{{ __('messages.total_subscriptions_count') }}</span>
                            <span class="m-stat-number">{{ $subscription->userSubscriptions->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="m-sticky-card">
                <div class="m-card-header">
                    <i class="fas fa-bolt"></i> {{ __('messages.quick_actions') }}
                </div>
                <div class="m-actions-list">
                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="m-action-btn primary">
                        <i class="fas fa-edit"></i>
                        <span>{{ __('messages.edit_subscription') }}</span>
                    </a>
                    <a href="{{ route('admin.subscription-requests.index', ['subscription_id' => $subscription->id]) }}" class="m-action-btn info">
                        <i class="fas fa-list"></i>
                        <span>{{ __('messages.view_requests') }}</span>
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>

<style>
.subscription-modern-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0;
}

/* Header */
.subscription-m-header {
    background: white;
    border-radius: 10px;
    padding: 25px 30px;
    margin-bottom: 20px;
    box-shadow: 0 0 35px 0 rgba(154, 161, 171, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.m-header-main {
    flex: 1;
}

.m-title {
    font-size: 28px;
    font-weight: 700;
    color: #343a40;
    margin: 0 0 8px 0;
}

.m-subtitle {
    font-size: 14px;
    color: #98a6ad;
    margin: 0;
}

.m-header-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.m-btn-back,
.m-btn-edit {
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.m-btn-back {
    background: #f1f3fa;
    color: #6c757d;
}

.m-btn-back:hover {
    background: #e9ecef;
    color: #343a40;
}

.m-btn-edit {
    background: #6658dd;
    color: white;
}

.m-btn-edit:hover {
    background: #564ab1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 88, 221, 0.3);
}

/* Stats Row */
.m-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.m-stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0 35px 0 rgba(154, 161, 171, 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s;
}

.m-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0 35px 0 rgba(154, 161, 171, 0.15);
}

.m-stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: white;
    flex-shrink: 0;
}

.m-stat-icon.icon-purple { background: #6658dd; }
.m-stat-icon.icon-blue { background: #4fc6e1; }
.m-stat-icon.icon-green { background: #1abc9c; }
.m-stat-icon.icon-orange { background: #f7b84b; }

.m-stat-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.m-stat-label {
    font-size: 12px;
    color: #98a6ad;
    font-weight: 600;
    text-transform: uppercase;
}

.m-stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #343a40;
}

.m-stat-status {
    font-size: 14px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    display: inline-block;
}

.m-stat-status.status-active {
    background: rgba(26, 188, 156, 0.1);
    color: #1abc9c;
}

.m-stat-status.status-inactive {
    background: rgba(241, 85, 108, 0.1);
    color: #f1556c;
}

/* Content Grid */
.m-content-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 25px;
}

.m-main-column {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.m-side-column {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

/* Cards */
.m-card,
.m-sticky-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 35px 0 rgba(154, 161, 171, 0.1);
    overflow: hidden;
}

.m-sticky-card {
    position: sticky;
    top: 90px;
}

.m-card-header {
    padding: 20px 25px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    font-size: 16px;
    font-weight: 700;
    color: #343a40;
    display: flex;
    align-items: center;
    gap: 10px;
}

.m-card-header i {
    color: #6658dd;
}

/* Details List */
.m-details-list {
    padding: 20px 25px;
}

.m-detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f1f3fa;
}

.m-detail-item:last-child {
    border-bottom: none;
}

.m-detail-label {
    font-size: 14px;
    color: #6c757d;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.m-detail-label i {
    color: #98a6ad;
    width: 18px;
}

.m-detail-value {
    font-size: 15px;
    color: #343a40;
    font-weight: 500;
    text-align: left;
}

.m-detail-value.highlight {
    color: #6658dd;
    font-weight: 700;
    font-size: 18px;
}

/* Features List */
.m-features-list {
    padding: 20px 25px;
}

.m-feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    font-size: 15px;
    color: #343a40;
}

.m-feature-item i {
    font-size: 16px;
}

/* Stats List */
.m-stats-list {
    padding: 20px 25px;
}

.m-stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f1f3fa;
}

.m-stat-item:last-child {
    border-bottom: none;
}

.m-stat-icon-small {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
    flex-shrink: 0;
}

.m-stat-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.m-stat-title {
    font-size: 13px;
    color: #98a6ad;
    font-weight: 600;
}

.m-stat-number {
    font-size: 24px;
    font-weight: 700;
    color: #343a40;
}

/* Actions List */
.m-actions-list {
    padding: 20px 25px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.m-action-btn {
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.m-action-btn.primary {
    background: #6658dd;
    color: white;
}

.m-action-btn.primary:hover {
    background: #564ab1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 88, 221, 0.3);
}

.m-action-btn.info {
    background: #4fc6e1;
    color: white;
}

.m-action-btn.info:hover {
    background: #3db5d0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(79, 198, 225, 0.3);
}

/* Badges */
.badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.badge-success {
    background: rgba(26, 188, 156, 0.1);
    color: #1abc9c;
}

.badge-danger {
    background: rgba(241, 85, 108, 0.1);
    color: #f1556c;
}

.badge-info {
    background: rgba(79, 198, 225, 0.1);
    color: #4fc6e1;
}

.badge-secondary {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

/* Responsive */
@media (max-width: 1200px) {
    .m-content-grid {
        grid-template-columns: 1fr;
    }
    
    .m-sticky-card {
        position: relative;
        top: 0;
    }
}

@media (max-width: 768px) {
    .subscription-m-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .m-header-actions {
        width: 100%;
    }
    
    .m-btn-back,
    .m-btn-edit {
        flex: 1;
        justify-content: center;
    }
    
    .m-stats-row {
        grid-template-columns: 1fr;
    }
    
    .m-detail-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .m-detail-value {
        text-align: right;
        width: 100%;
    }
}

/* Dark Mode Styles */
[data-theme="dark"] .subscription-modern-container {
    color: var(--text-primary);
}

[data-theme="dark"] .subscription-m-header {
    background: var(--card-bg) !important;
    border: 1px solid var(--border-color) !important;
    box-shadow: none !important;
}

[data-theme="dark"] .m-title {
    color: var(--text-primary) !important;
}

[data-theme="dark"] .m-subtitle {
    color: var(--text-secondary) !important;
}

[data-theme="dark"] .m-btn-back,
[data-theme="dark"] .m-btn-edit {
    background: var(--sidebar-active-bg) !important;
    color: var(--text-secondary) !important;
    border: 1px solid var(--border-color) !important;
}

[data-theme="dark"] .m-btn-back:hover,
[data-theme="dark"] .m-btn-edit:hover {
    background: var(--bg-light) !important;
    color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

[data-theme="dark"] .m-stat-card {
    background: var(--card-bg) !important;
    border: 1px solid var(--border-color) !important;
    box-shadow: none !important;
}

[data-theme="dark"] .m-stat-label {
    color: var(--text-secondary) !important;
}

[data-theme="dark"] .m-stat-value {
    color: var(--text-primary) !important;
}

[data-theme="dark"] .m-card,
[data-theme="dark"] .m-sticky-card {
    background: var(--card-bg) !important;
    border: 1px solid var(--border-color) !important;
    box-shadow: none !important;
}

[data-theme="dark"] .m-card-header {
    background: var(--sidebar-active-bg) !important;
    border-bottom-color: var(--border-color) !important;
    color: var(--text-primary) !important;
}

[data-theme="dark"] .m-card-header i {
    color: var(--primary-color) !important;
}

[data-theme="dark"] .m-details-list {
    color: var(--text-primary);
}

[data-theme="dark"] .m-detail-item {
    border-bottom-color: var(--border-color) !important;
}

[data-theme="dark"] .m-detail-label {
    color: var(--text-secondary) !important;
}

[data-theme="dark"] .m-detail-label i {
    color: var(--text-secondary) !important;
}

[data-theme="dark"] .m-detail-value {
    color: var(--text-primary) !important;
}

[data-theme="dark"] .m-detail-value.highlight {
    color: var(--primary-color) !important;
}

[data-theme="dark"] .m-features-list {
    color: var(--text-primary);
}

[data-theme="dark"] .m-feature-item {
    color: var(--text-primary);
}

[data-theme="dark"] .m-feature-item i {
    color: var(--success-color) !important;
}

[data-theme="dark"] .m-action-btn {
    background: var(--sidebar-active-bg) !important;
    color: var(--text-secondary) !important;
    border: 1px solid var(--border-color) !important;
}

[data-theme="dark"] .m-action-btn:hover {
    background: var(--bg-light) !important;
    color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

[data-theme="dark"] .m-action-btn.primary {
    background: var(--primary-color) !important;
    color: white !important;
    border-color: var(--primary-color) !important;
}

[data-theme="dark"] .m-action-btn.primary:hover {
    background: var(--primary-dark) !important;
}

[data-theme="dark"] .m-action-btn.info {
    background: var(--secondary-color) !important;
    color: white !important;
    border-color: var(--secondary-color) !important;
}

[data-theme="dark"] .m-action-btn.info:hover {
    background: #3db8d1 !important;
}

[data-theme="dark"] .badge {
    background: var(--sidebar-active-bg) !important;
    color: var(--text-primary) !important;
}

[data-theme="dark"] .badge-success {
    background: rgba(26, 188, 156, 0.2) !important;
    color: #1abc9c !important;
}

[data-theme="dark"] .badge-warning {
    background: rgba(247, 184, 75, 0.2) !important;
    color: #f7b84b !important;
}

[data-theme="dark"] .badge-info {
    background: rgba(79, 198, 225, 0.2) !important;
    color: #4fc6e1 !important;
}

[data-theme="dark"] .badge-secondary {
    background: rgba(108, 117, 125, 0.2) !important;
    color: var(--text-secondary) !important;
}

[data-theme="dark"] .text-success {
    color: var(--success-color) !important;
}

[data-theme="dark"] .text-warning {
    color: var(--warning-color) !important;
}

[data-theme="dark"] .text-danger {
    color: var(--danger-color) !important;
}
</style>
@endsection

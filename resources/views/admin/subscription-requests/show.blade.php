@extends('layouts.dashboard')

@section('title', __('messages.subscription_request_details'))
@section('page-title', __('messages.subscription_request_details'))

@section('content')
<div class="request-modern-container">
    <!-- Header: Soft & Cohesive -->
    <header class="request-m-header">
        <div class="m-header-main">
            <h1 class="m-title">{{ __('messages.subscription_request_details') }}</h1>
            <p class="m-subtitle">{{ __('messages.request_date') }}: {{ $request->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <div class="m-header-actions">
            <a href="{{ route('admin.subscription-requests.index') }}" class="m-btn-back">
                <i class="fas fa-long-arrow-alt-right"></i> {{ __('messages.back') }}
            </a>
        </div>
    </header>

    <!-- Status Badge -->
    <div class="m-status-banner">
        @if($request->status == 'pending')
            <div class="status-badge-pending">
                <i class="fas fa-hourglass-half"></i>
                <span>{{ __('messages.pending') }}</span>
            </div>
            <div class="m-approve-button-container">
                <form action="{{ route('admin.subscription-requests.approve', $request) }}" method="POST">
                    @csrf
                    <button type="submit" class="m-btn-approve">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ __('messages.approved') }}</span>
                    </button>
                </form>
            </div>
        @elseif($request->status == 'approved')
            <div class="status-badge-approved">
                <i class="fas fa-check-circle"></i>
                <span>{{ __('messages.approved') }}</span>
            </div>
        @else
            <div class="status-badge-rejected">
                <i class="fas fa-times-circle"></i>
                <span>{{ __('messages.rejected') }}</span>
            </div>
        @endif
    </div>

    <div class="m-content-grid">
        <!-- Main Info Side -->
        <div class="m-main-column">
            <!-- Request Info Card -->
            <div class="m-card">
                <div class="m-card-header">
                    <i class="fas fa-info-circle"></i> {{ __('messages.order_info') }}
                </div>
                <div class="m-details-list">
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-user"></i> {{ __('messages.user') }}
                        </span>
                        <span class="m-detail-value">
                            <span class="badge badge-info">{{ $request->user->name }}</span>
                        </span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-box"></i> {{ __('messages.package') }}
                        </span>
                        <span class="m-detail-value">
                            <span class="badge badge-warning">{{ $request->subscription->trans('name') }}</span>
                        </span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-money-bill-wave"></i> {{ __('messages.price') }}
                        </span>
                        <span class="m-detail-value highlight">{{ number_format($request->subscription->price, 2) }} {{ __('messages.sar') }}</span>
                    </div>
                    <div class="m-detail-item">
                        <span class="m-detail-label">
                            <i class="fas fa-calendar-alt"></i> {{ __('messages.request_date') }}
                        </span>
                        <span class="m-detail-value">{{ $request->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    @if($request->status !== 'pending')
                        <div class="m-detail-item">
                            <span class="m-detail-label">
                                <i class="fas fa-clock"></i> {{ $request->status == 'approved' ? __('messages.approved_at') : __('messages.rejected_at') }}
                            </span>
                            <span class="m-detail-value">
                                {{ $request->status == 'approved' ? $request->approved_at->format('Y-m-d H:i') : $request->rejected_at->format('Y-m-d H:i') }}
                            </span>
                        </div>
                        @if($request->approver)
                            <div class="m-detail-item">
                                <span class="m-detail-label">
                                    <i class="fas fa-user-shield"></i> {{ __('messages.processed_by') }}
                                </span>
                                <span class="m-detail-value">
                                    <span class="badge badge-secondary">{{ $request->approver->name }}</span>
                                </span>
                            </div>
                        @endif
                    @endif

                    @if($request->admin_notes)
                        <div class="m-detail-item full-width">
                            <span class="m-detail-label">
                                <i class="fas fa-sticky-note"></i> {{ __('messages.admin_notes') }}
                            </span>
                            <div class="m-notes-box">
                                {{ $request->admin_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Proof Card -->
            @if($request->payment_proof)
            <div class="m-card">
                <div class="m-card-header">
                    <i class="fas fa-receipt"></i> {{ __('messages.payment_proof') }}
                </div>
                <div class="m-payment-proof">
                    <a href="{{ asset('storage/' . $request->payment_proof) }}" target="_blank" class="m-proof-link">
                        <img src="{{ asset('storage/' . $request->payment_proof) }}" 
                             alt="{{ __('messages.payment_proof') }}" 
                             class="m-proof-image">
                        <div class="m-proof-overlay">
                            <i class="fas fa-expand"></i>
                            <span>{{ __('messages.click_to_view_full_image') }}</span>
                        </div>
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Side Column -->
        <aside class="m-side-column">
            @if($request->status == 'pending')
                <!-- Actions Card -->
                <div class="m-sticky-card">
                    <div class="m-card-header">
                        <i class="fas fa-tasks"></i> {{ __('messages.actions') }}
                    </div>
                    
                    <!-- Approve Form -->
                    <div class="m-action-section">
                        <div class="m-action-header approve">
                            <i class="fas fa-check-double"></i>
                            <span>{{ __('messages.approve_request') }}</span>
                        </div>
                        <form action="{{ route('admin.subscription-requests.approve', $request) }}" method="POST" class="m-action-form">
                            @csrf
                            <div class="m-form-group">
                                <label for="approve_notes">{{ __('messages.approve_notes') }}</label>
                                <textarea id="approve_notes" 
                                          name="admin_notes" 
                                          class="m-form-control" 
                                          rows="3"
                                          placeholder="{{ __('messages.admin_notes') }}..."></textarea>
                            </div>
                            <button type="submit" class="m-btn-submit approve">
                                <i class="fas fa-check"></i>
                                {{ __('messages.approve_request') }}
                            </button>
                        </form>
                    </div>

                    <div class="m-divider"></div>

                    <!-- Reject Form -->
                    <div class="m-action-section">
                        <div class="m-action-header reject">
                            <i class="fas fa-ban"></i>
                            <span>{{ __('messages.reject_request') }}</span>
                        </div>
                        <form action="{{ route('admin.subscription-requests.reject', $request) }}" method="POST" class="m-action-form">
                            @csrf
                            <div class="m-form-group">
                                <label for="reject_notes">
                                    {{ __('messages.rejection_reason') }} 
                                    <span class="required">*</span>
                                </label>
                                <textarea id="reject_notes" 
                                          name="admin_notes" 
                                          class="m-form-control" 
                                          rows="3" 
                                          required
                                          minlength="10"
                                          placeholder="{{ __('messages.rejection_reason') }}..."></textarea>
                            </div>
                            <button type="submit" class="m-btn-submit reject">
                                <i class="fas fa-times"></i>
                                {{ __('messages.reject_request') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- Status Info Card -->
                <div class="m-sticky-card">
                    <div class="m-card-header">
                        <i class="fas fa-info-circle"></i> {{ __('messages.request_status') }}
                    </div>
                    <div class="m-status-info">
                        <div class="m-status-item">
                            <span class="m-status-label">{{ __('messages.status') }}</span>
                            <span class="m-status-value status-{{ $request->status }}">
                                @if($request->status == 'approved')
                                    {{ __('messages.approved') }}
                                @else
                                    {{ __('messages.rejected') }}
                                @endif
                            </span>
                        </div>
                        @if($request->status == 'approved')
                            <div class="m-status-item">
                                <span class="m-status-label">{{ __('messages.approved_at') }}</span>
                                <span class="m-status-value">{{ $request->approved_at->format('Y-m-d H:i') }}</span>
                            </div>
                        @else
                            <div class="m-status-item">
                                <span class="m-status-label">{{ __('messages.rejected_at') }}</span>
                                <span class="m-status-value">{{ $request->rejected_at->format('Y-m-d H:i') }}</span>
                            </div>
                        @endif
                        @if($request->approver)
                            <div class="m-status-item">
                                <span class="m-status-label">{{ __('messages.processed_by') }}</span>
                                <span class="m-status-value">{{ $request->approver->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </aside>
    </div>
</div>

<style>
.request-modern-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0;
}

/* Header */
.request-m-header {
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

.m-btn-back {
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    background: #f1f3fa;
    color: #6c757d;
}

.m-btn-back:hover {
    background: #e9ecef;
    color: #343a40;
}

.m-btn-approve {
    padding: 12px 25px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    background: rgba(26, 188, 156, 0.1);
    color: #1abc9c;
    border: 2px solid #1abc9c;
    cursor: pointer;
    font-family: 'Cairo', sans-serif;
}

.m-btn-approve:hover {
    background: #1abc9c;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(26, 188, 156, 0.3);
}

.m-btn-approve i {
    font-size: 18px;
}

/* Status Banner */
.m-status-banner {
    margin-bottom: 25px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.m-approve-button-container {
    display: flex;
    justify-content: flex-start;
}

.status-badge-pending,
.status-badge-approved,
.status-badge-rejected {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 15px 25px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    box-shadow: 0 0 35px 0 rgba(154, 161, 171, 0.1);
}

.status-badge-pending {
    background: rgba(247, 184, 75, 0.1);
    color: #f7b84b;
    border: 2px solid #f7b84b;
}

.status-badge-approved {
    background: rgba(26, 188, 156, 0.1);
    color: #1abc9c;
    border: 2px solid #1abc9c;
}

.status-badge-rejected {
    background: rgba(241, 85, 108, 0.1);
    color: #f1556c;
    border: 2px solid #f1556c;
}

.status-badge-pending i,
.status-badge-approved i,
.status-badge-rejected i {
    font-size: 20px;
}

/* Content Grid */
.m-content-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
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

.m-detail-item.full-width {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
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

.m-notes-box {
    background: #f8fafc;
    padding: 15px;
    border-radius: 8px;
    border: 1px dashed #e2e8f0;
    width: 100%;
    font-size: 14px;
    color: #343a40;
    line-height: 1.6;
}

/* Payment Proof */
.m-payment-proof {
    padding: 20px 25px;
    text-align: center;
}

.m-proof-link {
    display: block;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    background: #f8f9fa;
    border: 2px solid #eef2f7;
    transition: all 0.3s;
}

.m-proof-link:hover {
    border-color: #6658dd;
    box-shadow: 0 4px 15px rgba(102, 88, 221, 0.2);
}

.m-proof-image {
    max-width: 100%;
    max-height: 500px;
    display: block;
    margin: 0 auto;
}

.m-proof-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: white;
    opacity: 0;
    transition: opacity 0.3s;
}

.m-proof-link:hover .m-proof-overlay {
    opacity: 1;
}

.m-proof-overlay i {
    font-size: 32px;
}

.m-proof-overlay span {
    font-size: 14px;
    font-weight: 600;
}

/* Action Sections */
.m-action-section {
    padding: 20px 25px;
}

.m-action-section:first-child {
    padding-top: 25px;
}

.m-action-header {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 15px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f1f3fa;
}

.m-action-header.approve {
    color: #1abc9c;
    border-bottom-color: rgba(26, 188, 156, 0.2);
}

.m-action-header.reject {
    color: #f1556c;
    border-bottom-color: rgba(241, 85, 108, 0.2);
}

.m-action-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.m-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.m-form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #6c757d;
}

.m-form-group label .required {
    color: #f1556c;
}

.m-form-control {
    padding: 12px 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Cairo', sans-serif;
    resize: vertical;
    transition: all 0.3s;
}

.m-form-control:focus {
    outline: none;
    border-color: #6658dd;
    box-shadow: 0 0 0 0.2rem rgba(102, 88, 221, 0.1);
}

.m-btn-submit {
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s;
    font-family: 'Cairo', sans-serif;
}

.m-btn-submit.approve {
    background: #1abc9c;
    color: white;
}

.m-btn-submit.approve:hover {
    background: #16a085;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(26, 188, 156, 0.3);
}

.m-btn-submit.reject {
    background: #f1556c;
    color: white;
}

.m-btn-submit.reject:hover {
    background: #e74c5c;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(241, 85, 108, 0.3);
}

.m-divider {
    height: 1px;
    background: #e9ecef;
    margin: 0 25px;
}

/* Status Info */
.m-status-info {
    padding: 20px 25px;
}

.m-status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f1f3fa;
}

.m-status-item:last-child {
    border-bottom: none;
}

.m-status-label {
    font-size: 14px;
    color: #6c757d;
    font-weight: 600;
}

.m-status-value {
    font-size: 15px;
    color: #343a40;
    font-weight: 600;
}

.m-status-value.status-approved {
    color: #1abc9c;
}

.m-status-value.status-rejected {
    color: #f1556c;
}

/* Badges */
.badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
}

.badge-info {
    background: rgba(79, 198, 225, 0.1);
    color: #4fc6e1;
}

.badge-warning {
    background: rgba(247, 184, 75, 0.1);
    color: #f7b84b;
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
    .request-m-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .m-header-actions {
        width: 100%;
    }
    
    .m-btn-back {
        flex: 1;
        justify-content: center;
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
    
    .m-status-banner {
        text-align: center;
    }
}
</style>
@endsection

@extends('layouts.dashboard')

@section('title', __('messages.subscription_request_details'))
@section('page-title', __('messages.subscription_request_details'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.subscription_request_details') }}</h2>
            <p>{{ __('messages.manage_subscription_request_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.subscription-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>

    <div class="details-container">
        <div class="details-card">
            <h3>{{ __('messages.order_info') }}</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">{{ __('messages.user') }}:</span>
                    <span class="detail-value">{{ $request->user->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">{{ __('messages.package') }}:</span>
                    <span class="detail-value">{{ $request->subscription->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">{{ __('messages.price') }}:</span>
                    <span class="detail-value">{{ number_format($request->subscription->price, 2) }}
                        {{ __('messages.sar') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">{{ __('messages.status') }}:</span>
                    <span class="detail-value">
                        @if($request->status == 'pending')
                            <span class="status-pill pending">{{ __('messages.pending') }}</span>
                        @elseif($request->status == 'approved')
                            <span class="status-pill completed">{{ __('messages.approved') }}</span>
                        @else
                            <span class="status-pill cancelled">{{ __('messages.rejected') }}</span>
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">{{ __('messages.request_date') }}:</span>
                    <span class="detail-value">{{ $request->created_at->format('Y-m-d H:i') }}</span>
                </div>
                @if($request->approved_at)
                    <div class="detail-item">
                        <span class="detail-label">{{ __('messages.approved_at') }}:</span>
                        <span class="detail-value">{{ $request->approved_at->format('Y-m-d H:i') }}</span>
                    </div>
                @endif
                @if($request->rejected_at)
                    <div class="detail-item">
                        <span class="detail-label">{{ __('messages.rejected_at') }}:</span>
                        <span class="detail-value">{{ $request->rejected_at->format('Y-m-d H:i') }}</span>
                    </div>
                @endif
                @if($request->approver)
                    <div class="detail-item">
                        <span class="detail-label">{{ __('messages.processed_by') }}:</span>
                        <span class="detail-value">{{ $request->approver->name }}</span>
                    </div>
                @endif
                @if($request->admin_notes)
                    <div class="detail-item">
                        <span class="detail-label">{{ __('messages.admin_notes') }}:</span>
                        <span class="detail-value">{{ $request->admin_notes }}</span>
                    </div>
                @endif
            </div>
        </div>

        @if($request->payment_proof)
            <div class="details-card">
                <h3>{{ __('messages.payment_proof') }}</h3>
                <div class="payment-proof-container">
                    <img src="{{ asset('storage/' . $request->payment_proof) }}" alt="{{ __('messages.payment_proof') }}"
                        style="max-width: 100%; height: auto; border-radius: 8px;">
                </div>
            </div>
        @endif

        @if($request->status == 'pending')
            <div class="details-card">
                <h3>{{ __('messages.actions') }}</h3>
                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                    <form action="{{ route('admin.subscription-requests.approve', $request) }}" method="POST"
                        style="flex: 1; min-width: 300px;">
                        @csrf
                        <div class="form-group">
                            <label for="approve_notes">{{ __('messages.approve_notes') }}</label>
                            <textarea id="approve_notes" name="admin_notes" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success" style="width: 100%;">
                            <i class="fas fa-check"></i> {{ __('messages.approve_request') }}
                        </button>
                    </form>

                    <form action="{{ route('admin.subscription-requests.reject', $request) }}" method="POST"
                        style="flex: 1; min-width: 300px;">
                        @csrf
                        <div class="form-group">
                            <label for="reject_notes">{{ __('messages.rejection_reason') }} <span
                                    class="required">*</span></label>
                            <textarea id="reject_notes" name="admin_notes" class="form-control" rows="3" required
                                minlength="10"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger" style="width: 100%;">
                            <i class="fas fa-times"></i> {{ __('messages.reject_request') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
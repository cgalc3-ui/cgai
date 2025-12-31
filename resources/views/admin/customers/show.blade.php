@extends('layouts.dashboard')

@section('title', __('messages.customer_details'))
@section('page-title', __('messages.customer_details'))

@section('content')
<div class="customer-details">
    <div class="detail-card">
        <div class="detail-header">
            <h3>{{ __('messages.customer_information') }}</h3>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
            </a>
        </div>
        <div class="detail-body">
            <div class="detail-row">
                <span class="detail-label">{{ __('messages.name') }}:</span>
                <span class="detail-value">{{ $customer->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">{{ __('messages.phone') }}:</span>
                <span class="detail-value">{{ $customer->phone }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">{{ __('messages.email') }}:</span>
                <span class="detail-value">{{ $customer->email ?? __('messages.not_available') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">{{ __('messages.registration_date') }}:</span>
                <span class="detail-value">{{ $customer->created_at->format('Y-m-d H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">{{ __('messages.last_updated') }}:</span>
                <span class="detail-value">{{ $customer->updated_at->format('Y-m-d H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">{{ __('messages.verification_status') }}:</span>
                <span class="detail-value">
                    @if($customer->phone_verified_at)
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> {{ __('messages.verified') }} - {{ $customer->phone_verified_at->format('Y-m-d') }}
                        </span>
                    @else
                        <span class="badge badge-warning">
                            <i class="fas fa-clock"></i> {{ __('messages.not_verified') }}
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="action-buttons">
        <a href="{{ route('admin.customers') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('messages.back_to_list') }}
        </a>
    </div>
</div>
@endsection


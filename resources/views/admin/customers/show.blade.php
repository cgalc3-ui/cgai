@extends('layouts.dashboard')

@section('title', 'تفاصيل العميل')
@section('page-title', 'تفاصيل العميل')

@section('content')
<div class="customer-details">
    <div class="detail-card">
        <div class="detail-header">
            <h3>معلومات العميل</h3>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
        </div>
        <div class="detail-body">
            <div class="detail-row">
                <span class="detail-label">الاسم:</span>
                <span class="detail-value">{{ $customer->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">رقم الهاتف:</span>
                <span class="detail-value">{{ $customer->phone }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">البريد الإلكتروني:</span>
                <span class="detail-value">{{ $customer->email ?? 'غير متوفر' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">تاريخ التسجيل:</span>
                <span class="detail-value">{{ $customer->created_at->format('Y-m-d H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">آخر تحديث:</span>
                <span class="detail-value">{{ $customer->updated_at->format('Y-m-d H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">حالة التحقق:</span>
                <span class="detail-value">
                    @if($customer->phone_verified_at)
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> مفعّل - {{ $customer->phone_verified_at->format('Y-m-d') }}
                        </span>
                    @else
                        <span class="badge badge-warning">
                            <i class="fas fa-clock"></i> غير مفعّل
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="action-buttons">
        <a href="{{ route('admin.customers') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>
</div>
@endsection


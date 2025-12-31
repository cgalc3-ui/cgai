@extends('layouts.dashboard')

@section('title', 'تفاصيل الباقة')
@section('page-title', 'تفاصيل الباقة')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>تفاصيل الباقة</h2>
        <p>عرض تفاصيل الباقة والإحصائيات</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
        <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> تعديل
        </a>
    </div>
</div>

<div class="details-container">
    <div class="details-card">
        <h3>معلومات الباقة</h3>
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">الاسم:</span>
                <span class="detail-value">{{ $subscription->name }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">الوصف:</span>
                <span class="detail-value">{{ $subscription->description ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">السعر:</span>
                <span class="detail-value">{{ number_format($subscription->price, 2) }} ر.س</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">نوع المدة:</span>
                <span class="detail-value">{{ $subscription->duration_text }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">الحد الأقصى للمديونين:</span>
                <span class="detail-value">{{ $subscription->max_debtors == 0 ? 'غير محدود' : $subscription->max_debtors }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">الحد الأقصى للرسائل:</span>
                <span class="detail-value">{{ $subscription->max_messages == 0 ? 'غير محدود' : $subscription->max_messages }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">الذكاء الاصطناعي:</span>
                <span class="detail-value">
                    @if($subscription->ai_enabled)
                        <span class="status-pill completed">مفعل</span>
                    @else
                        <span class="status-pill cancelled">معطل</span>
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">الحالة:</span>
                <span class="detail-value">
                    @if($subscription->is_active)
                        <span class="status-pill completed">نشط</span>
                    @else
                        <span class="status-pill cancelled">غير نشط</span>
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">تاريخ الإنشاء:</span>
                <span class="detail-value">{{ $subscription->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>

    <div class="details-card">
        <h3>إحصائيات</h3>
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">عدد طلبات الاشتراك:</span>
                <span class="detail-value">{{ $subscription->requests->count() }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">عدد الاشتراكات النشطة:</span>
                <span class="detail-value">{{ $subscription->userSubscriptions->where('status', 'active')->count() }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">إجمالي الاشتراكات:</span>
                <span class="detail-value">{{ $subscription->userSubscriptions->count() }}</span>
            </div>
        </div>
    </div>
</div>
@endsection


@extends('layouts.dashboard')

@section('title', 'تفاصيل طلب الاشتراك')
@section('page-title', 'تفاصيل طلب الاشتراك')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>تفاصيل طلب الاشتراك</h2>
        <p>مراجعة طلب الاشتراك واتخاذ الإجراء المناسب</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.subscription-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>
</div>

<div class="details-container">
    <div class="details-card">
        <h3>معلومات الطلب</h3>
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">المستخدم:</span>
                <span class="detail-value">{{ $request->user->name }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">الباقة:</span>
                <span class="detail-value">{{ $request->subscription->name }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">السعر:</span>
                <span class="detail-value">{{ number_format($request->subscription->price, 2) }} ر.س</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">الحالة:</span>
                <span class="detail-value">
                    @if($request->status == 'pending')
                        <span class="status-pill pending">معلق</span>
                    @elseif($request->status == 'approved')
                        <span class="status-pill completed">موافق عليه</span>
                    @else
                        <span class="status-pill cancelled">مرفوض</span>
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">تاريخ الطلب:</span>
                <span class="detail-value">{{ $request->created_at->format('Y-m-d H:i') }}</span>
            </div>
            @if($request->approved_at)
                <div class="detail-item">
                    <span class="detail-label">تاريخ الموافقة:</span>
                    <span class="detail-value">{{ $request->approved_at->format('Y-m-d H:i') }}</span>
                </div>
            @endif
            @if($request->rejected_at)
                <div class="detail-item">
                    <span class="detail-label">تاريخ الرفض:</span>
                    <span class="detail-value">{{ $request->rejected_at->format('Y-m-d H:i') }}</span>
                </div>
            @endif
            @if($request->approver)
                <div class="detail-item">
                    <span class="detail-label">من وافق/رفض:</span>
                    <span class="detail-value">{{ $request->approver->name }}</span>
                </div>
            @endif
            @if($request->admin_notes)
                <div class="detail-item">
                    <span class="detail-label">ملاحظات الإدارة:</span>
                    <span class="detail-value">{{ $request->admin_notes }}</span>
                </div>
            @endif
        </div>
    </div>

    @if($request->payment_proof)
        <div class="details-card">
            <h3>إثبات الدفع</h3>
            <div class="payment-proof-container">
                <img src="{{ asset('storage/' . $request->payment_proof) }}" alt="إثبات الدفع" style="max-width: 100%; height: auto; border-radius: 8px;">
            </div>
        </div>
    @endif

    @if($request->status == 'pending')
        <div class="details-card">
            <h3>الإجراءات</h3>
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <form action="{{ route('admin.subscription-requests.approve', $request) }}" method="POST" style="flex: 1; min-width: 300px;">
                    @csrf
                    <div class="form-group">
                        <label for="approve_notes">ملاحظات (اختياري)</label>
                        <textarea id="approve_notes" name="admin_notes" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success" style="width: 100%;">
                        <i class="fas fa-check"></i> الموافقة على الطلب
                    </button>
                </form>

                <form action="{{ route('admin.subscription-requests.reject', $request) }}" method="POST" style="flex: 1; min-width: 300px;">
                    @csrf
                    <div class="form-group">
                        <label for="reject_notes">سبب الرفض <span class="required">*</span></label>
                        <textarea id="reject_notes" name="admin_notes" class="form-control" rows="3" required minlength="10"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger" style="width: 100%;">
                        <i class="fas fa-times"></i> رفض الطلب
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection


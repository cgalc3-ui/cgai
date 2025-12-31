@extends('layouts.dashboard')

@section('title', 'الباقات')
@section('page-title', 'قائمة الباقات')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة الباقات</h2>
            <p>إدارة وعرض جميع باقات الاشتراك</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة باقة جديدة
            </a>
            <span class="total-count">إجمالي الباقات: {{ $subscriptions->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>السعر</th>
                    <th>نوع المدة</th>
                    <th>الحد الأقصى للمديونين</th>
                    <th>الحد الأقصى للرسائل</th>
                    <th>الذكاء الاصطناعي</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->name }}</td>
                        <td>{{ number_format($subscription->price, 2) }} ر.س</td>
                        <td>{{ $subscription->duration_text }}</td>
                        <td>{{ $subscription->max_debtors == 0 ? 'غير محدود' : $subscription->max_debtors }}</td>
                        <td>{{ $subscription->max_messages == 0 ? 'غير محدود' : $subscription->max_messages }}</td>
                        <td>
                            @if($subscription->ai_enabled)
                                <span class="status-pill completed">مفعل</span>
                            @else
                                <span class="status-pill cancelled">معطل</span>
                            @endif
                        </td>
                        <td>
                            @if($subscription->is_active)
                                <span class="status-pill completed">نشط</span>
                            @else
                                <span class="status-pill cancelled">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="calm-action-btn info"
                                    title="عرض">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="calm-action-btn warning"
                                    title="تعديل">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الباقة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="calm-action-btn danger" title="حذف">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">لا توجد باقات مسجلة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $subscriptions->links() }}
        </div>
    </div>
@endsection


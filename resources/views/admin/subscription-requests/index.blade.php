@extends('layouts.dashboard')

@section('title', 'طلبات الاشتراك')
@section('page-title', 'قائمة طلبات الاشتراك')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة طلبات الاشتراك</h2>
            <p>مراجعة وإدارة طلبات الاشتراك</p>
        </div>
        <div class="page-header-right">
            <span class="total-count">إجمالي الطلبات: {{ $requests->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>المستخدم</th>
                    <th>الباقة</th>
                    <th class="text-center">الحالة</th>
                    <th>تاريخ الطلب</th>
                    <th>من وافق/رفض</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->subscription->name }}</td>
                        <td class="text-center">
                            @if($request->status == 'pending')
                                <span class="status-pill pending">معلق</span>
                            @elseif($request->status == 'approved')
                                <span class="status-pill completed">موافق عليه</span>
                            @else
                                <span class="status-pill cancelled">مرفوض</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $request->approver ? $request->approver->name : '-' }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.subscription-requests.show', $request) }}" class="calm-action-btn info"
                                    title="عرض">
                                    <i class="far fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">لا توجد طلبات اشتراك</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
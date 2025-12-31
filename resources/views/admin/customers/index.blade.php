@extends('layouts.dashboard')

@section('title', 'إدارة العملاء')
@section('page-title', 'إدارة العملاء')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة العملاء</h2>
            <p>إدارة جميع العملاء المسجلين في النظام</p>
        </div>
        <div class="page-header-right">
            <span class="total-count">إجمالي العملاء: {{ $customers->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>رقم الهاتف</th>
                    <th>البريد الإلكتروني</th>
                    <th>تاريخ التسجيل</th>
                    <th>حالة التحقق</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email ?? 'غير متوفر' }}</td>
                        <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                        <td>
                            @if($customer->phone_verified_at)
                                <span class="status-pill completed">مفعّل</span>
                            @else
                                <span class="status-pill pending">غير مفعّل</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="calm-action-btn" title="عرض">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="calm-action-btn warning"
                                    title="تعديل">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.customers.delete', $customer) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
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
                        <td colspan="6" class="text-center">لا يوجد عملاء مسجلين</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $customers->links() }}
        </div>
    </div>
@endsection
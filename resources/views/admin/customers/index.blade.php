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
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> مفعّل
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-clock"></i> غير مفعّل
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('admin.customers.delete', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> حذف
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


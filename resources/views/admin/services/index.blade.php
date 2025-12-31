@extends('layouts.dashboard')

@section('title', 'الخدمات')
@section('page-title', 'قائمة الخدمات')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة الخدمات</h2>
            <p>إدارة وعرض جميع الخدمات</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة خدمة جديدة
            </a>
            <span class="total-count">إجمالي الخدمات: {{ $services->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>الفئة</th>
                    <th>الفئة الفرعية</th>
                    <th>التخصص</th>
                    <th>سعر الساعة</th>
                    <th>الحالة</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->subCategory->category->name }}</td>
                        <td>{{ $service->subCategory->name }}</td>
                        <td>{{ $service->specialization->name ?? '-' }}</td>
                        <td>
                            @if($service->price)
                                <span style="color: #28a745; font-weight: 600;">{{ number_format($service->price, 2) }}
                                    ر.س</span>
                            @else
                                <span style="color: #999; font-style: italic;">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @if($service->is_active)
                                <span class="status-pill completed">نشط</span>
                            @else
                                <span class="status-pill cancelled">غير نشط</span>
                            @endif
                        </td>
                        <td>{{ $service->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.services.edit', $service) }}" class="calm-action-btn warning"
                                    title="تعديل">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟ سيتم حذف جميع مدة الخدمات المرتبطة بها.')">
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
                        <td colspan="8" class="text-center">لا توجد خدمات مسجلة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $services->links() }}
        </div>
    </div>
@endsection
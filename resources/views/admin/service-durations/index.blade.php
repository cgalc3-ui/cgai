@extends('layouts.dashboard')

@section('title', 'مدة الخدمات')
@section('page-title', 'قائمة مدة الخدمات')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة مدة الخدمات</h2>
            <p>إدارة وعرض جميع مدة الخدمات</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.service-durations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة مدة خدمة جديدة
            </a>
            <span class="total-count">إجمالي مدة الخدمات: {{ $durations->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الخدمة</th>
                    <th>نوع المدة</th>
                    <th>قيمة المدة</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($durations as $duration)
                    <tr>
                        <td>{{ $duration->service->name }}</td>
                        <td>
                            @if($duration->duration_type == 'hour')
                                ساعة
                            @elseif($duration->duration_type == 'day')
                                يوم
                            @elseif($duration->duration_type == 'week')
                                أسبوع
                            @endif
                        </td>
                        <td>{{ $duration->duration_value }}</td>
                        <td>{{ number_format($duration->price, 2) }} ر.س</td>
                        <td>
                            @if($duration->is_active)
                                <span class="status-pill completed">نشط</span>
                            @else
                                <span class="status-pill cancelled">غير نشط</span>
                            @endif
                        </td>
                        <td>{{ $duration->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.service-durations.edit', $duration) }}" class="calm-action-btn warning"
                                    title="تعديل">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.service-durations.destroy', $duration) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المدة؟')">
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
                        <td colspan="7" class="text-center">لا توجد مدة خدمات مسجلة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $durations->links() }}
        </div>
    </div>
@endsection
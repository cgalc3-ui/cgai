@extends('layouts.dashboard')

@section('title', 'الاستشارات')
@section('page-title', 'قائمة الاستشارات')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة الاستشارات</h2>
            <p>إدارة وعرض جميع الاستشارات</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.consultations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة استشارة جديدة
            </a>
            <span class="total-count">إجمالي الاستشارات: {{ $consultations->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>التخصص</th>
                    <th>السعر الثابت</th>
                    <th>المدة</th>
                    <th class="text-center">الحالة</th>
                    <th>تاريخ الإنشاء</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultations as $consultation)
                    <tr>
                        <td>{{ $consultation->name }}</td>
                        <td>{{ $consultation->category->name }}</td>
                        <td>
                            <span style="color: #28a745; font-weight: 600;">{{ number_format($consultation->fixed_price, 2) }}
                                ر.س</span>
                        </td>
                        <td>حسب الـ Time Slot</td>
                        <td class="text-center">
                            @if($consultation->is_active)
                                <span class="status-pill completed">نشط</span>
                            @else
                                <span class="status-pill cancelled">غير نشط</span>
                            @endif
                        </td>
                        <td>{{ $consultation->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.consultations.edit', $consultation) }}" class="calm-action-btn warning"
                                    title="تعديل">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.consultations.destroy', $consultation) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الاستشارة؟')">
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
                        <td colspan="7" class="text-center">لا توجد استشارات مسجلة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $consultations->links() }}
        </div>
    </div>
@endsection
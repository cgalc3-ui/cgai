@extends('layouts.dashboard')

@section('title', 'المواعيد المتكررة')
@section('page-title', 'المواعيد المتكررة للموظفين')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>المواعيد المتكررة للموظفين</h2>
        <p>إدارة المواعيد المتكررة التي يتم إنشاء الأوقات المتاحة منها تلقائياً</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.time-slots.schedules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة مواعيد متكررة
        </a>
        <span class="total-count">إجمالي المواعيد: {{ $schedules->total() }}</span>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-container">
    <form method="GET" action="{{ route('admin.time-slots.schedules') }}" class="filter-form">
        <div class="filter-inputs">
            <div class="filter-group">
                <label for="employee_id">الموظف:</label>
                <select name="employee_id" id="employee_id" class="filter-input">
                    <option value="all">الكل</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> تطبيق
            </button>
            <a href="{{ route('admin.time-slots.schedules') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> إعادة تعيين
            </a>
        </div>
    </form>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>الموظف</th>
                <th>أيام الأسبوع</th>
                <th>من</th>
                <th>إلى</th>
                <th>الحالة</th>
                <th>تاريخ الإنشاء</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->employee->user->name ?? 'غير محدد' }}</td>
                    <td>
                        <div class="days-badges">
                            @php
                                $days = is_string($schedule->days_of_week) ? json_decode($schedule->days_of_week, true) : $schedule->days_of_week;
                                $dayNames = [0 => 'أحد', 1 => 'إثنين', 2 => 'ثلاثاء', 3 => 'أربعاء', 4 => 'خميس', 5 => 'جمعة', 6 => 'سبت'];
                            @endphp
                            @foreach($days as $day)
                                <span class="badge badge-info">{{ $dayNames[$day] ?? $day }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td>{{ $schedule->start_time }}</td>
                    <td>{{ $schedule->end_time }}</td>
                    <td>
                        @if($schedule->is_active)
                            <span class="badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-danger">غير نشط</span>
                        @endif
                    </td>
                    <td>{{ $schedule->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.time-slots.schedules.edit', $schedule) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('admin.time-slots.schedules.delete', $schedule) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المواعيد المتكررة؟')">
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
                    <td colspan="7" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>لا توجد مواعيد متكررة</h3>
                            <p>لم يتم إنشاء أي مواعيد متكررة بعد</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $schedules->links() }}
    </div>
</div>

@push('styles')
<style>
    .days-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
</style>
@endpush
@endsection


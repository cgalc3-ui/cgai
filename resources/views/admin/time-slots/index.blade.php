@extends('layouts.dashboard')

@section('title', 'إدارة الأوقات المتاحة')
@section('page-title', 'إدارة الأوقات المتاحة')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>قائمة الأوقات المتاحة</h2>
        <p>إدارة جميع الأوقات المتاحة للموظفين</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.time-slots.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة وقت متاح
        </a>
        <span class="total-count">إجمالي الأوقات: {{ $timeSlots->total() }}</span>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-container">
    <form method="GET" action="{{ route('admin.time-slots') }}" class="filter-form">
        <div class="filter-inputs">
            <div class="filter-group">
                <label for="employee_id">فلترة حسب الموظف:</label>
                <select name="employee_id" id="employee_id" class="filter-select">
                    <option value="all" {{ $employeeFilter == 'all' ? 'selected' : '' }}>جميع الموظفين</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $employeeFilter == $employee->id ? 'selected' : '' }}>
                            {{ $employee->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="date">فلترة حسب التاريخ:</label>
                <input type="date" name="date" id="date" value="{{ $dateFilter }}" class="filter-input">
            </div>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> تطبيق
            </button>
            <a href="{{ route('admin.time-slots') }}" class="btn btn-secondary">
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
                <th>التاريخ</th>
                <th>من</th>
                <th>إلى</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timeSlots as $timeSlot)
                <tr>
                    <td><strong>{{ $timeSlot->employee->user->name }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($timeSlot->date)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($timeSlot->start_time)->format('h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($timeSlot->end_time)->format('h:i A') }}</td>
                    <td>
                        @if($timeSlot->is_available)
                            <span class="badge badge-success">متاح</span>
                        @else
                            <span class="badge badge-danger">غير متاح</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.time-slots.edit', $timeSlot) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('admin.time-slots.delete', $timeSlot) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الوقت؟')">
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
                    <td colspan="6" class="text-center">لا توجد أوقات متاحة مسجلة</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $timeSlots->links() }}
    </div>
</div>
@endsection

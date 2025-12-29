@extends('layouts.dashboard')

@section('title', 'تعديل مواعيد متكررة')
@section('page-title', 'تعديل مواعيد متكررة')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>تعديل مواعيد متكررة</h2>
        <p>تعديل المواعيد المتكررة للموظف</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.time-slots.schedules') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.time-slots.schedules.update', $schedule) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="employee_id">الموظف *</label>
            <select id="employee_id" name="employee_id" class="form-control" required disabled>
                <option value="{{ $schedule->employee_id }}">{{ $schedule->employee->user->name ?? 'غير محدد' }}</option>
            </select>
            <input type="hidden" name="employee_id" value="{{ $schedule->employee_id }}">
            <small class="text-muted">لا يمكن تغيير الموظف بعد الإنشاء</small>
        </div>

        <div class="form-group">
            <label>أيام الأسبوع *</label>
            <div class="days-checkboxes">
                @php
                    $days = [
                        0 => ['name' => 'الأحد', 'value' => 0],
                        1 => ['name' => 'الإثنين', 'value' => 1],
                        2 => ['name' => 'الثلاثاء', 'value' => 2],
                        3 => ['name' => 'الأربعاء', 'value' => 3],
                        4 => ['name' => 'الخميس', 'value' => 4],
                        5 => ['name' => 'الجمعة', 'value' => 5],
                        6 => ['name' => 'السبت', 'value' => 6],
                    ];
                    $scheduleDays = is_string($schedule->days_of_week) 
                        ? json_decode($schedule->days_of_week, true) 
                        : $schedule->days_of_week;
                    $oldDays = old('days_of_week', $scheduleDays ?? []);
                @endphp
                @foreach($days as $day)
                    <label class="day-checkbox">
                        <input type="checkbox" name="days_of_week[]" value="{{ $day['value'] }}" 
                               {{ in_array($day['value'], $oldDays) ? 'checked' : '' }}>
                        <span>{{ $day['name'] }}</span>
                    </label>
                @endforeach
            </div>
            @error('days_of_week')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start_time">من *</label>
                <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $schedule->start_time) }}" class="form-control" required>
                @error('start_time')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_time">إلى *</label>
                <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $schedule->end_time) }}" class="form-control" required>
                @error('end_time')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $schedule->is_active) ? 'checked' : '' }}>
                <span>المواعيد نشطة (سيتم إنشاء الأوقات المتاحة تلقائياً)</span>
            </label>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>سيتم إعادة إنشاء الأوقات المتاحة للـ 30 يوم القادمة بناءً على التعديلات الجديدة.</p>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ
            </button>
            <a href="{{ route('admin.time-slots.schedules') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>

@push('styles')
<style>
    .days-checkboxes {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }
    .day-checkbox {
        display: flex;
        align-items: center;
        padding: 12px;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .day-checkbox:hover {
        background: #e9ecef;
        border-color: #007bff;
    }
    .day-checkbox input[type="checkbox"] {
        margin-left: 8px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .day-checkbox input[type="checkbox"]:checked + span {
        font-weight: 600;
        color: #007bff;
    }
    .day-checkbox:has(input[type="checkbox"]:checked) {
        background: #e7f3ff;
        border-color: #007bff;
    }
    .info-box {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 8px;
        padding: 15px;
        margin: 20px 0;
        display: flex;
        align-items: start;
        gap: 10px;
    }
    .info-box i {
        color: #007bff;
        font-size: 20px;
        margin-top: 2px;
    }
    .info-box p {
        margin: 0;
        color: #004085;
    }
</style>
@endpush
@endsection


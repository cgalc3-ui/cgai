@extends('layouts.dashboard')

@section('title', 'تعديل الوقت المتاح')
@section('page-title', 'تعديل الوقت المتاح')

@section('content')
<div class="page-header">
    <h2>تعديل الوقت المتاح</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.time-slots.update', $timeSlot) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="employee_id">الموظف *</label>
            <select id="employee_id" name="employee_id" class="form-control" required>
                <option value="">اختر الموظف</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id', $timeSlot->employee_id) == $employee->id ? 'selected' : '' }}>
                        {{ $employee->user->name }}
                    </option>
                @endforeach
            </select>
            @error('employee_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="date">التاريخ *</label>
            <input type="date" id="date" name="date" value="{{ old('date', $timeSlot->date->format('Y-m-d')) }}" class="form-control" required>
            @error('date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start_time">من *</label>
                <input type="time" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($timeSlot->start_time)->format('H:i')) }}" class="form-control" required>
                @error('start_time')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_time">إلى *</label>
                <input type="time" id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($timeSlot->end_time)->format('H:i')) }}" class="form-control" required>
                @error('end_time')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_available" value="1" {{ old('is_available', $timeSlot->is_available) ? 'checked' : '' }}>
                <span>الوقت متاح</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
            <a href="{{ route('admin.time-slots') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection

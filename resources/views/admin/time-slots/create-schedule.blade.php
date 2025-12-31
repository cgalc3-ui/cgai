@extends('layouts.dashboard')

@section('title', __('messages.add_recurring_appointments_title'))
@section('page-title', __('messages.add_recurring_appointments_title'))

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>{{ __('messages.add_recurring_appointments_title') }}</h2>
        <p>{{ __('messages.add_recurring_appointments_desc') }}</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.time-slots.schedules') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.time-slots.schedules.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="employee_id">{{ __('messages.employee') }} *</label>
            <select id="employee_id" name="employee_id" class="form-control" required>
                <option value="">{{ __('messages.select_employee') }}</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->user->name }}
                    </option>
                @endforeach
            </select>
            @error('employee_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>{{ __('messages.weekdays_label') }} *</label>
            <div class="days-checkboxes">
                @php
                    $days = [
                        0 => ['name' => __('messages.sunday'), 'value' => 0],
                        1 => ['name' => __('messages.monday'), 'value' => 1],
                        2 => ['name' => __('messages.tuesday'), 'value' => 2],
                        3 => ['name' => __('messages.wednesday'), 'value' => 3],
                        4 => ['name' => __('messages.thursday'), 'value' => 4],
                        5 => ['name' => __('messages.friday'), 'value' => 5],
                        6 => ['name' => __('messages.saturday'), 'value' => 6],
                    ];
                    $oldDays = old('days_of_week', []);
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
                <label for="start_time">{{ __('messages.from_hour') }} *</label>
                <input type="time" id="start_time" name="start_time" value="{{ old('start_time', '10:00') }}" class="form-control" required>
                @error('start_time')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_time">{{ __('messages.to_hour') }} *</label>
                <input type="time" id="end_time" name="end_time" value="{{ old('end_time', '18:00') }}" class="form-control" required>
                @error('end_time')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <span>{{ __('messages.appointments_active') }}</span>
            </label>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>{{ __('messages.auto_create_time_slots_info') }}</p>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('messages.save') }}
            </button>
            <a href="{{ route('admin.time-slots.schedules') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
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


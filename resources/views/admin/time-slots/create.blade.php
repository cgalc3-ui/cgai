@extends('layouts.dashboard')

@section('title', __('messages.add_time_slot'))
@section('page-title', __('messages.add_time_slot'))

@section('content')
<div class="page-header">
    <h2>{{ __('messages.add_time_slot') }}</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.time-slots.store') }}" method="POST">
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
            <label for="date">{{ __('messages.date') }} *</label>
            <input type="date" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" class="form-control" required>
            @error('date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start_time">{{ __('messages.from_hour') }} *</label>
                <input type="time" id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}" class="form-control" required>
                @error('start_time')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_time">{{ __('messages.to_hour') }} *</label>
                <input type="time" id="end_time" name="end_time" value="{{ old('end_time', '10:00') }}" class="form-control" required>
                @error('end_time')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                <span>{{ __('messages.time_available') }}</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('messages.save') }}
            </button>
            <a href="{{ route('admin.time-slots') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection

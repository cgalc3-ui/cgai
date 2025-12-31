@extends('layouts.dashboard')

@section('title', __('messages.edit_service_duration'))
@section('page-title', __('messages.edit_service_duration'))

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>{{ __('messages.edit_service_duration') }}</h2>
        <p>{{ __('messages.edit_service_duration_desc') }}</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.service-durations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.service-durations.update', $serviceDuration) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="service_id">{{ __('messages.service') }} <span class="required">*</span></label>
            <select id="service_id" name="service_id" class="form-control" required>
                <option value="">{{ __('messages.select_service') }}</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $serviceDuration->service_id) == $service->id ? 'selected' : '' }}>
                        {{ $service->subCategory->category->name }} - {{ $service->subCategory->name }} - {{ $service->name }}
                    </option>
                @endforeach
            </select>
            @error('service_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="duration_type">{{ __('messages.duration_type') }} <span class="required">*</span></label>
            <select id="duration_type" name="duration_type" class="form-control" required>
                <option value="">{{ __('messages.select_duration_type') }}</option>
                <option value="hour" {{ old('duration_type', $serviceDuration->duration_type) == 'hour' ? 'selected' : '' }}>{{ __('messages.hour') }}</option>
                <option value="day" {{ old('duration_type', $serviceDuration->duration_type) == 'day' ? 'selected' : '' }}>{{ __('messages.day') }}</option>
                <option value="week" {{ old('duration_type', $serviceDuration->duration_type) == 'week' ? 'selected' : '' }}>{{ __('messages.week') }}</option>
            </select>
            @error('duration_type')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="duration_value">{{ __('messages.duration_value') }} <span class="required">*</span></label>
            <input type="number" id="duration_value" name="duration_value" value="{{ old('duration_value', $serviceDuration->duration_value) }}" class="form-control" required min="1">
            @error('duration_value')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="price">{{ __('messages.price') }} <span class="required">*</span></label>
            <input type="number" id="price" name="price" value="{{ old('price', $serviceDuration->price) }}" class="form-control" required step="0.01" min="0">
            @error('price')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $serviceDuration->is_active) ? 'checked' : '' }}>
                <span>{{ __('messages.active') }}</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('messages.save') }}
            </button>
            <a href="{{ route('admin.service-durations.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection


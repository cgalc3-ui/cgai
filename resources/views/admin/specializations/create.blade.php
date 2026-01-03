@extends('layouts.dashboard')

@section('title', __('messages.add_specialization'))
@section('page-title', __('messages.add_specialization'))

@section('content')
<div class="page-header">
    <h2>{{ __('messages.add_specialization') }}</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.specializations.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">{{ __('messages.specialization_name') }} *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required placeholder="{{ __('messages.specialization_name_placeholder') }}">
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">{{ __('messages.description') }}</label>
            <textarea id="description" name="description" rows="4" class="form-control" placeholder="{{ __('messages.specialization_description_placeholder') }}">{{ old('description') }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <span>{{ __('messages.specialization_active') }}</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('messages.save') }}
            </button>
            <a href="{{ route('admin.specializations') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection

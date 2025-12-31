@extends('layouts.dashboard')

@section('title', __('messages.add_service'))
@section('page-title', __('messages.add_service'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.add_service') }}</h2>
            <p>{{ __('messages.manage_services_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="sub_category_id">{{ __('messages.sub_category') }} <span class="required">*</span></label>
                <select id="sub_category_id" name="sub_category_id" class="form-control" required>
                    <option value="">{{ __('messages.select_sub_category') }}</option>
                    @foreach($subCategories as $subCategory)
                        <option value="{{ $subCategory->id }}" {{ old('sub_category_id') == $subCategory->id ? 'selected' : '' }}>
                            {{ $subCategory->category->trans('name') }} - {{ $subCategory->trans('name') }}
                        </option>
                    @endforeach
                </select>
                @error('sub_category_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">{{ __('messages.name') }} (AR) <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name_en">{{ __('messages.name') }} (EN)</label>
                <input type="text" id="name_en" name="name_en" value="{{ old('name_en') }}" class="form-control"
                    style="direction: ltr; text-align: left;">
                @error('name_en')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">{{ __('messages.description') }} (AR)</label>
                <textarea id="description" name="description" class="form-control"
                    rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description_en">{{ __('messages.description') }} (EN)</label>
                <textarea id="description_en" name="description_en" class="form-control" rows="4"
                    style="direction: ltr; text-align: left;">{{ old('description_en') }}</textarea>
                @error('description_en')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span>{{ __('messages.active') }}</span>
                </label>
            </div>

            <div class="form-group">
                <label for="hourly_rate">{{ __('messages.price') }} ({{ __('messages.sar') }}) <span
                        class="required">*</span></label>
                <input type="number" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}"
                    class="form-control" step="0.01" min="0" required>
                @error('hourly_rate')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>

@endsection
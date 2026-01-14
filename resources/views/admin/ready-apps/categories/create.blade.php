@extends('layouts.dashboard')

@section('title', __('messages.add_category'))
@section('page-title', __('messages.add_category'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.add_category') }}</h2>
            <p>{{ __('messages.create_category_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.ready-apps.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.back_to_list') }}
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.ready-apps.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

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
                <label for="slug">{{ __('messages.slug') }}</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}" class="form-control"
                    placeholder="{{ __('messages.slug_auto_generate') }}">
                @error('slug')
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
                <label for="image">{{ __('messages.image') }}</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                @error('image')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="sort_order">{{ __('messages.sort_order') }}</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"
                    class="form-control" min="0">
                @error('sort_order')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span>{{ __('messages.active') }}</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
                <a href="{{ route('admin.ready-apps.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection
@extends('layouts.dashboard')

@section('title', __('messages.edit_help_guide'))
@section('page-title', __('messages.edit_help_guide'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.edit_help_guide') }}</h2>
            <p>{{ __('messages.edit_help_guide_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.help-guides.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.help-guides.update', $helpGuide) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="role">{{ __('messages.role') }} <span class="required">*</span></label>
                <select id="role" name="role" class="form-control" required>
                    <option value="">{{ __('messages.select_role') }}</option>
                    <option value="admin" {{ old('role', $helpGuide->role) == 'admin' ? 'selected' : '' }}>{{ __('messages.admin_role') }}</option>
                    <option value="staff" {{ old('role', $helpGuide->role) == 'staff' ? 'selected' : '' }}>{{ __('messages.staff_role') }}</option>
                    <option value="customer" {{ old('role', $helpGuide->role) == 'customer' ? 'selected' : '' }}>{{ __('messages.customer_role') }}</option>
                </select>
                @error('role')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="title">{{ __('messages.title') }} ({{ __('messages.arabic') }}) <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $helpGuide->title) }}" class="form-control" required>
                @error('title')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="title_en">{{ __('messages.title') }} ({{ __('messages.english') }})</label>
                <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $helpGuide->title_en) }}" class="form-control">
                @error('title_en')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="content">{{ __('messages.content') }} ({{ __('messages.arabic') }}) <span class="required">*</span></label>
                <textarea id="content" name="content" rows="6" class="form-control" required>{{ old('content', $helpGuide->content) }}</textarea>
                <small class="form-help">{{ __('messages.help_guide_content_help') }}</small>
                @error('content')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="content_en">{{ __('messages.content') }} ({{ __('messages.english') }})</label>
                <textarea id="content_en" name="content_en" rows="6" class="form-control">{{ old('content_en', $helpGuide->content_en) }}</textarea>
                @error('content_en')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="icon">{{ __('messages.icon') }}</label>
                <input type="text" id="icon" name="icon" value="{{ old('icon', $helpGuide->icon ?? 'fas fa-info-circle') }}" 
                    class="form-control" placeholder="fas fa-info-circle">
                <small class="form-help">{{ __('messages.help_guide_icon_help') }}</small>
                @error('icon')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="sort_order">{{ __('messages.sort_order') }}</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $helpGuide->sort_order) }}" 
                    class="form-control" min="0">
                @error('sort_order')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $helpGuide->is_active) ? 'checked' : '' }}>
                    <span>{{ __('messages.active') }}</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
                <a href="{{ route('admin.help-guides.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection


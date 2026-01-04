<form action="{{ route('admin.services.update', $service) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="sub_category_id">{{ __('messages.sub_category') }} <span class="required">*</span></label>
        <select id="sub_category_id" name="sub_category_id" class="form-control" required>
            <option value="">{{ __('messages.select_sub_category') }}</option>
            @foreach($subCategories as $subCategory)
                <option value="{{ $subCategory->id }}" {{ old('sub_category_id', $service->sub_category_id) == $subCategory->id ? 'selected' : '' }}>
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
        <input type="text" id="name" name="name" value="{{ old('name', $service->name) }}" class="form-control"
            required>
        @error('name')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="name_en">{{ __('messages.name') }} (EN)</label>
        <input type="text" id="name_en" name="name_en" value="{{ old('name_en', $service->name_en) }}"
            class="form-control" style="direction: ltr; text-align: left;">
        @error('name_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description">{{ __('messages.description') }} (AR)</label>
        <textarea id="description" name="description" class="form-control"
            rows="4">{{ old('description', $service->description) }}</textarea>
        @error('description')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description_en">{{ __('messages.description') }} (EN)</label>
        <textarea id="description_en" name="description_en" class="form-control" rows="4"
            style="direction: ltr; text-align: left;">{{ old('description_en', $service->description_en) }}</textarea>
        @error('description_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
            <span>{{ __('messages.active') }}</span>
        </label>
    </div>

    <div class="form-group">
        <label for="hourly_rate">{{ __('messages.price') }} ({{ __('messages.sar') }}) <span
                class="required">*</span></label>
        <input type="number" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', $service->price) }}"
            class="form-control" step="0.01" min="0" required>
        @error('hourly_rate')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('messages.save') }}
        </button>
        <button type="button" class="btn btn-secondary" onclick="closeModal('editServiceModal'); return false;">
            <i class="fas fa-times"></i> {{ __('messages.cancel') }}
        </button>
    </div>
</form>

<style>
    .modal-form .form-group {
        margin-bottom: 20px;
    }

    .modal-form .form-group label {
        color: #374151;
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
    }

    .modal-form .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: white;
        color: #1f2937;
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .modal-form .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modal-form .form-control:hover {
        border-color: #9ca3af;
    }

    .modal-form textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .modal-form .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .modal-form .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #374151;
        cursor: pointer;
    }

    .modal-form .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #3b82f6;
    }

    .modal-form .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .modal-form .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .modal-form .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .modal-form .btn-primary:hover {
        background: #2563eb;
    }

    .modal-form .btn-secondary {
        background: #e5e7eb;
        color: #6b7280;
    }

    .modal-form .btn-secondary:hover {
        background: #d1d5db;
    }

    /* Dark Mode Styles */
    [data-theme="dark"] .modal-form label {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .form-control {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--border-color, #2a2d3a) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .form-control:focus {
        border-color: var(--primary-color, #6658dd) !important;
        box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1) !important;
    }

    [data-theme="dark"] .modal-form .form-control:hover {
        border-color: var(--border-color, #2a2d3a) !important;
    }

    [data-theme="dark"] .modal-form select.form-control {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--border-color, #2a2d3a) !important;
        color: var(--text-primary, #f1f5f9) !important;
        background-image: none !important;
        padding-right: 16px !important;
        padding-left: 16px !important;
    }

    [data-theme="dark"] .modal-form select.form-control:focus {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--primary-color, #6658dd) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form select.form-control option {
        background: var(--sidebar-active-bg, #15171d) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .error-message {
        color: var(--danger-color, #ef4444) !important;
    }

    [data-theme="dark"] .modal-form .checkbox-label {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .checkbox-label input[type="checkbox"] {
        accent-color: var(--primary-color, #6658dd) !important;
    }

    [data-theme="dark"] .modal-form .form-actions {
        border-top-color: var(--border-color, #2a2d3a) !important;
    }

    [data-theme="dark"] .modal-form .btn-primary {
        background: var(--primary-color, #6658dd) !important;
        color: white !important;
    }

    [data-theme="dark"] .modal-form .btn-primary:hover {
        background: var(--primary-dark, #564ab1) !important;
    }

    [data-theme="dark"] .modal-form .btn-secondary {
        background: var(--sidebar-active-bg, #15171d) !important;
        color: var(--text-secondary, #94a3b8) !important;
        border: 1px solid var(--border-color, #2a2d3a) !important;
    }

    [data-theme="dark"] .modal-form .btn-secondary:hover {
        background: var(--bg-light, #15171d) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }
</style>


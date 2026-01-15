<form action="{{ isset($section) ? route('admin.customer-facing.subscriptions-section.update', $section) : route('admin.customer-facing.subscriptions-section.store') }}" method="POST" id="sectionForm" class="modal-form">
    @csrf
    @if(isset($section))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="title">{{ __('messages.title') }} (AR) <span class="required">*</span></label>
        <input type="text" id="title" name="title" value="{{ old('title', $section->title ?? '') }}" class="form-control" required>
        @error('title')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="title_en">{{ __('messages.title') }} (EN)</label>
        <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $section->title_en ?? '') }}" class="form-control" style="direction: ltr; text-align: left;">
        @error('title_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description">{{ __('messages.description') }} (AR)</label>
        <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $section->description ?? '') }}</textarea>
        @error('description')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description_en">{{ __('messages.description') }} (EN)</label>
        <textarea id="description_en" name="description_en" class="form-control" rows="4" style="direction: ltr; text-align: left;">{{ old('description_en', $section->description_en ?? '') }}</textarea>
        @error('description_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="background_color">{{ __('messages.background_color') ?? 'لون الخلفية' }} <span class="required">*</span></label>
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="color" id="background_color" name="background_color" value="{{ old('background_color', $section->background_color ?? '#02c0ce') }}" class="form-control" style="width: 80px; height: 40px; padding: 2px; cursor: pointer;" required>
            <input type="text" id="background_color_text" value="{{ old('background_color', $section->background_color ?? '#02c0ce') }}" class="form-control" style="flex: 1;" placeholder="#02c0ce" pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
        </div>
        <small class="form-help" style="color: var(--text-secondary); font-size: 12px; margin-top: 5px; display: block;">
            {{ __('messages.background_color_help') ?? 'اختر لون الخلفية للقسم' }}
        </small>
        @error('background_color')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', isset($section) ? $section->is_active : true) ? 'checked' : '' }}>
            <span>{{ __('messages.active') }}</span>
        </label>
    </div>

    <div class="form-group info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px;">
        <h4 style="font-size: 16px; margin-bottom: 10px; color: var(--text-primary);">
            <i class="fas fa-info-circle" style="color: var(--primary-color); margin-left: 6px;"></i>
            {{ __('messages.subscriptions_info') ?? 'معلومات الباقات' }}
        </h4>
        <p style="font-size: 13px; color: var(--text-secondary); margin: 0; line-height: 1.6;">
            {{ __('messages.subscriptions_info_desc') ?? 'الباقات المعروضة في هذا القسم يتم إدارتها من قسم الباقات في الداشبورد. يمكنك تعديل الباقات من هناك.' }}
        </p>
        <div style="margin-top: 10px;">
            <a href="{{ route('admin.subscriptions.index') }}" target="_blank" class="btn btn-sm btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                <i class="fas fa-external-link-alt"></i> {{ __('messages.manage_subscriptions') ?? 'إدارة الباقات' }}
            </a>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('messages.save') }}
        </button>
        <button type="button" class="btn btn-secondary" onclick="if(window.closeModal) window.closeModal('sectionModal'); return false;">
            <i class="fas fa-times"></i> {{ __('messages.cancel') }}
        </button>
    </div>
</form>

<script>
(function() {
    // Sync color picker with text input
    const colorPicker = document.getElementById('background_color');
    const colorText = document.getElementById('background_color_text');
    
    if (colorPicker && colorText) {
        colorPicker.addEventListener('input', function() {
            colorText.value = this.value;
        });
        
        colorText.addEventListener('input', function() {
            if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(this.value)) {
                colorPicker.value = this.value;
            }
        });
        
        colorText.addEventListener('change', function() {
            if (!/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(this.value)) {
                this.value = colorPicker.value;
            }
        });
    }
})();
</script>

<style>
    .modal-form .form-group {
        margin-bottom: 20px;
    }

    .modal-form label {
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
    }

    .modal-form .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        background: var(--card-bg);
        color: var(--text-primary);
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .modal-form .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1);
    }

    .modal-form .form-help {
        color: var(--text-secondary);
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .modal-form .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-primary);
        cursor: pointer;
    }

    .modal-form .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary-color);
    }

    .modal-form .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
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
        background: var(--primary-color);
        color: white;
    }

    .modal-form .btn-primary:hover {
        background: var(--primary-dark);
    }

    .modal-form .btn-secondary {
        background: var(--sidebar-active-bg);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .modal-form .btn-secondary:hover {
        background: var(--bg-light);
        color: var(--text-primary);
    }

    .modal-form .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .modal-form .required {
        color: #ef4444;
    }

    .modal-form .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
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
        background: var(--sidebar-active-bg, #15171d) !important;
    }

    [data-theme="dark"] .modal-form input[type="color"] {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--border-color, #2a2d3a) !important;
    }

    [data-theme="dark"] .modal-form .form-help {
        color: var(--text-secondary, #94a3b8) !important;
    }

    [data-theme="dark"] .modal-form .checkbox-label {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .checkbox-label span {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .checkbox-label input[type="checkbox"] {
        accent-color: var(--primary-color, #6658dd) !important;
        filter: brightness(1.2);
    }

    [data-theme="dark"] .modal-form .required {
        color: var(--danger-color, #ef4444) !important;
    }

    [data-theme="dark"] .modal-form .error-message {
        color: var(--danger-color, #ef4444) !important;
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

    [data-theme="dark"] .modal-form .info-box {
        background: var(--sidebar-active-bg, #15171d) !important;
        border: 1px solid var(--border-color, #2a2d3a) !important;
    }

    [data-theme="dark"] .modal-form .info-box h4 {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .info-box p {
        color: var(--text-secondary, #94a3b8) !important;
    }

    [data-theme="dark"] .modal-form .info-box .btn-sm {
        background: var(--sidebar-active-bg, #15171d) !important;
        color: var(--text-secondary, #94a3b8) !important;
        border: 1px solid var(--border-color, #2a2d3a) !important;
    }

    [data-theme="dark"] .modal-form .info-box .btn-sm:hover {
        background: var(--bg-light, #15171d) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }
</style>


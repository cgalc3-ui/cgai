<form action="{{ route('admin.categories.store') }}" method="POST" id="categoryCreateForm" class="modal-form">
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

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('messages.save') }}
        </button>
        <button type="button" class="btn btn-secondary" onclick="closeModal('createCategoryModal'); return false;">
            <i class="fas fa-times"></i> {{ __('messages.cancel') }}
        </button>
    </div>
</form>

<style>
    .modal-form .form-group {
        margin-bottom: 20px;
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

    /* Dark Mode Styles */
    [data-theme="dark"] .modal-form .form-control {
        background: var(--sidebar-active-bg, #15171d);
        border-color: var(--border-color, #2a2d3a);
        color: var(--text-primary, #f1f5f9);
    }

    [data-theme="dark"] .modal-form .form-control:focus {
        border-color: var(--primary-color, #6658dd);
        box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1);
    }

    [data-theme="dark"] .modal-form .form-control:hover {
        border-color: var(--border-color, #2a2d3a);
    }

    .modal-form label {
        color: #374151;
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
    }

    .modal-form .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    [data-theme="dark"] .modal-form label {
        color: var(--text-primary, #f1f5f9);
    }

    [data-theme="dark"] .modal-form .error-message {
        color: var(--danger-color, #ef4444);
    }

    .modal-form .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #374151;
        cursor: pointer;
    }

    [data-theme="dark"] .modal-form .checkbox-label {
        color: var(--text-primary, #f1f5f9);
    }

    .modal-form .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #3b82f6;
    }

    [data-theme="dark"] .modal-form .checkbox-label input[type="checkbox"] {
        accent-color: var(--primary-color, #6658dd);
    }

    .modal-form .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    [data-theme="dark"] .modal-form .form-actions {
        border-top-color: var(--border-color, #2a2d3a);
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

    [data-theme="dark"] .modal-form .btn-primary {
        background: var(--primary-color, #6658dd);
        color: white;
    }

    [data-theme="dark"] .modal-form .btn-primary:hover {
        background: var(--primary-dark, #564ab1);
    }

    .modal-form .btn-secondary {
        background: #e5e7eb;
        color: #6b7280;
    }

    .modal-form .btn-secondary:hover {
        background: #d1d5db;
    }

    [data-theme="dark"] .modal-form .btn-secondary {
        background: var(--sidebar-active-bg, #15171d);
        color: var(--text-secondary, #94a3b8);
        border: 1px solid var(--border-color, #2a2d3a);
    }

    [data-theme="dark"] .modal-form .btn-secondary:hover {
        background: var(--bg-light, #15171d);
        color: var(--text-primary, #f1f5f9);
    }
</style>

<script>
    document.getElementById('categoryCreateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add('error');
                            const errorMsg = document.createElement('span');
                            errorMsg.className = 'error-message';
                            errorMsg.textContent = data.errors[key][0];
                            input.parentNode.appendChild(errorMsg);
                        }
                    });
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
</script>


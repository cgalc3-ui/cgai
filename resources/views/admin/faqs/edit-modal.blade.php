<form action="{{ route('admin.faqs.update', $faq) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="question">{{ __('messages.question') }} (AR) <span class="required">*</span></label>
        <input type="text" id="question" name="question" value="{{ old('question', $faq->question) }}" class="form-control" required>
        @error('question')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="question_en">{{ __('messages.question') }} (EN)</label>
        <input type="text" id="question_en" name="question_en" value="{{ old('question_en', $faq->question_en) }}" class="form-control"
            style="direction: ltr; text-align: left;">
        @error('question_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="role">{{ __('messages.role') }} <span class="required">*</span></label>
        <select id="role" name="role" class="form-control" required>
            <option value="customer" {{ old('role', $faq->role ?? 'customer') == 'customer' ? 'selected' : '' }}>{{ __('messages.customer_role') }}</option>
            <option value="staff" {{ old('role', $faq->role ?? 'customer') == 'staff' ? 'selected' : '' }}>{{ __('messages.staff_role') }}</option>
            <option value="admin" {{ old('role', $faq->role ?? 'customer') == 'admin' ? 'selected' : '' }}>{{ __('messages.admin_role') }}</option>
        </select>
        @error('role')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="category">{{ __('messages.category') }} <span class="required">*</span></label>
        <select id="category" name="category" class="form-control" required>
            <option value="general" {{ old('category', $faq->category) == 'general' ? 'selected' : '' }}>{{ __('messages.general') }}</option>
            <option value="account" {{ old('category', $faq->category) == 'account' ? 'selected' : '' }}>{{ __('messages.account') }}</option>
            <option value="services" {{ old('category', $faq->category) == 'services' ? 'selected' : '' }}>{{ __('messages.services') }}</option>
            <option value="payment" {{ old('category', $faq->category) == 'payment' ? 'selected' : '' }}>{{ __('messages.payment') }}</option>
            <option value="technical" {{ old('category', $faq->category) == 'technical' ? 'selected' : '' }}>{{ __('messages.technical') }}</option>
        </select>
        @error('category')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="answer">{{ __('messages.answer') }} (AR) <span class="required">*</span></label>
        <textarea id="answer" name="answer" class="form-control" rows="6" required>{{ old('answer', $faq->answer) }}</textarea>
        @error('answer')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="answer_en">{{ __('messages.answer') }} (EN)</label>
        <textarea id="answer_en" name="answer_en" class="form-control" rows="6"
            style="direction: ltr; text-align: left;">{{ old('answer_en', $faq->answer_en) }}</textarea>
        @error('answer_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="sort_order">{{ __('messages.sort_order') }}</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $faq->sort_order) }}" class="form-control">
        @error('sort_order')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active) ? 'checked' : '' }}>
            <span>{{ __('messages.active') }}</span>
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('messages.save') }}
        </button>
        <button type="button" class="btn btn-secondary" onclick="closeModal('editFaqModal'); return false;">
            <i class="fas fa-times"></i> {{ __('messages.cancel') }}
        </button>
    </div>
</form>

<style>
    .modal-form .form-group {
        margin-bottom: 20px;
    }

    .modal-form label {
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
        min-height: 100px;
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

    [data-theme="dark"] .modal-form .checkbox-label span {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .checkbox-label input[type="checkbox"] {
        accent-color: var(--primary-color, #6658dd) !important;
        filter: brightness(1.2);
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

<script>
    (function() {
        const form = document.querySelector('.modal-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';
                
                fetch(form.action, {
                    method: 'PUT',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal('editFaqModal');
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        // Handle validation errors
                        form.querySelectorAll('.error-message').forEach(el => el.remove());
                        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
                        
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
        }
    })();
</script>


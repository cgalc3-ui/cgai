<form action="{{ isset($section) ? route('admin.customer-facing.services-section.update', $section) : route('admin.customer-facing.services-section.store') }}" method="POST" id="headerForm" class="modal-form">
    @csrf
    @if(isset($section))
        @method('PUT')
        <input type="hidden" name="category_ids" value="{{ json_encode($section->category_ids) }}">
    @endif

    <div class="form-group">
        <label for="heading">{{ __('messages.heading') }} (AR) <span class="required">*</span></label>
        <input type="text" id="heading" name="heading" value="{{ old('heading', $section->heading ?? '') }}" class="form-control" required>
        @error('heading')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="heading_en">{{ __('messages.heading') }} (EN)</label>
        <input type="text" id="heading_en" name="heading_en" value="{{ old('heading_en', $section->heading_en ?? '') }}" class="form-control" style="direction: ltr; text-align: left;">
        @error('heading_en')
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
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', isset($section) ? $section->is_active : true) ? 'checked' : '' }}>
            <span>{{ __('messages.active') }}</span>
        </label>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the form to be inserted into DOM
    setTimeout(function() {
        const form = document.getElementById('headerForm');
        if (form) {
            // Remove any existing listeners
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);
            
            newForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const modal = newForm.closest('.modal-overlay');
                const modalId = modal ? modal.id : 'sectionModal';
                
                const formData = new FormData(newForm);
                const submitBtn = newForm.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.innerHTML : '';

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';
                }

                // Determine method
                let method = newForm.method.toUpperCase();
                if (newForm.querySelector('input[name="_method"]')) {
                    method = newForm.querySelector('input[name="_method"]').value.toUpperCase();
                }

                fetch(newForm.action, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'حدث خطأ');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Success:', data);
                        if (data.success) {
                            if (data.message && typeof Toast !== 'undefined') {
                                Toast.success(data.message);
                            } else if (data.message) {
                                alert(data.message);
                            }
                            if (window.closeModal) {
                                window.closeModal(modalId);
                            }
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.reload();
                                }
                            }, 500);
                        } else {
                            if (data.errors) {
                                newForm.querySelectorAll('.error-message').forEach(el => el.remove());
                                newForm.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

                                Object.keys(data.errors).forEach(key => {
                                    const input = newForm.querySelector(`[name="${key}"]`);
                                    if (input) {
                                        input.classList.add('error');
                                        const errorMsg = document.createElement('span');
                                        errorMsg.className = 'error-message';
                                        errorMsg.style.color = 'red';
                                        errorMsg.textContent = data.errors[key][0];
                                        input.parentNode.appendChild(errorMsg);
                                    }
                                });
                            }
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                            }
                            if (data.message) {
                                alert(data.message);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                        alert('حدث خطأ أثناء الحفظ: ' + error.message);
                    });
            });
        }
    }, 300);
});
</script>


<form action="{{ route('admin.ready-apps.categories.store') }}" method="POST" id="readyAppCategoryCreateForm" class="modal-form" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="name">{{ __('messages.name') }} (AR) <span class="required">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" placeholder="أدخل اسم الفئة بالعربية" required>
    </div>

    <div class="form-group">
        <label for="name_en">{{ __('messages.name') }} (EN)</label>
        <input type="text" id="name_en" name="name_en" value="{{ old('name_en') }}" class="form-control"
            style="direction: ltr; text-align: left;" placeholder="Enter category name in English">
    </div>

    <div class="form-group">
        <label for="description">{{ __('messages.description') }} (AR)</label>
        <textarea id="description" name="description" class="form-control" rows="4" placeholder="أدخل وصف الفئة بالعربية"></textarea>
    </div>

    <div class="form-group">
        <label for="description_en">{{ __('messages.description') }} (EN)</label>
        <textarea id="description_en" name="description_en" class="form-control" rows="4"
            style="direction: ltr; text-align: left;" placeholder="Enter category description in English"></textarea>
    </div>

    <div class="form-group">
        <label for="image">{{ __('messages.image') }}</label>
        <div class="custom-file-upload">
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            <div class="file-dummy">
                <span class="default">No file chosen</span>
            </div>
        </div>
    </div>

    <div class="form-group d-flex justify-content-end" style="margin-top: 15px;">
        <label class="switch-container">
            <span class="label-text">{{ __('messages.active') }}</span>
            <div class="switch">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <span class="slider"></span>
            </div>
        </label>
    </div>

    {{-- Hidden/Optional fields pushed to background --}}
    <input type="hidden" name="sort_order" value="0">

    <div class="form-actions">
        <button type="submit" class="btn btn-save">
            <i class="fas fa-check"></i> {{ __('messages.save') }}
        </button>
    </div>
</form>

<style>
    .modal-form { display: flex; flex-direction: column; gap: 20px; }
    .modal-form .form-group { margin-bottom: 0; }
    .modal-form label { 
        display: block; 
        color: #374151;
        font-size: 14px; 
        font-weight: 500; 
        margin-bottom: 10px;
        text-align: right;
    }
    .modal-form .form-control {
        width: 100%;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        padding: 14px 16px;
        color: #1f2937;
        font-family: 'Cairo', sans-serif;
        font-size: 14px;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .modal-form .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: white;
    }
    .modal-form .form-control:hover {
        border-color: #9ca3af;
    }
    .modal-form textarea.form-control { resize: none; }

    /* Custom File Input */
    .custom-file-upload { position: relative; }
    .custom-file-upload input[type="file"] {
        opacity: 0;
        position: absolute;
        width: 100%;
        height: 100%;
        cursor: pointer;
        z-index: 2;
    }
    .file-dummy {
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        padding: 14px 16px;
        color: #6b7280;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    .file-dummy:hover {
        border-color: #9ca3af;
    }

    /* Switch Toggle */
    .switch-container {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        user-select: none;
        flex-direction: row-reverse;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #d1d5db;
        transition: .4s;
        border-radius: 22px;
        border: 1px solid #9ca3af;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 2px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    input:checked + .slider {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }
    input:checked + .slider:before {
        transform: translateX(20px);
        background-color: white;
    }

    .modal-form .form-actions {
        margin-top: 10px;
        display: flex;
        justify-content: center;
    }
    .btn-save {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 12px 40px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-save:hover { background: #2563eb; transform: translateY(-2px); }

    .error-message { color: #ef4444; font-size: 12px; margin-top: 8px; text-align: right; display: block; }
    .form-control.error { border-color: #ef4444; }

    /* Dark Mode Styles */
    [data-theme="dark"] .modal-form label {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    [data-theme="dark"] .modal-form .form-control {
        background: #23272e !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
        color: #fff !important;
    }
    [data-theme="dark"] .modal-form .form-control:focus {
        background: #2a2f38 !important;
        border-color: #3b82f6 !important;
    }
    [data-theme="dark"] .modal-form .form-control:hover {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    [data-theme="dark"] .file-dummy {
        background: #23272e !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
        color: rgba(255, 255, 255, 0.5) !important;
    }
    [data-theme="dark"] .file-dummy:hover {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    [data-theme="dark"] .slider {
        background-color: #23272e !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    [data-theme="dark"] .slider:before {
        background-color: #5f6673 !important;
    }
    [data-theme="dark"] input:checked + .slider {
        background-color: rgba(59, 130, 246, 0.2) !important;
        border-color: #3b82f6 !important;
    }
    [data-theme="dark"] input:checked + .slider:before {
        background-color: #3b82f6 !important;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.5) !important;
    }
</style>

<script>
    (function() {
        // Wait for DOM to be ready
        setTimeout(() => {
            // Update filename in dummy span
            const imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const fileName = e.target.files[0]?.name || 'No file chosen';
                    const dummy = this.nextElementSibling;
                    if (dummy) {
                        const span = dummy.querySelector('span');
                        if (span) {
                            span.textContent = fileName;
                        }
                    }
                });
            }

            const form = document.getElementById('readyAppCategoryCreateForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch(form.action, {
                        method: 'POST',
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
                            if (data.success) {
                                if (data.message && typeof Toast !== 'undefined') {
                                    Toast.success(data.message);
                                }
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                if (data.errors) {
                                    form.querySelectorAll('.error-message').forEach(el => el.remove());
                                    form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

                                    Object.keys(data.errors).forEach(key => {
                                        const input = form.querySelector(`[name="${key}"]`);
                                        const container = input ? input.closest('.form-group') : form;
                                        if (input) input.classList.add('error');
                                        
                                        const errorMsg = document.createElement('span');
                                        errorMsg.className = 'error-message';
                                        errorMsg.textContent = data.errors[key][0];
                                        container.appendChild(errorMsg);
                                    });
                                }
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                                if (data.message) {
                                    alert(data.message);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                            alert('حدث خطأ: ' + error.message);
                        });
                });
            }
        }, 100);
    })();
</script>
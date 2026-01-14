<form action="{{ $companyLogo ? route('admin.customer-facing.company-logo.update', $companyLogo) : route('admin.customer-facing.company-logo.store') }}" 
      method="POST" 
      id="companyLogoForm"
      class="modal-form"
      enctype="multipart/form-data">
    @csrf
    @if($companyLogo)
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="heading">{{ __('messages.heading') }} (AR) <span class="required">*</span></label>
        <input type="text" 
               id="heading" 
               name="heading" 
               class="form-control" 
               value="{{ old('heading', $companyLogo->heading ?? '') }}" 
               required>
    </div>

    <div class="form-group">
        <label for="heading_en">{{ __('messages.heading') }} (EN)</label>
        <input type="text" 
               id="heading_en" 
               name="heading_en" 
               class="form-control" 
               value="{{ old('heading_en', $companyLogo->heading_en ?? '') }}"
               style="direction: ltr; text-align: left;">
    </div>

    <div class="form-group">
        <label>{{ __('messages.logos') }}</label>
        <div id="logos-container">
            @if($companyLogo && $companyLogo->logos && count($companyLogo->logos) > 0)
                @foreach($companyLogo->logos as $index => $logo)
                    <div class="logo-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong>{{ __('messages.logo') }} #{{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-danger" onclick="window.removeLogo(this)">
                                <i class="fas fa-trash"></i> {{ __('messages.remove_logo') }}
                            </button>
                        </div>
                        @if(isset($logo['image']) && $logo['image'])
                            <div style="margin-bottom: 10px;">
                                <img src="{{ asset('storage/' . $logo['image']) }}" 
                                     alt="Current Logo" 
                                     style="max-height: 100px; max-width: 200px; object-fit: contain; border: 1px solid var(--border-color); padding: 5px; border-radius: 4px;">
                                <input type="hidden" name="logos[{{ $index }}][image]" value="{{ $logo['image'] }}">
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.change_image') }}</label>
                                <input type="file" name="logos[{{ $index }}][image_new]" class="form-control" accept="image/*">
                            </div>
                        @else
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.image') }} <span class="required">*</span></label>
                                <input type="file" name="logos[{{ $index }}][image]" class="form-control" accept="image/*" required>
                            </div>
                        @endif
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.link') }}</label>
                            <input type="url" 
                                   name="logos[{{ $index }}][link]" 
                                   class="form-control" 
                                   value="{{ $logo['link'] ?? '' }}"
                                   placeholder="https://example.com">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.name') }}</label>
                            <input type="text" 
                                   name="logos[{{ $index }}][name]" 
                                   class="form-control" 
                                   value="{{ $logo['name'] ?? '' }}"
                                   placeholder="{{ __('messages.company_name') }}">
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn btn-secondary" onclick="window.addLogo()">
            <i class="fas fa-plus"></i> {{ __('messages.add_logo') }}
        </button>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" 
                   name="is_active" 
                   value="1" 
                   {{ old('is_active', $companyLogo->is_active ?? true) ? 'checked' : '' }}>
            {{ __('messages.active') }}
        </label>
    </div>

    <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
        <button type="button" class="btn btn-secondary" onclick="if(window.closeModal) window.closeModal('companyLogoModal'); return false;">
            {{ __('messages.cancel') }}
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('messages.save') }}
        </button>
    </div>
</form>

<script>
    (function() {
        const container = document.getElementById('logos-container');
        if (!container) return;

        // Get current logo count
        const existingLogos = container.querySelectorAll('.logo-item');
        let logoIndex = existingLogos.length;

        const logoText = '{{ __('messages.logo') }}';
        const removeLogoText = '{{ __('messages.remove_logo') }}';
        const imageText = '{{ __('messages.image') }}';
        const linkText = '{{ __('messages.link') }}';
        const nameText = '{{ __('messages.name') }}';
        const changeImageText = '{{ __('messages.change_image') }}';
        const companyNameText = '{{ __('messages.company_name') }}';

        window.addLogo = function() {
            if (!container) {
                console.error('logos-container not found');
                return;
            }
            
            const logoHtml = `
                <div class="logo-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <strong>${logoText} #${logoIndex + 1}</strong>
                        <button type="button" class="btn btn-sm btn-danger" onclick="window.removeLogo(this)">
                            <i class="fas fa-trash"></i> ${removeLogoText}
                        </button>
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label>${imageText} <span class="required">*</span></label>
                        <input type="file" name="logos[${logoIndex}][image]" class="form-control" accept="image/*" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label>${linkText}</label>
                        <input type="url" name="logos[${logoIndex}][link]" class="form-control" placeholder="https://example.com">
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label>${nameText}</label>
                        <input type="text" name="logos[${logoIndex}][name]" class="form-control" placeholder="${companyNameText}">
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', logoHtml);
            logoIndex++;
            updateLogoNumbers();
        };

        window.removeLogo = function(btn) {
            btn.closest('.logo-item').remove();
            updateLogoNumbers();
        };

        function updateLogoNumbers() {
            const items = container.querySelectorAll('.logo-item');
            items.forEach((item, index) => {
                const strong = item.querySelector('strong');
                if (strong) {
                    strong.textContent = logoText + ' #' + (index + 1);
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

    .modal-form .modal-footer {
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

    .modal-form .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .modal-form .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .modal-form .required {
        color: var(--danger-color);
    }
</style>


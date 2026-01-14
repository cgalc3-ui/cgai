<form action="{{ isset($section) ? route('admin.customer-facing.consultation-booking-section.update', $section) : route('admin.customer-facing.consultation-booking-section.store') }}" method="POST" id="sectionForm" class="modal-form" enctype="multipart/form-data">
    @csrf
    @if(isset($section))
        @method('PUT')
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
        <label for="background_image">{{ __('messages.background_image') }}</label>
        <input type="file" id="background_image" name="background_image" accept="image/*" class="form-control">
        @if(isset($section) && $section->background_image)
            <small class="form-help">
                <img src="{{ asset('storage/' . $section->background_image) }}" alt="Current background" style="max-width: 200px; margin-top: 10px; border-radius: 8px;">
            </small>
        @endif
        @error('background_image')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label>{{ __('messages.buttons') }}</label>
        <div id="buttons-container">
            @php
                $buttons = old('buttons', isset($section) && $section->buttons ? $section->buttons : [['title' => '', 'title_en' => '', 'link' => '', 'target' => '_self', 'style' => 'primary']]);
            @endphp
            @foreach($buttons as $index => $button)
                <div class="button-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <strong>{{ __('messages.button') }} #{{ $index + 1 }}</strong>
                        @if($index > 0)
                            <button type="button" class="btn btn-sm btn-danger" onclick="window.removeButton(this)">
                                <i class="fas fa-trash"></i> {{ __('messages.remove_button') }}
                            </button>
                        @endif
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label>{{ __('messages.title') }} (AR) <span class="required">*</span></label>
                        <input type="text" name="buttons[{{ $index }}][title]" value="{{ $button['title'] ?? '' }}" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label>{{ __('messages.title') }} (EN)</label>
                        <input type="text" name="buttons[{{ $index }}][title_en]" value="{{ $button['title_en'] ?? '' }}" class="form-control" style="direction: ltr; text-align: left;">
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label>{{ __('messages.link') }} <span class="required">*</span></label>
                        <input type="url" name="buttons[{{ $index }}][link]" value="{{ $button['link'] ?? '' }}" class="form-control" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.target') }}</label>
                            <select name="buttons[{{ $index }}][target]" class="form-control">
                                <option value="_self" {{ ($button['target'] ?? '_self') == '_self' ? 'selected' : '' }}>{{ __('messages.same_window') }}</option>
                                <option value="_blank" {{ ($button['target'] ?? '_self') == '_blank' ? 'selected' : '' }}>{{ __('messages.new_window') }}</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.button_style') }}</label>
                            <select name="buttons[{{ $index }}][style]" class="form-control">
                                <option value="primary" {{ ($button['style'] ?? 'primary') == 'primary' ? 'selected' : '' }}>{{ __('messages.primary') }}</option>
                                <option value="secondary" {{ ($button['style'] ?? 'primary') == 'secondary' ? 'selected' : '' }}>{{ __('messages.secondary') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.addButton()">
            <i class="fas fa-plus"></i> {{ __('messages.add_button') }}
        </button>
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

<script>
(function() {
    // Initialize buttonIndex based on existing buttons
    const existingButtons = document.querySelectorAll('.button-item');
    let buttonIndex = existingButtons.length > 0 ? existingButtons.length : {{ count($buttons) }};
    
    const buttonText = '{{ __('messages.button') }}';
    const removeButtonText = '{{ __('messages.remove_button') }}';
    const titleText = '{{ __('messages.title') }}';
    const linkText = '{{ __('messages.link') }}';
    const targetText = '{{ __('messages.target') }}';
    const buttonStyleText = '{{ __('messages.button_style') }}';
    const sameWindowText = '{{ __('messages.same_window') }}';
    const newWindowText = '{{ __('messages.new_window') }}';
    const primaryText = '{{ __('messages.primary') }}';
    const secondaryText = '{{ __('messages.secondary') }}';

    window.addButton = function() {
        const container = document.getElementById('buttons-container');
        if (!container) {
            console.error('buttons-container not found');
            return;
        }
        
        const buttonHtml = `
            <div class="button-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong>${buttonText} #${buttonIndex + 1}</strong>
                    <button type="button" class="btn btn-sm btn-danger" onclick="window.removeButton(this)">
                        <i class="fas fa-trash"></i> ${removeButtonText}
                    </button>
                </div>
                <div class="form-group" style="margin-bottom: 10px;">
                    <label>${titleText} (AR) <span class="required">*</span></label>
                    <input type="text" name="buttons[${buttonIndex}][title]" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom: 10px;">
                    <label>${titleText} (EN)</label>
                    <input type="text" name="buttons[${buttonIndex}][title_en]" class="form-control" style="direction: ltr; text-align: left;">
                </div>
                <div class="form-group" style="margin-bottom: 10px;">
                    <label>${linkText} <span class="required">*</span></label>
                    <input type="url" name="buttons[${buttonIndex}][link]" class="form-control" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label>${targetText}</label>
                        <select name="buttons[${buttonIndex}][target]" class="form-control">
                            <option value="_self">${sameWindowText}</option>
                            <option value="_blank">${newWindowText}</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label>${buttonStyleText}</label>
                        <select name="buttons[${buttonIndex}][style]" class="form-control">
                            <option value="primary">${primaryText}</option>
                            <option value="secondary">${secondaryText}</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', buttonHtml);
        buttonIndex++;
        updateButtonNumbers();
    };

    window.removeButton = function(btn) {
        btn.closest('.button-item').remove();
        updateButtonNumbers();
    };

    function updateButtonNumbers() {
        const items = document.querySelectorAll('.button-item');
        items.forEach((item, index) => {
            const strong = item.querySelector('strong');
            if (strong) {
                strong.textContent = buttonText + ' #' + (index + 1);
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

    .modal-form .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .modal-form .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }
</style>


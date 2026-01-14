<form action="{{ isset($navigationItem) ? route('admin.customer-facing.navigation.menu-items.update', $navigationItem) : route('admin.customer-facing.navigation.menu-items.store') }}" method="POST" id="menuItemForm" class="modal-form">
    @csrf
    @if(isset($navigationItem))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="title">{{ __('messages.title') }} (AR) <span class="required">*</span></label>
        <input type="text" id="title" name="title" value="{{ old('title', $navigationItem->title ?? '') }}" class="form-control" required>
        @error('title')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="title_en">{{ __('messages.title') }} (EN)</label>
        <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $navigationItem->title_en ?? '') }}" class="form-control" style="direction: ltr; text-align: left;">
        @error('title_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="link">{{ __('messages.link') }}</label>
        <input type="url" id="link" name="link" value="{{ old('link', $navigationItem->link ?? '') }}" class="form-control">
        @error('link')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="target">{{ __('messages.target') }}</label>
        <select id="target" name="target" class="form-control">
            <option value="_self" {{ old('target', $navigationItem->target ?? '_self') == '_self' ? 'selected' : '' }}>{{ __('messages.same_window') }}</option>
            <option value="_blank" {{ old('target', $navigationItem->target ?? '_self') == '_blank' ? 'selected' : '' }}>{{ __('messages.new_window') }}</option>
        </select>
        @error('target')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $navigationItem->is_active ?? true) ? 'checked' : '' }}>
            <span>{{ __('messages.active') }}</span>
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('messages.save') }}
        </button>
        <button type="button" class="btn btn-secondary" onclick="if(window.closeModal) window.closeModal('menuItemModal'); return false;">
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


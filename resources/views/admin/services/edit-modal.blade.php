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

    .modal-form .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }

    .modal-form .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modal-form .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .modal-form .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
</style>


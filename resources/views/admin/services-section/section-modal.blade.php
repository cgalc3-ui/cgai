<form action="{{ isset($section) ? route('admin.customer-facing.services-section.update', $section) : route('admin.customer-facing.services-section.store') }}" method="POST" id="sectionForm" class="modal-form" enctype="multipart/form-data">
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
        <label>{{ __('messages.select_categories') ?? 'اختر الفئات' }} <span class="required">*</span></label>
        <div style="max-height: 300px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px; padding: 10px;">
            @foreach($categories as $category)
                <label class="checkbox-label" style="display: flex; align-items: center; gap: 10px; padding: 10px; cursor: pointer; border-radius: 6px; margin-bottom: 5px; transition: background 0.2s;">
                    <input type="checkbox" 
                           name="category_ids[]" 
                           value="{{ $category->id }}"
                           {{ (isset($section) && $section->category_ids && in_array($category->id, $section->category_ids)) ? 'checked' : '' }}
                           onchange="toggleCategoryEdit({{ $category->id }}, this.checked)">
                    <div style="flex: 1;">
                        <strong>{{ $category->trans('name') }}</strong>
                        @if($category->image)
                            <img src="{{ strpos($category->image, '/storage/') === 0 ? $category->image : asset('storage/' . $category->image) }}" alt="{{ $category->trans('name') }}" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px; float: right;">
                        @endif
                    </div>
                </label>
            @endforeach
        </div>
        @error('category_ids')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="update_categories" value="1" id="update_categories" checked>
            <span>{{ __('messages.update_categories_data') ?? 'تعديل بيانات الفئات المحددة' }}</span>
        </label>
        <small class="form-help">{{ __('messages.update_categories_help') ?? 'يمكنك تعديل بيانات الفئات المحددة (العنوان، الوصف، الصورة) من هنا' }}</small>
    </div>

    <div id="categories-edit-container">
        <h4 style="margin: 20px 0 15px; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">
            {{ __('messages.edit_categories_data') ?? 'تعديل بيانات الفئات المحددة' }}
        </h4>
        <div id="categories-edit-fields"></div>
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
    const categoriesData = @json($categories->map(function($cat) {
        return [
            'id' => $cat->id,
            'name' => $cat->name ?? '',
            'name_en' => $cat->name_en ?? '',
            'description' => $cat->description ?? '',
            'description_en' => $cat->description_en ?? '',
            'image' => $cat->image ?? '',
        ];
    }));
    
    // Convert to object with id as key
    const categories = {};
    categoriesData.forEach(cat => {
        categories[cat.id] = cat;
    });

    const selectedCategories = new Set();
    @if(isset($section) && $section->category_ids)
        @foreach($section->category_ids as $catId)
            selectedCategories.add({{ $catId }});
        @endforeach
    @endif

    // Initialize selected categories from checkboxes
    document.querySelectorAll('input[name="category_ids[]"]:checked').forEach(checkbox => {
        selectedCategories.add(parseInt(checkbox.value));
    });

    window.toggleCategoryEdit = function(categoryId, isChecked) {
        if (isChecked) {
            selectedCategories.add(categoryId);
        } else {
            selectedCategories.delete(categoryId);
        }
        updateCategoriesEditContainer();
    };

    function updateCategoriesEditContainer() {
        const container = document.getElementById('categories-edit-container');
        const fieldsContainer = document.getElementById('categories-edit-fields');
        const updateCheckbox = document.getElementById('update_categories');

        if (selectedCategories.size === 0) {
            container.style.display = 'none';
            return;
        }

        // Always show if categories are selected
        container.style.display = 'block';

        fieldsContainer.innerHTML = '';

        selectedCategories.forEach(categoryId => {
            const category = categories[categoryId];
            if (!category) return;

            const categoryHtml = `
                <div class="category-edit-item" style="border: 1px solid var(--border-color); padding: 20px; border-radius: 8px; margin-bottom: 20px; background: var(--card-bg);">
                    <h5 style="margin-bottom: 15px; color: var(--primary-color);">${category.name || 'Category #' + categoryId}</h5>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>{{ __('messages.title') }} (AR)</label>
                        <input type="text" name="category_names[${categoryId}]" value="${category.name || ''}" class="form-control">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>{{ __('messages.title') }} (EN)</label>
                        <input type="text" name="category_names_en[${categoryId}]" value="${category.name_en || ''}" class="form-control" style="direction: ltr; text-align: left;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>{{ __('messages.description') }} (AR)</label>
                        <textarea name="category_descriptions[${categoryId}]" class="form-control" rows="3">${category.description || ''}</textarea>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>{{ __('messages.description') }} (EN)</label>
                        <textarea name="category_descriptions_en[${categoryId}]" class="form-control" rows="3" style="direction: ltr; text-align: left;">${category.description_en || ''}</textarea>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>{{ __('messages.image') }}</label>
                        <input type="file" name="category_images[${categoryId}]" accept="image/*" class="form-control">
                        ${category.image ? `<small class="form-help"><img src="{{ asset('storage/') }}/${category.image}" alt="Current image" style="max-width: 150px; margin-top: 10px; border-radius: 4px;"></small>` : ''}
                    </div>
                </div>
            `;
            fieldsContainer.insertAdjacentHTML('beforeend', categoryHtml);
        });
    }

    // Watch for checkbox changes to update container
    document.querySelectorAll('input[name="category_ids[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleCategoryEdit(parseInt(this.value), this.checked);
        });
    });

    // Initialize on load
    updateCategoriesEditContainer();
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

    .modal-form .checkbox-label:hover {
        background: var(--bg-light);
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


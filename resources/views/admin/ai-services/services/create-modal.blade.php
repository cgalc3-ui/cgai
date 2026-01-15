<form action="{{ route('admin.ai-services.services.store') }}" method="POST" enctype="multipart/form-data"
    class="modal-form" id="createAiServiceForm">
    @csrf

    <div class="form-group">
        <label for="name">{{ __('messages.name') }} (AR) <span class="required">*</span></label>
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
        @error('name')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="name_en">{{ __('messages.name') }} (EN)</label>
        <input type="text" id="name_en" name="name_en" class="form-control" value="{{ old('name_en') }}"
            style="direction: ltr; text-align: left;">
        @error('name_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="category_id">{{ __('messages.ai_service_category') }} <span class="required">*</span></label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">{{ __('messages.select_category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->trans('name') }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group price-field">
            <label for="price">{{ __('messages.price') }} (SAR) <span class="required">*</span></label>
            <input type="number" id="price" name="price" class="form-control" value="{{ old('price', 0) }}" step="0.01"
                required>
            @error('price')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_free" id="is_free_checkbox" value="1" {{ old('is_free') ? 'checked' : '' }}>
            <span style="font-weight: 700; color: #3b82f6;">{{ __('messages.is_free') }}</span>
        </label>
    </div>

    <div class="form-group">
        <label for="original_price">{{ __('messages.original_price') }} (SAR) ({{ __('messages.optional') }})</label>
        <input type="number" id="original_price" name="original_price" class="form-control"
            value="{{ old('original_price') }}" step="0.01">
        @error('original_price')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="short_description">{{ __('messages.short_description') }} (AR)</label>
        <textarea id="short_description" name="short_description" class="form-control"
            rows="3">{{ old('short_description') }}</textarea>
        @error('short_description')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="short_description_en">{{ __('messages.short_description') }} (EN)</label>
        <textarea id="short_description_en" name="short_description_en" class="form-control" rows="3"
            style="direction: ltr; text-align: left;">{{ old('short_description_en') }}</textarea>
        @error('short_description_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description">{{ __('messages.description') }} (AR)</label>
        <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
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
        <label for="full_description">{{ __('messages.full_description') }} (AR)</label>
        <textarea id="full_description" name="full_description" class="form-control"
            rows="6">{{ old('full_description') }}</textarea>
        @error('full_description')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="full_description_en">{{ __('messages.full_description') }} (EN)</label>
        <textarea id="full_description_en" name="full_description_en" class="form-control" rows="6"
            style="direction: ltr; text-align: left;">{{ old('full_description_en') }}</textarea>
        @error('full_description_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="main_image">{{ __('messages.main_image') }} <span class="required">*</span></label>
        <div class="image-upload-wrapper drop-zone" id="main_image_drop_zone">
            <input type="file" id="main_image" name="main_image" class="file-input" accept="image/*" required>
            <div class="drop-zone-content">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>{{ __('messages.drop_main_image_here') }}</p>
                <small>{{ __('messages.click_to_browse') }}</small>
            </div>
        </div>
        @error('main_image')
            <span class="error-message">{{ $message }}</span>
        @enderror
        <div id="main_image_preview" class="selected-images-container">
            <div class="selected-image-card new">
                <div class="image-wrapper">
                    <img id="main_image_preview_img" src="" alt="Preview">
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="gallery_images">{{ __('messages.gallery_images') }} ({{ __('messages.optional') }})</label>
        <div class="image-upload-wrapper drop-zone" id="gallery_images_drop_zone">
            <input type="file" id="gallery_images" name="gallery_images[]" class="file-input" accept="image/*" multiple>
            <div class="drop-zone-content">
                <i class="fas fa-images"></i>
                <p>{{ __('messages.drop_images_here') }}</p>
                <small>{{ __('messages.click_to_browse') }}</small>
            </div>
        </div>
        @error('gallery_images')
            <span class="error-message">{{ $message }}</span>
        @enderror
        @error('gallery_images.*')
            <span class="error-message">{{ $message }}</span>
        @enderror
        <div id="gallery_preview" class="selected-images-container"></div>
    </div>

    <div class="form-group">
        <label for="screenshots">{{ __('messages.ai_service_screenshots') }} ({{ __('messages.optional') }})</label>
        <div class="image-upload-wrapper drop-zone" id="screenshots_drop_zone">
            <input type="file" id="screenshots" name="screenshots[]" class="file-input" accept="image/*" multiple>
            <div class="drop-zone-content">
                <i class="fas fa-camera"></i>
                <p>{{ __('messages.drop_images_here') }}</p>
                <small>{{ __('messages.click_to_browse') }}</small>
            </div>
        </div>
        @error('screenshots')
            <span class="error-message">{{ $message }}</span>
        @enderror
        @error('screenshots.*')
            <span class="error-message">{{ $message }}</span>
        @enderror
        <div id="screenshots_preview" class="selected-images-container"></div>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}>
            <span>{{ __('messages.is_popular') }}</span>
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_new" value="1" {{ old('is_new') ? 'checked' : '' }}>
            <span>{{ __('messages.is_new') }}</span>
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
            <span>{{ __('messages.is_featured') }}</span>
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_latest" value="1" {{ old('is_latest') ? 'checked' : '' }}>
            <span>{{ __('messages.is_latest') ?? 'أحدث التقنيات' }}</span>
        </label>
        <small class="form-text">{{ __('messages.is_latest_help') ?? 'سيظهر في قسم أحدث التقنيات' }}</small>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_best_of_month" value="1" {{ old('is_best_of_month') ? 'checked' : '' }}>
            <span>{{ __('messages.is_best_of_month') ?? 'أفضل التقنيات خلال الشهر' }}</span>
        </label>
        <small class="form-text">{{ __('messages.is_best_of_month_help') ?? 'سيظهر في قسم أفضل التقنيات خلال الشهر' }}</small>
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
        <button type="button" class="btn btn-secondary" onclick="closeModal('createAiServiceModal'); return false;">
            <i class="fas fa-times"></i> {{ __('messages.cancel') }}
        </button>
    </div>
</form>

<script>
    (function () {
        console.log("AI Services Create Modal Script Initialized");

        const galleryFiles = [];
        const screenshotsFiles = [];

        // Is Free Toggle Logic
        const isFreeCheckbox = document.getElementById('is_free_checkbox');
        const priceField = document.querySelector('.price-field');
        const priceInput = document.getElementById('price');
        const originalPriceInput = document.getElementById('original_price');

        if (isFreeCheckbox) {
            isFreeCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    priceInput.value = 0;
                    priceInput.readOnly = true;
                    if (originalPriceInput) originalPriceInput.value = 0;
                    if (originalPriceInput) originalPriceInput.readOnly = true;
                    priceField.style.opacity = '0.5';
                } else {
                    priceInput.readOnly = false;
                    if (originalPriceInput) originalPriceInput.readOnly = false;
                    priceField.style.opacity = '1';
                }
            });
            // Initial state
            if (isFreeCheckbox.checked) {
                priceInput.readOnly = true;
                if (originalPriceInput) originalPriceInput.readOnly = true;
                priceField.style.opacity = '0.5';
            }
        }

        function renderPreview(type, filesArray) {
            const previewContainer = document.getElementById(`${type}_preview`);
            if (!previewContainer) {
                console.error(`Preview container not found for ${type}`);
                return;
            }

            previewContainer.innerHTML = '';

            if (filesArray.length === 0) {
                previewContainer.classList.remove('has-content');
                return;
            }

            previewContainer.classList.add('has-content');

            filesArray.forEach((file, index) => {
                const url = URL.createObjectURL(file);
                const card = document.createElement('div');
                card.className = 'selected-image-card new';

                card.innerHTML = `
                    <div class="image-wrapper">
                        <img src="${url}" alt="Preview">
                    </div>
                    <button type="button" class="remove-image-btn" data-type="${type}" data-index="${index}">×</button>
                `;

                const removeBtn = card.querySelector('.remove-image-btn');
                removeBtn.onclick = function () {
                    const idx = parseInt(this.dataset.index);
                    const t = this.dataset.type;
                    if (t === 'gallery') {
                        galleryFiles.splice(idx, 1);
                        updateFileInput('gallery', galleryFiles);
                        renderPreview('gallery', galleryFiles);
                    } else {
                        screenshotsFiles.splice(idx, 1);
                        updateFileInput('screenshots', screenshotsFiles);
                        renderPreview('screenshots', screenshotsFiles);
                    }
                };

                previewContainer.appendChild(card);
            });
        }

        function updateFileInput(type, filesArray) {
            const inputId = type === 'gallery' ? 'gallery_images' : 'screenshots';
            const input = document.getElementById(inputId);
            if (!input) return;
            const dt = new DataTransfer();
            filesArray.forEach(file => dt.items.add(file));
            input.files = dt.files;
        }

        // Main Image Preview
        const mainInput = document.getElementById('main_image');
        if (mainInput) {
            mainInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                const preview = document.getElementById('main_image_preview');
                const img = document.getElementById('main_image_preview_img');
                if (file) {
                    if (img) img.src = URL.createObjectURL(file);
                    if (preview) preview.classList.add('has-content');
                } else {
                    if (preview) preview.classList.remove('has-content');
                }
            });
        }

        // Gallery & Screenshots Input Change
        ['gallery_images', 'screenshots'].forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('change', function (e) {
                    const type = id === 'gallery_images' ? 'gallery' : 'screenshots';
                    const filesArray = type === 'gallery' ? galleryFiles : screenshotsFiles;
                    const newFiles = Array.from(e.target.files);

                    newFiles.forEach(file => {
                        const exists = filesArray.some(f => f.name === file.name && f.size === file.size);
                        if (!exists) filesArray.push(file);
                    });

                    updateFileInput(type, filesArray);
                    renderPreview(type, filesArray);
                });
            }
        });

        // --- Drag & Drop Handlers ---
        ['main_image', 'gallery_images', 'screenshots'].forEach(id => {
            const dropZone = document.getElementById(`${id}_drop_zone`);
            const input = document.getElementById(id);

            if (!dropZone || !input) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, e => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
            });

            dropZone.addEventListener('drop', e => {
                const files = e.dataTransfer.files;
                if (!files.length) return;

                if (id === 'main_image') {
                    input.files = files;
                    input.dispatchEvent(new Event('change'));
                } else {
                    const type = id === 'gallery_images' ? 'gallery' : 'screenshots';
                    const filesArray = type === 'gallery' ? galleryFiles : screenshotsFiles;

                    Array.from(files).forEach(file => {
                        const exists = filesArray.some(f => f.name === file.name && f.size === file.size);
                        if (!exists) filesArray.push(file);
                    });

                    updateFileInput(type, filesArray);
                    renderPreview(type, filesArray);
                }
            }, false);

            dropZone.onclick = (e) => {
                if (e.target !== input) input.click();
            };
        });

        // Form Submit Handler
        const form = document.getElementById('createAiServiceForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                handleFormSubmit(form, 'createAiServiceModal');
            });
        }
    })();
</script>

<style>
    .modal-form .form-group {
        margin-bottom: 20px;
    }

    .modal-form .form-group label {
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

    .modal-form .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .modal-form .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
        display: block;
    }

    .modal-form .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .modal-form .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
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

    .modal-form .required {
        color: #ef4444;
    }

    .drop-zone {
        height: 140px;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        padding: 20px;
        text-align: center;
    }

    .drop-zone:hover,
    .drop-zone.dragover {
        border-color: #3b82f6;
        background: #eff6ff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .drop-zone.dragover {
        transform: scale(1.01);
    }

    .drop-zone input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }

    .drop-zone-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        color: #64748b;
        pointer-events: none;
        transition: transform 0.3s ease;
    }

    .drop-zone:hover .drop-zone-content {
        transform: translateY(-2px);
    }

    .drop-zone-content i {
        font-size: 36px;
        color: #94a3b8;
        transition: all 0.3s ease;
    }

    .drop-zone:hover i,
    .drop-zone.dragover i {
        color: #3b82f6;
        filter: drop-shadow(0 4px 6px rgba(59, 130, 246, 0.2));
    }

    .drop-zone-content p {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #475569;
    }

    .drop-zone-content small {
        font-size: 13px;
        color: #94a3b8;
    }

    [data-theme="dark"] .drop-zone {
        background: #111827;
        border-color: #374151;
    }

    [data-theme="dark"] .drop-zone:hover,
    [data-theme="dark"] .drop-zone.dragover {
        background: #1f2937;
        border-color: #3b82f6;
    }

    [data-theme="dark"] .drop-zone-content p {
        color: #e2e8f0;
    }

    .selected-images-container {
        display: none;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 15px;
        padding: 15px;
        background: #f8fafc;
        border-radius: 12px;
        border: 2px dashed #e2e8f0;
        min-height: 80px;
        transition: all 0.3s ease;
    }

    .selected-images-container.has-content {
        display: flex;
        border-style: solid;
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    [data-theme="dark"] .selected-images-container {
        background: #111827;
        border-color: #374151;
    }

    [data-theme="dark"] .selected-images-container.has-content {
        background: #1f2937;
        border-color: #4b5563;
    }

    .selected-image-card {
        position: relative;
        width: 100px;
        height: 100px;
        background: white;
        border-radius: 10px;
        padding: 0;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: visible;
    }

    .selected-image-card.new {
        border-color: #3b82f6;
        animation: cardAppear 0.3s ease-out;
    }

    @keyframes cardAppear {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(10px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    [data-theme="dark"] .selected-image-card {
        background: #374151;
        border-color: #4b5563;
    }

    .selected-image-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 5;
    }

    .image-wrapper {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
    }

    [data-theme="dark"] .image-wrapper {
        background: #1f2937;
    }

    .image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-image-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #ef4444;
        color: white;
        border: 2px solid white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.2s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        z-index: 10;
    }

    [data-theme="dark"] .remove-image-btn {
        border-color: #1f2937;
    }

    .remove-image-btn:hover {
        background: #dc2626;
        transform: scale(1.15);
    }
</style>
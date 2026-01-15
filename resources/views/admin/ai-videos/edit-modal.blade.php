<form id="editAiVideoForm" method="POST" action="{{ route('admin.ai-videos.update', $aiVideo) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="title">{{ __('messages.title') }} <span class="required">*</span></label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $aiVideo->title) }}" required>
        @error('title')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="title_en">{{ __('messages.title_en') }}</label>
        <input type="text" name="title_en" id="title_en" class="form-control" value="{{ old('title_en', $aiVideo->title_en) }}">
        @error('title_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description">{{ __('messages.description') }}</label>
        <textarea name="description" id="description" class="form-control" rows="5">{{ old('description', $aiVideo->description) }}</textarea>
        @error('description')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description_en">{{ __('messages.description_en') }}</label>
        <textarea name="description_en" id="description_en" class="form-control" rows="5">{{ old('description_en', $aiVideo->description_en) }}</textarea>
        @error('description_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="video_url">{{ __('messages.video_url') }} <span class="required">*</span></label>
        <input type="url" name="video_url" id="video_url" class="form-control" value="{{ old('video_url', $aiVideo->video_url) }}" required placeholder="https://youtube.com/watch?v=...">
        @error('video_url')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="thumbnail">{{ __('messages.thumbnail') }}</label>
        @if($aiVideo->thumbnail)
            <div style="margin-bottom: 10px;">
                <img src="{{ asset($aiVideo->thumbnail) }}" alt="Current thumbnail" style="max-width: 200px; border-radius: 8px;">
            </div>
        @endif
        <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*">
        @error('thumbnail')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="duration">{{ __('messages.duration_in_seconds') }}</label>
        <input type="number" name="duration" id="duration" class="form-control" value="{{ old('duration', $aiVideo->duration) }}" min="0" placeholder="{{ __('messages.duration_example') }}">
        @error('duration')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="category_id">{{ __('messages.category') }}</label>
        <select name="category_id" id="category_id" class="form-control">
            <option value="">{{ __('messages.select_category_placeholder') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $aiVideo->category_id) == $category->id ? 'selected' : '' }}>
                    {{ app()->getLocale() === 'en' && $category->name_en ? $category->name_en : $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $aiVideo->is_featured) ? 'checked' : '' }}>
            <span>{{ __('messages.is_featured') }}</span>
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $aiVideo->is_active) ? 'checked' : '' }}>
            <span>{{ __('messages.is_active') }}</span>
        </label>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editAiVideoModal')">{{ __('messages.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
    </div>
</form>

<script>
    document.getElementById('editAiVideoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.saving') }}';

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toast.success(data.message);
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route('admin.ai-videos.index') }}';
                }, 1000);
            } else {
                Toast.error(data.message || '{{ __('messages.error') }}');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toast.error('{{ __('messages.error') }}');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
</script>

<style>
    /* Modal Form Styling */
    #createAiVideoModal .modal-body,
    #editAiVideoModal .modal-body {
        padding: 28px;
    }

    #createAiVideoModal .form-group,
    #editAiVideoModal .form-group {
        margin-bottom: 20px;
    }

    #createAiVideoModal .form-group:last-child,
    #editAiVideoModal .form-group:last-child {
        margin-bottom: 0;
    }

    /* Form labels */
    #createAiVideoModal .form-group label,
    #editAiVideoModal .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-primary, #343a40);
        font-size: 14px;
    }

    /* Form inputs */
    #createAiVideoModal .form-control,
    #editAiVideoModal .form-control {
        width: 100% !important;
        padding: 12px 16px !important;
        border: 2px solid #e5e7eb !important;
        border-radius: 10px !important;
        font-size: 14px !important;
        font-family: 'Cairo', sans-serif !important;
        transition: all 0.3s !important;
        background: white !important;
        color: #1f2937 !important;
        box-sizing: border-box !important;
    }

    #createAiVideoModal .form-control:focus,
    #editAiVideoModal .form-control:focus {
        outline: none !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    #createAiVideoModal .form-control:hover,
    #editAiVideoModal .form-control:hover {
        border-color: #9ca3af !important;
    }

    #createAiVideoModal textarea.form-control,
    #editAiVideoModal textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    #createAiVideoModal select.form-control,
    #editAiVideoModal select.form-control {
        cursor: pointer;
    }

    #createAiVideoModal input[type="file"].form-control,
    #editAiVideoModal input[type="file"].form-control {
        padding: 8px 12px !important;
        cursor: pointer;
    }

    /* Error messages */
    #createAiVideoModal .error-message,
    #editAiVideoModal .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    /* Checkbox labels */
    #createAiVideoModal .checkbox-label,
    #editAiVideoModal .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-primary, #374151);
        cursor: pointer;
        font-weight: 500;
    }

    #createAiVideoModal .checkbox-label input[type="checkbox"],
    #editAiVideoModal .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #3b82f6;
    }

    /* Required indicator */
    #createAiVideoModal .required,
    #editAiVideoModal .required {
        color: #ef4444;
        margin-inline-start: 4px;
    }

    /* Dark mode support */
    [data-theme="dark"] #createAiVideoModal .form-control,
    [data-theme="dark"] #editAiVideoModal .form-control {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--border-color, #2a2d3a) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] #createAiVideoModal .form-control:focus,
    [data-theme="dark"] #editAiVideoModal .form-control:focus {
        border-color: var(--primary-color, #6658dd) !important;
    }

    [data-theme="dark"] #createAiVideoModal .form-group label,
    [data-theme="dark"] #editAiVideoModal .form-group label {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] #createAiVideoModal .checkbox-label,
    [data-theme="dark"] #editAiVideoModal .checkbox-label {
        color: var(--text-primary, #f1f5f9) !important;
    }
</style>


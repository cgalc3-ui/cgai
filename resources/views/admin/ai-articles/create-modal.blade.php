<form id="createAiArticleForm" method="POST" action="{{ route('admin.ai-articles.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="form-group">
        <label for="title">{{ __('messages.title') }} <span class="required">*</span></label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
        @error('title')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="title_en">{{ __('messages.title_en') }}</label>
        <input type="text" name="title_en" id="title_en" class="form-control" value="{{ old('title_en') }}">
        @error('title_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="excerpt">{{ __('messages.excerpt') }}</label>
        <textarea name="excerpt" id="excerpt" class="form-control" rows="3" maxlength="500">{{ old('excerpt') }}</textarea>
        @error('excerpt')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="excerpt_en">{{ __('messages.excerpt_en') }}</label>
        <textarea name="excerpt_en" id="excerpt_en" class="form-control" rows="3" maxlength="500">{{ old('excerpt_en') }}</textarea>
        @error('excerpt_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="content">{{ __('messages.content') }} <span class="required">*</span></label>
        <textarea name="content" id="content" class="form-control" rows="10" required>{{ old('content') }}</textarea>
        @error('content')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="content_en">{{ __('messages.content_en') }}</label>
        <textarea name="content_en" id="content_en" class="form-control" rows="10">{{ old('content_en') }}</textarea>
        @error('content_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="image">{{ __('messages.image') }}</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
        @error('image')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="category_id">{{ __('messages.category') }}</label>
        <select name="category_id" id="category_id" class="form-control">
            <option value="">{{ __('messages.select_category_placeholder') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ app()->getLocale() === 'en' && $category->name_en ? $category->name_en : $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="published_at">{{ __('messages.published_at') }}</label>
        <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at') }}">
        @error('published_at')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
            <span>{{ __('messages.is_featured') }}</span>
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
            <span>{{ __('messages.is_active') }}</span>
        </label>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('createAiArticleModal')">{{ __('messages.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
    </div>
</form>

<script>
    document.getElementById('createAiArticleForm').addEventListener('submit', function(e) {
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
                    window.location.href = data.redirect || '{{ route('admin.ai-articles.index') }}';
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
    #createAiArticleModal .modal-body,
    #editAiArticleModal .modal-body {
        padding: 28px;
    }

    #createAiArticleModal .form-group,
    #editAiArticleModal .form-group {
        margin-bottom: 20px;
    }

    #createAiArticleModal .form-group:last-child,
    #editAiArticleModal .form-group:last-child {
        margin-bottom: 0;
    }

    /* Form labels */
    #createAiArticleModal .form-group label,
    #editAiArticleModal .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-primary, #343a40);
        font-size: 14px;
    }

    /* Form inputs */
    #createAiArticleModal .form-control,
    #editAiArticleModal .form-control {
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

    #createAiArticleModal .form-control:focus,
    #editAiArticleModal .form-control:focus {
        outline: none !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    #createAiArticleModal .form-control:hover,
    #editAiArticleModal .form-control:hover {
        border-color: #9ca3af !important;
    }

    #createAiArticleModal textarea.form-control,
    #editAiArticleModal textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    #createAiArticleModal select.form-control,
    #editAiArticleModal select.form-control {
        cursor: pointer;
    }

    #createAiArticleModal input[type="file"].form-control,
    #editAiArticleModal input[type="file"].form-control {
        padding: 8px 12px !important;
        cursor: pointer;
    }

    #createAiArticleModal input[type="datetime-local"].form-control,
    #editAiArticleModal input[type="datetime-local"].form-control {
        cursor: pointer;
    }

    /* Error messages */
    #createAiArticleModal .error-message,
    #editAiArticleModal .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    /* Checkbox labels */
    #createAiArticleModal .checkbox-label,
    #editAiArticleModal .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-primary, #374151);
        cursor: pointer;
        font-weight: 500;
    }

    #createAiArticleModal .checkbox-label input[type="checkbox"],
    #editAiArticleModal .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #3b82f6;
    }

    /* Required indicator */
    #createAiArticleModal .required,
    #editAiArticleModal .required {
        color: #ef4444;
        margin-inline-start: 4px;
    }

    /* Dark mode support */
    [data-theme="dark"] #createAiArticleModal .form-control,
    [data-theme="dark"] #editAiArticleModal .form-control {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--border-color, #2a2d3a) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] #createAiArticleModal .form-control:focus,
    [data-theme="dark"] #editAiArticleModal .form-control:focus {
        border-color: var(--primary-color, #6658dd) !important;
    }

    [data-theme="dark"] #createAiArticleModal .form-group label,
    [data-theme="dark"] #editAiArticleModal .form-group label {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] #createAiArticleModal .checkbox-label,
    [data-theme="dark"] #editAiArticleModal .checkbox-label {
        color: var(--text-primary, #f1f5f9) !important;
    }
</style>


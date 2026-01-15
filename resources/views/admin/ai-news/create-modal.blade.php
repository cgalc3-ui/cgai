<form id="createAiNewsForm" method="POST" action="{{ route('admin.ai-news.store') }}" enctype="multipart/form-data">
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
        <button type="button" class="btn btn-secondary" onclick="closeModal('createAiNewsModal')">{{ __('messages.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
    </div>
</form>

<script>
    document.getElementById('createAiNewsForm').addEventListener('submit', function(e) {
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
                    window.location.href = data.redirect || '{{ route('admin.ai-news.index') }}';
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


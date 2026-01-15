<form id="editAiJobForm" method="POST" action="{{ route('admin.ai-jobs.update', $aiJob) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="title">{{ __('messages.title') }} <span class="required">*</span></label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $aiJob->title) }}" required>
        @error('title')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="title_en">{{ __('messages.title_en') }}</label>
        <input type="text" name="title_en" id="title_en" class="form-control" value="{{ old('title_en', $aiJob->title_en) }}">
        @error('title_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description">{{ __('messages.description') }} <span class="required">*</span></label>
        <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description', $aiJob->description) }}</textarea>
        @error('description')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="description_en">{{ __('messages.description_en') }}</label>
        <textarea name="description_en" id="description_en" class="form-control" rows="5">{{ old('description_en', $aiJob->description_en) }}</textarea>
        @error('description_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="company">{{ __('messages.company') }} <span class="required">*</span></label>
        <input type="text" name="company" id="company" class="form-control" value="{{ old('company', $aiJob->company) }}" required>
        @error('company')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="company_en">{{ __('messages.company_en') }}</label>
        <input type="text" name="company_en" id="company_en" class="form-control" value="{{ old('company_en', $aiJob->company_en) }}">
        @error('company_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="location">{{ __('messages.location') }} <span class="required">*</span></label>
        <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $aiJob->location) }}" required>
        @error('location')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="location_en">{{ __('messages.location_en') }}</label>
        <input type="text" name="location_en" id="location_en" class="form-control" value="{{ old('location_en', $aiJob->location_en) }}">
        @error('location_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="salary_range">{{ __('messages.salary_range') }}</label>
        <input type="text" name="salary_range" id="salary_range" class="form-control" value="{{ old('salary_range', $aiJob->salary_range) }}" placeholder="{{ __('messages.salary_range_example') }}">
        @error('salary_range')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="job_type">{{ __('messages.job_type') }} <span class="required">*</span></label>
        <select name="job_type" id="job_type" class="form-control" required>
            <option value="full_time" {{ old('job_type', $aiJob->job_type) === 'full_time' ? 'selected' : '' }}>{{ __('messages.full_time') }}</option>
            <option value="part_time" {{ old('job_type', $aiJob->job_type) === 'part_time' ? 'selected' : '' }}>{{ __('messages.part_time') }}</option>
            <option value="contract" {{ old('job_type', $aiJob->job_type) === 'contract' ? 'selected' : '' }}>{{ __('messages.contract') }}</option>
            <option value="freelance" {{ old('job_type', $aiJob->job_type) === 'freelance' ? 'selected' : '' }}>{{ __('messages.freelance') }}</option>
            <option value="internship" {{ old('job_type', $aiJob->job_type) === 'internship' ? 'selected' : '' }}>{{ __('messages.internship') }}</option>
        </select>
        @error('job_type')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="requirements">{{ __('messages.requirements') }}</label>
        <textarea name="requirements" id="requirements" class="form-control" rows="5">{{ old('requirements', $aiJob->requirements) }}</textarea>
        @error('requirements')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="requirements_en">{{ __('messages.requirements_en') }}</label>
        <textarea name="requirements_en" id="requirements_en" class="form-control" rows="5">{{ old('requirements_en', $aiJob->requirements_en) }}</textarea>
        @error('requirements_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="benefits">{{ __('messages.benefits') }}</label>
        <textarea name="benefits" id="benefits" class="form-control" rows="5">{{ old('benefits', $aiJob->benefits) }}</textarea>
        @error('benefits')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="benefits_en">{{ __('messages.benefits_en') }}</label>
        <textarea name="benefits_en" id="benefits_en" class="form-control" rows="5">{{ old('benefits_en', $aiJob->benefits_en) }}</textarea>
        @error('benefits_en')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="application_email">{{ __('messages.application_email') }}</label>
        <input type="email" name="application_email" id="application_email" class="form-control" value="{{ old('application_email', $aiJob->application_email) }}">
        @error('application_email')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="application_url">{{ __('messages.application_url') }}</label>
        <input type="url" name="application_url" id="application_url" class="form-control" value="{{ old('application_url', $aiJob->application_url) }}">
        @error('application_url')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="expires_at">{{ __('messages.expires_at') }}</label>
        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control" value="{{ old('expires_at', $aiJob->expires_at ? $aiJob->expires_at->format('Y-m-d\TH:i') : '') }}">
        @error('expires_at')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $aiJob->is_featured) ? 'checked' : '' }}>
            <span>{{ __('messages.is_featured') }}</span>
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $aiJob->is_active) ? 'checked' : '' }}>
            <span>{{ __('messages.is_active') }}</span>
        </label>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editAiJobModal')">{{ __('messages.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
    </div>
</form>

<script>
    document.getElementById('editAiJobForm').addEventListener('submit', function(e) {
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
                    window.location.href = data.redirect || '{{ route('admin.ai-jobs.index') }}';
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
    #createAiJobModal .modal-body,
    #editAiJobModal .modal-body {
        padding: 28px;
    }

    #createAiJobModal .form-group,
    #editAiJobModal .form-group {
        margin-bottom: 20px;
    }

    #createAiJobModal .form-group:last-child,
    #editAiJobModal .form-group:last-child {
        margin-bottom: 0;
    }

    /* Form labels */
    #createAiJobModal .form-group label,
    #editAiJobModal .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-primary, #343a40);
        font-size: 14px;
    }

    /* Form inputs */
    #createAiJobModal .form-control,
    #editAiJobModal .form-control {
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

    #createAiJobModal .form-control:focus,
    #editAiJobModal .form-control:focus {
        outline: none !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    #createAiJobModal .form-control:hover,
    #editAiJobModal .form-control:hover {
        border-color: #9ca3af !important;
    }

    #createAiJobModal textarea.form-control,
    #editAiJobModal textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    #createAiJobModal select.form-control,
    #editAiJobModal select.form-control {
        cursor: pointer;
    }

    #createAiJobModal input[type="email"].form-control,
    #editAiJobModal input[type="email"].form-control {
        cursor: text;
    }

    #createAiJobModal input[type="url"].form-control,
    #editAiJobModal input[type="url"].form-control {
        cursor: text;
    }

    #createAiJobModal input[type="datetime-local"].form-control,
    #editAiJobModal input[type="datetime-local"].form-control {
        cursor: pointer;
    }

    /* Error messages */
    #createAiJobModal .error-message,
    #editAiJobModal .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    /* Checkbox labels */
    #createAiJobModal .checkbox-label,
    #editAiJobModal .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-primary, #374151);
        cursor: pointer;
        font-weight: 500;
    }

    #createAiJobModal .checkbox-label input[type="checkbox"],
    #editAiJobModal .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #3b82f6;
    }

    /* Required indicator */
    #createAiJobModal .required,
    #editAiJobModal .required {
        color: #ef4444;
        margin-inline-start: 4px;
    }

    /* Dark mode support */
    [data-theme="dark"] #createAiJobModal .form-control,
    [data-theme="dark"] #editAiJobModal .form-control {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--border-color, #2a2d3a) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] #createAiJobModal .form-control:focus,
    [data-theme="dark"] #editAiJobModal .form-control:focus {
        border-color: var(--primary-color, #6658dd) !important;
    }

    [data-theme="dark"] #createAiJobModal .form-group label,
    [data-theme="dark"] #editAiJobModal .form-group label {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] #createAiJobModal .checkbox-label,
    [data-theme="dark"] #editAiJobModal .checkbox-label {
        color: var(--text-primary, #f1f5f9) !important;
    }
</style>


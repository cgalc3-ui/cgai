@extends('layouts.dashboard')

@section('title', isset($tag) ? __('messages.edit_tag') ?? 'تعديل التقنية' : __('messages.add_tag') ?? 'إضافة تقنية')
@section('page-title', isset($tag) ? __('messages.edit_tag') ?? 'تعديل التقنية' : __('messages.add_tag') ?? 'إضافة تقنية')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ isset($tag) ? __('messages.edit_tag') ?? 'تعديل التقنية' : __('messages.add_tag') ?? 'إضافة تقنية' }}</h2>
            <p>{{ isset($tag) ? __('messages.edit_tag_desc') ?? 'تعديل بيانات التقنية' : __('messages.create_tag_desc') ?? 'إضافة تقنية جديدة' }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.ai-services.tags.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.back_to_list') }}
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ isset($tag) ? route('admin.ai-services.tags.update', $tag) : route('admin.ai-services.tags.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($tag))
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="service_id">{{ __('messages.select_service') ?? 'اختر الأداة' }} <span class="required">*</span></label>
                <select id="service_id" name="service_id" class="form-control" {{ isset($tag) ? 'disabled' : 'required' }}>
                    <option value="">{{ __('messages.select_service_placeholder') ?? '-- اختر أداة من القائمة --' }}</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" 
                            data-name="{{ $service->name }}" 
                            data-name-en="{{ $service->name_en ?? '' }}"
                            {{ old('service_id', isset($tag) && $tag->name === $service->name ? $service->id : '') == $service->id ? 'selected' : '' }}>
                            {{ $service->trans('name') }}
                        </option>
                    @endforeach
                </select>
                <small class="form-help">{{ __('messages.select_service_help') ?? 'اختر الأداة التي تريد إضافتها كتقنية شائعة' }}</small>
                @if(isset($tag))
                    <input type="hidden" name="service_id" value="{{ old('service_id') }}">
                @endif
                @error('service_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">{{ __('messages.name') }} (AR) <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $tag->name ?? '') }}" class="form-control" required>
                <small class="form-help">{{ __('messages.name_auto_filled') ?? 'سيتم ملء الاسم تلقائياً عند اختيار الأداة' }}</small>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name_en">{{ __('messages.name') }} (EN)</label>
                <input type="text" id="name_en" name="name_en" value="{{ old('name_en', $tag->name_en ?? '') }}" class="form-control"
                    style="direction: ltr; text-align: left;">
                <small class="form-help">{{ __('messages.name_en_auto_filled') ?? 'سيتم ملء الاسم الإنجليزي تلقائياً عند اختيار الأداة' }}</small>
                @error('name_en')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="slug">{{ __('messages.slug') }}</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $tag->slug ?? '') }}" class="form-control"
                    placeholder="{{ __('messages.slug_auto_generate') ?? 'سيتم إنشاؤه تلقائياً' }}">
                @error('slug')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="image">{{ __('messages.image') }}</label>
                <input type="file" id="image" name="image" accept="image/*" class="form-control">
                @if(isset($tag) && $tag->image)
                    <small class="form-help">
                        <img src="{{ strpos($tag->image, '/storage/') === 0 ? $tag->image : asset('storage/' . $tag->image) }}" 
                             alt="Current image" 
                             style="max-width: 200px; margin-top: 10px; border-radius: 8px; display: block;">
                    </small>
                @endif
                @error('image')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', isset($tag) ? $tag->is_active : true) ? 'checked' : '' }}>
                    <span>{{ __('messages.active') }}</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
                <a href="{{ route('admin.ai-services.tags.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceSelect = document.getElementById('service_id');
            const nameInput = document.getElementById('name');
            const nameEnInput = document.getElementById('name_en');
            
            if (serviceSelect && nameInput && nameEnInput) {
                serviceSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        const serviceName = selectedOption.getAttribute('data-name');
                        const serviceNameEn = selectedOption.getAttribute('data-name-en');
                        
                        if (serviceName) {
                            nameInput.value = serviceName;
                        }
                        if (serviceNameEn) {
                            nameEnInput.value = serviceNameEn;
                        }
                    } else {
                        nameInput.value = '';
                        nameEnInput.value = '';
                    }
                });
            }
        });
    </script>
@endsection


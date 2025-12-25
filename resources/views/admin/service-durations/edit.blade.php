@extends('layouts.dashboard')

@section('title', 'تعديل مدة خدمة')
@section('page-title', 'تعديل مدة خدمة')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>تعديل مدة خدمة</h2>
        <p>تعديل بيانات مدة الخدمة</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.service-durations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.service-durations.update', $serviceDuration) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="service_id">الخدمة <span class="required">*</span></label>
            <select id="service_id" name="service_id" class="form-control" required>
                <option value="">اختر الخدمة</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $serviceDuration->service_id) == $service->id ? 'selected' : '' }}>
                        {{ $service->subCategory->category->name }} - {{ $service->subCategory->name }} - {{ $service->name }}
                    </option>
                @endforeach
            </select>
            @error('service_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="duration_type">نوع المدة <span class="required">*</span></label>
            <select id="duration_type" name="duration_type" class="form-control" required>
                <option value="">اختر نوع المدة</option>
                <option value="hour" {{ old('duration_type', $serviceDuration->duration_type) == 'hour' ? 'selected' : '' }}>ساعة</option>
                <option value="day" {{ old('duration_type', $serviceDuration->duration_type) == 'day' ? 'selected' : '' }}>يوم</option>
                <option value="week" {{ old('duration_type', $serviceDuration->duration_type) == 'week' ? 'selected' : '' }}>أسبوع</option>
            </select>
            @error('duration_type')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="duration_value">قيمة المدة <span class="required">*</span></label>
            <input type="number" id="duration_value" name="duration_value" value="{{ old('duration_value', $serviceDuration->duration_value) }}" class="form-control" required min="1">
            @error('duration_value')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="price">السعر <span class="required">*</span></label>
            <input type="number" id="price" name="price" value="{{ old('price', $serviceDuration->price) }}" class="form-control" required step="0.01" min="0">
            @error('price')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $serviceDuration->is_active) ? 'checked' : '' }}>
                <span>نشط</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ
            </button>
            <a href="{{ route('admin.service-durations.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection


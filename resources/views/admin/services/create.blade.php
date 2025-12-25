@extends('layouts.dashboard')

@section('title', 'إضافة خدمة جديدة')
@section('page-title', 'إضافة خدمة جديدة')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>إضافة خدمة جديدة</h2>
        <p>إنشاء خدمة جديدة في النظام</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.services.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="sub_category_id">الفئة الفرعية <span class="required">*</span></label>
            <select id="sub_category_id" name="sub_category_id" class="form-control" required>
                <option value="">اختر الفئة الفرعية</option>
                @foreach($subCategories as $subCategory)
                    <option value="{{ $subCategory->id }}" {{ old('sub_category_id') == $subCategory->id ? 'selected' : '' }}>
                        {{ $subCategory->category->name }} - {{ $subCategory->name }}
                    </option>
                @endforeach
            </select>
            @error('sub_category_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="specialization_id">التخصص</label>
            <select id="specialization_id" name="specialization_id" class="form-control">
                <option value="">اختر التخصص (اختياري)</option>
                @foreach($specializations as $specialization)
                    <option value="{{ $specialization->id }}" {{ old('specialization_id') == $specialization->id ? 'selected' : '' }}>
                        {{ $specialization->name }}
                    </option>
                @endforeach
            </select>
            @error('specialization_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">الاسم <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">الوصف</label>
            <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <span>نشط</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ
            </button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection


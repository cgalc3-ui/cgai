@extends('layouts.dashboard')

@section('title', 'تعديل فئة فرعية')
@section('page-title', 'تعديل فئة فرعية')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>تعديل فئة فرعية</h2>
        <p>تعديل بيانات الفئة الفرعية</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.sub-categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.sub-categories.update', $subCategory) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="category_id">الفئة الرئيسية <span class="required">*</span></label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">اختر الفئة الرئيسية</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $subCategory->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">الاسم <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $subCategory->name) }}" class="form-control" required>
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">الوصف</label>
            <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $subCategory->description) }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subCategory->is_active) ? 'checked' : '' }}>
                <span>نشط</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ
            </button>
            <a href="{{ route('admin.sub-categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection


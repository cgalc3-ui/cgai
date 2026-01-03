@extends('layouts.dashboard')

@section('title', 'تعديل التخصص')
@section('page-title', 'تعديل التخصص')

@section('content')
<div class="page-header">
    <h2>تعديل التخصص: {{ $specialization->name }}</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.specializations.update', $specialization) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">اسم التخصص *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $specialization->name) }}" class="form-control" required placeholder="مثال: Backend Development">
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">الوصف</label>
            <textarea id="description" name="description" rows="4" class="form-control" placeholder="وصف مختصر عن التخصص">{{ old('description', $specialization->description) }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $specialization->is_active) ? 'checked' : '' }}>
                <span>التخصص نشط</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
            <a href="{{ route('admin.specializations') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'تعديل استشارة')
@section('page-title', 'تعديل استشارة')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>تعديل استشارة</h2>
            <p>تعديل بيانات الاستشارة</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.consultations.update', $consultation) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="category_id">التخصص <span class="required">*</span></label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">اختر التخصص</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $consultation->category_id) == $category->id ? 'selected' : '' }}>
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
                <input type="text" id="name" name="name" value="{{ old('name', $consultation->name) }}" class="form-control"
                    required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="slug">الرابط (Slug)</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $consultation->slug) }}" class="form-control"
                    placeholder="سيتم إنشاؤه تلقائياً إذا تركت فارغاً">
                @error('slug')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">الوصف</label>
                <textarea id="description" name="description" class="form-control"
                    rows="4">{{ old('description', $consultation->description) }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="fixed_price">السعر الثابت (ريال) <span class="required">*</span></label>
                <input type="number" id="fixed_price" name="fixed_price" value="{{ old('fixed_price', $consultation->fixed_price) }}"
                    class="form-control" step="0.01" min="0" required>
                @error('fixed_price')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small class="form-text text-muted">مدة الاستشارة: حسب الـ Time Slot المختار</small>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $consultation->is_active) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </div>

@endsection


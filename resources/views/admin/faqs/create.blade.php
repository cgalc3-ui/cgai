@extends('layouts.dashboard')

@section('title', 'إضافة سؤال جديد')
@section('page-title', 'إضافة سؤال جديد')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>إضافة سؤال جديد</h2>
            <p>إنشاء سؤال شائع جديد في النظام</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.faqs.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="question">السؤال <span class="required">*</span></label>
                <input type="text" id="question" name="question" value="{{ old('question') }}" class="form-control" required
                    placeholder="مثال: كيف يمكنني حجز موعد؟">
                @error('question')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="category">الفئة <span class="required">*</span></label>
                <select id="category" name="category" class="form-control" required>
                    <option value="عام" {{ old('category') == 'عام' ? 'selected' : '' }}>عام</option>
                    <option value="الحجوزات" {{ old('category') == 'الحجوزات' ? 'selected' : '' }}>الحجوزات</option>
                    <option value="الدفع" {{ old('category') == 'الدفع' ? 'selected' : '' }}>الدفع</option>
                    <option value="الحساب" {{ old('category') == 'الحساب' ? 'selected' : '' }}>الحساب</option>
                </select>
                @error('category')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="answer">الإجابة <span class="required">*</span></label>
                <textarea id="answer" name="answer" class="form-control" rows="6" required>{{ old('answer') }}</textarea>
                @error('answer')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="sort_order">الترتيب</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"
                    class="form-control">
                @error('sort_order')
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
                    <i class="fas fa-save"></i> حفظ السؤال
                </button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </div>

    <style>
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        .required {
            color: #ef4444;
        }

        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: var(--text-primary);
            border: none;
        }
    </style>
@endsection
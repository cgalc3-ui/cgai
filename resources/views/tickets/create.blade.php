@extends('layouts.dashboard')

@section('title', 'إنشاء تذكرة جديدة')
@section('page-title', 'إنشاء تذكرة جديدة')

@section('content')
    <div class="create-ticket-container">
        <div class="create-ticket-header">
            <h2>إنشاء تذكرة دعم جديدة</h2>
            <a href="{{ route('tickets.index') }}" class="btn-back">
                <i class="fas fa-arrow-right"></i>
                العودة للتذاكر
            </a>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="ticket-form">
            @csrf

            <div class="form-group">
                <label for="subject">الموضوع <span class="required">*</span></label>
                <input type="text" name="subject" id="subject" class="form-input" 
                       value="{{ old('subject') }}" required>
                @error('subject')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="priority">الأولوية</label>
                <select name="priority" id="priority" class="form-select">
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>منخفضة</option>
                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>متوسطة</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>عالية</option>
                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>عاجلة</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">الوصف <span class="required">*</span></label>
                <textarea name="description" id="description" class="form-textarea" rows="6" required>{{ old('description') }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="attachments">المرفقات (صور)</label>
                <input type="file" name="attachments[]" id="attachments" 
                       class="form-input" multiple accept="image/*">
                <small class="form-help">يمكنك إرفاق حتى 5 صور (حجم كل صورة حتى 5MB)</small>
                @error('attachments.*')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    إرسال التذكرة
                </button>
                <a href="{{ route('tickets.index') }}" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            .create-ticket-container {
                background: white;
                border-radius: 12px;
                box-shadow: var(--card-shadow);
                padding: 24px;
            }

            .create-ticket-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
                padding-bottom: 16px;
                border-bottom: 2px solid #e5e7eb;
            }

            .create-ticket-header h2 {
                font-size: 24px;
                font-weight: 700;
                color: #1f2937;
                margin: 0;
            }

            .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                background: #f3f4f6;
                color: #4b5563;
                border-radius: 8px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                transition: all 0.2s;
            }

            .btn-back:hover {
                background: #e5e7eb;
            }

            .ticket-form {
                max-width: 800px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
            }

            .required {
                color: #ef4444;
            }

            .form-input,
            .form-select,
            .form-textarea {
                width: 100%;
                padding: 12px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 14px;
                font-family: inherit;
                transition: all 0.2s;
            }

            .form-input:focus,
            .form-select:focus,
            .form-textarea:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .form-textarea {
                resize: vertical;
            }

            .form-help {
                display: block;
                font-size: 12px;
                color: #6b7280;
                margin-top: 4px;
            }

            .error-message {
                display: block;
                font-size: 12px;
                color: #ef4444;
                margin-top: 4px;
            }

            .form-actions {
                display: flex;
                gap: 12px;
                margin-top: 24px;
            }

            .btn-submit {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                background: #3b82f6;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-submit:hover {
                background: #2563eb;
            }

            .btn-cancel {
                display: inline-flex;
                align-items: center;
                padding: 12px 24px;
                background: #f3f4f6;
                color: #4b5563;
                border-radius: 8px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                transition: all 0.2s;
            }

            .btn-cancel:hover {
                background: #e5e7eb;
            }
        </style>
    @endpush
@endsection


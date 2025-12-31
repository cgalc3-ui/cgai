@extends('layouts.dashboard')

@section('title', 'تعديل باقة')
@section('page-title', 'تعديل باقة')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>تعديل باقة</h2>
        <p>تعديل بيانات الباقة</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST" id="subscriptionForm">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">اسم الباقة <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $subscription->name) }}" class="form-control" required>
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">وصف الباقة</label>
            <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $subscription->description) }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="price">السعر (ر.س) <span class="required">*</span></label>
            <input type="number" id="price" name="price" value="{{ old('price', $subscription->price) }}" step="0.01" min="0" class="form-control" required>
            @error('price')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="duration_type">الفترة <span class="required">*</span></label>
            <select id="duration_type" name="duration_type" class="form-control" required>
                <option value="monthly" {{ old('duration_type', $subscription->duration_type) == 'monthly' ? 'selected' : '' }}>شهري</option>
                <option value="3months" {{ old('duration_type', $subscription->duration_type) == '3months' ? 'selected' : '' }}>3 أشهر</option>
                <option value="6months" {{ old('duration_type', $subscription->duration_type) == '6months' ? 'selected' : '' }}>6 أشهر</option>
                <option value="yearly" {{ old('duration_type', $subscription->duration_type) == 'yearly' ? 'selected' : '' }}>سنوي</option>
            </select>
            @error('duration_type')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>المميزات <span class="required">*</span></label>
            <div id="features-container">
                @php
                    $features = old('features', $subscription->features ?? []);
                    if (empty($features)) {
                        $features = [['name' => '']];
                    }
                    // Handle both old format (array of strings) and new format (array of objects)
                    if (!empty($features) && is_array($features[0]) && !isset($features[0]['name'])) {
                        // Old format: ['feature1', 'feature2'] - convert to new format
                        $features = array_map(function($f) {
                            return ['name' => is_string($f) ? $f : ''];
                        }, $features);
                    } elseif (!empty($features) && is_string($features[0])) {
                        // Old format: array of strings
                        $features = array_map(function($f) {
                            return ['name' => $f];
                        }, $features);
                    }
                @endphp
                @foreach($features as $index => $feature)
                    <div class="feature-item" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="features[{{ $index }}][name]" class="form-control" 
                               value="{{ is_array($feature) ? ($feature['name'] ?? '') : '' }}" 
                               placeholder="اسم الميزة" required>
                        <button type="button" class="btn btn-danger remove-feature" 
                                style="{{ count($features) > 1 ? '' : 'display: none;' }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-feature" class="btn btn-secondary" style="margin-top: 10px;">
                <i class="fas fa-plus"></i> إضافة ميزة
            </button>
            @error('features')
                <span class="error-message">{{ $message }}</span>
            @enderror
            @error('features.*.name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}>
                <span>نشط</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ
            </button>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let featureIndex = {{ count(old('features', $subscription->features ?? [])) }};
    const featuresContainer = document.getElementById('features-container');
    const addFeatureBtn = document.getElementById('add-feature');

    // إضافة ميزة جديدة
    addFeatureBtn.addEventListener('click', function() {
        const featureItem = document.createElement('div');
        featureItem.className = 'feature-item';
        featureItem.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
        featureItem.innerHTML = `
            <input type="text" name="features[${featureIndex}][name]" class="form-control" placeholder="اسم الميزة" required>
            <button type="button" class="btn btn-danger remove-feature">
                <i class="fas fa-trash"></i>
            </button>
        `;
        featuresContainer.appendChild(featureItem);
        featureIndex++;
        updateRemoveButtons();
    });

    // حذف ميزة
    featuresContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            e.target.closest('.feature-item').remove();
            updateRemoveButtons();
        }
    });

    // تحديث أزرار الحذف (إظهار/إخفاء)
    function updateRemoveButtons() {
        const featureItems = featuresContainer.querySelectorAll('.feature-item');
        featureItems.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-feature');
            if (featureItems.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    // تحديث عند التحميل
    updateRemoveButtons();
});
</script>
@endsection

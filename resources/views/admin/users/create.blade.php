@extends('layouts.dashboard')

@section('title', 'إضافة مستخدم')
@section('page-title', 'إضافة مستخدم')

@section('content')
<div class="form-container">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">الاسم</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">رقم الهاتف</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>
            @error('phone')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="role">الدور</label>
            <select id="role" name="role" required>
                <option value="">اختر الدور</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>أدمن</option>
                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>موظف</option>
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>عميل</option>
            </select>
            @error('role')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input type="password" id="password" name="password" required minlength="8">
            @error('password')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ
            </button>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection


@extends('layouts.dashboard')

@section('title', 'تعديل الأدمن')
@section('page-title', 'تعديل الأدمن: ' . $user->name)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>تعديل الأدمن</h2>
        <p>تعديل معلومات الأدمن</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.users.admins.show', $user) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.users.admins.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card">
            <div class="card-header">
                <h3>المعلومات الأساسية</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="name">الاسم *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">رقم الهاتف *</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" required>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور (اتركها فارغة إذا لم تريد تغييرها)</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
            <a href="{{ route('admin.users.admins.show', $user) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection


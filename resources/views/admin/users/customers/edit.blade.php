@extends('layouts.dashboard')

@section('title', 'تعديل العميل')
@section('page-title', 'تعديل العميل: ' . $user->name)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>تعديل العميل</h2>
        <p>تعديل معلومات العميل</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.users.customers.show', $user) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.users.customers.update', $user) }}" method="POST">
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
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
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

                <div class="form-group">
                    <label for="date_of_birth">تاريخ الميلاد</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}" class="form-control" max="{{ date('Y-m-d') }}">
                    @error('date_of_birth')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gender">النوع</label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="">اختر النوع</option>
                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                    @error('gender')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
            <a href="{{ route('admin.users.customers.show', $user) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection


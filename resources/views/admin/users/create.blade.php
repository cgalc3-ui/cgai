@extends('layouts.dashboard')

@section('title', __('messages.add_user'))
@section('page-title', __('messages.add_user'))

@section('content')
    <div class="form-container">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('messages.name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">{{ __('messages.email') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">{{ __('messages.phone') }}</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="role">{{ __('messages.role') }}</label>
                <select id="role" name="role" required>
                    <option value="">{{ __('messages.select_role') }}</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('messages.admin_role') }}
                    </option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>{{ __('messages.staff_role') }}
                    </option>
                    <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>
                        {{ __('messages.customer_role') }}</option>
                </select>
                @error('role')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">{{ __('messages.password') }}</label>
                <input type="password" id="password" name="password" required minlength="8">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection
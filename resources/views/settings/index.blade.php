@extends('layouts.dashboard')

@section('title', __('messages.settings'))
@section('page-title', __('messages.settings'))

@section('content')
    <div class="settings-container">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="settings-grid">
            <!-- Profile Settings Card -->
            <div class="card settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-cog"></i> {{ __('messages.personal_information') }}</h3>
                    <p>{{ __('messages.update_basic_info_and_phone') }}</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">{{ __('messages.full_name') }}</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">{{ __('messages.email') }}</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">{{ __('messages.phone') }}</label>
                                <input type="text" name="phone" id="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone) }}" required>
                                @error('phone')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="gender">{{ __('messages.gender') }}</label>
                                <select name="gender" id="gender"
                                    class="form-control @error('gender') is-invalid @enderror">
                                    <option value="">{{ __('messages.select_gender') }}</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}
                                    </option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>
                                        {{ __('messages.female') }}</option>
                                </select>
                                @error('gender')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="date_of_birth">{{ __('messages.date_of_birth') }}</label>
                                <input type="date" name="date_of_birth" id="date_of_birth"
                                    class="form-control @error('date_of_birth') is-invalid @enderror"
                                    value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                                @error('date_of_birth')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change Card -->
            <div class="card settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-lock"></i> {{ __('messages.change_password') }}</h3>
                    <p>{{ __('messages.use_strong_password_to_protect_account') }}</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="current_password">{{ __('messages.current_password') }}</label>
                            <input type="password" name="current_password" id="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('messages.new_password') }}</label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">{{ __('messages.confirm_new_password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-warning">{{ __('messages.change_password') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .settings-container {
                width: 100%;
            }

            .settings-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 24px;
            }

            @media (min-width: 992px) {
                .settings-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }

            .settings-card {
                background: white;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
                overflow: hidden;
            }

            .settings-card .card-header {
                padding: 20px 24px;
                background: #f9fafb;
                border-bottom: 1px solid #e5e7eb;
            }

            .settings-card .card-header h3 {
                margin: 0 0 4px 0;
                font-size: 18px;
                font-weight: 600;
                color: #111827;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .settings-card .card-header h3 i {
                color: #3b82f6;
                font-size: 18px;
            }

            .settings-card .card-header p {
                margin: 0;
                font-size: 13px;
                color: #6b7280;
            }

            .settings-card .card-body {
                padding: 24px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 6px;
                font-size: 14px;
                font-weight: 500;
                color: #374151;
            }

            .form-control {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                font-family: 'Cairo', sans-serif;
                font-size: 14px;
                transition: border-color 0.15s;
                background: white;
                box-sizing: border-box;
            }

            .form-control:focus {
                border-color: #3b82f6;
                outline: none;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .form-control.is-invalid {
                border-color: #ef4444;
            }

            .error-text {
                color: #ef4444;
                font-size: 12px;
                margin-top: 4px;
                display: block;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            @media (max-width: 600px) {
                .form-row {
                    grid-template-columns: 1fr;
                }
            }

            .form-actions {
                margin-top: 24px;
                display: flex;
                justify-content: flex-end;
            }

            .alert {
                padding: 12px 16px;
                border-radius: 6px;
                margin-bottom: 24px;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 14px;
            }

            .alert-success {
                background: #ecfdf5;
                color: #065f46;
                border: 1px solid #a7f3d0;
            }

            .alert-success i {
                color: #10b981;
            }

            .btn {
                padding: 10px 20px;
                font-weight: 500;
                font-size: 14px;
                cursor: pointer;
                transition: background-color 0.15s;
                border-radius: 6px;
                border: none;
            }

            .btn-primary {
                background: #3b82f6;
                color: white;
            }

            .btn-primary:hover {
                background: #2563eb;
            }

            .btn-warning {
                background: #f59e0b;
                color: white;
            }

            .btn-warning:hover {
                background: #d97706;
            }

            /* Dark Mode Styles */
            [data-theme="dark"] .settings-card {
                background: var(--card-bg);
                border-color: var(--border-color);
            }

            [data-theme="dark"] .settings-card .card-header {
                background: var(--sidebar-active-bg);
                border-bottom-color: var(--border-color);
            }

            [data-theme="dark"] .settings-card .card-header h3 {
                color: var(--text-primary);
            }

            [data-theme="dark"] .settings-card .card-header h3 i {
                color: var(--primary-color);
            }

            [data-theme="dark"] .settings-card .card-header p {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .form-group label {
                color: var(--text-primary);
            }

            [data-theme="dark"] .form-control {
                background: var(--sidebar-active-bg);
                border-color: var(--border-color);
                color: var(--text-primary);
            }

            [data-theme="dark"] .form-control:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1);
            }

            [data-theme="dark"] .form-control.is-invalid {
                border-color: var(--danger-color);
            }

            [data-theme="dark"] .error-text {
                color: var(--danger-color);
            }

            [data-theme="dark"] .alert-success {
                background: rgba(16, 185, 129, 0.1);
                color: var(--success-color);
                border-color: var(--success-color);
            }

            [data-theme="dark"] .alert-success i {
                color: var(--success-color);
            }

            [data-theme="dark"] .btn-primary {
                background: var(--primary-color);
            }

            [data-theme="dark"] .btn-primary:hover {
                background: var(--primary-dark);
            }

            [data-theme="dark"] .btn-warning {
                background: var(--warning-color);
            }

            [data-theme="dark"] .btn-warning:hover {
                background: #d97706;
            }

            [data-theme="dark"] select.form-control {
                background: var(--sidebar-active-bg);
                border-color: var(--border-color);
                color: var(--text-primary);
                background-image: none;
                padding-right: 16px;
                padding-left: 16px;
            }

            [data-theme="dark"] select.form-control:focus {
                background: var(--sidebar-active-bg);
                border-color: var(--primary-color);
                color: var(--text-primary);
            }

            [data-theme="dark"] select.form-control option {
                background: var(--sidebar-active-bg);
                color: var(--text-primary);
            }
        </style>
    @endpush
@endsection
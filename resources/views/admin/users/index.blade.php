@extends('layouts.dashboard')

@section('title', __('messages.user_management'))
@section('page-title', __('messages.user_management'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.user_management') }}</h2>
            <p>{{ __('messages.select_user_type') }}</p>
        </div>
    </div>

    <div class="user-types-grid">
        <a href="{{ route('admin.users.admins') }}" class="user-type-card">
            <div class="user-type-icon admin">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="user-type-content">
                <h3>{{ __('messages.admins') }}</h3>
                <p>{{ __('messages.manage_admins_desc') }}</p>
                <div class="user-type-count">
                    <span>{{ \App\Models\User::where('role', 'admin')->count() }}</span>
                    <span>{{ __('messages.admin_role') }}</span>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.staff') }}" class="user-type-card">
            <div class="user-type-icon staff">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="user-type-content">
                <h3>{{ __('messages.staff') }}</h3>
                <p>{{ __('messages.manage_staff_desc') }}</p>
                <div class="user-type-count">
                    <span>{{ \App\Models\User::where('role', 'staff')->count() }}</span>
                    <span>{{ __('messages.staff_role') }}</span>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.customers') }}" class="user-type-card">
            <div class="user-type-icon customer">
                <i class="fas fa-users"></i>
            </div>
            <div class="user-type-content">
                <h3>{{ __('messages.customers') }}</h3>
                <p>{{ __('messages.manage_customers_desc') }}</p>
                <div class="user-type-count">
                    <span>{{ \App\Models\User::where('role', 'customer')->count() }}</span>
                    <span>{{ __('messages.customer_role') }}</span>
                </div>
            </div>
        </a>
    </div>

    @push('styles')
        <style>
            .user-types-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 30px;
                margin-top: 30px;
            }

            .user-type-card {
                background: white;
                border-radius: 16px;
                padding: 30px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                text-decoration: none;
                color: inherit;
                transition: all 0.3s;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .user-type-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            }

            .user-type-icon {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 36px;
                color: white;
                margin-bottom: 20px;
            }

            .user-type-icon.admin {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .user-type-icon.staff {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            }

            .user-type-icon.customer {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            }

            .user-type-content h3 {
                font-size: 24px;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 10px 0;
            }

            .user-type-content p {
                color: #6b7280;
                font-size: 14px;
                margin: 0 0 20px 0;
            }

            .user-type-count {
                display: flex;
                flex-direction: column;
                gap: 5px;
                margin-top: 15px;
            }

            .user-type-count span:first-child {
                font-size: 32px;
                font-weight: 700;
                color: #1f2937;
            }

            .user-type-count span:last-child {
                font-size: 14px;
                color: #6b7280;
            }
        </style>
    @endpush
@endsection
@extends('layouts.dashboard')

@section('title', __('messages.customer_details'))
@section('page-title', __('messages.customer_details'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ $user->name }}</h2>
            <p>{{ __('messages.view_edit_customer_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.users.customers') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.back_to_list') }}
            </a>
            <a href="{{ route('admin.users.customers.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
            </a>
        </div>
    </div>

    <div class="user-profile-container">
        <!-- User Basic Info -->
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.basic_information') }}</h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">{{ __('messages.name') }}:</span>
                        <span class="info-value">{{ $user->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('messages.email') }}:</span>
                        <span class="info-value">{{ $user->email ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('messages.phone') }}:</span>
                        <span class="info-value">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('messages.role') }}:</span>
                        <span class="info-value">
                            <span class="badge badge-info status-pill active">
                                <i class="fas fa-user"></i> {{ __('messages.customer_role') }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('messages.date_of_birth') }}:</span>
                        <span
                            class="info-value">{{ $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('messages.gender') }}:</span>
                        <span class="info-value">
                            @if($user->gender == 'male')
                                {{ __('messages.male') }}
                            @elseif($user->gender == 'female')
                                {{ __('messages.female') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('messages.registration_date') }}:</span>
                        <span class="info-value">{{ $user->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('messages.last_update') }}:</span>
                        <span class="info-value">{{ $user->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">{{ __('messages.phone_verification_status') }}:</span>
                        <span class="info-value">
                            @if($user->phone_verified_at)
                                <span class="badge badge-success status-pill completed">{{ __('messages.verified') }}</span>
                            @else
                                <span class="badge badge-danger status-pill cancelled">{{ __('messages.unverified') }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .user-profile-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .card-body {
            padding: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .info-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            margin-top: 5px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-info {
            background: #3b82f6;
            color: white;
        }

        .badge-success {
            background: #10b981;
            color: white;
        }

        .badge-danger {
            background: #ef4444;
            color: white;
        }

        .badge-warning {
            background: #f59e0b;
            color: white;
        }

        .text-muted {
            color: #6b7280;
            font-style: italic;
        }
    </style>
@endsection
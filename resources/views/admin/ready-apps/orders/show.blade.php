@extends('layouts.dashboard')

@section('title', __('messages.ready_app_order'))
@section('page-title', __('messages.ready_app_order'))

@section('content')
    <div class="order-details-container">
        <!-- Header -->
        <header class="order-header">
            <div class="header-main">
                <h1 class="order-title">{{ __('messages.ready_app_order') }}</h1>
                <p class="order-subtitle">{{ __('messages.created_at') }}: {{ $order->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.ready-apps.orders.index') }}" class="btn-back">
                    <i class="fas fa-arrow-right"></i>
                    <span>{{ __('messages.back_to_list') }}</span>
                </a>
            </div>
        </header>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('messages.status') }}</span>
                    <span class="stat-status status-{{ $order->status }}">
                        @if($order->status === 'pending')
                            {{ __('messages.pending') }}
                        @elseif($order->status === 'processing')
                            {{ __('messages.processing') }}
                        @elseif($order->status === 'completed')
                            {{ __('messages.completed') }}
                        @elseif($order->status === 'cancelled')
                            {{ __('messages.cancelled') }}
                        @else
                            {{ __('messages.rejected') }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('messages.price') }}</span>
                    <span class="stat-value">{{ number_format($order->price, 2) }} {{ $order->currency }}</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-purple">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('messages.contact_preference') }}</span>
                    <span class="stat-value">
                        @if($order->contact_preference === 'phone')
                            <i class="fas fa-phone"></i> {{ __('messages.phone') }}
                        @elseif($order->contact_preference === 'email')
                            <i class="fas fa-envelope"></i> {{ __('messages.email') }}
                        @else
                            <i class="fas fa-comments"></i> {{ __('messages.both') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Main Column -->
            <div class="main-column">
                <!-- Application Details -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-mobile-alt"></i>
                        <span>{{ __('messages.ready_app_name') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="app-info-box">
                            <h3>{{ $order->app->trans('name') }}</h3>
                            <div class="app-meta">
                                @if($order->app->category)
                                    <span>
                                        <i class="far fa-folder"></i> {{ $order->app->category->trans('name') }}
                                    </span>
                                @endif
                                @if($order->app->mainImageUrl)
                                    <div class="app-image-preview" style="margin-top: 15px;">
                                        <img src="{{ asset($order->app->mainImageUrl) }}" alt="{{ $order->app->trans('name') }}" style="max-width: 200px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ __('messages.order_info') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-calendar"></i> {{ __('messages.created_at') }}:
                                </span>
                                <span class="info-value">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                            @if($order->processed_at)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-check-circle"></i> {{ __('messages.processed_at') }}:
                                </span>
                                <span class="info-value">{{ $order->processed_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                            @endif
                            @if($order->processor)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-user-check"></i> {{ __('messages.processed_by') }}:
                                </span>
                                <span class="info-value">{{ $order->processor->name }}</span>
                            </div>
                            @endif
                            @if($order->notes)
                            <div class="info-item full-width">
                                <span class="info-label">
                                    <i class="fas fa-sticky-note"></i> {{ __('messages.notes') }}:
                                </span>
                                <div class="info-value notes-content">{{ $order->notes }}</div>
                            </div>
                            @endif
                            @if($order->admin_notes)
                            <div class="info-item full-width">
                                <span class="info-label">
                                    <i class="fas fa-user-shield"></i> {{ __('messages.admin_notes') }}:
                                </span>
                                <div class="info-value notes-content admin-notes">{{ $order->admin_notes }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ __('messages.customer_data') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-user"></i> {{ __('messages.name') }}:
                                </span>
                                <span class="info-value">{{ $order->user->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-envelope"></i> {{ __('messages.email') }}:
                                </span>
                                <a href="mailto:{{ $order->user->email }}" class="info-value link">{{ $order->user->email }}</a>
                            </div>
                            @if($order->user->phone)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-phone"></i> {{ __('messages.phone') }}:
                                </span>
                                <a href="tel:{{ $order->user->phone }}" class="info-value link">{{ $order->user->phone }}</a>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-comments"></i> {{ __('messages.contact_preference') }}:
                                </span>
                                <span class="info-value">
                                    @if($order->contact_preference === 'phone')
                                        <span class="badge badge-info">
                                            <i class="fas fa-phone"></i> {{ __('messages.phone') }}
                                        </span>
                                    @elseif($order->contact_preference === 'email')
                                        <span class="badge badge-info">
                                            <i class="fas fa-envelope"></i> {{ __('messages.email') }}
                                        </span>
                                    @else
                                        <span class="badge badge-info">
                                            <i class="fas fa-comments"></i> {{ __('messages.both') }}
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar-column">
                <!-- Update Status Card -->
                <div class="action-card">
                    <div class="card-header">
                        <i class="fas fa-edit"></i>
                        <span>{{ __('messages.update_status') }}</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.ready-apps.orders.update-status', $order) }}" method="POST" class="status-form">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="status">
                                    <i class="fas fa-tasks"></i> {{ __('messages.status') }} <span class="required">*</span>
                                </label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>
                                        {{ __('messages.pending') }}
                                    </option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>
                                        {{ __('messages.processing') }}
                                    </option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>
                                        {{ __('messages.completed') }}
                                    </option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                                        {{ __('messages.cancelled') }}
                                    </option>
                                    <option value="rejected" {{ $order->status === 'rejected' ? 'selected' : '' }}>
                                        {{ __('messages.rejected') }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="admin_notes">
                                    <i class="fas fa-sticky-note"></i> {{ __('messages.admin_notes') }}
                                </label>
                                <textarea id="admin_notes" name="admin_notes" class="form-control" rows="5" placeholder="{{ __('messages.admin_notes_placeholder') }}">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                            </div>

                            <button type="submit" class="btn-update">
                                <i class="fas fa-save"></i> {{ __('messages.save') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="action-card">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        <span>{{ __('messages.quick_actions') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="{{ route('admin.ready-apps.apps.show', $order->app) ?? '#' }}" class="action-btn" target="_blank">
                                <i class="fas fa-eye"></i> {{ __('messages.view_app') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .order-details-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
            background: var(--bg-light);
            min-height: calc(100vh - 100px);
        }

        /* Header */
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
        }

        .header-main {
            flex: 1;
        }

        .order-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 8px 0;
        }

        .order-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--sidebar-active-bg);
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid var(--border-color);
        }

        .btn-back:hover {
            background: var(--bg-light);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg, white);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        [data-theme="dark"] .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            flex-shrink: 0;
        }

        .stat-icon.icon-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        [data-theme="dark"] .stat-icon.icon-blue {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .stat-icon.icon-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        [data-theme="dark"] .stat-icon.icon-green {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .stat-icon.icon-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        [data-theme="dark"] .stat-icon.icon-purple {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
        }

        .stat-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stat-status, .stat-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .stat-status.status-pending {
            color: var(--warning-color);
        }

        .stat-status.status-processing {
            color: #3b82f6;
        }

        .stat-status.status-completed {
            color: var(--success-color);
        }

        .stat-status.status-cancelled,
        .stat-status.status-rejected {
            color: var(--danger-color);
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Cards */
        .info-card, .action-card {
            background: var(--card-bg, white);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .info-card:hover, .action-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] .info-card:hover,
        [data-theme="dark"] .action-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .card-header {
            padding: 18px 20px;
            background: var(--sidebar-active-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 16px;
        }

        .card-header i {
            color: var(--primary-color);
        }

        .card-body {
            padding: 20px;
        }

        /* App Info Box */
        .app-info-box h3 {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 15px 0;
        }

        .app-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .app-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .app-image-preview img {
            max-width: 100%;
            height: auto;
            border: 1px solid var(--border-color);
        }

        /* Info List */
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-item.full-width {
            flex-direction: column;
            gap: 8px;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-primary);
            min-width: 180px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label i {
            color: var(--text-secondary);
            width: 16px;
        }

        .info-value {
            color: var(--text-primary);
            flex: 1;
        }

        .info-value.link {
            color: var(--primary-color);
            text-decoration: none;
        }

        .info-value.link:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }

        .notes-content {
            background: var(--sidebar-active-bg);
            padding: 12px;
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
            white-space: pre-wrap;
            line-height: 1.6;
            color: var(--text-primary);
        }

        .notes-content.admin-notes {
            border-left-color: var(--success-color);
            background: rgba(16, 185, 129, 0.1);
        }

        [data-theme="dark"] .notes-content.admin-notes {
            background: rgba(16, 185, 129, 0.15);
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .badge-info {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        [data-theme="dark"] .badge-info {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        /* Form */
        .status-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-group label i {
            color: var(--text-secondary);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            background: var(--sidebar-active-bg);
            color: var(--text-primary);
            font-family: 'Cairo', sans-serif;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(2, 192, 206, 0.1);
        }

        .form-control:hover {
            border-color: var(--text-secondary);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn-update {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-update:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(2, 192, 206, 0.3);
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: var(--sidebar-active-bg);
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid var(--border-color);
        }

        .action-btn:hover {
            background: var(--bg-light);
            border-color: var(--primary-color);
            transform: translateX(4px);
            color: var(--primary-color);
        }

        .action-btn i {
            color: var(--primary-color);
        }

        .required {
            color: #ef4444;
        }

        @media (max-width: 768px) {
            .order-details-container {
                padding: 15px;
            }

            .order-header {
                flex-direction: column;
                gap: 15px;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }

            .info-item {
                flex-direction: column;
                gap: 5px;
            }

            .info-label {
                min-width: auto;
            }
        }
    </style>
    @endpush
@endsection

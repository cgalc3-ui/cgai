@extends('layouts.dashboard')

@section('title', __('messages.ai_service_request'))
@section('page-title', __('messages.ai_service_request'))

@section('content')
    <div class="order-details-container">
        <!-- Header -->
        <header class="order-header">
            <div class="header-main">
                <h1 class="order-title">{{ $request->title }}</h1>
                <p class="order-subtitle">{{ __('messages.created_at') }}: {{ $request->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.ai-services.requests.index') }}" class="btn-back">
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
                    <span class="stat-status status-{{ $request->status }}">
                        @if($request->status === 'pending')
                            {{ __('messages.pending') }}
                        @elseif($request->status === 'reviewing')
                            {{ __('messages.reviewing') }}
                        @elseif($request->status === 'quoted')
                            {{ __('messages.quoted') }}
                        @elseif($request->status === 'approved')
                            {{ __('messages.approved') }}
                        @elseif($request->status === 'in_progress')
                            {{ __('messages.in_progress') }}
                        @elseif($request->status === 'completed')
                            {{ __('messages.completed') }}
                        @elseif($request->status === 'cancelled')
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
                    <span class="stat-label">{{ __('messages.ai_service_request_estimated_price') }}</span>
                    <span class="stat-value">
                        @if($request->estimated_price)
                            {{ number_format($request->estimated_price, 2) }} {{ $request->currency }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-purple">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">{{ __('messages.ai_service_request_urgency') }}</span>
                    <span class="stat-value">
                        @if($request->urgency === 'high')
                            <span style="color: #ef4444;">{{ __('messages.high') }}</span>
                        @elseif($request->urgency === 'medium')
                            <span style="color: #f59e0b;">{{ __('messages.medium') }}</span>
                        @else
                            <span style="color: #10b981;">{{ __('messages.low') }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Main Column -->
            <div class="main-column">
                <!-- Request Details -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-file-alt"></i>
                        <span>{{ __('messages.ai_service_request') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            <div class="info-item full-width">
                                <span class="info-label">
                                    <i class="fas fa-heading"></i> {{ __('messages.ai_service_request_title') }}:
                                </span>
                                <span class="info-value"><strong>{{ $request->title }}</strong></span>
                            </div>
                            <div class="info-item full-width">
                                <span class="info-label">
                                    <i class="fas fa-align-right"></i> {{ __('messages.ai_service_request_description') }}:
                                </span>
                                <div class="info-value notes-content">{{ $request->description }}</div>
                            </div>
                            <div class="info-item full-width">
                                <span class="info-label">
                                    <i class="fas fa-lightbulb"></i> {{ __('messages.ai_service_request_use_case') }}:
                                </span>
                                <div class="info-value notes-content">{{ $request->use_case }}</div>
                            </div>
                            @if($request->expected_features && count($request->expected_features) > 0)
                            <div class="info-item full-width">
                                <span class="info-label">
                                    <i class="fas fa-list-check"></i> {{ __('messages.ai_service_request_expected_features') }}:
                                </span>
                                <div class="info-value">
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        @foreach($request->expected_features as $feature)
                                            <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                                                <i class="fas fa-check-circle" style="color: var(--success-color); margin-inline-end: 8px;"></i>
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Category & Budget -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ __('messages.request_info') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-folder"></i> {{ __('messages.ai_service_category') }}:
                                </span>
                                <span class="info-value">{{ $request->category->trans('name') }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-wallet"></i> {{ __('messages.ai_service_request_budget_range') }}:
                                </span>
                                <span class="info-value">
                                    @if($request->budget_range === 'custom' && $request->custom_budget)
                                        <span style="color: #28a745; font-weight: 600;">{{ number_format($request->custom_budget, 2) }} {{ $request->currency }}</span>
                                    @else
                                        <span class="badge badge-info">{{ __('messages.' . $request->budget_range) }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-exclamation-triangle"></i> {{ __('messages.ai_service_request_urgency') }}:
                                </span>
                                <span class="info-value">
                                    @if($request->urgency === 'high')
                                        <span class="badge badge-danger">{{ __('messages.high') }}</span>
                                    @elseif($request->urgency === 'medium')
                                        <span class="badge badge-warning">{{ __('messages.medium') }}</span>
                                    @else
                                        <span class="badge badge-success">{{ __('messages.low') }}</span>
                                    @endif
                                </span>
                            </div>
                            @if($request->deadline)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-calendar-times"></i> {{ __('messages.ai_service_request_deadline') }}:
                                </span>
                                <span class="info-value">{{ $request->deadline->format('Y-m-d') }}</span>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-comments"></i> {{ __('messages.contact_preference') }}:
                                </span>
                                <span class="info-value">
                                    @if($request->contact_preference === 'phone')
                                        <span class="badge badge-info">
                                            <i class="fas fa-phone"></i> {{ __('messages.phone') }}
                                        </span>
                                    @elseif($request->contact_preference === 'email')
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

                <!-- Attachments -->
                @if($request->attachments && $request->attachments->count() > 0)
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-paperclip"></i>
                        <span>{{ __('messages.attachments') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="attachments-list">
                            @foreach($request->attachments as $attachment)
                                <div class="attachment-item">
                                    <i class="fas fa-file"></i>
                                    <a href="{{ asset($attachment->file_path) }}" target="_blank" class="attachment-link">
                                        {{ $attachment->file_name }}
                                    </a>
                                    <span class="attachment-size">({{ number_format($attachment->file_size / 1024, 2) }} KB)</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Timeline -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-history"></i>
                        <span>{{ __('messages.timeline') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-calendar"></i> {{ __('messages.created_at') }}:
                                </span>
                                <span class="info-value">{{ $request->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                            @if($request->quoted_at)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-dollar-sign"></i> {{ __('messages.quoted_at') }}:
                                </span>
                                <span class="info-value">{{ $request->quoted_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                            @endif
                            @if($request->started_at)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-play-circle"></i> {{ __('messages.started_at') }}:
                                </span>
                                <span class="info-value">{{ $request->started_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                            @endif
                            @if($request->completed_at)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-check-circle"></i> {{ __('messages.completed_at') }}:
                                </span>
                                <span class="info-value">{{ $request->completed_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                            @endif
                            @if($request->processed_at)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-user-check"></i> {{ __('messages.processed_at') }}:
                                </span>
                                <span class="info-value">{{ $request->processed_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                            @endif
                            @if($request->processor)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-user-shield"></i> {{ __('messages.processed_by') }}:
                                </span>
                                <span class="info-value">{{ $request->processor->name }}</span>
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
                                <span class="info-value">{{ $request->user->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-envelope"></i> {{ __('messages.email') }}:
                                </span>
                                <a href="mailto:{{ $request->user->email }}" class="info-value link">{{ $request->user->email }}</a>
                            </div>
                            @if($request->user->phone)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-phone"></i> {{ __('messages.phone') }}:
                                </span>
                                <a href="tel:{{ $request->user->phone }}" class="info-value link">{{ $request->user->phone }}</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($request->admin_notes)
                <!-- Admin Notes -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-user-shield"></i>
                        <span>{{ __('messages.admin_notes') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-value notes-content admin-notes">{{ $request->admin_notes }}</div>
                    </div>
                </div>
                @endif
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
                        <form action="{{ route('admin.ai-services.requests.update-status', $request) }}" method="POST" class="status-form">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="status">
                                    <i class="fas fa-tasks"></i> {{ __('messages.status') }} <span class="required">*</span>
                                </label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>
                                        {{ __('messages.pending') }}
                                    </option>
                                    <option value="reviewing" {{ $request->status === 'reviewing' ? 'selected' : '' }}>
                                        {{ __('messages.reviewing') }}
                                    </option>
                                    <option value="quoted" {{ $request->status === 'quoted' ? 'selected' : '' }}>
                                        {{ __('messages.quoted') }}
                                    </option>
                                    <option value="approved" {{ $request->status === 'approved' ? 'selected' : '' }}>
                                        {{ __('messages.approved') }}
                                    </option>
                                    <option value="in_progress" {{ $request->status === 'in_progress' ? 'selected' : '' }}>
                                        {{ __('messages.in_progress') }}
                                    </option>
                                    <option value="completed" {{ $request->status === 'completed' ? 'selected' : '' }}>
                                        {{ __('messages.completed') }}
                                    </option>
                                    <option value="cancelled" {{ $request->status === 'cancelled' ? 'selected' : '' }}>
                                        {{ __('messages.cancelled') }}
                                    </option>
                                    <option value="rejected" {{ $request->status === 'rejected' ? 'selected' : '' }}>
                                        {{ __('messages.rejected') }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="admin_notes">
                                    <i class="fas fa-sticky-note"></i> {{ __('messages.admin_notes') }}
                                </label>
                                <textarea id="admin_notes" name="admin_notes" class="form-control" rows="5" placeholder="{{ __('messages.admin_notes_placeholder') }}">{{ old('admin_notes', $request->admin_notes) }}</textarea>
                            </div>

                            <button type="submit" class="btn-update">
                                <i class="fas fa-save"></i> {{ __('messages.save') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Update Quote Card -->
                @if($request->status === 'pending' || $request->status === 'reviewing')
                <div class="action-card">
                    <div class="card-header">
                        <i class="fas fa-dollar-sign"></i>
                        <span>{{ __('messages.update_quote') }}</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.ai-services.requests.update-quote', $request) }}" method="POST" class="status-form">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="estimated_price">
                                    <i class="fas fa-wallet"></i> {{ __('messages.ai_service_request_estimated_price') }} <span class="required">*</span>
                                </label>
                                <input type="number" id="estimated_price" name="estimated_price" class="form-control" 
                                    value="{{ old('estimated_price', $request->estimated_price) }}" 
                                    step="0.01" min="0" required>
                            </div>

                            <div class="form-group">
                                <label for="admin_notes_quote">
                                    <i class="fas fa-sticky-note"></i> {{ __('messages.admin_notes') }}
                                </label>
                                <textarea id="admin_notes_quote" name="admin_notes" class="form-control" rows="5" placeholder="{{ __('messages.admin_notes_placeholder') }}">{{ old('admin_notes', $request->admin_notes) }}</textarea>
                            </div>

                            <button type="submit" class="btn-update">
                                <i class="fas fa-save"></i> {{ __('messages.save_quote') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="action-card">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        <span>{{ __('messages.quick_actions') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="mailto:{{ $request->user->email }}?subject={{ __('messages.ai_service_request') }} #{{ $request->id }}" class="action-btn">
                                <i class="fas fa-envelope"></i> {{ __('messages.send_email') }}
                            </a>
                            @if($request->user->phone)
                            <a href="tel:{{ $request->user->phone }}" class="action-btn">
                                <i class="fas fa-phone"></i> {{ __('messages.call') }}
                            </a>
                            @endif
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

        .stat-status.status-reviewing,
        .stat-status.status-processing {
            color: #3b82f6;
        }

        .stat-status.status-quoted {
            color: #8b5cf6;
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

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        [data-theme="dark"] .badge-info {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        [data-theme="dark"] .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        [data-theme="dark"] .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        [data-theme="dark"] .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        /* Attachments */
        .attachments-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: var(--sidebar-active-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .attachment-item i {
            color: var(--primary-color);
        }

        .attachment-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            flex: 1;
        }

        .attachment-link:hover {
            text-decoration: underline;
        }

        .attachment-size {
            color: var(--text-secondary);
            font-size: 12px;
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


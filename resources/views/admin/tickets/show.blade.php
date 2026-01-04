@extends('layouts.dashboard')

@section('title', __('messages.ticket_details'))
@section('page-title', __('messages.ticket_details'))

@section('content')
    <div class="ticket-details-container">
        <!-- Header -->
        <div class="ticket-details-header">
            <div class="header-left">
                <h2>{{ $ticket->trans('subject') }}</h2>
                <div class="ticket-meta-info">
                    <span class="ticket-date">
                        <i class="fas fa-calendar"></i> {{ $ticket->created_at->format('Y-m-d H:i') }}
                    </span>
                </div>
            </div>
            <div class="header-right">
                <a href="{{ route('admin.tickets') }}" class="btn-back">
                    <i class="fas fa-arrow-right"></i>
                    <span>{{ __('messages.back') }}</span>
                </a>
            </div>
        </div>

        <!-- Ticket Info -->
        <div class="ticket-info-section">
            <div class="info-card">
                <div class="info-item">
                    <label>{{ __('messages.client') }}:</label>
                    <span>{{ $ticket->user->name }}</span>
                </div>
                <div class="info-item">
                    <label>{{ __('messages.email') }}:</label>
                    <span>{{ $ticket->user->email }}</span>
                </div>
                <div class="info-item">
                    <label>{{ __('messages.phone') }}:</label>
                    <span>{{ $ticket->user->phone ?? __('messages.not_specified') }}</span>
                </div>
            </div>

            <div class="admin-actions-card">
                <h3>{{ __('messages.manage_ticket') }}</h3>
                <form action="{{ route('admin.tickets.update-status', $ticket) }}" method="POST" class="status-form">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>{{ __('messages.status') }}:</label>
                        <select name="status" class="form-select">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>{{ __('messages.open') }}
                            </option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>
                                {{ __('messages.in_progress') }}</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>
                                {{ __('messages.resolved') }}</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>
                                {{ __('messages.closed') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.priority') }}:</label>
                        <select name="priority" class="form-select" disabled>
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>{{ __('messages.low') }}
                            </option>
                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>
                                {{ __('messages.medium') }}</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>
                                {{ __('messages.high') }}</option>
                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>
                                {{ __('messages.urgent') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-update">{{ __('messages.update_status') }}</button>
                </form>
            </div>
        </div>

        <!-- Initial Description -->
        <div class="description-section">
            <h3>{{ __('messages.initial_description') }}</h3>
            <div class="description-content">
                <p>{{ $ticket->trans('description') }}</p>
            </div>
        </div>

        <!-- Messages -->
        <div class="messages-section">
            <h3>{{ __('messages.conversation') }}</h3>
            <div class="messages-list">
                @foreach($ticket->messages as $message)
                    <div class="message-item {{ $message->user_id === auth()->id() ? 'own-message' : '' }}">
                        <div class="message-header">
                            <div class="message-user">
                                <strong>{{ $message->user->name }}</strong>
                                @if($message->is_internal)
                                    <span class="badge-internal">{{ __('messages.internal_note') }}</span>
                                @endif
                            </div>
                            <span class="message-time">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="message-body">
                            <p>{{ $message->message }}</p>
                            @if($message->attachments->count() > 0)
                                <div class="message-attachments">
                                    @foreach($message->attachments as $attachment)
                                        <div class="attachment-item">
                                            @if($attachment->isImage())
                                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank">
                                                    <img src="{{ Storage::url($attachment->file_path) }}" alt="{{ $attachment->file_name }}"
                                                        class="attachment-image">
                                                </a>
                                            @else
                                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                                    class="attachment-link">
                                                    <i class="fas fa-file"></i>
                                                    {{ $attachment->file_name }}
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Reply Form -->
        <div class="reply-section">
            <h3>{{ __('messages.add_reply') }}</h3>
            <form action="{{ route('tickets.add-message', $ticket) }}" method="POST" enctype="multipart/form-data"
                class="reply-form">
                @csrf
                <div class="form-group">
                    <textarea name="message" class="form-textarea" rows="4"
                        placeholder="{{ __('messages.reply_placeholder') }}" required></textarea>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_internal" value="1">
                        <span>{{ __('messages.internal_note_desc') }}</span>
                    </label>
                </div>
                <div class="form-group">
                    <label for="attachments">{{ __('messages.attach_images') }} ({{ __('messages.optional') }})</label>
                    <input type="file" name="attachments[]" id="attachments" class="form-input" multiple accept="image/*">
                    <small class="form-help">{{ __('messages.attachment_help') }}</small>
                </div>
                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i>
                    {{ __('messages.send') }}
                </button>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .ticket-details-container {
                background: white;
                border-radius: 16px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                padding: 32px;
                max-width: 1200px;
                margin: 0 auto;
            }

            .ticket-details-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 32px;
                padding-bottom: 20px;
                border-bottom: 2px solid #e5e7eb;
            }

            .ticket-details-header h2 {
                font-size: 28px;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 12px 0;
                line-height: 1.3;
            }

            .ticket-meta-info {
                display: flex;
                gap: 20px;
                font-size: 14px;
                color: #6b7280;
                flex-wrap: wrap;
            }

            .ticket-meta-info span {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .ticket-meta-info i {
                color: #9ca3af;
            }

            .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 16px;
                background: #f3f4f6;
                color: #4b5563;
                border-radius: 8px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                transition: all 0.2s;
                border: 1px solid #e5e7eb;
            }

            .btn-back:hover {
                background: #e5e7eb;
                color: #1f2937;
                border-color: #d1d5db;
            }

            .btn-back i {
                font-size: 12px;
            }

            .ticket-info-section {
                display: grid;
                grid-template-columns: 1fr 320px;
                gap: 24px;
                margin-bottom: 32px;
            }

            .info-card {
                background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
                padding: 20px;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .info-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .info-item:last-child {
                border-bottom: none;
            }

            .info-item label {
                font-weight: 600;
                color: #4b5563;
                font-size: 14px;
            }

            .info-item span:not(.badge) {
                color: #1f2937;
                font-weight: 500;
            }

            .admin-actions-card {
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                padding: 20px;
                border-radius: 12px;
                border: 1px solid #bfdbfe;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .admin-actions-card h3 {
                font-size: 18px;
                font-weight: 700;
                margin: 0 0 16px 0;
                color: #1e40af;
            }

            .status-form {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .status-form .form-group {
                margin: 0;
            }

            .status-form label {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
            }

            .form-select {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 14px;
                background: white;
                transition: all 0.2s;
            }

            .form-select:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .btn-update {
                padding: 10px 20px;
                background: #3b82f6;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-update:hover {
                background: #2563eb;
                transform: translateY(-1px);
                box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
            }

            .description-section {
                background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
                padding: 24px;
                border-radius: 12px;
                margin-bottom: 32px;
                border: 1px solid #e5e7eb;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .description-section h3 {
                font-size: 20px;
                font-weight: 700;
                margin: 0 0 16px 0;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .description-section h3::before {
                content: '';
                width: 4px;
                height: 20px;
                background: #3b82f6;
                border-radius: 2px;
            }

            .description-content p {
                color: #374151;
                line-height: 1.8;
                margin: 0;
                font-size: 15px;
                white-space: pre-wrap;
            }

            .messages-section {
                margin-bottom: 32px;
            }

            .messages-section h3 {
                font-size: 22px;
                font-weight: 700;
                margin: 0 0 24px 0;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .messages-section h3::before {
                content: '';
                width: 4px;
                height: 24px;
                background: #3b82f6;
                border-radius: 2px;
            }

            .messages-list {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .message-item {
                background: #ffffff;
                padding: 20px;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                transition: all 0.2s;
            }

            .message-item:hover {
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .message-item.own-message {
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                border-color: #93c5fd;
            }

            .message-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
                padding-bottom: 12px;
                border-bottom: 1px solid #e5e7eb;
            }

            .message-user {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .message-user strong {
                color: #1f2937;
                font-size: 15px;
            }

            .badge-internal {
                padding: 4px 10px;
                background: #fef3c7;
                color: #92400e;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 600;
            }

            .message-time {
                font-size: 12px;
                color: #9ca3af;
                font-weight: 500;
            }

            .message-body {
                padding-top: 8px;
            }

            .message-body p {
                margin: 0 0 12px 0;
                color: #374151;
                line-height: 1.8;
                font-size: 15px;
                white-space: pre-wrap;
            }

            .message-attachments {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 12px;
            }

            .attachment-item {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                overflow: hidden;
                background: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                transition: all 0.2s;
            }

            .attachment-item:hover {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
                transform: translateY(-2px);
            }

            .attachment-item a {
                display: block;
                text-decoration: none;
            }

            .attachment-image {
                max-width: 300px;
                max-height: 300px;
                width: auto;
                height: auto;
                display: block;
                object-fit: contain;
                cursor: pointer;
                border-radius: 8px;
            }

            .attachment-link {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 16px;
                color: #3b82f6;
                text-decoration: none;
                font-size: 14px;
            }

            .attachment-link:hover {
                background: #f3f4f6;
            }

            .attachment-link i {
                font-size: 16px;
            }

            .reply-section {
                background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
                padding: 24px;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .reply-section h3 {
                font-size: 20px;
                font-weight: 700;
                margin: 0 0 20px 0;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .reply-section h3::before {
                content: '';
                width: 4px;
                height: 20px;
                background: #3b82f6;
                border-radius: 2px;
            }

            .reply-form {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .reply-form .form-group {
                margin: 0;
            }

            .form-textarea {
                width: 100%;
                padding: 14px;
                border: 2px solid #d1d5db;
                border-radius: 10px;
                font-size: 15px;
                font-family: inherit;
                transition: all 0.2s;
                resize: vertical;
                min-height: 120px;
            }

            .form-textarea:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .form-input {
                width: 100%;
                padding: 10px 12px;
                border: 2px solid #d1d5db;
                border-radius: 8px;
                font-size: 14px;
                transition: all 0.2s;
            }

            .form-input:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .form-help {
                display: block;
                font-size: 12px;
                color: #6b7280;
                margin-top: 6px;
            }

            .checkbox-label {
                display: flex;
                align-items: center;
                gap: 10px;
                cursor: pointer;
                padding: 8px;
                border-radius: 6px;
                transition: background 0.2s;
            }

            .checkbox-label:hover {
                background: #f3f4f6;
            }

            .checkbox-label input[type="checkbox"] {
                width: 18px;
                height: 18px;
                cursor: pointer;
            }

            .btn-send {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 14px 28px;
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                color: white;
                border: none;
                border-radius: 10px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                align-self: flex-start;
                transition: all 0.2s;
                box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
            }

            .btn-send:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
            }

            .btn-send:active {
                transform: translateY(0);
            }

            @media (max-width: 768px) {
                .ticket-details-container {
                    padding: 20px;
                }

                .ticket-details-header {
                    flex-direction: column;
                    gap: 16px;
                }

                .ticket-info-section {
                    grid-template-columns: 1fr;
                }

                .ticket-details-header h2 {
                    font-size: 22px;
                }

                .attachment-image {
                    max-width: 100%;
                }
            }

            /* Dark Mode Styles */
            [data-theme="dark"] .ticket-details-container {
                background: var(--card-bg) !important;
                border: 1px solid var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .ticket-details-header {
                border-bottom-color: var(--border-color) !important;
            }

            [data-theme="dark"] .ticket-details-header h2 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .ticket-meta-info {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .ticket-meta-info i {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .btn-back {
                background: var(--sidebar-active-bg) !important;
                color: var(--text-secondary) !important;
                border-color: var(--border-color) !important;
            }

            [data-theme="dark"] .btn-back:hover {
                background: var(--bg-light) !important;
                color: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
            }

            [data-theme="dark"] .info-card {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .info-item {
                border-bottom-color: var(--border-color) !important;
            }

            [data-theme="dark"] .info-item label {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .info-item span:not(.badge) {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .admin-actions-card {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .admin-actions-card h3 {
                color: var(--primary-color) !important;
            }

            [data-theme="dark"] .status-form label {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .form-select {
                background: var(--card-bg) !important;
                border-color: var(--border-color) !important;
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .form-select:focus {
                border-color: var(--primary-color) !important;
                box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.2) !important;
            }

            [data-theme="dark"] .btn-update {
                background: var(--primary-color) !important;
            }

            [data-theme="dark"] .btn-update:hover {
                background: var(--primary-dark) !important;
            }

            [data-theme="dark"] .description-section {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .description-section h3 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .description-section h3::before {
                background: var(--primary-color) !important;
            }

            [data-theme="dark"] .description-content p {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .messages-section h3 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .messages-section h3::before {
                background: var(--primary-color) !important;
            }

            [data-theme="dark"] .message-item {
                background: var(--card-bg) !important;
                border-color: var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .message-item:hover {
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2) !important;
            }

            [data-theme="dark"] .message-item.own-message {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--primary-color) !important;
            }

            [data-theme="dark"] .message-header {
                border-bottom-color: var(--border-color) !important;
            }

            [data-theme="dark"] .message-user strong {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .badge-internal {
                background: rgba(245, 158, 11, 0.2) !important;
                color: #f59e0b !important;
            }

            [data-theme="dark"] .message-time {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .message-body p {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .attachment-item {
                background: var(--card-bg) !important;
                border-color: var(--border-color) !important;
            }

            [data-theme="dark"] .attachment-link {
                color: var(--primary-color) !important;
            }

            [data-theme="dark"] .attachment-link:hover {
                background: var(--sidebar-active-bg) !important;
            }

            [data-theme="dark"] .reply-section {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .reply-section h3 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .reply-section h3::before {
                background: var(--primary-color) !important;
            }

            [data-theme="dark"] .form-textarea,
            [data-theme="dark"] .form-input {
                background: var(--card-bg) !important;
                border-color: var(--border-color) !important;
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .form-textarea:focus,
            [data-theme="dark"] .form-input:focus {
                border-color: var(--primary-color) !important;
                box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.2) !important;
            }

            [data-theme="dark"] .form-help {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .checkbox-label {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .checkbox-label:hover {
                background: var(--bg-light) !important;
            }

            [data-theme="dark"] .checkbox-label input[type="checkbox"] {
                accent-color: var(--primary-color);
            }

            [data-theme="dark"] .btn-send {
                background: var(--primary-color) !important;
            }

            [data-theme="dark"] .btn-send:hover {
                background: var(--primary-dark) !important;
            }
        </style>
    @endpush
@endsection
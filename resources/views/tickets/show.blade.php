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
                    @if($ticket->assignedUser)
                        <span class="ticket-assigned">
                            <i class="fas fa-user"></i> {{ __('messages.assigned_to_label') }}: {{ $ticket->assignedUser->name }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="header-right">
                <a href="{{ route('tickets.index') }}" class="btn-back">
                    <i class="fas fa-arrow-right"></i>
                    <span>{{ __('messages.back') }}</span>
                </a>
            </div>
        </div>

        <!-- Ticket Description -->
        <div class="ticket-description-section">
            <h3>{{ __('messages.ticket_description') }}</h3>
            <p class="ticket-description-text">{{ $ticket->trans('description') }}</p>
            
            @if($ticket->attachments->whereNull('message_id')->count() > 0)
                <div class="ticket-attachments">
                    <h4>{{ __('messages.attachments_label') }}:</h4>
                    <div class="attachments-grid">
                        @foreach($ticket->attachments->whereNull('message_id') as $attachment)
                            <div class="attachment-item">
                                @if($attachment->isImage())
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" title="{{ __('messages.click_to_view_full_image') }}">
                                        <img src="{{ Storage::url($attachment->file_path) }}" 
                                             alt="{{ $attachment->file_name }}" 
                                             class="attachment-image"
                                             loading="lazy">
                                    </a>
                                @else
                                    <a href="{{ Storage::url($attachment->file_path) }}" 
                                       target="_blank" class="attachment-link"
                                       download="{{ $attachment->file_name }}">
                                        <i class="fas fa-file"></i>
                                        <span>{{ $attachment->file_name }}</span>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Ticket Info -->
        <div class="ticket-info-section">
            <div class="info-card">
                <div class="info-item">
                    <label>{{ __('messages.status') }}:</label>
                    @if($ticket->status === 'open')
                        <span class="badge badge-warning">{{ __('messages.open') }}</span>
                    @elseif($ticket->status === 'in_progress')
                        <span class="badge badge-info">{{ __('messages.in_progress') }}</span>
                    @elseif($ticket->status === 'resolved')
                        <span class="badge badge-success">{{ __('messages.resolved') }}</span>
                    @else
                        <span class="badge badge-secondary">{{ __('messages.closed') }}</span>
                    @endif
                </div>
                <div class="info-item">
                    <label>{{ __('messages.priority') }}:</label>
                    @if($ticket->priority === 'urgent')
                        <span class="badge badge-danger">{{ __('messages.urgent') }}</span>
                    @elseif($ticket->priority === 'high')
                        <span class="badge badge-warning">{{ __('messages.high') }}</span>
                    @elseif($ticket->priority === 'medium')
                        <span class="badge badge-info">{{ __('messages.medium') }}</span>
                    @else
                        <span class="badge badge-secondary">{{ __('messages.low') }}</span>
                    @endif
                </div>
                <div class="info-item">
                    <label>{{ __('messages.client') }}:</label>
                    <span>{{ $ticket->user->name }}</span>
                </div>
            </div>

            @if(auth()->user()->isAdminOrStaff())
                <div class="admin-actions-card">
                    <h3>{{ __('messages.administrative_actions') }}</h3>
                    <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="status-form">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>{{ __('messages.update_status') }}:</label>
                            <select name="status" class="form-select">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>{{ __('messages.open') }}</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>{{ __('messages.in_progress') }}</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>{{ __('messages.resolved') }}</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>{{ __('messages.closed') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-update">{{ __('messages.update_status') }}</button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Messages -->
        <div class="messages-section">
            <h3>{{ __('messages.conversation') }}</h3>
            <div class="messages-list">
                @foreach($ticket->messages as $message)
                    @if(!$message->is_internal || auth()->user()->isAdminOrStaff())
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
                                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" title="{{ __('messages.click_to_view_full_image') }}">
                                                        <img src="{{ Storage::url($attachment->file_path) }}" 
                                                             alt="{{ $attachment->file_name }}" 
                                                             class="attachment-image"
                                                             loading="lazy">
                                                    </a>
                                                @else
                                                    <a href="{{ Storage::url($attachment->file_path) }}" 
                                                       target="_blank" class="attachment-link"
                                                       download="{{ $attachment->file_name }}">
                                                        <i class="fas fa-file"></i>
                                                        <span>{{ $attachment->file_name }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Reply Form -->
        <div class="reply-section">
            <h3>{{ __('messages.add_reply') }}</h3>
            <form action="{{ route('tickets.add-message', $ticket) }}" method="POST" enctype="multipart/form-data" class="reply-form">
                @csrf
                <div class="form-group">
                    <textarea name="message" class="form-textarea" rows="4" 
                              placeholder="{{ __('messages.reply_placeholder') }}" required></textarea>
                </div>
                @if(auth()->user()->isAdminOrStaff())
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_internal" value="1">
                            <span>{{ __('messages.internal_note_desc') }}</span>
                        </label>
                    </div>
                @endif
                <div class="form-group">
                    <label for="attachments">{{ __('messages.attach_images') }} ({{ __('messages.optional') }})</label>
                    <input type="file" name="attachments[]" id="attachments" 
                           class="form-input" multiple accept="image/*">
                    <small class="form-help">{{ __('messages.attachments_help_text') }}</small>
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

            .ticket-description-section {
                background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
                padding: 24px;
                border-radius: 12px;
                margin-bottom: 32px;
                border: 1px solid #e5e7eb;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .ticket-description-section h3 {
                font-size: 20px;
                font-weight: 700;
                margin: 0 0 16px 0;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .ticket-description-section h3::before {
                content: '';
                width: 4px;
                height: 20px;
                background: #3b82f6;
                border-radius: 2px;
            }

            .ticket-description-text {
                color: #374151;
                line-height: 1.8;
                margin: 0 0 20px 0;
                white-space: pre-wrap;
                font-size: 15px;
            }

            .ticket-attachments {
                margin-top: 16px;
                padding-top: 16px;
                border-top: 1px solid #e5e7eb;
            }

            .ticket-attachments h4 {
                font-size: 14px;
                font-weight: 600;
                margin: 0 0 12px 0;
                color: #374151;
            }

            .attachments-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
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

            /* Badge Styles */
            .badge {
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .badge-warning {
                background: #fef3c7;
                color: #92400e;
            }

            .badge-info {
                background: #dbeafe;
                color: #1e40af;
            }

            .badge-success {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-secondary {
                background: #e5e7eb;
                color: #374151;
            }

            .badge-danger {
                background: #fee2e2;
                color: #991b1b;
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
        </style>
    @endpush
@endsection


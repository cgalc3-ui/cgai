@extends('layouts.dashboard')

@section('title', __('messages.help_guide_customer_title'))
@section('page-title', __('messages.help_guide_customer_title'))

@section('content')
    <div class="help-guide-container">
        <div class="page-header">
            <div class="page-header-left">
                <h2>{{ __('messages.help_guide_customer_title') }}</h2>
                <p>{{ __('messages.help_guide_desc') }}</p>
            </div>
        </div>

        <div class="guide-sections">
            <!-- كيفية الحجز -->
            <div class="guide-section">
                <h3><i class="fas fa-calendar-plus"></i> {{ __('messages.help_guide_how_to_book') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_booking_steps') }}</p>
                    <ol>
                        <li>{{ __('messages.help_guide_choose_service') }}</li>
                        <li>{{ __('messages.help_guide_choose_employee') }}</li>
                        <li>{{ __('messages.help_guide_choose_date_time') }}</li>
                        <li>{{ __('messages.help_guide_review_booking') }}</li>
                        <li>{{ __('messages.help_guide_complete_payment') }}</li>
                        <li>{{ __('messages.help_guide_receive_confirmation') }}</li>
                    </ol>
                </div>
            </div>

            <!-- متابعة الحجوزات -->
            <div class="guide-section">
                <h3><i class="fas fa-list"></i> {{ __('messages.help_guide_track_bookings') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_track_bookings_desc') }}</p>
                    <ul>
                        <li>{{ __('messages.help_guide_view_all_my_bookings') }}</li>
                        <li>{{ __('messages.help_guide_track_each_booking') }} ({{ __('messages.pending') }}، {{ __('messages.confirmed') }}، {{ __('messages.completed') }})</li>
                        <li>{{ __('messages.help_guide_track_payment_status') }}</li>
                        <li>{{ __('messages.help_guide_view_full_details') }}</li>
                        <li>{{ __('messages.help_guide_cancel_booking') }}</li>
                    </ul>
                </div>
            </div>

            <!-- الدعم الفني -->
            <div class="guide-section">
                <h3><i class="fas fa-headset"></i> {{ __('messages.help_guide_technical_support') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_technical_support_desc') }}</p>
                    <ul>
                        <li>{{ __('messages.help_guide_create_ticket') }}</li>
                        <li>{{ __('messages.help_guide_track_open_tickets') }}</li>
                        <li>{{ __('messages.help_guide_reply_to_tickets') }}</li>
                        <li>{{ __('messages.help_guide_track_ticket_status') }}</li>
                        <li>{{ __('messages.help_guide_attach_images') }}</li>
                    </ul>
                </div>
            </div>

            <!-- الأسئلة الشائعة -->
            <div class="guide-section">
                <h3><i class="fas fa-question-circle"></i> {{ __('messages.faqs') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_faqs_desc') }}</p>
                    <ul>
                        <li>{{ __('messages.help_guide_browse_faqs') }}</li>
                        <li>{{ __('messages.help_guide_search_faq') }}</li>
                        <li>{{ __('messages.help_guide_read_detailed_answers') }}</li>
                    </ul>
                </div>
            </div>

            <!-- الإشعارات -->
            <div class="guide-section">
                <h3><i class="fas fa-bell"></i> {{ __('messages.notifications') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_notifications_desc') }}</p>
                    <ul>
                        <li>{{ __('messages.help_guide_view_all_notifications') }}</li>
                        <li>{{ __('messages.help_guide_track_unread_notifications') }}</li>
                        <li>{{ __('messages.help_guide_mark_notifications_read') }}</li>
                        <li>{{ __('messages.help_guide_track_booking_updates') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .help-guide-container {
                background: white;
                border-radius: 12px;
                box-shadow: var(--card-shadow);
                padding: 24px;
            }

            .guide-sections {
                display: flex;
                flex-direction: column;
                gap: 20px;
                margin-top: 24px;
            }

            .guide-section {
                background: #f9fafb;
                border-radius: 12px;
                padding: 20px;
                border: 2px solid #e5e7eb;
                transition: all 0.3s;
            }

            .guide-section:hover {
                border-color: var(--primary-color);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .guide-section h3 {
                font-size: 20px;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 16px 0;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .guide-section h3 i {
                color: var(--primary-color);
                font-size: 24px;
            }

            .guide-content {
                color: #4b5563;
                line-height: 1.8;
                font-size: 15px;
            }

            .guide-content p {
                margin: 0 0 12px 0;
                font-weight: 500;
            }

            .guide-content ul,
            .guide-content ol {
                margin: 12px 0;
                padding-right: 20px;
            }

            .guide-content li {
                margin: 8px 0;
            }

            .guide-content strong {
                color: #1f2937;
                font-weight: 600;
            }

            /* Dark Mode Styles */
            [data-theme="dark"] .help-guide-container {
                background: var(--card-bg) !important;
                border: 1px solid var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .help-guide-container .page-header {
                background: var(--card-bg) !important;
                border: 1px solid var(--border-color) !important;
                border-bottom: 1px solid var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .help-guide-container .page-header-left h2 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .help-guide-container .page-header-left p {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .guide-sections {
                color: var(--text-primary);
            }

            [data-theme="dark"] .guide-section {
                background: var(--sidebar-active-bg) !important;
                border-color: var(--border-color) !important;
            }

            [data-theme="dark"] .guide-section:hover {
                border-color: var(--primary-color) !important;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
            }

            [data-theme="dark"] .guide-section h3 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .guide-section h3 i {
                color: var(--primary-color) !important;
            }

            [data-theme="dark"] .guide-content {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .guide-content p,
            [data-theme="dark"] .guide-content ul,
            [data-theme="dark"] .guide-content ol,
            [data-theme="dark"] .guide-content li {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .guide-content strong {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .guide-content * {
                color: var(--text-primary) !important;
            }
        </style>
    @endpush
@endsection


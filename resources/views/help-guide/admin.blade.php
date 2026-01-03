@extends('layouts.dashboard')

@section('title', __('messages.help_guide_admin_title'))
@section('page-title', __('messages.help_guide_admin_title'))

@section('content')
    <div class="help-guide-container">
        <div class="page-header">
            <div class="page-header-left">
                <h2>{{ __('messages.help_guide_admin_title') }}</h2>
                <p>{{ __('messages.help_guide_desc') }}</p>
            </div>
        </div>

        <div class="guide-sections">
            <!-- إدارة المستخدمين -->
            <div class="guide-section">
                <h3><i class="fas fa-users"></i> {{ __('messages.help_guide_manage_users') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_manage_users_desc') }}</p>
                    <ul>
                        <li><strong>{{ __('messages.admins') }}:</strong> {{ __('messages.help_guide_admins_desc') }}</li>
                        <li><strong>{{ __('messages.staff') }}:</strong> {{ __('messages.help_guide_staff_desc') }}</li>
                        <li><strong>{{ __('messages.customers') }}:</strong> {{ __('messages.help_guide_customers_desc') }}</li>
                    </ul>
                </div>
            </div>

            <!-- إدارة الحجوزات -->
            <div class="guide-section">
                <h3><i class="fas fa-calendar-check"></i> {{ __('messages.help_guide_manage_bookings') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_manage_bookings_desc') }}</p>
                    <ul>
                        <li>{{ __('messages.help_guide_view_all_bookings') }}</li>
                        <li>{{ __('messages.help_guide_update_booking_status') }} ({{ __('messages.confirmed') }}، {{ __('messages.completed') }}، {{ __('messages.cancelled') }})</li>
                        <li>{{ __('messages.help_guide_update_payment_status') }}</li>
                        <li>{{ __('messages.help_guide_print_invoices') }}</li>
                    </ul>
                </div>
            </div>

            <!-- إدارة الخدمات -->
            <div class="guide-section">
                <h3><i class="fas fa-concierge-bell"></i> {{ __('messages.help_guide_manage_services') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_manage_services_desc') }}</p>
                    <ul>
                        <li>{{ __('messages.help_guide_create_categories') }}</li>
                        <li>{{ __('messages.help_guide_add_services') }}</li>
                        <li>{{ __('messages.help_guide_manage_consultations') }}</li>
                    </ul>
                </div>
            </div>

            <!-- إدارة الاشتراكات -->
            <div class="guide-section">
                <h3><i class="fas fa-crown"></i> {{ __('messages.help_guide_manage_subscriptions') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_manage_subscriptions_desc') }}</p>
                    <ul>
                        <li>{{ __('messages.help_guide_create_packages') }}</li>
                        <li>{{ __('messages.help_guide_review_requests') }}</li>
                        <li>{{ __('messages.help_guide_track_active_subscriptions') }}</li>
                    </ul>
                </div>
            </div>

            <!-- إدارة التذاكر -->
            <div class="guide-section">
                <h3><i class="fas fa-headset"></i> {{ __('messages.support_tickets') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_manage_tickets_desc') }}</p>
                    <ul>
                        <li>{{ __('messages.help_guide_view_all_tickets') }}</li>
                        <li>{{ __('messages.help_guide_reply_tickets') }}</li>
                        <li>{{ __('messages.help_guide_update_ticket_status') }}</li>
                        <li>{{ __('messages.help_guide_add_internal_notes') }}</li>
                    </ul>
                </div>
            </div>

            <!-- إدارة الأسئلة الشائعة -->
            <div class="guide-section">
                <h3><i class="fas fa-question-circle"></i> {{ __('messages.faqs_management') }}</h3>
                <div class="guide-content">
                    <p>{{ __('messages.help_guide_manage_faqs_desc') }}</p>
                    <ul>
                        <li>{{ __('messages.help_guide_add_faqs') }}</li>
                        <li>{{ __('messages.help_guide_edit_delete_faqs') }}</li>
                        <li>{{ __('messages.help_guide_organize_faqs') }}</li>
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

            .guide-content ul {
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
        </style>
    @endpush
@endsection


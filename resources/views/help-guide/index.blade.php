@extends('layouts.dashboard')

@section('title', __('messages.help_and_guide'))
@section('page-title', __('messages.help_and_guide'))

@section('content')
    <div class="help-guide-container">
        <div class="page-header">
            <div class="page-header-left">
                <h2>
                    @if($role === 'admin')
                        {{ __('messages.help_guide_admin_title') }}
                    @elseif($role === 'staff')
                        {{ __('messages.help_guide_staff_title') }}
                    @else
                        {{ __('messages.help_guide_customer_title') }}
                    @endif
                </h2>
                <p>{{ __('messages.help_guide_desc') }}</p>
            </div>
        </div>

        <div class="guide-sections">
            @forelse($helpGuides as $helpGuide)
                <div class="guide-section">
                    <h3>
                        @if($helpGuide->icon)
                            <i class="{{ $helpGuide->icon }}"></i>
                        @else
                            <i class="fas fa-info-circle"></i>
                        @endif
                        {{ $helpGuide->trans('title') }}
                    </h3>
                    <div class="guide-content">
                        {!! nl2br(e($helpGuide->trans('content'))) !!}
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-book"></i>
                    <h3>{{ __('messages.no_help_guides_available') }}</h3>
                    <p>{{ __('messages.help_guides_will_appear_here') }}</p>
                </div>
            @endforelse
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

            .empty-state {
                text-align: center;
                padding: 60px 20px;
            }

            .empty-state i {
                font-size: 64px;
                color: #d1d5db;
                margin-bottom: 16px;
            }

            .empty-state h3 {
                font-size: 20px;
                font-weight: 700;
                color: #4b5563;
                margin: 0 0 8px 0;
            }

            .empty-state p {
                color: #9ca3af;
                font-size: 14px;
                margin: 0;
            }

            /* Dark Mode Styles */
            [data-theme="dark"] .help-guide-container {
                background: var(--card-bg) !important;
                border: 1px solid var(--border-color) !important;
                box-shadow: none !important;
            }

            [data-theme="dark"] .help-guide-container .page-header {
                background: transparent !important;
                border-bottom: 1px solid var(--border-color) !important;
            }

            [data-theme="dark"] .help-guide-container .page-header-left h2 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .help-guide-container .page-header-left p {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .help-guide-container .total-count {
                color: var(--text-primary) !important;
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

            [data-theme="dark"] .guide-content p {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .guide-content * {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .empty-state {
                background: transparent !important;
            }

            [data-theme="dark"] .empty-state i {
                color: var(--text-secondary) !important;
            }

            [data-theme="dark"] .empty-state h3 {
                color: var(--text-primary) !important;
            }

            [data-theme="dark"] .empty-state p {
                color: var(--text-secondary) !important;
            }
        </style>
    @endpush
@endsection


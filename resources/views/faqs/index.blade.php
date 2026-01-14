@extends('layouts.dashboard')

@section('title', __('messages.faqs'))
@section('page-title', __('messages.faqs'))

@section('content')
    @if(isset($user) && $user->isAdmin())
    <div class="faq-role-filter">
        <label for="roleFilter">{{ __('messages.filter_by_role') }}:</label>
        <select id="roleFilter" onchange="filterByRole(this.value)">
            <option value="staff" {{ (isset($role) && $role === 'staff') ? 'selected' : '' }}>{{ __('messages.staff_role') }}</option>
            <option value="customer" {{ (isset($role) && $role === 'customer') ? 'selected' : '' }}>{{ __('messages.customer_role') }}</option>
        </select>
    </div>
    @endif
    <div class="faq-container">
        @if(empty($translatedFaqs))
            <div class="empty-state">
                <i class="fas fa-question-circle"></i>
                <h3>{{ __('messages.no_faqs_available') }}</h3>
            </div>
        @else
            @foreach($translatedFaqs as $category => $items)
                <div class="faq-section">
                    <h2 class="faq-category-title">{{ $category }}</h2>
                    <div class="faq-list">
                        @foreach($items as $faq)
                            @php
                                $question = $faq->trans('question');
                                $answer = $faq->trans('answer');
                            @endphp
                            @if($question && $answer)
                            <div class="faq-item" id="faq-{{ $faq->id }}">
                                <div class="faq-question" onclick="toggleFaq({{ $faq->id }})">
                                    <span>{{ $question }}</span>
                                    <i class="fas fa-chevron-down faq-icon"></i>
                                </div>
                                <div class="faq-answer" id="answer-{{ $faq->id }}">
                                    <div class="faq-answer-content">
                                        {!! nl2br(e($answer)) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <style>
        .faq-role-filter {
            max-width: 1000px;
            margin: 0 auto 20px auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .faq-role-filter label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }

        .faq-role-filter select {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: white;
            color: var(--text-primary);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .faq-role-filter select:hover {
            border-color: var(--primary-color);
        }

        .faq-role-filter select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1);
        }

        [data-theme="dark"] .faq-role-filter select {
            background: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .faq-role-filter label {
            color: var(--text-primary);
        }

        .faq-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .faq-section {
            margin-bottom: 40px;
        }

        .faq-category-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding: 15px 20px;
            background: linear-gradient(135deg, rgba(102, 88, 221, 0.1) 0%, rgba(102, 88, 221, 0.05) 100%);
            border-radius: 12px;
            border-right: 4px solid var(--primary-color);
            display: inline-block;
            width: 100%;
            box-sizing: border-box;
        }

        html[dir="ltr"] .faq-category-title {
            border-right: none;
            border-left: 4px solid var(--primary-color);
        }

        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .faq-item {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .faq-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .faq-question {
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-primary);
            transition: all 0.3s;
            font-size: 16px;
        }

        .faq-question:hover {
            background: var(--bg-light);
        }

        .faq-question span {
            flex: 1;
            padding-right: 15px;
        }

        html[dir="ltr"] .faq-question span {
            padding-right: 0;
            padding-left: 15px;
        }

        .faq-icon {
            font-size: 16px;
            transition: transform 0.3s ease;
            color: var(--primary-color);
            flex-shrink: 0;
        }

        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out;
            background: #f8f9fa;
        }

        .faq-answer-content {
            padding: 0 24px 24px 24px;
            color: var(--text-secondary);
            line-height: 1.8;
            font-size: 15px;
            margin-top: 10px;
        }

        .faq-item.active {
            border-color: var(--primary-color);
            box-shadow: 0 4px 16px rgba(102, 88, 221, 0.15);
        }

        .faq-item.active .faq-question {
            background: linear-gradient(135deg, rgba(102, 88, 221, 0.05) 0%, rgba(102, 88, 221, 0.02) 100%);
            color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 64px;
            color: var(--text-secondary);
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 20px;
            color: var(--text-primary);
            margin-top: 15px;
        }

        /* Dark Mode Styles */
        [data-theme="dark"] .faq-item {
            background: var(--card-bg);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .faq-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        [data-theme="dark"] .faq-question {
            color: var(--text-primary);
        }

        [data-theme="dark"] .faq-question:hover {
            background: var(--sidebar-active-bg);
        }

        [data-theme="dark"] .faq-item.active .faq-question {
            background: linear-gradient(135deg, rgba(102, 88, 221, 0.15) 0%, rgba(102, 88, 221, 0.08) 100%);
        }

        [data-theme="dark"] .faq-answer {
            background: var(--sidebar-active-bg) !important;
        }

        [data-theme="dark"] .faq-answer-content {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .faq-category-title {
            color: var(--primary-color);
            background: linear-gradient(135deg, rgba(102, 88, 221, 0.2) 0%, rgba(102, 88, 221, 0.1) 100%);
        }

        [data-theme="dark"] .empty-state {
            color: var(--text-secondary);
        }

        [data-theme="dark"] .empty-state i {
            color: var(--text-secondary);
        }

        [data-theme="dark"] .empty-state h3 {
            color: var(--text-primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .faq-container {
                padding: 15px;
            }

            .faq-category-title {
                font-size: 20px;
                padding: 12px 16px;
            }

            .faq-question {
                padding: 16px 20px;
                font-size: 15px;
            }

            .faq-answer-content {
                padding: 0 20px 20px 20px;
                font-size: 14px;
            }
        }
    </style>

    <script>
        function toggleFaq(id) {
            const item = document.getElementById('faq-' + id);
            const answer = document.getElementById('answer-' + id);
            const isActive = item.classList.contains('active');

            // Close all other faqs
            document.querySelectorAll('.faq-item').forEach(el => {
                el.classList.remove('active');
                el.querySelector('.faq-answer').style.maxHeight = null;
            });

            if (!isActive) {
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + "px";
            }
        }

        function filterByRole(role) {
            const url = new URL(window.location.href);
            url.searchParams.set('role', role);
            window.location.href = url.toString();
        }
    </script>
@endsection
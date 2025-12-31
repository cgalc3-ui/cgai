@extends('layouts.dashboard')

@section('title', __('messages.faqs'))
@section('page-title', __('messages.faqs'))

@section('content')
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
                            <div class="faq-item" id="faq-{{ $faq->id }}">
                                <div class="faq-question" onclick="toggleFaq({{ $faq->id }})">
                                    <span>{{ $faq->question }}</span>
                                    <i class="fas fa-chevron-down faq-icon"></i>
                                </div>
                                <div class="faq-answer" id="answer-{{ $faq->id }}">
                                    <div class="faq-answer-content">
                                        {!! nl2br(e($faq->answer)) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <style>
        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-section {
            margin-bottom: 30px;
        }

        .faq-category-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
            padding-right: 10px;
            border-right: 4px solid var(--primary-color);
        }

        .faq-item {
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid var(--border-color);
        }

        .faq-question {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-primary);
            transition: all 0.3s;
        }

        .faq-question:hover {
            background: var(--bg-light);
        }

        .faq-icon {
            font-size: 14px;
            transition: transform 0.3s;
            color: var(--text-secondary);
        }

        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: #fafafa;
        }

        .faq-answer-content {
            padding: 20px;
            color: var(--text-secondary);
            line-height: 1.8;
            font-size: 15px;
        }

        .faq-item.active {
            border-color: var(--primary-color);
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
    </script>
@endsection
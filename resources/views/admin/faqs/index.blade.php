@extends('layouts.dashboard')

@section('title', __('messages.faqs'))
@section('page-title', __('messages.faqs_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.faqs_list') }}</h2>
            <p>{{ __('messages.manage_faqs_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('faqs.index') }}" class="btn btn-secondary" style="margin-left: 10px;">
                <i class="fas fa-eye"></i> {{ __('messages.view_as_user') }}
            </a>
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_faq') }}
            </a>
            <span class="total-count">{{ __('messages.total_faqs') }}: {{ $faqs->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.faqs.index') }}" class="filter-form">
            <div class="filter-inputs" style="gap: 30px;">
                <div class="filter-group">
                    <label for="category"><i class="fas fa-tags"></i> {{ __('messages.category') }}:</label>
                    <select name="category" id="category" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($categories as $category)
                            @php
                                $categoryKey = $category;
                                $categoryMap = [
                                    'الحساب' => 'account',
                                    'عام' => 'general',
                                    'الخدمات' => 'services',
                                    'الدفع' => 'payment',
                                    'تقني' => 'technical',
                                ];
                                if (isset($categoryMap[$categoryKey])) {
                                    $categoryKey = $categoryMap[$categoryKey];
                                }
                                $validKeys = ['account', 'general', 'services', 'payment', 'technical'];
                                if (!in_array($categoryKey, $validKeys)) {
                                    $categoryKey = 'general';
                                }
                            @endphp
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ __('messages.' . $categoryKey) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="status"><i class="fas fa-toggle-on"></i> {{ __('messages.status') }}:</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                            {{ __('messages.active') }}
                        </option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            {{ __('messages.inactive') }}
                        </option>
                    </select>
                </div>
                <div class="filter-group" style="margin-inline-start: 40px;">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search') }}:</label>
                    <input type="text" name="search" id="search" class="filter-input"
                        placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="filter-actions" style="margin-inline-start: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> {{ __('messages.apply_filter') }}
                </button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.question') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th class="text-center">{{ __('messages.sort_order') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                    <tr>
                        <td>{{ Str::limit($faq->trans('question'), 60) }}</td>
                        <td><span class="status-pill active"
                                style="font-size: 11px;">
                            @php
                                $categoryKey = $faq->category;
                                // Map Arabic values to English keys for translation
                                $categoryMap = [
                                    'الحساب' => 'account',
                                    'عام' => 'general',
                                    'الخدمات' => 'services',
                                    'الدفع' => 'payment',
                                    'تقني' => 'technical',
                                ];
                                if (isset($categoryMap[$categoryKey])) {
                                    $categoryKey = $categoryMap[$categoryKey];
                                }
                                // If category is already an English key, use it directly
                                $validKeys = ['account', 'general', 'services', 'payment', 'technical'];
                                if (!in_array($categoryKey, $validKeys)) {
                                    $categoryKey = 'general'; // Default fallback
                                }
                            @endphp
                            {{ __('messages.' . $categoryKey) }}
                        </span></td>
                        <td class="text-center">{{ $faq->sort_order }}</td>
                        <td class="text-center">
                            @if($faq->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_faq_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="calm-action-btn danger" title="{{ __('messages.delete') }}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">{{ __('messages.no_faqs') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
            border: none;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            border: none;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 13px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
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

        .action-buttons {
            display: flex;
            gap: 8px;
        }
    </style>
@endsection
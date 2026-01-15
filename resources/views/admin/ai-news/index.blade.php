@extends('layouts.dashboard')

@section('title', __('messages.latest_technologies'))
@section('page-title', __('messages.latest_technologies'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.latest_technologies') }}</h2>
            <p>{{ __('messages.manage_latest_technologies_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.ai-services.services.index') }}" class="btn btn-secondary">
                <i class="fas fa-cog"></i> {{ __('messages.manage_services') }}
            </a>
            <span class="total-count">{{ __('messages.latest_count') }}: {{ $latestTechnologies->total() }}</span>
        </div>
    </div>

    <!-- Latest Technologies Section -->
    <div class="card dashboard-card" style="margin-bottom: 30px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-star" style="color: #f59e0b; margin-left: 8px;"></i>
                {{ __('messages.latest_technologies') }}
            </h3>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}" style="margin-bottom: 20px;">
                <form method="GET" action="{{ route('admin.ai-news.index') }}" class="filter-form">
                    <div class="filter-inputs" style="gap: 30px;">
                        <div class="filter-group">
                            <label for="latest_category_id"><i class="fas fa-tags"></i> {{ __('messages.category') }}:</label>
                            <select name="latest_category_id" id="latest_category_id" class="filter-input">
                                <option value="">{{ __('messages.all') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('latest_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' && $category->name_en ? $category->name_en : $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group" style="margin-inline-start: 40px;">
                            <label for="latest_search"><i class="fas fa-search"></i> {{ __('messages.search') }}:</label>
                            <input type="text" name="latest_search" id="latest_search" class="filter-input"
                                placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('latest_search') }}">
                        </div>
                    </div>
                    <div class="filter-actions" style="margin-inline-start: 20px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> {{ __('messages.apply_filter') }}
                        </button>
                        <a href="{{ route('admin.ai-news.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th>{{ __('messages.views') }}</th>
                            <th>{{ __('messages.purchases') }}</th>
                            <th>{{ __('messages.created_at') }}</th>
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestTechnologies as $service)
                            <tr>
                                <td>{{ app()->getLocale() === 'en' && $service->name_en ? $service->name_en : $service->name }}</td>
                                <td>{{ $service->category ? (app()->getLocale() === 'en' && $service->category->name_en ? $service->category->name_en : $service->category->name) : '-' }}</td>
                                <td>{{ number_format($service->price, 2) }} {{ $service->currency }}</td>
                                <td>{{ number_format($service->views_count) }}</td>
                                <td>{{ number_format($service->purchases_count) }}</td>
                                <td>{{ $service->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.ai-services.services.show', $service) }}" class="calm-action-btn info"
                                            title="{{ __('messages.view') }}">
                                            <i class="far fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.ai-news.remove-from-latest', $service) }}" method="POST" class="d-inline"
                                            onsubmit="event.preventDefault(); Confirm.delete({{ json_encode(__('messages.remove_from_latest_confirm')) }}, {{ json_encode(__('messages.confirm_delete_title')) }}).then(confirmed => { if(confirmed) this.submit(); }); return false;">
                                            @csrf
                                            <button type="submit" class="calm-action-btn danger" title="{{ __('messages.remove_from_latest') }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <p style="color: var(--text-secondary); margin: 20px 0;">
                                        {{ __('messages.no_latest_technologies') }}
                                    </p>
                                    <p style="color: var(--text-secondary); font-size: 14px;">
                                        {{ __('messages.select_latest_technologies_help') }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $latestTechnologies->links('vendor.pagination.custom', ['itemName' => 'latest technologies']) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Best Technologies of the Month Section -->
    <div class="card dashboard-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-trophy" style="color: #f59e0b; margin-left: 8px;"></i>
                {{ __('messages.best_technologies_of_month') }}
            </h3>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}" style="margin-bottom: 20px;">
                <form method="GET" action="{{ route('admin.ai-news.index') }}" class="filter-form">
                    <div class="filter-inputs" style="gap: 30px;">
                        <div class="filter-group">
                            <label for="best_category_id"><i class="fas fa-tags"></i> {{ __('messages.category') }}:</label>
                            <select name="best_category_id" id="best_category_id" class="filter-input">
                                <option value="">{{ __('messages.all') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('best_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' && $category->name_en ? $category->name_en : $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group" style="margin-inline-start: 40px;">
                            <label for="best_search"><i class="fas fa-search"></i> {{ __('messages.search') }}:</label>
                            <input type="text" name="best_search" id="best_search" class="filter-input"
                                placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('best_search') }}">
                        </div>
                    </div>
                    <div class="filter-actions" style="margin-inline-start: 20px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> {{ __('messages.apply_filter') }}
                        </button>
                        <a href="{{ route('admin.ai-news.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th>{{ __('messages.rating') }}</th>
                            <th>{{ __('messages.purchases') }}</th>
                            <th>{{ __('messages.created_at') }}</th>
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bestTechnologies as $service)
                            <tr>
                                <td>{{ app()->getLocale() === 'en' && $service->name_en ? $service->name_en : $service->name }}</td>
                                <td>{{ $service->category ? (app()->getLocale() === 'en' && $service->category->name_en ? $service->category->name_en : $service->category->name) : '-' }}</td>
                                <td>{{ number_format($service->price, 2) }} {{ $service->currency }}</td>
                                <td>
                                    <span style="color: #f59e0b;">
                                        <i class="fas fa-star"></i> {{ number_format($service->rating, 1) }}
                                    </span>
                                </td>
                                <td>{{ number_format($service->purchases_count) }}</td>
                                <td>{{ $service->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.ai-services.services.show', $service) }}" class="calm-action-btn info"
                                            title="{{ __('messages.view') }}">
                                            <i class="far fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.ai-news.remove-from-best', $service) }}" method="POST" class="d-inline"
                                            onsubmit="event.preventDefault(); Confirm.delete({{ json_encode(__('messages.remove_from_best_confirm')) }}, {{ json_encode(__('messages.confirm_delete_title')) }}).then(confirmed => { if(confirmed) this.submit(); }); return false;">
                                            @csrf
                                            <button type="submit" class="calm-action-btn danger" title="{{ __('messages.remove_from_best') }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <p style="color: var(--text-secondary); margin: 20px 0;">
                                        {{ __('messages.no_best_technologies') }}
                                    </p>
                                    <p style="color: var(--text-secondary); font-size: 14px;">
                                        {{ __('messages.select_best_technologies_help') }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $bestTechnologies->links('vendor.pagination.custom', ['itemName' => 'best technologies']) }}
                </div>
            </div>
        </div>
    </div>
@endsection

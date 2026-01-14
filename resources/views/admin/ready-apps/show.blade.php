@extends('layouts.dashboard')

@section('title', __('messages.ready_app_name'))
@section('page-title', __('messages.ready_app_name'))

@section('content')
    <div class="app-details-container">
        <!-- Header -->
        <header class="app-header">
            <div class="header-main">
                <h1 class="app-title">{{ $app->trans('name') }}</h1>
                <p class="app-subtitle">{{ __('messages.created_at') }}: {{ $app->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.ready-apps.apps.index') }}" class="btn-back">
                    <i class="fas fa-arrow-right"></i>
                    <span>{{ __('messages.back_to_list') }}</span>
                </a>
            </div>
        </header>

        <div class="content-grid">
            <!-- Main Column -->
            <div class="main-column">
                <!-- App Image -->
                @if($app->mainImageUrl)
                <div class="info-card">
                    <div class="card-body">
                        <div class="app-image-container">
                            <img src="{{ asset($app->mainImageUrl) }}" alt="{{ $app->trans('name') }}" class="app-main-image">
                        </div>
                    </div>
                </div>
                @endif

                <!-- App Details -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ __('messages.ready_app_name') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            @php
                                $currentLocale = app()->getLocale();
                            @endphp
                            @if($currentLocale == 'ar')
                                @if($app->name)
                                <div class="info-item">
                                    <span class="info-label">
                                        <i class="fas fa-tag"></i> {{ __('messages.name') }}:
                                    </span>
                                    <span class="info-value">{{ $app->name }}</span>
                                </div>
                                @endif
                            @else
                                @if($app->name_en)
                                <div class="info-item">
                                    <span class="info-label">
                                        <i class="fas fa-tag"></i> {{ __('messages.name') }}:
                                    </span>
                                    <span class="info-value">{{ $app->name_en }}</span>
                                </div>
                                @endif
                            @endif
                            @if($app->category)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="far fa-folder"></i> {{ __('messages.ready_app_category') }}:
                                </span>
                                <span class="info-value">{{ $app->category->trans('name') }}</span>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-wallet"></i> {{ __('messages.price') }}:
                                </span>
                                <span class="info-value" style="color: var(--success-color); font-weight: 600;">
                                    {{ number_format($app->price, 2) }} {{ $app->currency }}
                                </span>
                            </div>
                            @if($app->original_price)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-tag"></i> {{ __('messages.original_price') }}:
                                </span>
                                <span class="info-value" style="text-decoration: line-through; color: var(--text-secondary);">
                                    {{ number_format($app->original_price, 2) }} {{ $app->currency }}
                                </span>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-toggle-on"></i> {{ __('messages.status') }}:
                                </span>
                                <span class="info-value">
                                    @if($app->is_active)
                                        <span class="badge badge-success">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('messages.inactive') }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-star"></i> {{ __('messages.is_popular') }}:
                                </span>
                                <span class="info-value">
                                    @if($app->is_popular)
                                        <span class="badge badge-info">{{ __('messages.yes') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('messages.no') }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-sparkles"></i> {{ __('messages.is_new') }}:
                                </span>
                                <span class="info-value">
                                    @if($app->is_new)
                                        <span class="badge badge-info">{{ __('messages.yes') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('messages.no') }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-star"></i> {{ __('messages.is_featured') }}:
                                </span>
                                <span class="info-value">
                                    @if($app->is_featured)
                                        <span class="badge badge-info">{{ __('messages.yes') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('messages.no') }}</span>
                                    @endif
                                </span>
                            </div>
                            @if($currentLocale == 'ar')
                                @if($app->short_description)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.short_description') }}:
                                    </span>
                                    <div class="info-value">{{ $app->short_description }}</div>
                                </div>
                                @endif
                                @if($app->description)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.description') }}:
                                    </span>
                                    <div class="info-value">{{ $app->description }}</div>
                                </div>
                                @endif
                                @if($app->full_description)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.full_description') }}:
                                    </span>
                                    <div class="info-value">{{ $app->full_description }}</div>
                                </div>
                                @endif
                            @else
                                @if($app->short_description_en)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.short_description') }}:
                                    </span>
                                    <div class="info-value">{{ $app->short_description_en }}</div>
                                </div>
                                @endif
                                @if($app->description_en)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.description') }}:
                                    </span>
                                    <div class="info-value">{{ $app->description_en }}</div>
                                </div>
                                @endif
                                @if($app->full_description_en)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.full_description') }}:
                                    </span>
                                    <div class="info-value">{{ $app->full_description_en }}</div>
                                </div>
                                @endif
                            @endif
                            @if($app->video_url)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-video"></i> {{ __('messages.video_url') }}:
                                </span>
                                <a href="{{ $app->video_url }}" target="_blank" class="info-value link">{{ $app->video_url }}</a>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-calendar"></i> {{ __('messages.created_at') }}:
                                </span>
                                <span class="info-value">{{ $app->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gallery Images -->
                @if($app->galleryImages->count() > 0)
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-images"></i>
                        <span>{{ __('messages.gallery_images') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="gallery-grid">
                            @foreach($app->galleryImages as $image)
                                <div class="gallery-item">
                                    <img src="{{ asset($image->url) }}" alt="Gallery image">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Screenshots -->
                @if($app->screenshots->count() > 0)
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-desktop"></i>
                        <span>{{ __('messages.ready_app_screenshots') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="gallery-grid">
                            @foreach($app->screenshots as $screenshot)
                                <div class="gallery-item">
                                    <img src="{{ asset($screenshot->url) }}" alt="Screenshot">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="sidebar-column">
                <!-- Quick Actions -->
                <div class="action-card">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        <span>{{ __('messages.quick_actions') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="{{ route('admin.ready-apps.apps.edit', $app) }}" class="action-btn">
                                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                            </a>
                            <a href="{{ route('admin.ready-apps.orders.index', ['app_id' => $app->id]) }}" class="action-btn">
                                <i class="fas fa-shopping-cart"></i> {{ __('messages.ready_app_orders') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="action-card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i>
                        <span>{{ __('messages.statistics') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-eye"></i> {{ __('messages.views') }}:
                                </span>
                                <span class="info-value">{{ number_format($app->views_count) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-shopping-cart"></i> {{ __('messages.purchases') }}:
                                </span>
                                <span class="info-value">{{ number_format($app->purchases_count) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-heart"></i> {{ __('messages.favorites') }}:
                                </span>
                                <span class="info-value">{{ number_format($app->favorites_count) }}</span>
                            </div>
                            @if($app->rating > 0)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-star"></i> {{ __('messages.rating') }}:
                                </span>
                                <span class="info-value">{{ number_format($app->rating, 1) }} / 5.0</span>
                            </div>
                            @endif
                            @if($app->reviews_count > 0)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-comments"></i> {{ __('messages.reviews') }}:
                                </span>
                                <span class="info-value">{{ number_format($app->reviews_count) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .app-details-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
            background: var(--bg-light);
            min-height: calc(100vh - 100px);
        }

        /* Header */
        .app-header {
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

        .app-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 8px 0;
        }

        .app-subtitle {
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

        /* App Image */
        .app-image-container {
            text-align: center;
        }

        .app-main-image {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .gallery-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
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

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        [data-theme="dark"] .badge-success {
            background: rgba(16, 185, 129, 0.2);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        [data-theme="dark"] .badge-danger {
            background: rgba(239, 68, 68, 0.2);
        }

        .badge-info {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        [data-theme="dark"] .badge-info {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .badge-secondary {
            background: rgba(107, 114, 128, 0.1);
            color: var(--text-secondary);
        }

        [data-theme="dark"] .badge-secondary {
            background: rgba(107, 114, 128, 0.2);
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

        @media (max-width: 768px) {
            .app-details-container {
                padding: 15px;
            }

            .app-header {
                flex-direction: column;
                gap: 15px;
            }

            .info-item {
                flex-direction: column;
                gap: 5px;
            }

            .info-label {
                min-width: auto;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
        }
    </style>
    @endpush
@endsection


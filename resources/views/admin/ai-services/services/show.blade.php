@extends('layouts.dashboard')

@section('title', __('messages.ai_service_name'))
@section('page-title', __('messages.ai_service_name'))

@section('content')
    <div class="app-details-container">
        <!-- Header -->
        <header class="app-header">
            <div class="header-main">
                <h1 class="app-title">{{ $service->trans('name') }}</h1>
                <p class="app-subtitle">{{ __('messages.created_at') }}: {{ $service->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.ai-services.services.index') }}" class="btn-back">
                    <i class="fas fa-arrow-right"></i>
                    <span>{{ __('messages.back_to_list') }}</span>
                </a>
            </div>
        </header>

        <div class="content-grid">
            <!-- Main Column -->
            <div class="main-column">
                <!-- Service Image -->
                @if($service->mainImageUrl)
                <div class="info-card">
                    <div class="card-body">
                        <div class="app-image-container">
                            <img src="{{ asset($service->mainImageUrl) }}" alt="{{ $service->trans('name') }}" class="app-main-image">
                        </div>
                    </div>
                </div>
                @endif

                <!-- Service Details -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ __('messages.ai_service_name') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            @php
                                $currentLocale = app()->getLocale();
                            @endphp
                            @if($currentLocale == 'ar')
                                @if($service->name)
                                <div class="info-item">
                                    <span class="info-label">
                                        <i class="fas fa-tag"></i> {{ __('messages.name') }}:
                                    </span>
                                    <span class="info-value">{{ $service->name }}</span>
                                </div>
                                @endif
                            @else
                                @if($service->name_en)
                                <div class="info-item">
                                    <span class="info-label">
                                        <i class="fas fa-tag"></i> {{ __('messages.name') }}:
                                    </span>
                                    <span class="info-value">{{ $service->name_en }}</span>
                                </div>
                                @endif
                            @endif
                            @if($service->category)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="far fa-folder"></i> {{ __('messages.ai_service_category') }}:
                                </span>
                                <span class="info-value">{{ $service->category->trans('name') }}</span>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-wallet"></i> {{ __('messages.price') }}:
                                </span>
                                <span class="info-value" style="color: var(--success-color); font-weight: 600;">
                                    {{ number_format($service->price, 2) }} {{ $service->currency }}
                                </span>
                            </div>
                            @if($service->original_price)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-tag"></i> {{ __('messages.original_price') }}:
                                </span>
                                <span class="info-value" style="text-decoration: line-through; color: var(--text-secondary);">
                                    {{ number_format($service->original_price, 2) }} {{ $service->currency }}
                                </span>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-toggle-on"></i> {{ __('messages.status') }}:
                                </span>
                                <span class="info-value">
                                    @if($service->is_active)
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
                                    @if($service->is_popular)
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
                                    @if($service->is_new)
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
                                    @if($service->is_featured)
                                        <span class="badge badge-info">{{ __('messages.yes') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('messages.no') }}</span>
                                    @endif
                                </span>
                            </div>
                            @if($currentLocale == 'ar')
                                @if($service->short_description)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.short_description') }}:
                                    </span>
                                    <div class="info-value">{{ $service->short_description }}</div>
                                </div>
                                @endif
                                @if($service->description)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.description') }}:
                                    </span>
                                    <div class="info-value">{{ $service->description }}</div>
                                </div>
                                @endif
                                @if($service->full_description)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.full_description') }}:
                                    </span>
                                    <div class="info-value">{{ $service->full_description }}</div>
                                </div>
                                @endif
                            @else
                                @if($service->short_description_en)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.short_description') }}:
                                    </span>
                                    <div class="info-value">{{ $service->short_description_en }}</div>
                                </div>
                                @endif
                                @if($service->description_en)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.description') }}:
                                    </span>
                                    <div class="info-value">{{ $service->description_en }}</div>
                                </div>
                                @endif
                                @if($service->full_description_en)
                                <div class="info-item full-width">
                                    <span class="info-label">
                                        <i class="fas fa-align-right"></i> {{ __('messages.full_description') }}:
                                    </span>
                                    <div class="info-value">{{ $service->full_description_en }}</div>
                                </div>
                                @endif
                            @endif
                            @if($service->video_url)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-video"></i> {{ __('messages.video_url') }}:
                                </span>
                                <a href="{{ $service->video_url }}" target="_blank" class="info-value link">{{ $service->video_url }}</a>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-calendar"></i> {{ __('messages.created_at') }}:
                                </span>
                                <span class="info-value">{{ $service->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                @if($service->features && $service->features->count() > 0)
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-list-check"></i>
                        <span>{{ __('messages.ai_service_features') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="features-list">
                            @foreach($service->features as $feature)
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ $feature->trans('name') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Gallery Images -->
                @if($service->galleryImages && $service->galleryImages->count() > 0)
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-images"></i>
                        <span>{{ __('messages.gallery_images') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="gallery-grid">
                            @foreach($service->galleryImages as $image)
                                <div class="gallery-item">
                                    <img src="{{ asset($image->url) }}" alt="Gallery image">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Screenshots -->
                @if($service->screenshots && $service->screenshots->count() > 0)
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-desktop"></i>
                        <span>{{ __('messages.ai_service_screenshots') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="gallery-grid">
                            @foreach($service->screenshots as $screenshot)
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
                            <button type="button" class="action-btn" 
                                onclick="openEditModal('{{ route('admin.ai-services.services.edit', $service) }}', 'editAiServiceModal', '{{ __('messages.edit_ai_service') }}')">
                                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                            </button>
                            <a href="{{ route('admin.ai-services.orders.index', ['service_id' => $service->id]) }}" class="action-btn">
                                <i class="fas fa-shopping-cart"></i> {{ __('messages.ai_service_orders') }}
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
                                <span class="info-value">{{ number_format($service->views_count ?? 0) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-shopping-cart"></i> {{ __('messages.purchases') }}:
                                </span>
                                <span class="info-value">{{ number_format($service->purchases_count ?? 0) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-heart"></i> {{ __('messages.favorites') }}:
                                </span>
                                <span class="info-value">{{ number_format($service->favorites_count ?? 0) }}</span>
                            </div>
                            @if($service->rating > 0)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-star"></i> {{ __('messages.rating') }}:
                                </span>
                                <span class="info-value">{{ number_format($service->rating, 1) }} / 5.0</span>
                            </div>
                            @endif
                            @if($service->reviews_count > 0)
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-comments"></i> {{ __('messages.reviews') }}:
                                </span>
                                <span class="info-value">{{ number_format($service->reviews_count) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editAiServiceModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.edit_ai_service') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('editAiServiceModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="editAiServiceModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        function openEditModal(url, modalId, title) {
            const modal = document.getElementById(modalId);
            const modalBody = document.getElementById(modalId + 'Body');

            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #3b82f6;"></i></div>';
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data && data.html) {
                        modalBody.innerHTML = data.html;
                        const scripts = modalBody.querySelectorAll('script');
                        scripts.forEach(script => {
                            const newScript = document.createElement('script');
                            Array.from(script.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.appendChild(document.createTextNode(script.innerHTML));
                            script.parentNode.replaceChild(newScript, script);
                        });

                        const form = modalBody.querySelector('form');
                        if (form) {
                            form.addEventListener('submit', function (e) {
                                e.preventDefault();
                                handleFormSubmit(form, modalId);
                            });
                        }
                    } else {
                        console.error('Invalid response:', data);
                        modalBody.innerHTML = '<div class="alert alert-error">Invalid server response</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="alert alert-error">{{ __('messages.error') }}</div>';
                });
        }

        function handleFormSubmit(form, modalId) {
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';

            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal(modalId);
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        if (data.errors) {
                            form.querySelectorAll('.error-message').forEach(el => el.remove());
                            form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

                            Object.keys(data.errors).forEach(key => {
                                const input = form.querySelector(`[name="${key}"]`);
                                if (input) {
                                    input.classList.add('error');
                                    const errorMsg = document.createElement('span');
                                    errorMsg.className = 'error-message';
                                    errorMsg.textContent = data.errors[key][0];
                                    input.parentNode.appendChild(errorMsg);
                                }
                            });
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeModal(e.target.id);
            }
        });
    </script>

    @push('styles')
    <style>
        .app-details-container {
            padding: 24px;
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
            margin-bottom: 32px;
            padding: 24px;
            background: var(--card-bg, white);
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .header-main {
            flex: 1;
        }

        .app-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 10px 0;
            line-height: 1.2;
        }

        .app-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #2563eb 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-back i {
            font-size: 14px;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Cards */
        .info-card, .action-card {
            background: var(--card-bg, white);
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .info-card:hover, .action-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        [data-theme="dark"] .info-card:hover,
        [data-theme="dark"] .action-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, var(--sidebar-active-bg) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: var(--text-primary);
            font-size: 18px;
        }

        .card-header i {
            color: var(--primary-color);
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        .card-body {
            padding: 24px;
        }

        /* App Image */
        .app-image-container {
            text-align: center;
            padding: 8px;
        }

        .app-main-image {
            max-width: 100%;
            height: auto;
            border-radius: 16px;
            border: 2px solid var(--border-color);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .app-main-image:hover {
            transform: scale(1.02);
        }

        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--sidebar-active-bg);
        }

        .gallery-item:hover {
            border-color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
        }

        .gallery-item img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        /* Features List */
        .features-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: linear-gradient(135deg, var(--sidebar-active-bg) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .feature-item i {
            color: var(--success-color);
            font-size: 18px;
        }

        /* Info List */
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }

        .info-item:hover {
            padding-right: 8px;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-item.full-width {
            flex-direction: column;
            gap: 12px;
            padding: 20px;
            background: var(--sidebar-active-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .info-label {
            font-weight: 600;
            color: var(--text-primary);
            min-width: 200px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
        }

        .info-label i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .info-value {
            color: var(--text-primary);
            flex: 1;
            font-size: 15px;
            line-height: 1.6;
        }

        .info-value.link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .info-value.link:hover {
            text-decoration: underline;
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        [data-theme="dark"] .badge-success {
            background: rgba(16, 185, 129, 0.25);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .badge-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.1) 100%);
            color: var(--danger-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        [data-theme="dark"] .badge-danger {
            background: rgba(239, 68, 68, 0.25);
            border-color: rgba(239, 68, 68, 0.3);
        }

        .badge-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(59, 130, 246, 0.1) 100%);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        [data-theme="dark"] .badge-info {
            background: rgba(59, 130, 246, 0.25);
            border-color: rgba(59, 130, 246, 0.3);
            color: #60a5fa;
        }

        .badge-secondary {
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.15) 0%, rgba(107, 114, 128, 0.1) 100%);
            color: var(--text-secondary);
            border: 1px solid rgba(107, 114, 128, 0.2);
        }

        [data-theme="dark"] .badge-secondary {
            background: rgba(107, 114, 128, 0.25);
            border-color: rgba(107, 114, 128, 0.3);
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            background: var(--sidebar-active-bg);
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid var(--border-color) !important;
            cursor: pointer;
            width: 100%;
            font-size: 15px;
            box-sizing: border-box;
        }
        
        .action-btn,
        a.action-btn,
        button.action-btn {
            border: 2px solid var(--border-color) !important;
        }

        .action-btn:hover {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2563eb 100%);
            color: white;
            border-color: var(--primary-color);
            transform: translateX(6px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .action-btn i {
            color: var(--primary-color);
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .action-btn:hover i {
            color: white;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            transform: scale(0.9) translateY(20px);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="dark"] .modal-container {
            background: var(--card-bg);
        }

        .modal-overlay.show .modal-container {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 28px;
            border-bottom: 2px solid var(--border-color);
            background: linear-gradient(135deg, var(--sidebar-active-bg) 0%, rgba(59, 130, 246, 0.05) 100%);
        }

        .modal-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .modal-close {
            background: var(--sidebar-active-bg);
            border: 2px solid var(--border-color);
            font-size: 18px;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: var(--danger-color);
            color: white;
            border-color: var(--danger-color);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 28px;
        }

        @media (max-width: 768px) {
            .app-details-container {
                padding: 16px;
            }

            .app-header {
                flex-direction: column;
                gap: 16px;
                padding: 20px;
            }

            .app-title {
                font-size: 24px;
            }

            .info-item {
                flex-direction: column;
                gap: 8px;
            }

            .info-label {
                min-width: auto;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }

            .card-body {
                padding: 20px;
            }
        }
    </style>
    @endpush
@endsection


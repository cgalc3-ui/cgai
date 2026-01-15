@extends('layouts.dashboard')

@section('title', __('messages.subscriptions_section_management') ?? 'قسم الباقات')
@section('page-title', __('messages.subscriptions_section_management') ?? 'قسم الباقات')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.subscriptions_section_management') ?? 'قسم الباقات' }}</h2>
            <p>{{ __('messages.manage_subscriptions_section_desc') ?? 'إدارة قسم عرض الباقات في الصفحة الرئيسية' }}</p>
        </div>
        <div class="page-header-right">
            @if(!$section)
                <button type="button" class="btn btn-primary"
                    onclick="openCreateModal('{{ route('admin.customer-facing.subscriptions-section.create') }}', 'sectionModal', '{{ __('messages.add_title_and_description') ?? 'إضافة عنوان ووصف' }}')">
                    <i class="fas fa-plus"></i> {{ __('messages.add_title_and_description') ?? 'إضافة عنوان ووصف' }}
                </button>
            @endif
        </div>
    </div>

    <!-- Section Header (Title and Description) -->
    @if($section)
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3>{{ __('messages.section_header') ?? 'عنوان ووصف القسم' }}</h3>
                <button type="button" class="btn btn-sm btn-primary"
                    onclick="openEditSectionHeader('{{ route('admin.customer-facing.subscriptions-section.edit', $section) }}', 'sectionModal', '{{ __('messages.edit_section_header') ?? 'تعديل عنوان ووصف القسم' }}')">
                    <i class="fas fa-edit"></i> {{ __('messages.edit') ?? 'تعديل' }}
                </button>
            </div>
            <div class="card-body">
                <div style="padding: 20px;">
                    <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">
                        {{ $section->trans('title') ?? __('messages.no_heading') ?? 'لا يوجد عنوان' }}
                    </h2>
                    <p style="font-size: 16px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 0;">
                        {{ $section->trans('description') ?? __('messages.no_description') ?? 'لا يوجد وصف' }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="card" style="margin-bottom: 24px; background: var(--card-bg, var(--sidebar-bg)); border: 1px solid var(--border-color);">
            <div class="card-body">
                <div style="padding: 20px; text-align: center;">
                    <i class="fas fa-info-circle" style="font-size: 24px; color: var(--text-secondary, #6b7280); margin-bottom: 10px;"></i>
                    <p style="color: var(--text-primary, #1f2937); margin: 0; font-size: 14px;">
                        {{ __('messages.no_section_header_set') ?? 'لم يتم تعيين عنوان ووصف القسم بعد. اضغط على "إضافة عنوان ووصف" لإنشاء القسم.' }}
                    </p>
                    <button type="button" class="btn btn-primary" style="margin-top: 15px;"
                        onclick="openCreateModal('{{ route('admin.customer-facing.subscriptions-section.create') }}', 'sectionModal', '{{ __('messages.add_title_and_description') ?? 'إضافة عنوان ووصف' }}')">
                        <i class="fas fa-plus"></i> {{ __('messages.add_title_and_description') ?? 'إضافة عنوان ووصف' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Subscriptions List -->
    @if($subscriptions && $subscriptions->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.subscriptions_list') ?? 'قائمة الباقات' }}</h3>
            </div>
            <div class="card-body">
                <div class="subscriptions-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                    @foreach($subscriptions as $subscription)
                        <div class="subscription-card" style="background: var(--card-bg, var(--sidebar-bg)); border-radius: 12px; overflow: hidden; box-shadow: var(--card-shadow, 0 2px 8px rgba(0,0,0,0.1)); border: 1px solid var(--border-color); transition: transform 0.2s, box-shadow 0.2s; position: relative; {{ $subscription->is_pro ? 'border: 2px solid #fbbf24; background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, var(--card-bg, var(--sidebar-bg)) 100%);' : '' }}" 
                             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--card-shadow, 0 4px 12px rgba(0,0,0,0.15))'"
                             onmouseout="this.style.transform=''; this.style.boxShadow='var(--card-shadow, 0 2px 8px rgba(0,0,0,0.1))'">
                            @if($subscription->is_pro)
                                <div style="position: absolute; top: 15px; {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 15px; background: #fbbf24; color: #1f2937; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; z-index: 5;">
                                    {{ __('messages.pro') ?? 'Pro' }}
                                </div>
                            @endif
                            <div class="subscription-card-actions" style="position: absolute; top: 10px; {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 10px; z-index: 10; display: flex; gap: 8px;">
                                <button type="button" 
                                        class="btn btn-sm btn-primary" 
                                        onclick="event.stopPropagation(); openEditSubscriptionModal({{ $subscription->id }}, 'subscriptionModal', '{{ __('messages.edit_subscription') ?? 'تعديل الباقة' }}')"
                                        style="background: rgba(102, 88, 221, 0.95); color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; display: flex; align-items: center; gap: 4px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') ?? 'تعديل' }}
                                </button>
                            </div>
                            
                            <div class="subscription-content" style="padding: 20px; {{ $subscription->is_pro ? 'padding-top: 45px;' : '' }}">
                                <h3 class="subscription-title" style="font-size: 20px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary); line-height: 1.4;">
                                    {{ $subscription->trans('name') }}
                                </h3>
                                
                                <div style="margin-bottom: 15px;">
                                    <span style="font-size: 14px; color: var(--text-secondary); display: block; margin-bottom: 5px;">{{ __('messages.starts_from') ?? 'يبدأ من' }}</span>
                                    <div style="font-size: 28px; font-weight: 700; color: var(--primary-color); line-height: 1;">
                                        {{ number_format($subscription->price, 0) }} <span style="font-size: 16px; font-weight: 600;">SAR</span>
                                    </div>
                                </div>
                                
                                @if($subscription->trans('description'))
                                    <p class="subscription-description" style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $subscription->trans('description') }}
                                    </p>
                                @endif
                                
                                @if($subscription->features && count($subscription->features) > 0)
                                    <ul style="list-style: none; padding: 0; margin: 15px 0 0 0;">
                                        @php
                                            $features = app()->getLocale() === 'en' && !empty($subscription->features_en) 
                                                ? $subscription->features_en 
                                                : ($subscription->features ?? []);
                                            $displayFeatures = array_slice($features, 0, 3); // Show only first 3 features
                                        @endphp
                                        @foreach($displayFeatures as $feature)
                                            <li style="padding: 6px 0; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary);">
                                                <i class="fas fa-check-circle" style="color: var(--primary-color); font-size: 12px; flex-shrink: 0;"></i>
                                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    {{ is_string($feature) ? $feature : ($feature['name'] ?? '') }}
                                                </span>
                                            </li>
                                        @endforeach
                                        @if(count($features) > 3)
                                            <li style="padding: 6px 0; font-size: 13px; color: var(--text-secondary); font-style: italic;">
                                                + {{ count($features) - 3 }} {{ __('messages.more_features') ?? 'ميزات أخرى' }}
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div style="text-align: center; padding: 60px;">
                    <i class="fas fa-inbox" style="font-size: 48px; color: var(--text-secondary, #9ca3af); margin-bottom: 20px; opacity: 0.5;"></i>
                    <p style="color: var(--text-secondary, #6b7280); font-size: 16px; margin-bottom: 20px;">{{ __('messages.no_subscriptions_available') ?? 'لا توجد باقات متاحة حالياً' }}</p>
                    <a href="{{ route('admin.subscriptions.index') }}" target="_blank" class="btn btn-primary" style="padding: 12px 24px; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block;">
                        <i class="fas fa-plus"></i> {{ __('messages.add_subscription') ?? 'إضافة باقة' }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Section Modal -->
    <div class="modal-overlay" id="sectionModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="sectionModalTitle">{{ __('messages.add_title_and_description') ?? 'إضافة عنوان ووصف' }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('sectionModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="sectionModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Subscription Edit Modal -->
    <div class="modal-overlay" id="subscriptionModal" style="display: none;">
        <div class="modal-container" style="max-width: 800px;">
            <div class="modal-header">
                <h3 class="modal-title" id="subscriptionModalTitle">{{ __('messages.edit_subscription') ?? 'تعديل الباقة' }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('subscriptionModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="subscriptionModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        window.closeModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
                document.body.style.overflow = '';
            }
        };

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                window.closeModal(e.target.id);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal-overlay.show');
                if (openModal) {
                    window.closeModal(openModal.id);
                }
            }
        });

        function openCreateModal(url, modalId, title) {
            const modal = document.getElementById(modalId);
            const modalBody = document.getElementById(modalId + 'Body');
            const modalTitle = document.getElementById(modalId + 'Title');

            if (modalTitle) {
                modalTitle.textContent = title;
            }

            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><span style="font-size: 24px; color: var(--primary-color);">{{ __('messages.loading') }}...</span></div>';
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
                    if (data.html) {
                        modalBody.innerHTML = data.html;
                        const form = modalBody.querySelector('form');
                        if (form) {
                            form.addEventListener('submit', function (e) {
                                e.preventDefault();
                                handleFormSubmit(form, modalId);
                            });
                        }
                    } else {
                        modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') }}</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') }}</div>';
                });
        }

        function openEditModal(url, modalId, title) {
            const modal = document.getElementById(modalId);
            const modalBody = document.getElementById(modalId + 'Body');
            const modalTitle = document.getElementById(modalId + 'Title');

            if (modalTitle) {
                modalTitle.textContent = title;
            }

            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><span style="font-size: 24px; color: var(--primary-color);">{{ __('messages.loading') }}...</span></div>';
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
                    if (data.html) {
                        modalBody.innerHTML = data.html;
                        const form = modalBody.querySelector('form');
                        if (form) {
                            form.addEventListener('submit', function (e) {
                                e.preventDefault();
                                handleFormSubmit(form, modalId);
                            });
                        }
                    } else {
                        modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') }}</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') }}</div>';
                });
        }

        function openEditSectionHeader(url, modalId, title) {
            openEditModal(url, modalId, title);
        }

        function openEditSubscriptionModal(subscriptionId, modalId, title) {
            const modal = document.getElementById(modalId);
            const modalBody = document.getElementById(modalId + 'Body');
            const modalTitle = document.getElementById(modalId + 'Title');

            if (modalTitle) {
                modalTitle.textContent = title;
            }

            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><span style="font-size: 24px; color: var(--primary-color);">{{ __('messages.loading') }}...</span></div>';
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
            document.body.style.overflow = 'hidden';

            const url = '{{ route("admin.subscriptions.edit", ":id") }}'.replace(':id', subscriptionId);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.html) {
                        modalBody.innerHTML = data.html;
                        const form = modalBody.querySelector('form');
                        if (form) {
                            form.addEventListener('submit', function (e) {
                                e.preventDefault();
                                handleSubscriptionFormSubmit(form, modalId);
                            });
                        }
                        // Initialize feature add/remove buttons
                        initializeFeatureButtons();
                    } else {
                        modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') }}</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') }}</div>';
                });
        }

        function initializeFeatureButtons() {
            // Remove existing event listeners by cloning buttons
            const addFeatureBtn = document.querySelector('.add-feature');
            if (addFeatureBtn) {
                const newBtn = addFeatureBtn.cloneNode(true);
                addFeatureBtn.parentNode.replaceChild(newBtn, addFeatureBtn);
                newBtn.addEventListener('click', function() {
                    const container = document.getElementById('featuresContainer');
                    if (container) {
                        const newFeature = document.createElement('div');
                        newFeature.className = 'feature-item';
                        newFeature.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
                        newFeature.innerHTML = `
                            <input type="text" name="features[]" class="form-control" placeholder="{{ __('messages.feature_name_placeholder') ?? 'اسم الميزة' }}">
                            <button type="button" class="btn btn-danger remove-feature" style="padding: 8px 12px;">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        container.appendChild(newFeature);
                        // Attach remove listener to new button
                        newFeature.querySelector('.remove-feature').addEventListener('click', function() {
                            newFeature.remove();
                        });
                    }
                });
            }

            // Add feature buttons (EN)
            const addFeatureEnBtn = document.querySelector('.add-feature-en');
            if (addFeatureEnBtn) {
                const newBtnEn = addFeatureEnBtn.cloneNode(true);
                addFeatureEnBtn.parentNode.replaceChild(newBtnEn, addFeatureEnBtn);
                newBtnEn.addEventListener('click', function() {
                    const container = document.getElementById('featuresEnContainer');
                    if (container) {
                        const newFeature = document.createElement('div');
                        newFeature.className = 'feature-item';
                        newFeature.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
                        newFeature.innerHTML = `
                            <input type="text" name="features_en[]" class="form-control" placeholder="{{ __('messages.feature_name_placeholder') ?? 'Feature Name' }}" style="direction: ltr; text-align: left;">
                            <button type="button" class="btn btn-danger remove-feature" style="padding: 8px 12px;">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        container.appendChild(newFeature);
                        // Attach remove listener to new button
                        newFeature.querySelector('.remove-feature').addEventListener('click', function() {
                            newFeature.remove();
                        });
                    }
                });
            }

            // Remove feature buttons
            document.querySelectorAll('.remove-feature').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.feature-item').remove();
                });
            });
        }

        function handleSubscriptionFormSubmit(form, modalId) {
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';
            }

            let method = form.method.toUpperCase();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                method = methodInput.value.toUpperCase();
            }

            fetch(form.action, {
                method: method === 'PUT' || method === 'DELETE' ? 'POST' : method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.message && typeof Toast !== 'undefined') {
                            Toast.success(data.message);
                        }
                        if (window.closeModal) {
                            window.closeModal(modalId);
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
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
                                    errorMsg.style.color = 'red';
                                    errorMsg.textContent = data.errors[key][0];
                                    input.parentNode.appendChild(errorMsg);
                                }
                            });
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
        }

        function handleFormSubmit(form, modalId) {
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';
            }

            let method = form.method.toUpperCase();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                method = methodInput.value.toUpperCase();
            }

            fetch(form.action, {
                method: method === 'PUT' || method === 'DELETE' ? 'POST' : method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.message && typeof Toast !== 'undefined') {
                            Toast.success(data.message);
                        }
                        if (window.closeModal) {
                            window.closeModal(modalId);
                        }
                        setTimeout(() => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        }, 500);
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
                                    errorMsg.style.color = 'red';
                                    errorMsg.textContent = data.errors[key][0];
                                    input.parentNode.appendChild(errorMsg);
                                }
                            });
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
        }
    </script>

    <style>
        /* Dark Mode Styles for Subscriptions Section */
        [data-theme="dark"] .subscription-card {
            background: var(--card-bg, var(--sidebar-bg)) !important;
            border-color: var(--border-color, #2a2d3a) !important;
        }

        [data-theme="dark"] .subscription-title {
            color: var(--text-primary, #f1f5f9) !important;
        }

        [data-theme="dark"] .subscription-description {
            color: var(--text-secondary, #94a3b8) !important;
        }

        [data-theme="dark"] .subscription-card ul li {
            color: var(--text-secondary, #94a3b8) !important;
        }

        [data-theme="dark"] .subscription-card ul li i {
            color: var(--primary-color, #6658dd) !important;
        }

        [data-theme="dark"] .card {
            background: var(--card-bg, var(--sidebar-bg)) !important;
            border-color: var(--border-color, #2a2d3a) !important;
        }

        [data-theme="dark"] .card-header {
            background: var(--card-bg, var(--sidebar-bg)) !important;
            border-bottom-color: var(--border-color, #2a2d3a) !important;
        }

        [data-theme="dark"] .card-header h3 {
            color: var(--text-primary, #f1f5f9) !important;
        }

        [data-theme="dark"] .card-body {
            color: var(--text-primary, #f1f5f9) !important;
        }

        /* Light Mode Styles */
        [data-theme="light"] .subscription-card,
        :not([data-theme]) .subscription-card {
            background: var(--card-bg, #ffffff) !important;
            border-color: var(--border-color, #e5e7eb) !important;
        }

        [data-theme="light"] .subscription-title,
        :not([data-theme]) .subscription-title {
            color: var(--text-primary, #1f2937) !important;
        }

        [data-theme="light"] .subscription-description,
        :not([data-theme]) .subscription-description {
            color: var(--text-secondary, #6b7280) !important;
        }

        [data-theme="light"] .subscription-card ul li,
        :not([data-theme]) .subscription-card ul li {
            color: var(--text-secondary, #6b7280) !important;
        }

        [data-theme="light"] .card,
        :not([data-theme]) .card {
            background: var(--card-bg, #ffffff) !important;
            border-color: var(--border-color, #e5e7eb) !important;
        }

        [data-theme="light"] .card-header,
        :not([data-theme]) .card-header {
            background: var(--card-bg, #ffffff) !important;
            border-bottom-color: var(--border-color, #e5e7eb) !important;
        }

        [data-theme="light"] .card-header h3,
        :not([data-theme]) .card-header h3 {
            color: var(--text-primary, #1f2937) !important;
        }

        [data-theme="light"] .card-body,
        :not([data-theme]) .card-body {
            color: var(--text-primary, #1f2937) !important;
        }

        /* Ensure text colors are properly set */
        .subscription-card * {
            transition: color 0.2s, background-color 0.2s, border-color 0.2s;
        }
    </style>
@endsection

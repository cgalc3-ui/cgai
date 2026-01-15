@extends('layouts.dashboard')

@section('title', __('messages.footer_management') ?? 'إدارة الفوتر')
@section('page-title', __('messages.footer_management') ?? 'إدارة الفوتر')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.footer_management') ?? 'إدارة الفوتر' }}</h2>
            <p>{{ __('messages.manage_footer_desc') ?? 'إدارة محتوى الفوتر للموقع' }}</p>
        </div>
        <div class="page-header-right">
            @if($footer)
                <button type="button" class="btn btn-primary"
                    onclick="openEditModal('{{ route('admin.customer-facing.footer.edit', $footer) }}', 'footerModal', '{{ __('messages.edit_footer') ?? 'تعديل الفوتر' }}')">
                    <i class="fas fa-edit"></i> {{ __('messages.edit_footer') ?? 'تعديل الفوتر' }}
                </button>
            @else
                <button type="button" class="btn btn-primary"
                    onclick="openCreateModal('{{ route('admin.customer-facing.footer.create') }}', 'footerModal', '{{ __('messages.add_footer') ?? 'إضافة الفوتر' }}')">
                    <i class="fas fa-plus"></i> {{ __('messages.add_footer') ?? 'إضافة الفوتر' }}
                </button>
            @endif
        </div>
    </div>

    @if($footer)
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.footer_preview') ?? 'معاينة الفوتر' }}</h3>
            </div>
            <div class="card-body">
                <div class="footer-preview" style="padding: 40px; border-radius: 12px; background: #0f172a; color: white; min-height: 400px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-bottom: 30px;">
                        <!-- Company Info -->
                        <div>
                            @if($footer->trans('logo'))
                                <img src="{{ asset('storage/' . $footer->trans('logo')) }}" alt="Logo" style="max-height: 60px; margin-bottom: 15px;">
                            @endif
                            @if($footer->trans('description'))
                                <p style="font-size: 14px; line-height: 1.6; opacity: 0.9; margin-bottom: 15px;">
                                    {{ $footer->trans('description') }}
                                </p>
                            @endif
                            @if($footer->email)
                                <p style="font-size: 14px; margin-bottom: 8px;">
                                    <i class="fas fa-envelope" style="margin-left: 8px;"></i> {{ $footer->email }}
                                </p>
                            @endif
                            @if($footer->phone)
                                <p style="font-size: 14px; margin-bottom: 8px;">
                                    <i class="fas fa-phone" style="margin-left: 8px;"></i> {{ $footer->phone }}
                                </p>
                            @endif
                            @if($footer->trans('working_hours'))
                                <p style="font-size: 14px;">
                                    <i class="fas fa-clock" style="margin-left: 8px;"></i> {{ $footer->trans('working_hours') }}
                                </p>
                            @endif
                        </div>

                        <!-- Quick Links -->
                        @if($footer->quick_links && count($footer->quick_links) > 0)
                            <div>
                                <h4 style="color: #fbbf24; margin-bottom: 15px; font-size: 18px;">{{ __('messages.quick_links') ?? 'روابط سريعة' }}</h4>
                                <ul style="list-style: none; padding: 0;">
                                    @foreach($footer->quick_links as $link)
                                        <li style="margin-bottom: 10px;">
                                            <a href="{{ $link['link'] ?? '#' }}" style="color: white; text-decoration: none; font-size: 14px; opacity: 0.9;">
                                                @if(app()->getLocale() === 'en' && !empty($link['title_en']))
                                                    {{ $link['title_en'] }}
                                                @else
                                                    {{ $link['title'] ?? ($link['title_en'] ?? '') }}
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Content Links -->
                        @if($footer->content_links && count($footer->content_links) > 0)
                            <div>
                                <h4 style="color: #fbbf24; margin-bottom: 15px; font-size: 18px;">{{ __('messages.content') ?? 'المحتوى' }}</h4>
                                <ul style="list-style: none; padding: 0;">
                                    @foreach($footer->content_links as $link)
                                        <li style="margin-bottom: 10px;">
                                            <a href="{{ $link['link'] ?? '#' }}" style="color: white; text-decoration: none; font-size: 14px; opacity: 0.9;">
                                                @if(app()->getLocale() === 'en' && !empty($link['title_en']))
                                                    {{ $link['title_en'] }}
                                                @else
                                                    {{ $link['title'] ?? ($link['title_en'] ?? '') }}
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Support Links -->
                        @if($footer->support_links && count($footer->support_links) > 0)
                            <div>
                                <h4 style="color: #fbbf24; margin-bottom: 15px; font-size: 18px;">{{ __('messages.support_help') ?? 'الدعم والمساعدة' }}</h4>
                                <ul style="list-style: none; padding: 0;">
                                    @foreach($footer->support_links as $link)
                                        <li style="margin-bottom: 10px;">
                                            <a href="{{ $link['link'] ?? '#' }}" style="color: white; text-decoration: none; font-size: 14px; opacity: 0.9;">
                                                @if(app()->getLocale() === 'en' && !empty($link['title_en']))
                                                    {{ $link['title_en'] }}
                                                @else
                                                    {{ $link['title'] ?? ($link['title_en'] ?? '') }}
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <!-- Social Media -->
                    @if($footer->social_media && count($footer->social_media) > 0)
                        <div style="border-top: 1px solid #fbbf24; padding-top: 20px; margin-top: 20px; display: flex; align-items: center; justify-content: space-between;">
                            <p style="font-size: 14px; opacity: 0.9;">{{ $footer->trans('copyright_text') ?? 'جميع الحقوق محفوظة' }}</p>
                            <div style="display: flex; gap: 15px;">
                                <span style="font-size: 14px; opacity: 0.9;">{{ __('messages.follow_us') ?? 'تابعنا' }}</span>
                                @foreach($footer->social_media as $social)
                                    <a href="{{ $social['url'] ?? '#' }}" target="_blank" style="color: #fbbf24; font-size: 20px; text-decoration: none;">
                                        <i class="{{ $social['icon'] ?? 'fab fa-' . strtolower($social['platform'] ?? '') }}"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div style="border-top: 1px solid #fbbf24; padding-top: 20px; margin-top: 20px;">
                            <p style="font-size: 14px; opacity: 0.9; text-align: center;">{{ $footer->trans('copyright_text') ?? 'جميع الحقوق محفوظة' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <p class="text-center text-muted" style="padding: 60px;">
                    {{ __('messages.no_footer_set') ?? 'لم يتم إعداد الفوتر بعد' }}
                </p>
            </div>
        </div>
    @endif

    <!-- Footer Modal -->
    <div class="modal-overlay" id="footerModal" style="display: none;">
        <div class="modal-container" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-header">
                <h3 class="modal-title" id="footerModalTitle">{{ __('messages.add_footer') ?? 'إضافة الفوتر' }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('footerModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="footerModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Make closeModal globally available first
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

        // Close modal on overlay click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                window.closeModal(e.target.id);
            }
        });

        // Close modal on Escape key
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

            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><span style="font-size: 24px; color: var(--primary-color);">{{ __('messages.loading') ?? 'جاري التحميل' }}...</span></div>';
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
                        setTimeout(() => {
                            const form = modalBody.querySelector('form');
                            if (form) {
                                const newForm = form.cloneNode(true);
                                form.parentNode.replaceChild(newForm, form);
                                newForm.addEventListener('submit', function (e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    handleFormSubmit(newForm, modalId);
                                    return false;
                                });
                            }
                            initializeFooterFunctions();
                        }, 100);
                    } else {
                        modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') ?? 'حدث خطأ' }}</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') ?? 'حدث خطأ' }}</div>';
                });
        }

        function openEditModal(url, modalId, title) {
            const modal = document.getElementById(modalId);
            const modalBody = document.getElementById(modalId + 'Body');
            const modalTitle = document.getElementById(modalId + 'Title');

            if (modalTitle) {
                modalTitle.textContent = title;
            }

            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><span style="font-size: 24px; color: var(--primary-color);">{{ __('messages.loading') ?? 'جاري التحميل' }}...</span></div>';
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
                        setTimeout(() => {
                            const form = modalBody.querySelector('form');
                            if (form) {
                                const newForm = form.cloneNode(true);
                                form.parentNode.replaceChild(newForm, form);
                                newForm.addEventListener('submit', function (e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    handleFormSubmit(newForm, modalId);
                                    return false;
                                });
                            }
                            initializeFooterFunctions();
                        }, 100);
                    } else {
                        modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') ?? 'حدث خطأ' }}</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') ?? 'حدث خطأ' }}</div>';
                });
        }

        function handleFormSubmit(form, modalId) {
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') ?? 'جاري الحفظ' }}...';
            }

            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        throw new Error('Invalid response format');
                    }
                })
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
                                    errorMsg.style.color = 'var(--danger-color)';
                                    errorMsg.style.fontSize = '12px';
                                    errorMsg.style.marginTop = '5px';
                                    errorMsg.style.display = 'block';
                                    errorMsg.textContent = data.errors[key][0];
                                    input.parentNode.appendChild(errorMsg);
                                }
                            });
                        }
                        if (data.message && typeof Toast !== 'undefined') {
                            Toast.error(data.message);
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Toast !== 'undefined') {
                        Toast.error('{{ __('messages.error_occurred') ?? 'حدث خطأ' }}');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
        }

        function initializeFooterFunctions() {
            // Initialize all dynamic functions for footer form
            const quickLinksContainer = document.getElementById('quick-links-container');
            const contentLinksContainer = document.getElementById('content-links-container');
            const supportLinksContainer = document.getElementById('support-links-container');
            const socialMediaContainer = document.getElementById('social-media-container');

            // Quick Links functions
            if (quickLinksContainer) {
                let quickLinkIndex = quickLinksContainer.querySelectorAll('.link-item').length;
                window.addQuickLink = function() {
                    const linkHtml = `
                        <div class="link-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <strong>{{ __('messages.link') ?? 'رابط' }} #${quickLinkIndex + 1}</strong>
                                <button type="button" class="btn btn-sm btn-danger" onclick="window.removeQuickLink(this)">
                                    <i class="fas fa-trash"></i> {{ __('messages.remove') ?? 'حذف' }}
                                </button>
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.title') ?? 'العنوان' }} (AR)</label>
                                <input type="text" name="quick_links[${quickLinkIndex}][title]" class="form-control">
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.title') ?? 'العنوان' }} (EN)</label>
                                <input type="text" name="quick_links[${quickLinkIndex}][title_en]" class="form-control" style="direction: ltr; text-align: left;">
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.link') ?? 'الرابط' }}</label>
                                <input type="url" name="quick_links[${quickLinkIndex}][link]" class="form-control">
                            </div>
                        </div>
                    `;
                    quickLinksContainer.insertAdjacentHTML('beforeend', linkHtml);
                    quickLinkIndex++;
                };
                window.removeQuickLink = function(btn) {
                    btn.closest('.link-item').remove();
                };
            }

            // Content Links functions
            if (contentLinksContainer) {
                let contentLinkIndex = contentLinksContainer.querySelectorAll('.link-item').length;
                window.addContentLink = function() {
                    const linkHtml = `
                        <div class="link-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <strong>{{ __('messages.link') ?? 'رابط' }} #${contentLinkIndex + 1}</strong>
                                <button type="button" class="btn btn-sm btn-danger" onclick="window.removeContentLink(this)">
                                    <i class="fas fa-trash"></i> {{ __('messages.remove') ?? 'حذف' }}
                                </button>
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.title') ?? 'العنوان' }} (AR)</label>
                                <input type="text" name="content_links[${contentLinkIndex}][title]" class="form-control">
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.title') ?? 'العنوان' }} (EN)</label>
                                <input type="text" name="content_links[${contentLinkIndex}][title_en]" class="form-control" style="direction: ltr; text-align: left;">
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.link') ?? 'الرابط' }}</label>
                                <input type="url" name="content_links[${contentLinkIndex}][link]" class="form-control">
                            </div>
                        </div>
                    `;
                    contentLinksContainer.insertAdjacentHTML('beforeend', linkHtml);
                    contentLinkIndex++;
                };
                window.removeContentLink = function(btn) {
                    btn.closest('.link-item').remove();
                };
            }

            // Support Links functions
            if (supportLinksContainer) {
                let supportLinkIndex = supportLinksContainer.querySelectorAll('.link-item').length;
                window.addSupportLink = function() {
                    const linkHtml = `
                        <div class="link-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <strong>{{ __('messages.link') ?? 'رابط' }} #${supportLinkIndex + 1}</strong>
                                <button type="button" class="btn btn-sm btn-danger" onclick="window.removeSupportLink(this)">
                                    <i class="fas fa-trash"></i> {{ __('messages.remove') ?? 'حذف' }}
                                </button>
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.title') ?? 'العنوان' }} (AR)</label>
                                <input type="text" name="support_links[${supportLinkIndex}][title]" class="form-control">
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.title') ?? 'العنوان' }} (EN)</label>
                                <input type="text" name="support_links[${supportLinkIndex}][title_en]" class="form-control" style="direction: ltr; text-align: left;">
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.link') ?? 'الرابط' }}</label>
                                <input type="url" name="support_links[${supportLinkIndex}][link]" class="form-control">
                            </div>
                        </div>
                    `;
                    supportLinksContainer.insertAdjacentHTML('beforeend', linkHtml);
                    supportLinkIndex++;
                };
                window.removeSupportLink = function(btn) {
                    btn.closest('.link-item').remove();
                };
            }

            // Social Media functions
            if (socialMediaContainer) {
                let socialIndex = socialMediaContainer.querySelectorAll('.social-item').length;
                window.addSocialMedia = function() {
                    const socialHtml = `
                        <div class="social-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <strong>{{ __('messages.social_media') ?? 'وسائل التواصل' }} #${socialIndex + 1}</strong>
                                <button type="button" class="btn btn-sm btn-danger" onclick="window.removeSocialMedia(this)">
                                    <i class="fas fa-trash"></i> {{ __('messages.remove') ?? 'حذف' }}
                                </button>
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.platform') ?? 'المنصة' }}</label>
                                <input type="text" name="social_media[${socialIndex}][platform]" class="form-control" placeholder="Facebook, Twitter, etc.">
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.url') ?? 'الرابط' }}</label>
                                <input type="url" name="social_media[${socialIndex}][url]" class="form-control">
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>{{ __('messages.icon') ?? 'الأيقونة' }} (FontAwesome class)</label>
                                <input type="text" name="social_media[${socialIndex}][icon]" class="form-control" placeholder="fab fa-facebook">
                            </div>
                        </div>
                    `;
                    socialMediaContainer.insertAdjacentHTML('beforeend', socialHtml);
                    socialIndex++;
                };
                window.removeSocialMedia = function(btn) {
                    btn.closest('.social-item').remove();
                };
            }
        }
    </script>
@endsection



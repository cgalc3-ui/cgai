@extends('layouts.dashboard')

@section('title', __('messages.hero_management'))
@section('page-title', __('messages.hero_management'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.hero_management') }}</h2>
            <p>{{ __('messages.manage_hero_desc') }}</p>
        </div>
        <div class="page-header-right">
            @if($hero)
                <button type="button" class="btn btn-primary"
                    onclick="openEditModal('{{ route('admin.customer-facing.hero.edit', $hero) }}', 'heroModal', '{{ __('messages.edit_hero') }}')">
                    <i class="fas fa-edit"></i> {{ __('messages.edit_hero') }}
                </button>
            @else
                <button type="button" class="btn btn-primary"
                    onclick="openCreateModal('{{ route('admin.customer-facing.hero.create') }}', 'heroModal', '{{ __('messages.add_hero') }}')">
                    <i class="fas fa-plus"></i> {{ __('messages.add_hero') }}
                </button>
            @endif
        </div>
    </div>

    @if($hero)
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.hero_preview') }}</h3>
            </div>
            <div class="card-body">
                <div class="hero-preview" style="position: relative; padding: 40px; border-radius: 12px; background: linear-gradient(135deg, #1e3a8a 0%, #7c2d12 100%); min-height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white;">
                    @if($hero->background_image)
                        <img src="{{ asset('storage/' . $hero->background_image) }}" alt="Hero Background" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.3; border-radius: 12px;">
                    @endif
                    <div style="position: relative; z-index: 1; max-width: 800px;">
                        @if($hero->trans('heading'))
                            <h1 style="font-size: 48px; font-weight: 700; margin-bottom: 20px; line-height: 1.2;">
                                {!! $hero->trans('heading') !!}
                            </h1>
                        @endif
                        @if($hero->trans('subheading'))
                            <h2 style="font-size: 32px; font-weight: 600; margin-bottom: 20px; line-height: 1.3;">
                                {!! $hero->trans('subheading') !!}
                            </h2>
                        @endif
                        @if($hero->trans('description'))
                            <p style="font-size: 18px; margin-bottom: 30px; line-height: 1.6; opacity: 0.9;">
                                {{ $hero->trans('description') }}
                            </p>
                        @endif
                        @if($hero->buttons && count($hero->buttons) > 0)
                            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                                @foreach($hero->buttons as $button)
                                    <a href="{{ $button['link'] ?? '#' }}" 
                                       target="{{ $button['target'] ?? '_self' }}"
                                       class="btn"
                                       style="padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s; {{ $button['style'] === 'secondary' ? 'background: rgba(255,255,255,0.1); border: 2px solid #fbbf24; color: white;' : 'background: #fbbf24; color: #1f2937;' }}">
                                        @if(app()->getLocale() === 'en' && !empty($button['title_en']))
                                            {{ $button['title_en'] }}
                                        @else
                                            {{ $button['title'] ?? ($button['title_en'] ?? '') }}
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <p class="text-center text-muted" style="padding: 60px;">
                    {{ __('messages.no_hero_set') }}
                </p>
            </div>
        </div>
    @endif

    <!-- Hero Modal -->
    <div class="modal-overlay" id="heroModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="heroModalTitle">{{ __('messages.add_hero') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('heroModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="heroModalBody">
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

        // Initialize button functions for hero modal
        function initializeButtonFunctions() {
            const container = document.getElementById('buttons-container');
            if (!container) return;

            // Get current button count
            const existingButtons = container.querySelectorAll('.button-item');
            let buttonIndex = existingButtons.length;

            const buttonText = '{{ __('messages.button') }}';
            const removeButtonText = '{{ __('messages.remove_button') }}';
            const titleText = '{{ __('messages.title') }}';
            const linkText = '{{ __('messages.link') }}';
            const targetText = '{{ __('messages.target') }}';
            const buttonStyleText = '{{ __('messages.button_style') }}';
            const sameWindowText = '{{ __('messages.same_window') }}';
            const newWindowText = '{{ __('messages.new_window') }}';
            const primaryText = '{{ __('messages.primary') }}';
            const secondaryText = '{{ __('messages.secondary') }}';

            window.addButton = function() {
                if (!container) {
                    console.error('buttons-container not found');
                    return;
                }
                
                const buttonHtml = `
                    <div class="button-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong>${buttonText} #${buttonIndex + 1}</strong>
                            <button type="button" class="btn btn-sm btn-danger" onclick="window.removeButton(this)">
                                <i class="fas fa-trash"></i> ${removeButtonText}
                            </button>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>${titleText} (AR) <span class="required">*</span></label>
                            <input type="text" name="buttons[${buttonIndex}][title]" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>${titleText} (EN)</label>
                            <input type="text" name="buttons[${buttonIndex}][title_en]" class="form-control" style="direction: ltr; text-align: left;">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>${linkText} <span class="required">*</span></label>
                            <input type="url" name="buttons[${buttonIndex}][link]" class="form-control" required>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>${targetText}</label>
                                <select name="buttons[${buttonIndex}][target]" class="form-control">
                                    <option value="_self">${sameWindowText}</option>
                                    <option value="_blank">${newWindowText}</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label>${buttonStyleText}</label>
                                <select name="buttons[${buttonIndex}][style]" class="form-control">
                                    <option value="primary">${primaryText}</option>
                                    <option value="secondary">${secondaryText}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', buttonHtml);
                buttonIndex++;
                updateButtonNumbers();
            };

            window.removeButton = function(btn) {
                btn.closest('.button-item').remove();
                updateButtonNumbers();
            };

            function updateButtonNumbers() {
                const items = container.querySelectorAll('.button-item');
                items.forEach((item, index) => {
                    const strong = item.querySelector('strong');
                    if (strong) {
                        strong.textContent = buttonText + ' #' + (index + 1);
                    }
                });
            }
        }

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
                        // Attach form submit handler
                        const form = modalBody.querySelector('form');
                        if (form) {
                            form.addEventListener('submit', function (e) {
                                e.preventDefault();
                                handleFormSubmit(form, modalId);
                            });
                        }
                        // Re-initialize button functions after content is loaded
                        initializeButtonFunctions();
                        // Close buttons with onclick will work automatically since window.closeModal is global
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
                        // Attach form submit handler
                        const form = modalBody.querySelector('form');
                        if (form) {
                            form.addEventListener('submit', function (e) {
                                e.preventDefault();
                                handleFormSubmit(form, modalId);
                            });
                        }
                        // Re-initialize button functions after content is loaded
                        initializeButtonFunctions();
                        // Close buttons with onclick will work automatically since window.closeModal is global
                    } else {
                        modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') }}</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="error-message">{{ __('messages.error') }}</div>';
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
                        // Show success toast message
                        if (data.message && typeof Toast !== 'undefined') {
                            Toast.success(data.message);
                        }
                        if (window.closeModal) {
                            window.closeModal(modalId);
                        }
                        // Wait a bit for toast to show, then redirect
                        setTimeout(() => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        }, 500);
                    } else {
                        // Handle validation errors
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
@endsection


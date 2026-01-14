@extends('layouts.dashboard')

@section('title', __('messages.technologies_section_management') ?? 'قسم التقنيات')
@section('page-title', __('messages.technologies_section_management') ?? 'قسم التقنيات')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.technologies_section_management') ?? 'قسم التقنيات' }}</h2>
            <p>{{ __('messages.manage_technologies_section_desc') ?? 'إدارة قسم التقنيات في الصفحة الرئيسية' }}</p>
        </div>
        <div class="page-header-right">
            @if($section)
                <button type="button" class="btn btn-primary"
                    onclick="openEditModal('{{ route('admin.customer-facing.technologies-section.edit', $section) }}', 'sectionModal', '{{ __('messages.edit_technologies_section') ?? 'تعديل قسم التقنيات' }}')">
                    <i class="fas fa-edit"></i> {{ __('messages.edit_technologies_section') ?? 'تعديل' }}
                </button>
            @else
                <button type="button" class="btn btn-primary"
                    onclick="openCreateModal('{{ route('admin.customer-facing.technologies-section.create') }}', 'sectionModal', '{{ __('messages.add_technologies_section') ?? 'إضافة قسم التقنيات' }}')">
                    <i class="fas fa-plus"></i> {{ __('messages.add_technologies_section') ?? 'إضافة' }}
                </button>
            @endif
        </div>
    </div>

    @if($section)
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.technologies_section_preview') ?? 'معاينة قسم التقنيات' }}</h3>
            </div>
            <div class="card-body">
                <div class="section-preview" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-radius: 12px; overflow: hidden; max-width: 900px; margin: 0 auto; min-height: 300px;">
                    <!-- Left Side - Text Content -->
                    <div style="background: #0f172a; padding: 40px 30px; display: flex; flex-direction: column; justify-content: center; color: white;">
                        @if($section->trans('heading'))
                            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 15px; line-height: 1.2;">
                                {!! $section->trans('heading') !!}
                            </h1>
                        @endif
                        @if($section->trans('description'))
                            <p style="font-size: 14px; margin-bottom: 20px; line-height: 1.6; opacity: 0.9;">
                                {{ $section->trans('description') }}
                            </p>
                        @endif
                        @if($section->buttons && count($section->buttons) > 0)
                            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                                @foreach($section->buttons as $button)
                                    <a href="{{ $button['link'] ?? '#' }}" 
                                       target="{{ $button['target'] ?? '_self' }}"
                                       class="btn"
                                       style="padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.3s; {{ $button['style'] === 'secondary' ? 'background: transparent; border: 2px solid #fbbf24; color: white;' : 'background: #fbbf24; color: #1f2937;' }}">
                                        {{ $button['title'] ?? ($button['title_en'] ?? '') }}
                                        @if($button['style'] === 'primary')
                                            <i class="fas fa-arrow-left" style="margin-right: 6px;"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <!-- Right Side - Image -->
                    <div style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        @if($section->background_image)
                            <img src="{{ asset('storage/' . $section->background_image) }}" alt="Section Image" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="text-align: center; color: #999; padding: 30px;">
                                <i class="fas fa-image" style="font-size: 36px; margin-bottom: 10px; opacity: 0.3;"></i>
                                <p style="font-size: 12px;">صورة القسم</p>
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
                    {{ __('messages.no_technologies_section_set') ?? 'لم يتم تعيين قسم التقنيات بعد' }}
                </p>
            </div>
        </div>
    @endif

    <!-- Section Modal -->
    <div class="modal-overlay" id="sectionModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="sectionModalTitle">{{ __('messages.add_technologies_section') ?? 'إضافة قسم التقنيات' }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('sectionModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="sectionModalBody">
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

        // Initialize button functions for section modal
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


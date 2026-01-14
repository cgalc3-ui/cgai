@extends('layouts.dashboard')

@section('title', __('messages.company_logo_management'))
@section('page-title', __('messages.company_logo_management'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.company_logo_management') }}</h2>
            <p>{{ __('messages.manage_company_logo_desc') }}</p>
        </div>
        <div class="page-header-right">
            @if($companyLogo)
                <button type="button" class="btn btn-primary"
                    onclick="openEditModal('{{ route('admin.customer-facing.company-logo.edit', $companyLogo) }}', 'companyLogoModal', '{{ __('messages.edit_company_logo') }}')">
                    <i class="fas fa-edit"></i> {{ __('messages.edit_company_logo') }}
                </button>
            @else
                <button type="button" class="btn btn-primary"
                    onclick="openCreateModal('{{ route('admin.customer-facing.company-logo.create') }}', 'companyLogoModal', '{{ __('messages.add_company_logo') }}')">
                    <i class="fas fa-plus"></i> {{ __('messages.add_company_logo') }}
                </button>
            @endif
        </div>
    </div>

    @if($companyLogo)
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.company_logo_preview') }}</h3>
            </div>
            <div class="card-body">
                <div class="company-logo-preview" style="padding: 40px; border-radius: 12px; background: var(--card-bg); border: 1px solid var(--border-color);">
                    @if($companyLogo->trans('heading'))
                        <h2 style="font-size: 32px; font-weight: 600; margin-bottom: 30px; text-align: center; color: var(--text-color);">
                            {{ $companyLogo->trans('heading') }}
                        </h2>
                    @endif
                    @if($companyLogo->logos && count($companyLogo->logos) > 0)
                        <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; align-items: center;">
                            @foreach($companyLogo->logos as $logo)
                                <div style="text-align: center;">
                                    <a href="{{ $logo['link'] ?? '#' }}" target="_blank" style="display: inline-block; text-decoration: none;">
                                        @if(isset($logo['image']) && $logo['image'])
                                            <img src="{{ asset('storage/' . $logo['image']) }}" 
                                                 alt="{{ $logo['name'] ?? 'Company Logo' }}"
                                                 style="max-height: 80px; max-width: 200px; object-fit: contain; filter: grayscale(100%) opacity(0.7); transition: all 0.3s;">
                                        @endif
                                        @if(isset($logo['name']) && $logo['name'])
                                            <p style="margin-top: 10px; color: var(--text-color); font-size: 14px;">{{ $logo['name'] }}</p>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <p class="text-center text-muted" style="padding: 60px;">
                    {{ __('messages.no_company_logo_set') }}
                </p>
            </div>
        </div>
    @endif

    <!-- Company Logo Modal -->
    <div class="modal-overlay" id="companyLogoModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="companyLogoModalTitle">{{ __('messages.add_company_logo') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('companyLogoModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="companyLogoModalBody">
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

        // Initialize logo functions for company logo modal
        function initializeLogoFunctions() {
            const container = document.getElementById('logos-container');
            if (!container) return;

            // Get current logo count
            const existingLogos = container.querySelectorAll('.logo-item');
            let logoIndex = existingLogos.length;

            const logoText = '{{ __('messages.logo') }}';
            const removeLogoText = '{{ __('messages.remove_logo') }}';
            const imageText = '{{ __('messages.image') }}';
            const linkText = '{{ __('messages.link') }}';
            const nameText = '{{ __('messages.name') }}';

            window.addLogo = function() {
                if (!container) {
                    console.error('logos-container not found');
                    return;
                }
                
                const logoHtml = `
                    <div class="logo-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong>${logoText} #${logoIndex + 1}</strong>
                            <button type="button" class="btn btn-sm btn-danger" onclick="window.removeLogo(this)">
                                <i class="fas fa-trash"></i> ${removeLogoText}
                            </button>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>${imageText} <span class="required">*</span></label>
                            <input type="file" name="logos[${logoIndex}][image]" class="form-control" accept="image/*" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>${linkText}</label>
                            <input type="url" name="logos[${logoIndex}][link]" class="form-control" placeholder="https://example.com">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>${nameText}</label>
                            <input type="text" name="logos[${logoIndex}][name]" class="form-control" placeholder="{{ __('messages.company_name') }}">
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', logoHtml);
                logoIndex++;
                updateLogoNumbers();
            };

            window.removeLogo = function(btn) {
                btn.closest('.logo-item').remove();
                updateLogoNumbers();
            };

            function updateLogoNumbers() {
                const items = container.querySelectorAll('.logo-item');
                items.forEach((item, index) => {
                    const strong = item.querySelector('strong');
                    if (strong) {
                        strong.textContent = logoText + ' #' + (index + 1);
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
                        setTimeout(() => {
                            const form = modalBody.querySelector('form');
                            if (form) {
                                // Remove any existing event listeners by cloning
                                const newForm = form.cloneNode(true);
                                form.parentNode.replaceChild(newForm, form);
                                
                                // Attach new event listener
                                newForm.addEventListener('submit', function (e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    console.log('Form submitted:', newForm.action);
                                    handleFormSubmit(newForm, modalId);
                                    return false;
                                });
                                
                                // Also attach to submit button as backup
                                const submitBtn = newForm.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        console.log('Submit button clicked');
                                        newForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                                        return false;
                                    });
                                }
                            }
                        }, 100);
                        // Re-initialize logo functions after content is loaded
                        setTimeout(() => {
                            initializeLogoFunctions();
                        }, 100);
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
                        setTimeout(() => {
                            const form = modalBody.querySelector('form');
                            if (form) {
                                // Remove any existing event listeners by cloning
                                const newForm = form.cloneNode(true);
                                form.parentNode.replaceChild(newForm, form);
                                
                                // Attach new event listener
                                newForm.addEventListener('submit', function (e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    console.log('Form submitted:', newForm.action);
                                    handleFormSubmit(newForm, modalId);
                                    return false;
                                });
                                
                                // Also attach to submit button as backup
                                const submitBtn = newForm.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        console.log('Submit button clicked');
                                        newForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                                        return false;
                                    });
                                }
                            }
                        }, 100);
                        // Re-initialize logo functions after content is loaded
                        setTimeout(() => {
                            initializeLogoFunctions();
                        }, 100);
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
                .then(response => {
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // If not JSON, return error
                        throw new Error('Invalid response format');
                    }
                })
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
                    } else {
                        alert('{{ __('messages.error_occurred') ?? 'حدث خطأ' }}');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
        }
    </script>
@endsection


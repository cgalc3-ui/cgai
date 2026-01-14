@extends('layouts.dashboard')

@section('title', __('messages.services_section_management') ?? 'قسم الخدمات')
@section('page-title', __('messages.services_section_management') ?? 'قسم الخدمات')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.services_section_management') ?? 'قسم الخدمات' }}</h2>
            <p>{{ __('messages.manage_services_section_desc') ?? 'إدارة قسم الخدمات في الصفحة الرئيسية' }}</p>
        </div>
        <div class="page-header-right">
            @if(!$section)
                <button type="button" class="btn btn-primary"
                    onclick="openCreateModal('{{ route('admin.customer-facing.services-section.create') }}', 'sectionModal', '{{ __('messages.add_services_section') ?? 'إضافة قسم الخدمات' }}')">
                    <i class="fas fa-plus"></i> {{ __('messages.add_services_section') ?? 'إضافة' }}
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
                    onclick="openEditSectionHeader('{{ route('admin.customer-facing.services-section.edit', $section) }}', 'sectionModal', '{{ __('messages.edit_section_header') ?? 'تعديل عنوان ووصف القسم' }}')">
                    <i class="fas fa-edit"></i> {{ __('messages.edit') ?? 'تعديل' }}
                </button>
            </div>
            <div class="card-body">
                <div style="padding: 20px;">
                    <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">
                        {{ $section->trans('heading') ?? __('messages.no_heading') ?? 'لا يوجد عنوان' }}
                    </h2>
                    <p style="font-size: 16px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 0;">
                        {{ $section->trans('description') ?? __('messages.no_description') ?? 'لا يوجد وصف' }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="card" style="margin-bottom: 24px; background: var(--warning-color); opacity: 0.1; border: 1px solid var(--warning-color);">
            <div class="card-body">
                <div style="padding: 20px; text-align: center;">
                    <i class="fas fa-info-circle" style="font-size: 24px; color: var(--warning-color); margin-bottom: 10px;"></i>
                    <p style="color: var(--text-primary); margin: 0;">
                        {{ __('messages.no_section_header_set') ?? 'لم يتم تعيين عنوان ووصف القسم بعد. اضغط على "إضافة قسم الخدمات" لإنشاء القسم.' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Services List -->
    @if(isset($sectionCategories) && $sectionCategories->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('messages.services_list') ?? 'قائمة الخدمات' }}</h3>
                </div>
                <div class="card-body">
                    <div class="services-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                        @foreach($sectionCategories as $category)
                            <div class="service-card" style="background: var(--card-bg, var(--sidebar-bg)); border-radius: 12px; overflow: hidden; box-shadow: var(--card-shadow, 0 2px 8px rgba(0,0,0,0.1)); border: 1px solid var(--border-color); transition: transform 0.2s, box-shadow 0.2s; position: relative;" 
                                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--card-shadow, 0 4px 12px rgba(0,0,0,0.15))'"
                                 onmouseout="this.style.transform=''; this.style.boxShadow='var(--card-shadow, 0 2px 8px rgba(0,0,0,0.1))'">
                                <div class="service-card-actions" style="position: absolute; top: 10px; left: 10px; z-index: 10; display: flex; gap: 8px;">
                                    <button type="button" 
                                            class="btn btn-sm btn-primary" 
                                            onclick="event.stopPropagation(); openEditCategoryModal({{ $category->id }}, 'categoryModal', '{{ __('messages.edit_service') ?? 'تعديل الخدمة' }}')"
                                            style="background: rgba(102, 88, 221, 0.95); color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; display: flex; align-items: center; gap: 4px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                        <i class="fas fa-edit"></i> {{ __('messages.edit') ?? 'تعديل' }}
                                    </button>
                                </div>
                                
                                @if($category->image)
                                    <div class="service-image" style="width: 100%; height: 220px; overflow: hidden; background: var(--bg-light);">
                                        <img src="{{ strpos($category->image, '/storage/') === 0 ? $category->image : asset('storage/' . $category->image) }}" 
                                             alt="{{ $category->trans('name') }}" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @else
                                    <div class="service-image-placeholder" style="width: 100%; height: 220px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                                
                                <div class="service-content" style="padding: 20px;">
                                    <h3 class="service-title" style="font-size: 20px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary); line-height: 1.4;">
                                        {{ $category->trans('name') }}
                                    </h3>
                                    @if($category->trans('description'))
                                        <p class="service-description" style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $category->trans('description') }}
                                        </p>
                                    @else
                                        <p class="service-description text-muted" style="font-size: 14px; color: var(--text-secondary); font-style: italic; opacity: 0.7;">
                                            {{ __('messages.no_description') ?? 'لا يوجد وصف' }}
                                        </p>
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
                <p class="text-center text-muted" style="padding: 60px;">
                    {{ __('messages.no_categories_available') ?? 'لا توجد خدمات متاحة' }}
                </p>
            </div>
        </div>
    @endif

    <!-- Section Modal -->
    <div class="modal-overlay" id="sectionModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="sectionModalTitle">{{ __('messages.add_services_section') ?? 'إضافة قسم الخدمات' }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('sectionModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="sectionModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Category Edit Modal -->
    <div class="modal-overlay" id="categoryModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="categoryModalTitle">{{ __('messages.edit_service') ?? 'تعديل الخدمة' }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('categoryModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="categoryModalBody">
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
                        
                        // Wait a bit for scripts to execute, then attach form handler
                        setTimeout(() => {
                            const form = modalBody.querySelector('#headerForm');
                            if (form) {
                                // Remove any existing listeners by cloning
                                const newForm = form.cloneNode(true);
                                form.parentNode.replaceChild(newForm, form);
                                
                                // Add new submit listener
                                newForm.addEventListener('submit', function (e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    console.log('Form submitted:', newForm.action, newForm.method);
                                    handleFormSubmit(newForm, modalId);
                                });
                                
                                console.log('Form handler attached');
                            } else {
                                console.error('Form #headerForm not found in modal');
                            }
                        }, 300);
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

        function openEditCategoryModal(categoryId, modalId, title) {
            const modal = document.getElementById(modalId);
            const modalBody = document.getElementById(modalId + 'Body');
            const modalTitle = document.getElementById(modalId + 'Title');

            if (modalTitle) {
                modalTitle.textContent = title;
            }

            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><span style="font-size: 24px; color: var(--primary-color);">{{ __('messages.loading') }}...</span></div>';
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);

            const url = '{{ route("admin.customer-facing.services-section.category.edit", ":id") }}'.replace(':id', categoryId);

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

        function handleFormSubmit(form, modalId) {
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';
            }

            // Determine method
            let method = form.method.toUpperCase();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                method = methodInput.value.toUpperCase();
            }

            console.log('Submitting form:', form.action, method);

            fetch(form.action, {
                method: method === 'PUT' || method === 'DELETE' ? 'POST' : method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                }
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        return response.json().then(data => {
                            console.error('Error response:', data);
                            const errorMsg = data.message || data.error || 'حدث خطأ';
                            throw new Error(errorMsg);
                        }).catch(err => {
                            // If JSON parsing fails, throw with status
                            throw new Error('حدث خطأ: ' + response.status + ' ' + response.statusText);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        if (data.message && typeof Toast !== 'undefined') {
                            Toast.success(data.message);
                        } else if (data.message) {
                            alert(data.message);
                        }
                        if (window.closeModal) {
                            window.closeModal(modalId);
                        }
                        setTimeout(() => {
                            // Check if we're in the customer-facing dashboard
                            const isInCustomerFacing = window.location.pathname.includes('/admin/customer-facing');
                            
                            if (data.redirect) {
                                // If redirect is to customer-facing main page, show sections grid
                                if (data.redirect.includes('/admin/customer-facing') && 
                                    !data.redirect.includes('/load-section/') &&
                                    !data.redirect.match(/\/admin\/customer-facing\/(navigation|hero|company-logo|footer|consultation-booking-section|technologies-section|services-section|ready-apps-section)$/)) {
                                    // Store flag to show sections grid after redirect
                                    sessionStorage.setItem('showSectionsGrid', 'true');
                                }
                                window.location.href = data.redirect;
                            } else {
                                // If we're in customer-facing dashboard, show sections grid before reload
                                if (isInCustomerFacing && 
                                    !window.location.pathname.includes('/load-section/') &&
                                    !window.location.pathname.match(/\/admin\/customer-facing\/(navigation|hero|company-logo|footer|consultation-booking-section|technologies-section|services-section|ready-apps-section)$/)) {
                                    sessionStorage.setItem('showSectionsGrid', 'true');
                                }
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
                        if (data.message) {
                            alert(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                    
                    // Try to get more details from the error
                    let errorMessage = 'حدث خطأ أثناء الحفظ';
                    if (error.message) {
                        errorMessage += ': ' + error.message;
                    } else if (error.toString) {
                        errorMessage += ': ' + error.toString();
                    }
                    
                    alert(errorMessage);
                });
        }
    </script>
@endsection


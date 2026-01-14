@extends('layouts.dashboard')

@section('title', __('messages.navigation_management'))
@section('page-title', __('messages.navigation_management'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.navigation_management') }}</h2>
            <p>{{ __('messages.manage_navigation_desc') }}</p>
        </div>
    </div>

    <!-- Logo Section -->
    <div class="card" style="margin-bottom: 30px;">
        <div class="card-header">
            <h3>{{ __('messages.logo') }}</h3>
            @if($logo)
                <button type="button" class="btn btn-primary btn-sm"
                    onclick="openCreateModal('{{ route('admin.customer-facing.navigation.logo.create') }}', 'logoModal', '{{ __('messages.edit_logo') }}')">
                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                </button>
            @else
                <button type="button" class="btn btn-primary btn-sm"
                    onclick="openCreateModal('{{ route('admin.customer-facing.navigation.logo.create') }}', 'logoModal', '{{ __('messages.add_logo') }}')">
                    <i class="fas fa-plus"></i> {{ __('messages.add_logo') }}
                </button>
            @endif
        </div>
        <div class="card-body">
            @if($logo)
                <div class="logo-preview" style="display: flex; align-items: center; gap: 15px;">
                    @if($logo->image)
                        <img src="{{ asset('storage/' . $logo->image) }}" alt="{{ $logo->trans('title') }}"
                            style="max-height: 60px;">
                    @endif
                    <div>
                        <strong>{{ $logo->trans('title') }}</strong>
                        @if($logo->link)
                            <br><small><a href="{{ $logo->link }}" target="_blank">{{ $logo->link }}</a></small>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-muted">{{ __('messages.no_logo_set') }}</p>
            @endif
        </div>
    </div>

    <!-- Menu Items Section -->
    <div class="page-header" style="margin-bottom: 20px;">
        <div class="page-header-left">
            <h3 style="margin: 0;">{{ __('messages.menu_items') }}</h3>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary"
                onclick="openCreateModal('{{ route('admin.customer-facing.navigation.menu-items.create') }}', 'menuItemModal', '{{ __('messages.add_menu_item') }}')">
                <i class="fas fa-plus"></i> {{ __('messages.add_menu_item') }}
            </button>
            <span class="total-count">{{ __('messages.total') }}: {{ $menuItems->count() }}</span>
        </div>
    </div>

    @if($menuItems->count() > 0)
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.title') }}</th>
                        <th>{{ __('messages.link') }}</th>
                        <th class="text-center">{{ __('messages.status') }}</th>
                        <th class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menuItems as $item)
                        <tr>
                            <td>{{ $item->trans('title') }}</td>
                            <td><a href="{{ $item->link ?? '#' }}" target="_blank">{{ Str::limit($item->link ?? '-', 50) }}</a></td>
                            <td class="text-center">
                                @if($item->is_active)
                                    <span class="status-pill completed">{{ __('messages.active') }}</span>
                                @else
                                    <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button type="button" class="calm-action-btn warning"
                                        onclick="openEditModal('{{ route('admin.customer-facing.navigation.menu-items.edit', $item) }}', 'menuItemModal', '{{ __('messages.edit_menu_item') }}')"
                                        title="{{ __('messages.edit') }}">
                                        <i class="far fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.customer-facing.navigation.destroy', $item) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="event.preventDefault(); Confirm.delete({{ json_encode(__('messages.delete_navigation_item_confirm')) }}, {{ json_encode(__('messages.confirm_delete_title')) }}).then(confirmed => { if(confirmed) this.submit(); }); return false;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="calm-action-btn danger" title="{{ __('messages.delete') }}">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="table-container">
            <p class="text-center text-muted" style="padding: 40px;">{{ __('messages.no_menu_items') }}</p>
        </div>
    @endif

    <!-- Buttons Section -->
    <div class="page-header" style="margin-top: 40px; margin-bottom: 20px;">
        <div class="page-header-left">
            <h3 style="margin: 0;">{{ __('messages.buttons') }}</h3>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary"
                onclick="openCreateModal('{{ route('admin.customer-facing.navigation.buttons.create') }}', 'buttonModal', '{{ __('messages.add_button') }}')">
                <i class="fas fa-plus"></i> {{ __('messages.add_button') }}
            </button>
            <span class="total-count">{{ __('messages.total') }}: {{ $buttons->count() }}</span>
        </div>
    </div>

    @if($buttons->count() > 0)
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.title') }}</th>
                        <th>{{ __('messages.link') }}</th>
                        <th class="text-center">{{ __('messages.status') }}</th>
                        <th class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($buttons as $button)
                        <tr>
                            <td>{{ $button->trans('title') }}</td>
                            <td><a href="{{ $button->link }}" target="_blank">{{ Str::limit($button->link, 50) }}</a></td>
                            <td class="text-center">
                                @if($button->is_active)
                                    <span class="status-pill completed">{{ __('messages.active') }}</span>
                                @else
                                    <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button type="button" class="calm-action-btn warning"
                                        onclick="openEditModal('{{ route('admin.customer-facing.navigation.buttons.edit', $button) }}', 'buttonModal', '{{ __('messages.edit_button') }}')"
                                        title="{{ __('messages.edit') }}">
                                        <i class="far fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.customer-facing.navigation.destroy', $button) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="event.preventDefault(); Confirm.delete({{ json_encode(__('messages.delete_navigation_item_confirm')) }}, {{ json_encode(__('messages.confirm_delete_title')) }}).then(confirmed => { if(confirmed) this.submit(); }); return false;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="calm-action-btn danger" title="{{ __('messages.delete') }}">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="table-container">
            <p class="text-center text-muted" style="padding: 40px;">{{ __('messages.no_buttons') }}</p>
        </div>
    @endif

    <!-- Modals -->
    <!-- Logo Modal -->
    <div class="modal-overlay" id="logoModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="logoModalTitle">{{ __('messages.add_logo') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('logoModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="logoModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Menu Item Modal -->
    <div class="modal-overlay" id="menuItemModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="menuItemModalTitle">{{ __('messages.add_menu_item') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('menuItemModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="menuItemModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Button Modal -->
    <div class="modal-overlay" id="buttonModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="buttonModalTitle">{{ __('messages.add_button') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('buttonModal')">
                    ×
                </button>
            </div>
            <div class="modal-body" id="buttonModalBody">
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
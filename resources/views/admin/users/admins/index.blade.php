@extends('layouts.dashboard')

@section('title', __('messages.admins_list'))
@section('page-title', __('messages.admins_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.admins_list') }}</h2>
            <p>{{ __('messages.manage_admins_all_desc') }}</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary" onclick="openModal('addAdminModal')">
                <i class="fas fa-user-plus"></i> {{ __('messages.add_admin') }}
            </button>
            <span class="total-count">{{ __('messages.all_admins_count') }}: {{ $users->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.users.admins') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search_admin_label') }}:</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}"
                        placeholder="{{ __('messages.search_admin_placeholder') }}" class="filter-input">
                </div>
            </div>
            <div class="filter-actions" style="margin-inline-start: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> {{ __('messages.search') }}
                </button>
                <a href="{{ route('admin.users.admins') }}" class="btn btn-secondary">
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
                    <th>{{ __('messages.email') }}</th>
                    <th>{{ __('messages.phone') }}</th>
                    <th class="text-center">{{ __('messages.role') }}</th>
                    <th>{{ __('messages.registration_date') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td class="text-center">
                            <span class="status-pill active">
                                <i class="fas fa-user-shield" style="margin-left: 5px;"></i> {{ __('messages.admin_role') }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.users.admins.show', $user) }}" class="calm-action-btn"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                                <button type="button" class="calm-action-btn warning" title="{{ __('messages.edit') }}"
                                    onclick="openEditAdminModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->phone }}')">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.users.admins.delete', $user) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_admin_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="calm-action-btn danger" title="{{ __('messages.delete') }}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('messages.no_admins_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $users->links('vendor.pagination.custom', ['itemName' => 'admins']) }}
        </div>
    </div>

    <!-- Add Admin Modal -->
    <x-modal modalId="addAdminModal" title="{{ __('messages.add_new_admin') }}" formId="addAdminForm">
        <form id="addAdminForm" action="{{ route('admin.users.admins.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="add_name">{{ __('messages.full_name') }} <span class="required">*</span></label>
                <input type="text" id="add_name" name="name" value="{{ old('name') }}" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_email">{{ __('messages.email') }} <span class="required">*</span></label>
                <input type="email" id="add_email" name="email" value="{{ old('email') }}" class="form-control" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_phone">{{ __('messages.phone') }} <span class="required">*</span></label>
                <input type="text" id="add_phone" name="phone" value="{{ old('phone') }}" class="form-control" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_password">{{ __('messages.password') }} <span class="required">*</span></label>
                <input type="password" id="add_password" name="password" class="form-control" required minlength="8">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </form>
    </x-modal>

    <!-- Edit Admin Modal -->
    <x-modal modalId="editAdminModal" title="{{ __('messages.edit') }} {{ __('messages.admin_role') }}"
        formId="editAdminForm">
        <form id="editAdminForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_name">{{ __('messages.full_name') }} <span class="required">*</span></label>
                <input type="text" id="edit_name" name="name" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_email">{{ __('messages.email') }} <span class="required">*</span></label>
                <input type="email" id="edit_email" name="email" class="form-control" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_phone">{{ __('messages.phone') }} <span class="required">*</span></label>
                <input type="text" id="edit_phone" name="phone" class="form-control" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_password">{{ __('messages.password') }} ({{ __('messages.password_help_edit') }})</label>
                <input type="password" id="edit_password" name="password" class="form-control" minlength="8">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </form>
    </x-modal>

    @push('styles')
        <style>
            /* Admin Modal Form Styling */
            #addAdminModal .modal-body,
            #editAdminModal .modal-body {
                padding: 28px;
            }

            #addAdminModal .form-group,
            #editAdminModal .form-group {
                margin-bottom: 20px;
            }

            #addAdminModal .form-group:last-child,
            #editAdminModal .form-group:last-child {
                margin-bottom: 0;
            }

            /* Form labels */
            #addAdminModal .form-group label,
            #editAdminModal .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: var(--text-primary, #343a40);
                font-size: 14px;
            }

            /* Form inputs */
            #addAdminModal .form-control,
            #editAdminModal .form-control {
                width: 100% !important;
                padding: 12px 16px !important;
                border: 2px solid #e5e7eb !important;
                border-radius: 10px !important;
                font-size: 14px !important;
                font-family: 'Cairo', sans-serif !important;
                transition: all 0.3s !important;
                background: white !important;
                box-sizing: border-box !important;
            }

            #addAdminModal .form-control:focus,
            #editAdminModal .form-control:focus {
                outline: none !important;
                border-color: var(--primary-color, #6658dd) !important;
                box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1) !important;
            }

            #addAdminModal .form-control:hover,
            #editAdminModal .form-control:hover {
                border-color: #d1d5db !important;
            }

            /* Modal footer */
            #addAdminModal .modal-footer,
            #editAdminModal .modal-footer {
                padding: 20px 28px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                background: #f9fafb;
            }

            #addAdminModal .modal-footer .btn,
            #editAdminModal .modal-footer .btn {
                padding: 10px 20px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            #addAdminModal .modal-footer .btn-primary,
            #editAdminModal .modal-footer .btn-primary {
                background: var(--primary-color, #6658dd);
                color: white;
            }

            #addAdminModal .modal-footer .btn-primary:hover,
            #editAdminModal .modal-footer .btn-primary:hover {
                background: var(--primary-dark, #564ab1);
            }

            #addAdminModal .modal-footer .btn-secondary,
            #editAdminModal .modal-footer .btn-secondary {
                background: #e5e7eb;
                color: #6b7280;
            }

            #addAdminModal .modal-footer .btn-secondary:hover,
            #editAdminModal .modal-footer .btn-secondary:hover {
                background: #d1d5db;
            }

            /* Dark Mode Styles */
            [data-theme="dark"] #addAdminModal .form-group label,
            [data-theme="dark"] #editAdminModal .form-group label {
                color: var(--text-primary, #f1f5f9) !important;
            }

            [data-theme="dark"] #addAdminModal .form-control,
            [data-theme="dark"] #editAdminModal .form-control {
                background: var(--sidebar-active-bg, #15171d) !important;
                border-color: var(--border-color, #2a2d3a) !important;
                color: var(--text-primary, #f1f5f9) !important;
            }

            [data-theme="dark"] #addAdminModal .form-control:focus,
            [data-theme="dark"] #editAdminModal .form-control:focus {
                border-color: var(--primary-color, #6658dd) !important;
                box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1) !important;
                background: var(--sidebar-active-bg, #15171d) !important;
                color: var(--text-primary, #f1f5f9) !important;
            }

            [data-theme="dark"] #addAdminModal .form-control:hover,
            [data-theme="dark"] #editAdminModal .form-control:hover {
                border-color: var(--border-color, #2a2d3a) !important;
            }

            [data-theme="dark"] #addAdminModal .modal-footer,
            [data-theme="dark"] #editAdminModal .modal-footer {
                background: var(--card-bg, #1e1f27) !important;
                border-top-color: var(--border-color, #2a2d3a) !important;
            }

            [data-theme="dark"] #addAdminModal .modal-footer .btn-secondary,
            [data-theme="dark"] #editAdminModal .modal-footer .btn-secondary {
                background: var(--sidebar-active-bg, #15171d) !important;
                color: var(--text-secondary, #94a3b8) !important;
                border: 1px solid var(--border-color, #2a2d3a) !important;
            }

            [data-theme="dark"] #addAdminModal .modal-footer .btn-secondary:hover,
            [data-theme="dark"] #editAdminModal .modal-footer .btn-secondary:hover {
                background: var(--bg-light, #15171d) !important;
                color: var(--text-primary, #f1f5f9) !important;
            }

            /* Mobile: Table Borders - Stronger */
            @media (max-width: 767px) {
                .data-table tr {
                    border: 3px solid #9ca3af !important;
                    border-radius: 20px !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
                }

                [data-theme="dark"] .data-table tr {
                    border: 3px solid #4b5563 !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4) !important;
                }

                .data-table td {
                    border-bottom: 2px solid #d1d5db !important;
                }

                [data-theme="dark"] .data-table td {
                    border-bottom: 2px solid #4b5563 !important;
                }

                .data-table td:last-child {
                    border-bottom: none !important;
                }

                /* Actions column - last row, aligned to start (first column) */
                .data-table td:last-child {
                    display: flex !important;
                    justify-content: flex-start !important;
                    background: #f8fafc !important;
                    padding: 14px 20px !important;
                    margin: 0 !important;
                    border-bottom: none !important;
                    grid-template-columns: 1fr !important;
                }

                [data-theme="dark"] .data-table td:last-child {
                    background: var(--sidebar-active-bg, #15171d) !important;
                }

                html[dir='rtl'] .data-table td:last-child {
                    justify-content: flex-end !important;
                }

                .data-table td:last-child::before {
                    display: none !important;
                }

                .data-table td:last-child > div {
                    display: flex !important;
                    gap: 8px !important;
                    flex-wrap: wrap !important;
                    justify-content: flex-start !important;
                    width: 100% !important;
                    align-items: center !important;
                }

                html[dir='rtl'] .data-table td:last-child > div {
                    justify-content: flex-end !important;
                }

                /* Smaller action buttons */
                .data-table .calm-action-btn {
                    width: 36px !important;
                    height: 36px !important;
                    min-width: 36px !important;
                    padding: 0 !important;
                    border-radius: 10px !important;
                    font-size: 14px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }

                .data-table .calm-action-btn i {
                    font-size: 14px !important;
                    margin: 0 !important;
                }

                .data-table .calm-action-btn.warning {
                    background: #fffbeb !important;
                    color: #d97706 !important;
                    border-color: #fef3c7 !important;
                }

                .data-table .calm-action-btn.danger {
                    background: #fff1f2 !important;
                    color: #e11d48 !important;
                    border-color: #fecaca !important;
                }

                .data-table td:last-child > div form {
                    display: inline-block !important;
                    margin: 0 !important;
                }
            }

            /* Extra Small Screens */
            @media (max-width: 575px) {
                .data-table .calm-action-btn {
                    width: 32px !important;
                    height: 32px !important;
                    min-width: 32px !important;
                    font-size: 13px !important;
                }

                .data-table .calm-action-btn i {
                    font-size: 13px !important;
                }

                .data-table td:last-child {
                    padding: 12px 20px !important;
                }

                .data-table td:last-child > div {
                    gap: 6px !important;
                }
            }

            /* Mobile: Smaller Filter Buttons */
            @media (max-width: 767px) {
                .filter-actions .btn {
                    height: 38px !important;
                    padding: 0 16px !important;
                    font-size: 13px !important;
                    min-width: 80px !important;
                    gap: 6px !important;
                }

                .filter-actions .btn i {
                    font-size: 12px !important;
                }
            }

            @media (max-width: 575px) {
                .filter-actions .btn {
                    height: 36px !important;
                    padding: 0 12px !important;
                    font-size: 12px !important;
                    min-width: 70px !important;
                    gap: 4px !important;
                }

                .filter-actions .btn i {
                    font-size: 11px !important;
                }

                .filter-actions {
                    gap: 8px !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function openEditAdminModal(id, name, email, phone) {
                document.getElementById('editAdminForm').action = '{{ route("admin.users.admins.update", ":id") }}'.replace(':id', id);
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_phone').value = phone || '';
                document.getElementById('edit_password').value = '';
                openModal('editAdminModal');
            }

            // Handle form submission success
            document.getElementById('addAdminForm')?.addEventListener('submit', function (e) {
                const form = this;
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeModal('addAdminModal');
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });

            document.getElementById('editAdminForm')?.addEventListener('submit', function (e) {
                const form = this;
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeModal('editAdminModal');
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        </script>
    @endpush
@endsection
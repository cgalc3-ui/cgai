@extends('layouts.dashboard')

@section('title', __('messages.customers_list'))
@section('page-title', __('messages.customers_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.customers_list') }}</h2>
            <p>{{ __('messages.manage_customers_all_desc') }}</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary" onclick="openModal('addCustomerModal')">
                <i class="fas fa-user-plus"></i> {{ __('messages.add_customer') }}
            </button>
            <span class="total-count">{{ __('messages.all_customers_count') }}: {{ $users->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.users.customers') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search_customer_label') }}:</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}"
                        placeholder="{{ __('messages.search_customer_placeholder') }}" class="filter-input">
                </div>
            </div>
            <div class="filter-actions" style="margin-inline-start: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> {{ __('messages.search') }}
                </button>
                <a href="{{ route('admin.users.customers') }}" class="btn btn-secondary">
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
                                <i class="fas fa-user" style="margin-left: 5px;"></i> {{ __('messages.customer_role') }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.users.customers.show', $user) }}" class="calm-action-btn"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                                <button type="button" class="calm-action-btn warning" title="{{ __('messages.edit') }}"
                                    onclick="openEditCustomerModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email ?? '') }}', '{{ addslashes($user->phone ?? '') }}', '{{ $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '' }}', '{{ $user->gender ?? '' }}')">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.users.customers.delete', $user) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_customer_confirm') }}')">
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
                        <td colspan="6" class="text-center">{{ __('messages.no_customers_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Add Customer Modal -->
    <x-modal modalId="addCustomerModal" title="{{ __('messages.add_new_customer') }}" formId="addCustomerForm">
        <form id="addCustomerForm" action="{{ route('admin.users.customers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="add_customer_name">{{ __('messages.full_name') }} <span
                        class="required">{{ __('messages.required_field') }}</span></label>
                <input type="text" id="add_customer_name" name="name" value="{{ old('name') }}" class="form-control"
                    required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_customer_email">{{ __('messages.email') }}</label>
                <input type="email" id="add_customer_email" name="email" value="{{ old('email') }}" class="form-control">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_customer_phone">{{ __('messages.phone') }} <span
                        class="required">{{ __('messages.required_field') }}</span></label>
                <input type="text" id="add_customer_phone" name="phone" value="{{ old('phone') }}" class="form-control"
                    required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="add_customer_date_of_birth">{{ __('messages.date_of_birth') }}</label>
                    <div class="date-input-wrapper">
                        <input type="date" id="add_customer_date_of_birth" name="date_of_birth"
                            value="{{ old('date_of_birth') }}" class="form-control" max="{{ date('Y-m-d') }}">
                        <i class="fas fa-calendar-alt date-icon"></i>
                    </div>
                    @error('date_of_birth')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="add_customer_gender">{{ __('messages.gender') }}</label>
                    <select id="add_customer_gender" name="gender" class="form-control">
                        <option value="">{{ __('messages.select_gender') }}</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}
                        </option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('messages.female') }}
                        </option>
                    </select>
                    @error('gender')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </form>
    </x-modal>

    <!-- Edit Customer Modal -->
    <x-modal modalId="editCustomerModal" title="{{ __('messages.edit') }} {{ __('messages.customer_role') }}"
        formId="editCustomerForm">
        <form id="editCustomerForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_customer_name">{{ __('messages.full_name') }} <span
                        class="required">{{ __('messages.required_field') }}</span></label>
                <input type="text" id="edit_customer_name" name="name" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_customer_email">{{ __('messages.email') }}</label>
                <input type="email" id="edit_customer_email" name="email" class="form-control">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_customer_phone">{{ __('messages.phone') }} <span
                        class="required">{{ __('messages.required_field') }}</span></label>
                <input type="text" id="edit_customer_phone" name="phone" class="form-control" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit_customer_date_of_birth">{{ __('messages.date_of_birth') }}</label>
                    <div class="date-input-wrapper">
                        <input type="date" id="edit_customer_date_of_birth" name="date_of_birth" class="form-control"
                            max="{{ date('Y-m-d') }}">
                        <i class="fas fa-calendar-alt date-icon"></i>
                    </div>
                    @error('date_of_birth')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="edit_customer_gender">{{ __('messages.gender') }}</label>
                    <select id="edit_customer_gender" name="gender" class="form-control">
                        <option value="">{{ __('messages.select_gender') }}</option>
                        <option value="male">{{ __('messages.male') }}</option>
                        <option value="female">{{ __('messages.female') }}</option>
                    </select>
                    @error('gender')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </form>
    </x-modal>

    @push('styles')
        <style>
            /* Customer Modal Form Styling */
            #addCustomerModal .modal-body,
            #editCustomerModal .modal-body {
                padding: 28px;
            }
            
            #addCustomerModal .form-group,
            #editCustomerModal .form-group {
                margin-bottom: 20px;
            }
            
            #addCustomerModal .form-group:last-child,
            #editCustomerModal .form-group:last-child,
            #addCustomerModal .form-row:last-child,
            #editCustomerModal .form-row:last-child {
                margin-bottom: 0;
            }
            
            /* Date and Gender fields styling */
            #addCustomerModal .form-row,
            #editCustomerModal .form-row {
                display: flex !important;
                gap: 12px !important;
                align-items: flex-end !important;
                width: 100% !important;
                max-width: 100% !important;
                margin-bottom: 20px;
            }
            
            #addCustomerModal .form-row .form-group,
            #editCustomerModal .form-row .form-group {
                flex: 1 1 0 !important;
                min-width: 0 !important;
                margin-bottom: 0 !important;
            }
            
            /* Form labels */
            #addCustomerModal .form-group label,
            #editCustomerModal .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: var(--text-primary, #343a40);
                font-size: 14px;
            }
            
            /* Form inputs */
            #addCustomerModal .form-control,
            #editCustomerModal .form-control {
                width: 100%;
                padding: 12px 16px;
                border: 2px solid #e5e7eb !important;
                border-radius: 10px;
                font-size: 14px;
                font-family: 'Cairo', sans-serif;
                transition: all 0.3s;
                background: white;
                box-sizing: border-box;
            }
            
            #addCustomerModal .form-control:focus,
            #editCustomerModal .form-control:focus {
                outline: none !important;
                border-color: var(--primary-color, #6658dd) !important;
                box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1);
            }
            
            #addCustomerModal .form-control:hover,
            #editCustomerModal .form-control:hover {
                border-color: #d1d5db;
            }
            
            /* Date input wrapper */
            #addCustomerModal .date-input-wrapper,
            #editCustomerModal .date-input-wrapper {
                position: relative;
                width: 100%;
            }
            
            #addCustomerModal .date-input-wrapper .date-icon,
            #editCustomerModal .date-input-wrapper .date-icon {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #6b7280;
                pointer-events: none;
            }
            
            html[dir='ltr'] #addCustomerModal .date-input-wrapper .date-icon,
            html[dir='ltr'] #editCustomerModal .date-input-wrapper .date-icon {
                right: auto;
                left: 12px;
            }
            
            /* Modal footer */
            #addCustomerModal .modal-footer,
            #editCustomerModal .modal-footer {
                padding: 20px 28px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                background: #f9fafb;
            }
            
            #addCustomerModal .modal-footer .btn,
            #editCustomerModal .modal-footer .btn {
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
            
            #addCustomerModal .modal-footer .btn-primary,
            #editCustomerModal .modal-footer .btn-primary {
                background: var(--primary-color, #6658dd);
                color: white;
            }
            
            #addCustomerModal .modal-footer .btn-primary:hover,
            #editCustomerModal .modal-footer .btn-primary:hover {
                background: var(--primary-dark, #564ab1);
            }
            
            #addCustomerModal .modal-footer .btn-secondary,
            #editCustomerModal .modal-footer .btn-secondary {
                background: #e5e7eb;
                color: #6b7280;
            }
            
            #addCustomerModal .modal-footer .btn-secondary:hover,
            #editCustomerModal .modal-footer .btn-secondary:hover {
                background: #d1d5db;
            }

            /* Dark Mode Styles */
            [data-theme="dark"] #addCustomerModal .form-group label,
            [data-theme="dark"] #editCustomerModal .form-group label {
                color: var(--text-primary, #f1f5f9) !important;
            }

            [data-theme="dark"] #addCustomerModal .form-control,
            [data-theme="dark"] #editCustomerModal .form-control {
                background: var(--sidebar-active-bg, #15171d) !important;
                border-color: var(--border-color, #2a2d3a) !important;
                color: var(--text-primary, #f1f5f9) !important;
            }

            [data-theme="dark"] #addCustomerModal .form-control:focus,
            [data-theme="dark"] #editCustomerModal .form-control:focus {
                border-color: var(--primary-color, #6658dd) !important;
                box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1) !important;
                background: var(--sidebar-active-bg, #15171d) !important;
                color: var(--text-primary, #f1f5f9) !important;
            }

            [data-theme="dark"] #addCustomerModal .form-control:hover,
            [data-theme="dark"] #editCustomerModal .form-control:hover {
                border-color: var(--border-color, #2a2d3a) !important;
            }

            [data-theme="dark"] #addCustomerModal select.form-control,
            [data-theme="dark"] #editCustomerModal select.form-control {
                background: var(--sidebar-active-bg, #15171d) !important;
                border-color: var(--border-color, #2a2d3a) !important;
                color: var(--text-primary, #f1f5f9) !important;
                background-image: none !important;
                padding-right: 16px !important;
                padding-left: 16px !important;
            }

            [data-theme="dark"] #addCustomerModal select.form-control:focus,
            [data-theme="dark"] #editCustomerModal select.form-control:focus {
                background: var(--sidebar-active-bg, #15171d) !important;
                border-color: var(--primary-color, #6658dd) !important;
                color: var(--text-primary, #f1f5f9) !important;
            }

            [data-theme="dark"] #addCustomerModal select.form-control option,
            [data-theme="dark"] #editCustomerModal select.form-control option {
                background: var(--sidebar-active-bg, #15171d) !important;
                color: var(--text-primary, #f1f5f9) !important;
            }

            [data-theme="dark"] #addCustomerModal .date-input-wrapper .date-icon,
            [data-theme="dark"] #editCustomerModal .date-input-wrapper .date-icon {
                color: var(--text-secondary, #94a3b8) !important;
            }

            [data-theme="dark"] #addCustomerModal .modal-footer,
            [data-theme="dark"] #editCustomerModal .modal-footer {
                background: var(--card-bg, #1e1f27) !important;
                border-top-color: var(--border-color, #2a2d3a) !important;
            }

            [data-theme="dark"] #addCustomerModal .modal-footer .btn-secondary,
            [data-theme="dark"] #editCustomerModal .modal-footer .btn-secondary {
                background: var(--sidebar-active-bg, #15171d) !important;
                color: var(--text-secondary, #94a3b8) !important;
                border: 1px solid var(--border-color, #2a2d3a) !important;
            }

            [data-theme="dark"] #addCustomerModal .modal-footer .btn-secondary:hover,
            [data-theme="dark"] #editCustomerModal .modal-footer .btn-secondary:hover {
                background: var(--bg-light, #15171d) !important;
                color: var(--text-primary, #f1f5f9) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function openEditCustomerModal(id, name, email, phone, dateOfBirth, gender) {
                document.getElementById('editCustomerForm').action = '{{ route("admin.users.customers.update", ":id") }}'.replace(':id', id);
                document.getElementById('edit_customer_name').value = name;
                document.getElementById('edit_customer_email').value = email || '';
                document.getElementById('edit_customer_phone').value = phone || '';
                document.getElementById('edit_customer_date_of_birth').value = dateOfBirth || '';
                document.getElementById('edit_customer_gender').value = gender || '';
                openModal('editCustomerModal');
            }
        </script>
    @endpush
@endsection
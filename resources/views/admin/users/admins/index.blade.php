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
            <div class="filter-actions">
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
            {{ $users->links() }}
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
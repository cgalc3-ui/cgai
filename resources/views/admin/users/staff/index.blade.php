@extends('layouts.dashboard')

@section('title', __('messages.staff_list'))
@section('page-title', __('messages.staff_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.staff_list') }}</h2>
            <p>{{ __('messages.manage_staff_all_desc') }}</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary" onclick="openModal('addStaffModal')">
                <i class="fas fa-user-plus"></i> {{ __('messages.add_staff') }}
            </button>
            <span class="total-count">{{ __('messages.all_staff_count') }}: {{ $users->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('admin.users.staff') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search_staff_label') }}:</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}"
                        placeholder="{{ __('messages.search_staff_placeholder') }}" class="filter-input">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> {{ __('messages.search') }}
                </button>
                <a href="{{ route('admin.users.staff') }}" class="btn btn-secondary">
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
                    <th>{{ __('messages.specializations') }}</th>
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
                                <i class="fas fa-user-tie" style="margin-left: 5px;"></i> {{ __('messages.staff_role') }}
                            </span>
                        </td>
                        <td>
                            @if($user->employee && $user->employee->categories->count() > 0)
                                @foreach($user->employee->categories->take(2) as $cat)
                                    <span class="status-pill active" style="font-size: 10px; padding: 2px 8px;">{{ $cat->name }}</span>
                                @endforeach
                                @if($user->employee->categories->count() > 2)
                                    <span class="text-muted"
                                        style="font-size: 11px;">+{{ $user->employee->categories->count() - 2 }}</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.users.staff.show', $user) }}" class="calm-action-btn"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                                <button type="button" class="calm-action-btn warning" title="{{ __('messages.edit') }}"
                                    onclick="openEditStaffModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ addslashes($user->phone ?? '') }}', {{ $user->employee ? json_encode($user->employee->categories->pluck('id')->toArray()) : '[]' }}, '{{ addslashes($user->employee->bio ?? '') }}', {{ $user->employee->hourly_rate ?? 'null' }}, {{ $user->employee && $user->employee->is_available ? 'true' : 'false' }})">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.users.staff.delete', $user) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_staff_confirm') }}')">
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
                        <td colspan="7" class="text-center">{{ __('messages.no_staff_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Add Staff Modal -->
    <x-modal modalId="addStaffModal" title="{{ __('messages.add_new_staff') }}" formId="addStaffForm">
        <form id="addStaffForm" action="{{ route('admin.users.staff.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="add_staff_name">{{ __('messages.full_name') }} <span class="required">*</span></label>
                <input type="text" id="add_staff_name" name="name" value="{{ old('name') }}" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_staff_email">{{ __('messages.email') }} <span class="required">*</span></label>
                <input type="email" id="add_staff_email" name="email" value="{{ old('email') }}" class="form-control"
                    required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_staff_phone">{{ __('messages.phone') }} <span class="required">*</span></label>
                <input type="text" id="add_staff_phone" name="phone" value="{{ old('phone') }}" class="form-control"
                    required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_staff_password">{{ __('messages.password') }} <span class="required">*</span></label>
                <input type="password" id="add_staff_password" name="password" class="form-control" required minlength="8">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_staff_categories_select">{{ __('messages.categories_specializations') }}</label>
                <select id="add_staff_categories_select" class="form-control">
                    <option value="">{{ __('messages.select_category_placeholder') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-name="{{ $category->name }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <div id="add_staff_selected_categories" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;">
                </div>
            </div>

            <div class="form-group">
                <label for="add_staff_bio">{{ __('messages.bio') }}</label>
                <textarea id="add_staff_bio" name="bio" rows="3" class="form-control"
                    placeholder="{{ __('messages.bio_placeholder') }}">{{ old('bio') }}</textarea>
            </div>

            <div class="form-group">
                <label for="add_staff_hourly_rate">{{ __('messages.hourly_rate_sar') }}</label>
                <input type="number" id="add_staff_hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}"
                    step="0.01" min="0" class="form-control">
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                    <span>{{ __('messages.employee_available') }}</span>
                </label>
            </div>
        </form>
    </x-modal>

    <!-- Edit Staff Modal -->
    <x-modal modalId="editStaffModal" title="{{ __('messages.edit') }} {{ __('messages.staff_role') }}"
        formId="editStaffForm">
        <form id="editStaffForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_staff_name">{{ __('messages.full_name') }} <span class="required">*</span></label>
                <input type="text" id="edit_staff_name" name="name" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_staff_email">{{ __('messages.email') }} <span class="required">*</span></label>
                <input type="email" id="edit_staff_email" name="email" class="form-control" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_staff_phone">{{ __('messages.phone') }} <span class="required">*</span></label>
                <input type="text" id="edit_staff_phone" name="phone" class="form-control" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_staff_password">{{ __('messages.password') }}
                    ({{ __('messages.password_help_edit') }})</label>
                <input type="password" id="edit_staff_password" name="password" class="form-control" minlength="8">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_staff_categories_select">{{ __('messages.categories_specializations') }}</label>
                <select id="edit_staff_categories_select" class="form-control">
                    <option value="">{{ __('messages.select_category_placeholder') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-name="{{ $category->name }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <div id="edit_staff_selected_categories"
                    style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;"></div>
            </div>

            <div class="form-group">
                <label for="edit_staff_bio">{{ __('messages.bio') }}</label>
                <textarea id="edit_staff_bio" name="bio" rows="3" class="form-control"
                    placeholder="{{ __('messages.bio_placeholder') }}"></textarea>
            </div>

            <div class="form-group">
                <label for="edit_staff_hourly_rate">{{ __('messages.hourly_rate_sar') }}</label>
                <input type="number" id="edit_staff_hourly_rate" name="hourly_rate" step="0.01" min="0"
                    class="form-control">
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="edit_staff_is_available" name="is_available" value="1">
                    <span>{{ __('messages.employee_available') }}</span>
                </label>
            </div>
        </form>
    </x-modal>

    @push('styles')
        <style>
            .category-tag {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background-color: #007bff;
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 14px;
            }

            .category-tag .remove-cat {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 0;
                margin: 0;
                font-size: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                transition: background-color 0.2s;
            }

            .category-tag .remove-cat:hover {
                background-color: rgba(255, 255, 255, 0.2);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Add Staff Modal - Categories
            document.getElementById('add_staff_categories_select').addEventListener('change', function () {
                const select = this;
                const selectedId = select.value;
                const selectedOption = select.options[select.selectedIndex];

                if (!selectedId) return;

                const selectedName = selectedOption.getAttribute('data-name');
                const container = document.getElementById('add_staff_selected_categories');

                // Check if already selected
                const existingTags = container.querySelectorAll('.category-tag');
                for (let tag of existingTags) {
                    if (tag.getAttribute('data-id') === selectedId) {
                        select.value = '';
                        return;
                    }
                }

                // Create new tag
                const tagDiv = document.createElement('div');
                tagDiv.className = 'category-tag';
                tagDiv.setAttribute('data-id', selectedId);
                tagDiv.innerHTML = `
                                                    <input type="hidden" name="employee[categories][]" value="${selectedId}">
                                                    <span>${selectedName}</span>
                                                    <button type="button" class="remove-cat" onclick="removeCategory(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                `;

                container.appendChild(tagDiv);
                select.value = '';
            });

            // Edit Staff Modal - Categories
            document.getElementById('edit_staff_categories_select').addEventListener('change', function () {
                const select = this;
                const selectedId = select.value;
                const selectedOption = select.options[select.selectedIndex];

                if (!selectedId) return;

                const selectedName = selectedOption.getAttribute('data-name');
                const container = document.getElementById('edit_staff_selected_categories');

                // Check if already selected
                const existingTags = container.querySelectorAll('.category-tag');
                for (let tag of existingTags) {
                    if (tag.getAttribute('data-id') === selectedId) {
                        select.value = '';
                        return;
                    }
                }

                // Create new tag
                const tagDiv = document.createElement('div');
                tagDiv.className = 'category-tag';
                tagDiv.setAttribute('data-id', selectedId);
                tagDiv.innerHTML = `
                                                    <input type="hidden" name="employee[categories][]" value="${selectedId}">
                                                    <span>${selectedName}</span>
                                                    <button type="button" class="remove-cat" onclick="removeCategory(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                `;

                container.appendChild(tagDiv);
                select.value = '';
            });

            function removeCategory(button) {
                const tag = button.closest('.category-tag');
                tag.remove();
            }

            function openEditStaffModal(id, name, email, phone, categories, bio, hourlyRate, isAvailable) {
                document.getElementById('editStaffForm').action = '{{ route("admin.users.staff.update", ":id") }}'.replace(':id', id);
                document.getElementById('edit_staff_name').value = name;
                document.getElementById('edit_staff_email').value = email;
                document.getElementById('edit_staff_phone').value = phone || '';
                document.getElementById('edit_staff_password').value = '';
                document.getElementById('edit_staff_bio').value = bio || '';
                document.getElementById('edit_staff_hourly_rate').value = hourlyRate || '';
                document.getElementById('edit_staff_is_available').checked = isAvailable;

                // Reset and set categories
                const container = document.getElementById('edit_staff_selected_categories');
                container.innerHTML = '';

                if (categories && Array.isArray(categories) && categories.length > 0) {
                    const catSelect = document.getElementById('edit_staff_categories_select');
                    categories.forEach(catId => {
                        const option = catSelect.querySelector(`option[value="${catId}"]`);
                        if (option) {
                            const catName = option.getAttribute('data-name');
                            const tagDiv = document.createElement('div');
                            tagDiv.className = 'category-tag';
                            tagDiv.setAttribute('data-id', catId);
                            tagDiv.innerHTML = `
                                                                <input type="hidden" name="employee[categories][]" value="${catId}">
                                                                <span>${catName}</span>
                                                                <button type="button" class="remove-cat" onclick="removeCategory(this)">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            `;
                            container.appendChild(tagDiv);
                        }
                    });
                }

                openModal('editStaffModal');
            }

            // Clear add modal when closed
            document.addEventListener('DOMContentLoaded', function () {
                const addModal = document.getElementById('addStaffModal');
                if (addModal) {
                    addModal.addEventListener('hidden.bs.modal', function () {
                        document.getElementById('addStaffForm').reset();
                        document.getElementById('add_staff_selected_categories').innerHTML = '';
                    });
                }
            });
        </script>
    @endpush
@endsection
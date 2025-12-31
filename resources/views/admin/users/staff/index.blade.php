@extends('layouts.dashboard')

@section('title', 'إدارة الموظفين')
@section('page-title', 'قائمة الموظفين')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة الموظفين</h2>
            <p>إدارة وعرض جميع الموظفين في مكان واحد</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary" onclick="openModal('addStaffModal')">
                <i class="fas fa-user-plus"></i> إضافة موظف
            </button>
            <span class="total-count">جميع الموظفين: {{ $users->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('admin.users.staff') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> بحث عن موظف:</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}"
                        placeholder="ابحث بالاسم، البريد أو الهاتف..." class="filter-input">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> بحث
                </button>
                <a href="{{ route('admin.users.staff') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> مسح
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>رقم الهاتف</th>
                    <th>الدور</th>
                    <th>التخصصات</th>
                    <th>تاريخ التسجيل</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            <span class="status-pill active">
                                <i class="fas fa-user-tie" style="margin-left: 5px;"></i> موظف
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
                        <td>
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.users.staff.show', $user) }}" class="calm-action-btn" title="عرض">
                                    <i class="far fa-eye"></i>
                                </a>
                                <button type="button" class="calm-action-btn warning" title="تعديل"
                                    onclick="openEditStaffModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ addslashes($user->phone ?? '') }}', {{ $user->employee ? json_encode($user->employee->categories->pluck('id')->toArray()) : '[]' }}, '{{ addslashes($user->employee->bio ?? '') }}', {{ $user->employee->hourly_rate ?? 'null' }}, {{ $user->employee && $user->employee->is_available ? 'true' : 'false' }})">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.users.staff.delete', $user) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="calm-action-btn danger" title="حذف">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">لا يوجد موظفين مسجلين</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Add Staff Modal -->
    <x-modal modalId="addStaffModal" title="إضافة موظف جديد" formId="addStaffForm">
        <form id="addStaffForm" action="{{ route('admin.users.staff.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="add_staff_name">الاسم الكامل <span class="required">*</span></label>
                <input type="text" id="add_staff_name" name="name" value="{{ old('name') }}" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_staff_email">البريد الإلكتروني <span class="required">*</span></label>
                <input type="email" id="add_staff_email" name="email" value="{{ old('email') }}" class="form-control"
                    required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_staff_phone">رقم الهاتف <span class="required">*</span></label>
                <input type="text" id="add_staff_phone" name="phone" value="{{ old('phone') }}" class="form-control"
                    required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_staff_password">كلمة المرور <span class="required">*</span></label>
                <input type="password" id="add_staff_password" name="password" class="form-control" required minlength="8">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_staff_categories_select">الفئات (التخصصات)</label>
                <select id="add_staff_categories_select" class="form-control">
                    <option value="">اختر فئة...</option>
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
                <label for="add_staff_bio">السيرة الذاتية</label>
                <textarea id="add_staff_bio" name="bio" rows="3" class="form-control"
                    placeholder="وصف مختصر عن الموظف">{{ old('bio') }}</textarea>
            </div>

            <div class="form-group">
                <label for="add_staff_hourly_rate">السعر/ساعة (ريال)</label>
                <input type="number" id="add_staff_hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}"
                    step="0.01" min="0" class="form-control">
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                    <span>الموظف متاح</span>
                </label>
            </div>
        </form>
    </x-modal>

    <!-- Edit Staff Modal -->
    <x-modal modalId="editStaffModal" title="تعديل موظف" formId="editStaffForm">
        <form id="editStaffForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_staff_name">الاسم الكامل <span class="required">*</span></label>
                <input type="text" id="edit_staff_name" name="name" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_staff_email">البريد الإلكتروني <span class="required">*</span></label>
                <input type="email" id="edit_staff_email" name="email" class="form-control" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_staff_phone">رقم الهاتف <span class="required">*</span></label>
                <input type="text" id="edit_staff_phone" name="phone" class="form-control" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_staff_password">كلمة المرور (اتركها فارغة إذا لم تريد تغييرها)</label>
                <input type="password" id="edit_staff_password" name="password" class="form-control" minlength="8">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_staff_categories_select">الفئات (التخصصات)</label>
                <select id="edit_staff_categories_select" class="form-control">
                    <option value="">اختر فئة...</option>
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
                <label for="edit_staff_bio">السيرة الذاتية</label>
                <textarea id="edit_staff_bio" name="bio" rows="3" class="form-control"
                    placeholder="وصف مختصر عن الموظف"></textarea>
            </div>

            <div class="form-group">
                <label for="edit_staff_hourly_rate">السعر/ساعة (ريال)</label>
                <input type="number" id="edit_staff_hourly_rate" name="hourly_rate" step="0.01" min="0"
                    class="form-control">
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="edit_staff_is_available" name="is_available" value="1">
                    <span>الموظف متاح</span>
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
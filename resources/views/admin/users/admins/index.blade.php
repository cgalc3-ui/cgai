@extends('layouts.dashboard')

@section('title', 'إدارة الأدمن')
@section('page-title', 'قائمة الأدمن')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة الأدمن</h2>
            <p>إدارة وعرض جميع الأدمن في مكان واحد</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary" onclick="openModal('addAdminModal')">
                <i class="fas fa-user-plus"></i> إنشاء أدمن
            </button>
            <span class="total-count">جميع الأدمن: {{ $users->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('admin.users.admins') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> بحث عن أدمن:</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}"
                        placeholder="ابحث بالاسم، البريد أو الهاتف..." class="filter-input">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> بحث
                </button>
                <a href="{{ route('admin.users.admins') }}" class="btn btn-secondary">
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
                            <span class="badge badge-danger">
                                <i class="fas fa-user-shield"></i> أدمن
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.users.admins.show', $user) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                                <button type="button" class="btn btn-sm btn-warning"
                                    onclick="openEditAdminModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->phone }}')">
                                    <i class="fas fa-edit"></i> تعديل
                                </button>
                                <form action="{{ route('admin.users.admins.delete', $user) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا الأدمن؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">لا يوجد أدمن مسجلين</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Add Admin Modal -->
    <x-modal modalId="addAdminModal" title="إضافة أدمن جديد" formId="addAdminForm">
        <form id="addAdminForm" action="{{ route('admin.users.admins.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="add_name">الاسم الكامل <span class="required">*</span></label>
                <input type="text" id="add_name" name="name" value="{{ old('name') }}" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_email">البريد الإلكتروني <span class="required">*</span></label>
                <input type="email" id="add_email" name="email" value="{{ old('email') }}" class="form-control" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_phone">رقم الهاتف <span class="required">*</span></label>
                <input type="text" id="add_phone" name="phone" value="{{ old('phone') }}" class="form-control" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_password">كلمة المرور <span class="required">*</span></label>
                <input type="password" id="add_password" name="password" class="form-control" required minlength="8">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </form>
    </x-modal>

    <!-- Edit Admin Modal -->
    <x-modal modalId="editAdminModal" title="تعديل أدمن" formId="editAdminForm">
        <form id="editAdminForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_name">الاسم الكامل <span class="required">*</span></label>
                <input type="text" id="edit_name" name="name" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_email">البريد الإلكتروني <span class="required">*</span></label>
                <input type="email" id="edit_email" name="email" class="form-control" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_phone">رقم الهاتف <span class="required">*</span></label>
                <input type="text" id="edit_phone" name="phone" class="form-control" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_password">كلمة المرور (اتركها فارغة إذا لم تريد تغييرها)</label>
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
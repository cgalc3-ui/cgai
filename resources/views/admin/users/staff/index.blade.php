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
                <label for="search">بحث:</label>
                <input type="text" name="search" id="search" value="{{ $searchQuery }}" placeholder="ابحث بالاسم، البريد أو الهاتف" class="filter-input">
            </div>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> تطبيق
            </button>
            <a href="{{ route('admin.users.staff') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> إعادة تعيين
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
                        <span class="badge badge-warning">
                            <i class="fas fa-user-tie"></i> موظف
                        </span>
                    </td>
                    <td>
                        @if($user->employee && $user->employee->specializations->count() > 0)
                            @foreach($user->employee->specializations->take(2) as $spec)
                                <span class="badge badge-info">{{ $spec->name }}</span>
                            @endforeach
                            @if($user->employee->specializations->count() > 2)
                                <span class="text-muted">+{{ $user->employee->specializations->count() - 2 }}</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.users.staff.show', $user) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            <button type="button" class="btn btn-sm btn-warning" onclick="openEditStaffModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ addslashes($user->phone ?? '') }}', {{ $user->employee ? json_encode($user->employee->specializations->pluck('id')->toArray()) : '[]' }}, '{{ addslashes($user->employee->bio ?? '') }}', {{ $user->employee->hourly_rate ?? 'null' }}, {{ $user->employee && $user->employee->is_available ? 'true' : 'false' }})">
                                <i class="fas fa-edit"></i> تعديل
                            </button>
                            <form action="{{ route('admin.users.staff.delete', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟')">
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
            <input type="email" id="add_staff_email" name="email" value="{{ old('email') }}" class="form-control" required>
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="add_staff_phone">رقم الهاتف <span class="required">*</span></label>
            <input type="text" id="add_staff_phone" name="phone" value="{{ old('phone') }}" class="form-control" required>
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
            <label for="add_staff_specializations_select">التخصصات</label>
            <select id="add_staff_specializations_select" class="form-control">
                <option value="">اختر تخصص...</option>
                @foreach($specializations as $specialization)
                    <option value="{{ $specialization->id }}" data-name="{{ $specialization->name }}">
                        {{ $specialization->name }}
                    </option>
                @endforeach
            </select>
            <div id="add_staff_selected_specializations" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;"></div>
        </div>

        <div class="form-group">
            <label for="add_staff_bio">السيرة الذاتية</label>
            <textarea id="add_staff_bio" name="bio" rows="3" class="form-control" placeholder="وصف مختصر عن الموظف">{{ old('bio') }}</textarea>
        </div>

        <div class="form-group">
            <label for="add_staff_hourly_rate">السعر/ساعة (ريال)</label>
            <input type="number" id="add_staff_hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}" step="0.01" min="0" class="form-control">
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
            <label for="edit_staff_specializations_select">التخصصات</label>
            <select id="edit_staff_specializations_select" class="form-control">
                <option value="">اختر تخصص...</option>
                @foreach($specializations as $specialization)
                    <option value="{{ $specialization->id }}" data-name="{{ $specialization->name }}">
                        {{ $specialization->name }}
                    </option>
                @endforeach
            </select>
            <div id="edit_staff_selected_specializations" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;"></div>
        </div>

        <div class="form-group">
            <label for="edit_staff_bio">السيرة الذاتية</label>
            <textarea id="edit_staff_bio" name="bio" rows="3" class="form-control" placeholder="وصف مختصر عن الموظف"></textarea>
        </div>

        <div class="form-group">
            <label for="edit_staff_hourly_rate">السعر/ساعة (ريال)</label>
            <input type="number" id="edit_staff_hourly_rate" name="hourly_rate" step="0.01" min="0" class="form-control">
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
    .specialization-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #007bff;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
    }
    .specialization-tag .remove-spec {
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
    .specialization-tag .remove-spec:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
</style>
@endpush

@push('scripts')
<script>
    // Add Staff Modal - Specializations
    document.getElementById('add_staff_specializations_select').addEventListener('change', function() {
        const select = this;
        const selectedId = select.value;
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedId) return;
        
        const selectedName = selectedOption.getAttribute('data-name');
        const container = document.getElementById('add_staff_selected_specializations');
        
        // Check if already selected
        const existingTags = container.querySelectorAll('.specialization-tag');
        for (let tag of existingTags) {
            if (tag.getAttribute('data-id') === selectedId) {
                select.value = '';
                return;
            }
        }
        
        // Create new tag
        const tagDiv = document.createElement('div');
        tagDiv.className = 'specialization-tag';
        tagDiv.setAttribute('data-id', selectedId);
        tagDiv.innerHTML = `
            <input type="hidden" name="specializations[]" value="${selectedId}">
            <span>${selectedName}</span>
            <button type="button" class="remove-spec" onclick="removeSpecialization(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(tagDiv);
        select.value = '';
    });
    
    // Edit Staff Modal - Specializations
    document.getElementById('edit_staff_specializations_select').addEventListener('change', function() {
        const select = this;
        const selectedId = select.value;
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedId) return;
        
        const selectedName = selectedOption.getAttribute('data-name');
        const container = document.getElementById('edit_staff_selected_specializations');
        
        // Check if already selected
        const existingTags = container.querySelectorAll('.specialization-tag');
        for (let tag of existingTags) {
            if (tag.getAttribute('data-id') === selectedId) {
                select.value = '';
                return;
            }
        }
        
        // Create new tag
        const tagDiv = document.createElement('div');
        tagDiv.className = 'specialization-tag';
        tagDiv.setAttribute('data-id', selectedId);
        tagDiv.innerHTML = `
            <input type="hidden" name="specializations[]" value="${selectedId}">
            <span>${selectedName}</span>
            <button type="button" class="remove-spec" onclick="removeSpecialization(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(tagDiv);
        select.value = '';
    });
    
    function removeSpecialization(button) {
        const tag = button.closest('.specialization-tag');
        tag.remove();
    }
    
    function openEditStaffModal(id, name, email, phone, specializations, bio, hourlyRate, isAvailable) {
        document.getElementById('editStaffForm').action = '{{ route("admin.users.staff.update", ":id") }}'.replace(':id', id);
        document.getElementById('edit_staff_name').value = name;
        document.getElementById('edit_staff_email').value = email;
        document.getElementById('edit_staff_phone').value = phone || '';
        document.getElementById('edit_staff_password').value = '';
        document.getElementById('edit_staff_bio').value = bio || '';
        document.getElementById('edit_staff_hourly_rate').value = hourlyRate || '';
        document.getElementById('edit_staff_is_available').checked = isAvailable;
        
        // Reset and set specializations
        const container = document.getElementById('edit_staff_selected_specializations');
        container.innerHTML = '';
        
        if (specializations && Array.isArray(specializations) && specializations.length > 0) {
            const specSelect = document.getElementById('edit_staff_specializations_select');
            specializations.forEach(specId => {
                const option = specSelect.querySelector(`option[value="${specId}"]`);
                if (option) {
                    const specName = option.getAttribute('data-name');
                    const tagDiv = document.createElement('div');
                    tagDiv.className = 'specialization-tag';
                    tagDiv.setAttribute('data-id', specId);
                    tagDiv.innerHTML = `
                        <input type="hidden" name="specializations[]" value="${specId}">
                        <span>${specName}</span>
                        <button type="button" class="remove-spec" onclick="removeSpecialization(this)">
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
    document.addEventListener('DOMContentLoaded', function() {
        const addModal = document.getElementById('addStaffModal');
        if (addModal) {
            addModal.addEventListener('hidden.bs.modal', function() {
                document.getElementById('addStaffForm').reset();
                document.getElementById('add_staff_selected_specializations').innerHTML = '';
            });
        }
    });
</script>
@endpush
@endsection


@extends('layouts.dashboard')

@section('title', 'إدارة العملاء')
@section('page-title', 'قائمة العملاء')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة العملاء</h2>
            <p>إدارة وعرض جميع عملائك في مكان واحد</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary" onclick="openModal('addCustomerModal')">
                <i class="fas fa-user-plus"></i> إضافة عميل
            </button>
            <span class="total-count">جميع العملاء: {{ $users->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('admin.users.customers') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> بحث عن عميل:</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}"
                        placeholder="ابحث بالاسم، البريد أو الهاتف..." class="filter-input">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> بحث
                </button>
                <a href="{{ route('admin.users.customers') }}" class="btn btn-secondary">
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
                    <th class="text-center">الدور</th>
                    <th>تاريخ التسجيل</th>
                    <th class="text-center">الإجراءات</th>
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
                                <i class="fas fa-user" style="margin-left: 5px;"></i> عميل
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.users.customers.show', $user) }}" class="calm-action-btn" title="عرض">
                                    <i class="far fa-eye"></i>
                                </a>
                                <button type="button" class="calm-action-btn warning" title="تعديل"
                                    onclick="openEditCustomerModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email ?? '') }}', '{{ addslashes($user->phone ?? '') }}', '{{ $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '' }}', '{{ $user->gender ?? '' }}')">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.users.customers.delete', $user) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
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
                        <td colspan="6" class="text-center">لا يوجد عملاء مسجلين</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Add Customer Modal -->
    <x-modal modalId="addCustomerModal" title="إضافة عميل جديد" formId="addCustomerForm">
        <form id="addCustomerForm" action="{{ route('admin.users.customers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="add_customer_name">الاسم الكامل <span class="required">*</span></label>
                <input type="text" id="add_customer_name" name="name" value="{{ old('name') }}" class="form-control"
                    required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_customer_email">البريد الإلكتروني</label>
                <input type="email" id="add_customer_email" name="email" value="{{ old('email') }}" class="form-control">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="add_customer_phone">رقم الهاتف <span class="required">*</span></label>
                <input type="text" id="add_customer_phone" name="phone" value="{{ old('phone') }}" class="form-control"
                    required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="add_customer_date_of_birth">تاريخ الميلاد</label>
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
                    <label for="add_customer_gender">النوع</label>
                    <select id="add_customer_gender" name="gender" class="form-control">
                        <option value="">اختر النوع</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                    @error('gender')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </form>
    </x-modal>

    <!-- Edit Customer Modal -->
    <x-modal modalId="editCustomerModal" title="تعديل عميل" formId="editCustomerForm">
        <form id="editCustomerForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_customer_name">الاسم الكامل <span class="required">*</span></label>
                <input type="text" id="edit_customer_name" name="name" class="form-control" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_customer_email">البريد الإلكتروني</label>
                <input type="email" id="edit_customer_email" name="email" class="form-control">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="edit_customer_phone">رقم الهاتف <span class="required">*</span></label>
                <input type="text" id="edit_customer_phone" name="phone" class="form-control" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit_customer_date_of_birth">تاريخ الميلاد</label>
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
                    <label for="edit_customer_gender">النوع</label>
                    <select id="edit_customer_gender" name="gender" class="form-control">
                        <option value="">اختر النوع</option>
                        <option value="male">ذكر</option>
                        <option value="female">أنثى</option>
                    </select>
                    @error('gender')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </form>
    </x-modal>

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
@extends('layouts.dashboard')

@section('title', 'إدارة التخصصات')
@section('page-title', 'إدارة التخصصات')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة التخصصات</h2>
            <p>إدارة جميع التخصصات المتاحة للموظفين</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.specializations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة تخصص
            </a>
            <span class="total-count">إجمالي التخصصات: {{ $specializations->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('admin.specializations') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> بحث في التخصصات:</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}"
                        placeholder="ابحث بالاسم أو الوصف..." class="filter-input">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> بحث
                </button>
                <a href="{{ route('admin.specializations') }}" class="btn btn-secondary">
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
                    <th>الوصف</th>
                    <th>عدد الموظفين</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($specializations as $specialization)
                    <tr>
                        <td>
                            <strong>{{ $specialization->name }}</strong>
                            <br><small class="text-muted">Slug: {{ $specialization->slug }}</small>
                        </td>
                        <td>
                            @if($specialization->description)
                                {{ Str::limit($specialization->description, 50) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $specialization->employees()->count() }} موظف</span>
                        </td>
                        <td>
                            @if($specialization->is_active)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-danger">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.specializations.edit', $specialization) }}"
                                    class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <form action="{{ route('admin.specializations.delete', $specialization) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التخصص؟')">
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
                        <td colspan="5" class="text-center">لا توجد تخصصات مسجلة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $specializations->links() }}
        </div>
    </div>
@endsection
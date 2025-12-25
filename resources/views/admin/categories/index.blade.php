@extends('layouts.dashboard')

@section('title', 'الفئات')
@section('page-title', 'قائمة الفئات')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>قائمة الفئات</h2>
        <p>إدارة وعرض جميع الفئات</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة فئة جديدة
        </a>
        <span class="total-count">إجمالي الفئات: {{ $categories->total() }}</span>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>الوصف</th>
                <th>الحالة</th>
                <th>تاريخ الإنشاء</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ Str::limit($category->description ?? '-', 50) }}</td>
                    <td>
                        @if($category->is_active)
                            <span class="badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-danger">غير نشط</span>
                        @endif
                    </td>
                    <td>{{ $category->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟ سيتم حذف جميع الفئات الفرعية والخدمات المرتبطة بها.')">
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
                    <td colspan="5" class="text-center">لا توجد فئات مسجلة</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $categories->links() }}
    </div>
</div>
@endsection


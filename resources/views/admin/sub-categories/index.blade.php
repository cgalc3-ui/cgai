@extends('layouts.dashboard')

@section('title', 'الفئات الفرعية')
@section('page-title', 'قائمة الفئات الفرعية')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>قائمة الفئات الفرعية</h2>
        <p>إدارة وعرض جميع الفئات الفرعية</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.sub-categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة فئة فرعية جديدة
        </a>
        <span class="total-count">إجمالي الفئات الفرعية: {{ $subCategories->total() }}</span>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>الفئة الرئيسية</th>
                <th>الوصف</th>
                <th>الحالة</th>
                <th>تاريخ الإنشاء</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subCategories as $subCategory)
                <tr>
                    <td>{{ $subCategory->name }}</td>
                    <td>{{ $subCategory->category->name }}</td>
                    <td>{{ Str::limit($subCategory->description ?? '-', 50) }}</td>
                    <td>
                        @if($subCategory->is_active)
                            <span class="badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-danger">غير نشط</span>
                        @endif
                    </td>
                    <td>{{ $subCategory->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.sub-categories.edit', $subCategory) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('admin.sub-categories.destroy', $subCategory) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة الفرعية؟ سيتم حذف جميع الخدمات المرتبطة بها.')">
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
                    <td colspan="6" class="text-center">لا توجد فئات فرعية مسجلة</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $subCategories->links() }}
    </div>
</div>
@endsection


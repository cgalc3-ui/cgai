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
                    <th class="text-center">الحالة</th>
                    <th>تاريخ الإنشاء</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subCategories as $subCategory)
                    <tr>
                        <td>{{ $subCategory->name }}</td>
                        <td>{{ $subCategory->category->name }}</td>
                        <td>{{ Str::limit($subCategory->description ?? '-', 50) }}</td>
                        <td class="text-center">
                            @if($subCategory->is_active)
                                <span class="status-pill completed">نشط</span>
                            @else
                                <span class="status-pill cancelled">غير نشط</span>
                            @endif
                        </td>
                        <td>{{ $subCategory->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.sub-categories.edit', $subCategory) }}" class="calm-action-btn warning"
                                    title="تعديل">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.sub-categories.destroy', $subCategory) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة الفرعية؟ سيتم حذف جميع الخدمات المرتبطة بها.')">
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
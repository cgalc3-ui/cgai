@extends('layouts.dashboard')

@section('title', 'إدارة الأسئلة الشائعة')
@section('page-title', 'إدارة الأسئلة الشائعة')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>إدارة الأسئلة الشائعة</h2>
            <p>إضافة وتعديل وحذف الأسئلة الشائعة للمستخدمين</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('faqs.index') }}" class="btn btn-secondary" style="margin-left: 10px;">
                <i class="fas fa-eye"></i> عرض كما يرى المستخدم
            </a>
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة سؤال جديد
            </a>
            <span class="total-count">إجمالي الأسئلة: {{ $faqs->count() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>السؤال</th>
                    <th>الفئة</th>
                    <th class="text-center">الترتيب</th>
                    <th class="text-center">الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                    <tr>
                        <td>{{ Str::limit($faq->question, 60) }}</td>
                        <td><span class="status-pill active" style="font-size: 11px;">{{ $faq->category }}</span></td>
                        <td class="text-center">{{ $faq->sort_order }}</td>
                        <td class="text-center">
                            @if($faq->is_active)
                                <span class="status-pill completed">نشط</span>
                            @else
                                <span class="status-pill cancelled">غير نشط</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="calm-action-btn warning" title="تعديل">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟')">
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
                        <td colspan="5" class="text-center">لا توجد أسئلة مسجلة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
            border: none;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            border: none;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 13px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-info {
            background: #3b82f6;
            color: white;
        }

        .badge-success {
            background: #10b981;
            color: white;
        }

        .badge-danger {
            background: #ef4444;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }
    </style>
@endsection
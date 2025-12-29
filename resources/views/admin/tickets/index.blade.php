@extends('layouts.dashboard')

@section('title', 'إدارة التذاكر')
@section('page-title', 'إدارة التذاكر')

@section('content')
    <div class="tickets-admin-container">
        <!-- Header -->
        <div class="tickets-header">
            <div class="header-left">
                <h2>إدارة التذاكر</h2>
                <p>جميع تذاكر الدعم في النظام</p>
            </div>
            <div class="header-right">
                <span class="total-count">إجمالي التذاكر: {{ $tickets->total() }}</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-container">
            <form method="GET" action="{{ route('admin.tickets') }}" class="filters-form">
                <div class="filter-group">
                    <label>الحالة:</label>
                    <select name="status" class="filter-select">
                        <option value="">الكل</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوحة</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>قيد المعالجة
                        </option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>محلولة</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>مغلقة</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>الأولوية:</label>
                    <select name="priority" class="filter-select">
                        <option value="">الكل</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>عاجلة</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>البحث:</label>
                    <input type="text" name="search" class="filter-input" placeholder="الموضوع أو اسم العميل"
                        value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn-filter">تطبيق</button>
                <a href="{{ route('admin.tickets') }}" class="btn-clear">مسح</a>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>العميل</th>
                        <th>الموضوع</th>
                        <th>الحالة</th>
                        <th>الأولوية</th>
                        <th>معين ل</th>
                        <th>التاريخ</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">{{ $ticket->user->name }}</div>
                                    <div class="user-email">{{ $ticket->user->email }}</div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="ticket-link">
                                    {{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}
                                </a>
                            </td>
                            <td>
                                @if($ticket->status === 'open')
                                    <span class="badge badge-warning">مفتوحة</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="badge badge-info">قيد المعالجة</span>
                                @elseif($ticket->status === 'resolved')
                                    <span class="badge badge-success">محلولة</span>
                                @else
                                    <span class="badge badge-secondary">مغلقة</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->priority === 'urgent')
                                    <span class="badge badge-danger">عاجلة</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="badge badge-warning">عالية</span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="badge badge-info">متوسطة</span>
                                @else
                                    <span class="badge badge-secondary">منخفضة</span>
                                @endif
                            </td>
                            <td>
                                {{ $ticket->assignedUser->name ?? 'غير معين' }}
                            </td>
                            <td>
                                {{ $ticket->created_at->format('Y-m-d') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn-view">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-headset"></i>
                                    <h3>لا توجد تذاكر</h3>
                                    <p>لم يتم العثور على أي تذاكر</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
            <div class="pagination-wrapper">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .tickets-admin-container {
                background: white;
                border-radius: 12px;
                box-shadow: var(--card-shadow);
                padding: 24px;
            }

            .tickets-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
                padding-bottom: 16px;
                border-bottom: 2px solid #e5e7eb;
            }

            .tickets-header h2 {
                font-size: 24px;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 4px 0;
            }

            .tickets-header p {
                color: #6b7280;
                font-size: 14px;
                margin: 0;
            }

            .filters-container {
                background: #f9fafb;
                padding: 16px;
                border-radius: 8px;
                margin-bottom: 24px;
            }

            .filters-form {
                display: flex;
                gap: 16px;
                align-items: flex-end;
            }

            .filter-group {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .filter-group label {
                font-size: 12px;
                font-weight: 600;
                color: #4b5563;
            }

            .filter-select,
            .filter-input {
                padding: 8px 12px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                font-size: 14px;
                background: white;
            }

            .btn-filter,
            .btn-clear {
                padding: 8px 16px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                border: none;
                text-decoration: none;
                display: inline-block;
            }

            .btn-filter {
                background: #3b82f6;
                color: white;
            }

            .btn-clear {
                background: #e5e7eb;
                color: #4b5563;
            }

            .ticket-link {
                color: #3b82f6;
                text-decoration: none;
                font-weight: 600;
            }

            .ticket-link:hover {
                text-decoration: underline;
            }

            .btn-view {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: #3b82f6;
                color: white;
                border-radius: 6px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 600;
            }

            .btn-view:hover {
                background: #2563eb;
            }

            .empty-state {
                text-align: center;
                padding: 40px 20px;
            }

            .empty-state i {
                font-size: 48px;
                color: #d1d5db;
                margin-bottom: 16px;
            }

            .empty-state h3 {
                color: #4b5563;
                margin-bottom: 8px;
            }

            .empty-state p {
                color: #9ca3af;
            }
        </style>
    @endpush
@endsection
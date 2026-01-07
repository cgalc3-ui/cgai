@extends('layouts.dashboard')

@section('title', __('messages.points_transactions') ?? 'معاملات النقاط')
@section('page-title', __('messages.points_transactions') ?? 'معاملات النقاط')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.points_transactions') ?? 'معاملات النقاط' }}</h2>
            <p>{{ __('messages.view_all_points_transactions') ?? 'عرض جميع معاملات النقاط' }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.points.settings') }}" class="btn btn-secondary">
                <i class="fas fa-cog"></i> {{ __('messages.settings') ?? 'الإعدادات' }}
            </a>
            <a href="{{ route('admin.points.wallets') }}" class="btn btn-secondary">
                <i class="fas fa-wallet"></i> {{ __('messages.wallets') ?? 'المحافظ' }}
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') ?? 'خيارات التصفية' }}">
        <form method="GET" action="{{ route('admin.points.transactions') }}" class="filter-form">
            <div class="filter-inputs" style="gap: 30px;">
                <div class="filter-group">
                    <label for="type"><i class="fas fa-filter"></i> {{ __('messages.type') ?? 'النوع' }}:</label>
                    <select name="type" id="type" class="filter-input">
                        <option value="">{{ __('messages.all') ?? 'الكل' }}</option>
                        <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>
                            {{ __('messages.purchase') ?? 'شراء' }}
                        </option>
                        <option value="usage" {{ request('type') === 'usage' ? 'selected' : '' }}>
                            {{ __('messages.usage') ?? 'استخدام' }}
                        </option>
                        <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>
                            {{ __('messages.refund') ?? 'استرجاع' }}
                        </option>
                        <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>
                            {{ __('messages.adjustment') ?? 'تعديل' }}
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="user_id"><i class="fas fa-user"></i> {{ __('messages.user') ?? 'المستخدم' }}:</label>
                    <select name="user_id" id="user_id" class="filter-input">
                        <option value="">{{ __('messages.all') ?? 'الكل' }}</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('user_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} 
                                @if($customer->phone)
                                    - {{ $customer->phone }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group" style="margin-inline-start: 40px;">
                    <label for="from_date"><i class="fas fa-calendar-alt"></i> {{ __('messages.from_date') ?? 'من تاريخ' }}:</label>
                    <input type="date" name="from_date" id="from_date" class="filter-input" 
                           value="{{ request('from_date') }}">
                </div>
                <div class="filter-group" style="margin-inline-start: 40px;">
                    <label for="to_date"><i class="fas fa-calendar-alt"></i> {{ __('messages.to_date') ?? 'إلى تاريخ' }}:</label>
                    <input type="date" name="to_date" id="to_date" class="filter-input" 
                           value="{{ request('to_date') }}">
                </div>
            </div>
            <div class="filter-actions" style="margin-inline-start: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> {{ __('messages.apply_filter') ?? 'تطبيق التصفية' }}
                </button>
                <a href="{{ route('admin.points.transactions') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') ?? 'مسح' }}
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="card dashboard-card">
        <div class="card-header">
            <h3 class="card-title">{{ __('messages.transactions_list') ?? 'قائمة المعاملات' }}</h3>
            <span class="total-count">{{ __('messages.total') ?? 'الإجمالي' }}: {{ $transactions->total() }}</span>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.id') ?? 'المعرف' }}</th>
                            <th>{{ __('messages.user') ?? 'المستخدم' }}</th>
                            <th>{{ __('messages.type') ?? 'النوع' }}</th>
                            <th>{{ __('messages.points') ?? 'النقاط' }}</th>
                            <th>{{ __('messages.amount_paid') ?? 'المبلغ المدفوع' }}</th>
                            <th>{{ __('messages.balance_before') ?? 'الرصيد قبل' }}</th>
                            <th>{{ __('messages.balance_after') ?? 'الرصيد بعد' }}</th>
                            <th>{{ __('messages.booking') ?? 'الحجز' }}</th>
                            <th>{{ __('messages.date') ?? 'التاريخ' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>
                                <td>
                                    <div class="user-cell">
                                        <span>{{ $transaction->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($transaction->type === 'purchase')
                                        <span class="status-pill active">{{ __('messages.purchase') ?? 'شراء' }}</span>
                                    @elseif($transaction->type === 'usage')
                                        <span class="status-pill pending">{{ __('messages.usage') ?? 'استخدام' }}</span>
                                    @elseif($transaction->type === 'refund')
                                        <span class="status-pill completed">{{ __('messages.refund') ?? 'استرجاع' }}</span>
                                    @else
                                        <span class="status-pill confirmed">{{ __('messages.adjustment') ?? 'تعديل' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $transaction->points > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($transaction->amount_paid)
                                        {{ number_format($transaction->amount_paid, 2) }} {{ __('messages.sar') ?? 'ريال' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ number_format($transaction->balance_before, 2) }}</td>
                                <td>{{ number_format($transaction->balance_after, 2) }}</td>
                                <td>
                                    @if($transaction->booking_id)
                                        <a href="{{ route('admin.bookings.show', $transaction->booking_id) }}" class="btn btn-sm btn-link">
                                            #{{ $transaction->booking_id }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="datetime-info">
                                        <div class="date">{{ $transaction->created_at->format('Y-m-d') }}</div>
                                        <div class="time">{{ $transaction->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h3>{{ __('messages.no_transactions') ?? 'لا توجد معاملات' }}</h3>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="pagination-wrapper">
                    {{ $transactions->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection


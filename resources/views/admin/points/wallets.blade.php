@extends('layouts.dashboard')

@section('title', __('messages.wallets') ?? 'المحافظ')
@section('page-title', __('messages.wallets') ?? 'المحافظ')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.wallets') ?? 'المحافظ' }}</h2>
            <p>{{ __('messages.view_all_wallets') ?? 'عرض جميع محافظ العملاء' }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.points.settings') }}" class="btn btn-secondary">
                <i class="fas fa-cog"></i> {{ __('messages.settings') ?? 'الإعدادات' }}
            </a>
            <a href="{{ route('admin.points.transactions') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> {{ __('messages.transactions') ?? 'المعاملات' }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid" style="margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_wallets') ?? 'إجمالي المحافظ' }}</h3>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle teal">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-card-info">
                    <h2 class="stat-card-value">{{ number_format($totalWallets) }}</h2>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.total_balance') ?? 'إجمالي الرصيد' }}</h3>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle green">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-card-info">
                    <h2 class="stat-card-value">{{ number_format($totalBalance, 2) }}</h2>
                    <span class="stat-card-subtitle">{{ __('messages.points') ?? 'نقطة' }}</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.active_wallets') ?? 'المحافظ النشطة' }}</h3>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle light-blue">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-card-info">
                    <h2 class="stat-card-value">{{ number_format($activeWallets) }}</h2>
                    <span class="stat-card-subtitle">{{ __('messages.with_balance') ?? 'برصيد' }}</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">{{ __('messages.empty_wallets') ?? 'المحافظ الفارغة' }}</h3>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle orange">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-card-info">
                    <h2 class="stat-card-value">{{ number_format($emptyWallets) }}</h2>
                    <span class="stat-card-subtitle">{{ __('messages.no_balance') ?? 'بدون رصيد' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') ?? 'خيارات التصفية' }}">
        <form method="GET" action="{{ route('admin.points.wallets') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search') ?? 'بحث' }}:</label>
                    <input type="text" name="search" id="search" class="filter-input"
                           placeholder="{{ __('messages.search_by_name_phone_email') ?? 'بحث بالاسم، الهاتف، أو البريد' }}"
                           value="{{ request('search') }}">
                </div>
                <div class="filter-group">
                    <label for="user_id"><i class="fas fa-user"></i> {{ __('messages.user_id') ?? 'معرف المستخدم' }}:</label>
                    <input type="number" name="user_id" id="user_id" class="filter-input"
                           placeholder="{{ __('messages.user_id') ?? 'معرف المستخدم' }}"
                           value="{{ request('user_id') }}">
                </div>
                <div class="filter-group">
                    <label for="sort_by"><i class="fas fa-sort"></i> {{ __('messages.sort_by') ?? 'ترتيب حسب' }}:</label>
                    <select name="sort_by" id="sort_by" class="filter-input">
                        <option value="balance" {{ request('sort_by') === 'balance' ? 'selected' : '' }}>
                            {{ __('messages.balance') ?? 'الرصيد' }}
                        </option>
                        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>
                            {{ __('messages.date') ?? 'التاريخ' }}
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sort_order"><i class="fas fa-sort-amount-down"></i> {{ __('messages.order') ?? 'الترتيب' }}:</label>
                    <select name="sort_order" id="sort_order" class="filter-input">
                        <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>
                            {{ __('messages.descending') ?? 'تنازلي' }}
                        </option>
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>
                            {{ __('messages.ascending') ?? 'تصاعدي' }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> {{ __('messages.search') ?? 'بحث' }}
                </button>
                <a href="{{ route('admin.points.wallets') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') ?? 'مسح' }}
                </a>
            </div>
        </form>
    </div>

    <!-- Wallets Table -->
    <div class="card dashboard-card">
        <div class="card-header">
            <h3 class="card-title">{{ __('messages.wallets_list') ?? 'قائمة المحافظ' }}</h3>
            <span class="total-count">{{ __('messages.total') ?? 'الإجمالي' }}: {{ $wallets->total() }}</span>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="data-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="min-width: 300px;">{{ __('messages.user') ?? 'المستخدم' }}</th>
                            <th style="width: 150px;">{{ __('messages.phone') ?? 'الهاتف' }}</th>
                            <th style="width: 180px; text-align: center;">{{ __('messages.balance') ?? 'الرصيد' }}</th>
                            <th style="width: 150px;">{{ __('messages.created_at') ?? 'تاريخ الإنشاء' }}</th>
                            <th style="width: 140px; text-align: center;">{{ __('messages.actions') ?? 'الإجراءات' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wallets as $wallet)
                            <tr>
                                <td>
                                    <div class="user-cell" style="display: flex; align-items: center; gap: 14px;">
                                        @if($wallet->user->avatar_url)
                                            <div class="user-avatar-wrapper" style="flex-shrink: 0;">
                                                <img src="{{ $wallet->user->avatar_url }}" 
                                                     alt="{{ $wallet->user->name }}" 
                                                     class="user-avatar"
                                                     style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                                            </div>
                                        @else
                                            <div class="user-avatar-wrapper" style="width: 45px; height: 45px; flex-shrink: 0; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                                                {{ mb_substr($wallet->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="user-info" style="flex: 1; min-width: 0;">
                                            <div class="user-name" style="font-weight: 700; color: var(--text-primary, #1a202c); font-size: 15px; margin-bottom: 4px;">
                                                {{ $wallet->user->name }}
                                            </div>
                                            @if($wallet->user->email)
                                                <div class="user-details" style="font-size: 12px; color: var(--text-secondary, #6b7280);">
                                                    <span><i class="fas fa-envelope" style="font-size: 10px; margin-left: 4px;"></i> {{ $wallet->user->email }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-phone" style="color: var(--text-secondary, #9ca3af); font-size: 13px;"></i>
                                        <span style="color: var(--text-primary, #1a202c); font-weight: 600; font-size: 14px;">{{ $wallet->user->phone ?? '-' }}</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                        <span class="price" style="font-weight: 800; font-size: 18px; color: var(--primary-color, #02c0ce); line-height: 1.2;">
                                            {{ number_format($wallet->balance, 2) }}
                                        </span>
                                        <span style="font-size: 11px; color: var(--text-secondary, #6b7280); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            {{ __('messages.points') ?? 'نقطة' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="datetime-info">
                                        <div class="date" style="font-weight: 500; color: var(--text-primary, #1a202c);">
                                            {{ $wallet->created_at->format('Y-m-d') }}
                                        </div>
                                        <div class="time" style="font-size: 12px; color: var(--text-secondary, #6b7280);">
                                            {{ $wallet->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.points.transactions', ['user_id' => $wallet->user_id]) }}" 
                                           class="calm-action-btn" 
                                           title="{{ __('messages.view_transactions') ?? 'عرض المعاملات' }}">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <a href="{{ route('admin.users.customers.show', $wallet->user_id) }}" 
                                           class="calm-action-btn" 
                                           title="{{ __('messages.view_user') ?? 'عرض المستخدم' }}">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center empty-state">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: var(--text-secondary, #9ca3af); margin-bottom: 16px;"></i>
                                    <h3 style="color: var(--text-secondary, #6b7280); font-weight: 500;">{{ __('messages.no_wallets') ?? 'لا توجد محافظ' }}</h3>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($wallets->hasPages())
                <div class="pagination-wrapper">
                    {{ $wallets->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection


@extends('layouts.dashboard')

@section('title', 'لوحة تحكم الموظفين')
@section('page-title', 'لوحة تحكم الموظفين')

@section('content')
<div class="dashboard-description">
    <h2>لوحة تحكم الموظفين</h2>
    <p>إدارة العملاء ومتابعة الطلبات والمعاملات. الوصول السريع إلى المعلومات المهمة وإدارة العمليات اليومية.</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-icon purple">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card-value">{{ $stats['total_customers'] ?? 0 }}</div>
        <div class="stat-card-label">إجمالي العملاء المسجلين</div>
    </div>
</div>

<!-- Action Buttons -->
<div class="actions-grid">
    <button class="action-btn" onclick="window.location.href='{{ route('staff.customers') }}'">
        <i class="fas fa-users"></i>
        <span>عرض العملاء</span>
    </button>
    <button class="action-btn" onclick="window.location.href='#'">
        <i class="fas fa-file-invoice"></i>
        <span>عرض الطلبات</span>
    </button>
    <button class="action-btn" onclick="window.location.href='#'">
        <i class="fas fa-chart-line"></i>
        <span>التقارير</span>
    </button>
</div>
@endsection


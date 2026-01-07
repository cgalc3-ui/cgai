@extends('layouts.dashboard')

@section('title', __('messages.points_system') ?? 'نظام النقاط')
@section('page-title', __('messages.points_settings') ?? 'إعدادات النقاط')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.points_settings') ?? 'إعدادات النقاط' }}</h2>
            <p>{{ __('messages.manage_points_system') ?? 'إدارة نظام النقاط وتحديد أسعار الخدمات' }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.points.transactions') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> {{ __('messages.transactions') ?? 'المعاملات' }}
            </a>
            <a href="{{ route('admin.points.wallets') }}" class="btn btn-secondary">
                <i class="fas fa-wallet"></i> {{ __('messages.wallets') ?? 'المحافظ' }}
            </a>
        </div>
    </div>

    <!-- Points Settings Section -->
    <div class="section-container" style="background: transparent; border: none; padding: 0;">
        <div class="section-header" style="margin-bottom: 25px; border: none; padding-bottom: 0;">
            <h3><i class="fas fa-cog" style="color: var(--primary-color); margin-left: 8px;"></i> {{ __('messages.general_settings') ?? 'الإعدادات العامة' }}</h3>
            <p>{{ __('messages.manage_global_points_config') ?? 'تحكم في الإعدادات الأساسية لنظام النقاط' }}</p>
        </div>

        <form action="{{ route('admin.points.settings.update') }}" method="POST">
            @csrf
            <div class="settings-grid">
                <!-- Cell 1: Points Conversion Rate -->
                <div class="setting-cell">
                    <div class="setting-header">
                        <div class="setting-icon primary">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="setting-info">
                            <h4 class="setting-title">{{ __('messages.points_per_riyal') ?? 'معدل تحويل النقاط' }}</h4>
                            <p class="setting-description">{{ __('messages.points_per_riyal_desc') ?? 'حدد عدد النقاط التي يكتسبها العميل مقابل كل ريال يتم دفعه' }}</p>
                        </div>
                    </div>
                    <div class="setting-body">
                        <div class="modern-input-group">
                            <input type="number" 
                                   name="points_per_riyal" 
                                   id="points_per_riyal" 
                                   class="modern-input" 
                                   value="{{ $settings->points_per_riyal ?? 10 }}" 
                                   step="0.01" 
                                   min="0.01" 
                                   required
                                   placeholder="10.00">
                            <span class="input-suffix-label">{{ __('messages.points_short') ?? 'نقطة' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cell 2: System Activation -->
                <div class="setting-cell">
                    <div class="setting-header">
                        <div class="setting-icon success">
                            <i class="fas fa-power-off"></i>
                        </div>
                        <div class="setting-info">
                            <h4 class="setting-title">{{ __('messages.system_status') ?? 'نشاط النظام' }}</h4>
                            <p class="setting-description">{{ __('messages.activate_points_system_desc') ?? 'تحكم في تفعيل أو تعطيل نظام النقاط بالكامل عبر التطبيق' }}</p>
                        </div>
                    </div>
                    <div class="setting-body">
                        <label class="modern-toggle">
                            <div class="toggle-switch">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       id="is_active"
                                       {{ ($settings->is_active ?? true) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                            <span class="toggle-label">{{ __('messages.activate_points_system') ?? 'تفعيل نظام النقاط' }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="settings-save-bar">
                <button type="submit" class="btn btn-primary btn-save">
                    <i class="fas fa-save"></i>
                    <span>{{ __('messages.save_settings') ?? 'حفظ كافة الإعدادات' }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Services Pricing -->
    <div class="card dashboard-card" style="margin-bottom: 30px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-concierge-bell"></i> {{ __('messages.services_points_pricing') ?? 'أسعار الخدمات بالنقاط' }}
            </h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.service_name') ?? 'اسم الخدمة' }}</th>
                            <th>{{ __('messages.cash_price') ?? 'السعر النقدي' }}</th>
                            <th>{{ __('messages.points_price') ?? 'سعر النقاط' }}</th>
                            <th>{{ __('messages.status') ?? 'الحالة' }}</th>
                            <th>{{ __('messages.actions') ?? 'الإجراءات' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>
                                    <span style="font-weight: 600; color: var(--text-primary, #1a202c);">
                                        {{ $service->name }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: var(--text-primary, #1a202c);">
                                        {{ number_format($service->price ?? 0, 2) }}
                                    </span>
                                    <span style="font-size: 12px; color: var(--text-secondary, #6b7280); margin-right: 4px;">
                                        {{ __('messages.sar') ?? 'ريال' }}
                                    </span>
                                </td>
                                <td>
                                    @if($service->pointsPricing)
                                        <span style="font-weight: 700; color: var(--primary-color, #02c0ce); font-size: 15px;">
                                            {{ number_format($service->pointsPricing->points_price, 2) }}
                                        </span>
                                        <span style="font-size: 12px; color: var(--text-secondary, #6b7280); margin-right: 4px;">
                                            {{ __('messages.points') ?? 'نقطة' }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-style: italic;">
                                            <i class="fas fa-minus-circle" style="margin-left: 4px;"></i>
                                            {{ __('messages.not_set') ?? 'غير محدد' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->pointsPricing && $service->pointsPricing->is_active)
                                        <span class="status-pill active">{{ __('messages.active') ?? 'نشط' }}</span>
                                    @else
                                        <span class="status-pill cancelled">{{ __('messages.inactive') ?? 'غير نشط' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="calm-action-btn warning" 
                                            onclick="openEditPricingModal({{ $service->id }}, 'service', '{{ $service->name }}', {{ $service->pointsPricing ? $service->pointsPricing->points_price : 'null' }}, {{ $service->pointsPricing && $service->pointsPricing->is_active ? 'true' : 'false' }})"
                                            title="{{ __('messages.edit') ?? 'تعديل' }}">
                                        <i class="far fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center empty-state">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: var(--text-secondary, #9ca3af); margin-bottom: 16px;"></i>
                                    <h3 style="color: var(--text-secondary, #6b7280); font-weight: 500;">{{ __('messages.no_services') ?? 'لا توجد خدمات' }}</h3>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Consultations Pricing -->
    <div class="card dashboard-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-comments"></i> {{ __('messages.consultations_points_pricing') ?? 'أسعار الاستشارات بالنقاط' }}
            </h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.consultation_name') ?? 'اسم الاستشارة' }}</th>
                            <th>{{ __('messages.cash_price') ?? 'السعر النقدي' }}</th>
                            <th>{{ __('messages.points_price') ?? 'سعر النقاط' }}</th>
                            <th>{{ __('messages.status') ?? 'الحالة' }}</th>
                            <th>{{ __('messages.actions') ?? 'الإجراءات' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consultations as $consultation)
                            <tr>
                                <td>
                                    <span style="font-weight: 600; color: var(--text-primary, #1a202c);">
                                        {{ $consultation->name }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: var(--text-primary, #1a202c);">
                                        {{ number_format($consultation->fixed_price ?? 0, 2) }}
                                    </span>
                                    <span style="font-size: 12px; color: var(--text-secondary, #6b7280); margin-right: 4px;">
                                        {{ __('messages.sar') ?? 'ريال' }}
                                    </span>
                                </td>
                                <td>
                                    @if($consultation->pointsPricing)
                                        <span style="font-weight: 700; color: var(--primary-color, #02c0ce); font-size: 15px;">
                                            {{ number_format($consultation->pointsPricing->points_price, 2) }}
                                        </span>
                                        <span style="font-size: 12px; color: var(--text-secondary, #6b7280); margin-right: 4px;">
                                            {{ __('messages.points') ?? 'نقطة' }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-style: italic;">
                                            <i class="fas fa-minus-circle" style="margin-left: 4px;"></i>
                                            {{ __('messages.not_set') ?? 'غير محدد' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($consultation->pointsPricing && $consultation->pointsPricing->is_active)
                                        <span class="status-pill active">{{ __('messages.active') ?? 'نشط' }}</span>
                                    @else
                                        <span class="status-pill cancelled">{{ __('messages.inactive') ?? 'غير نشط' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="calm-action-btn warning" 
                                            onclick="openEditPricingModal({{ $consultation->id }}, 'consultation', '{{ $consultation->name }}', {{ $consultation->pointsPricing ? $consultation->pointsPricing->points_price : 'null' }}, {{ $consultation->pointsPricing && $consultation->pointsPricing->is_active ? 'true' : 'false' }})"
                                            title="{{ __('messages.edit') ?? 'تعديل' }}">
                                        <i class="far fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center empty-state">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: var(--text-secondary, #9ca3af); margin-bottom: 16px;"></i>
                                    <h3 style="color: var(--text-secondary, #6b7280); font-weight: 500;">{{ __('messages.no_consultations') ?? 'لا توجد استشارات' }}</h3>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Pricing Modal -->
    <div id="editPricingModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>{{ __('messages.edit_points_pricing') ?? 'تعديل سعر النقاط' }}</h2>
                <button class="modal-close" onclick="closeEditPricingModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPricingForm" method="POST">
                    @csrf
                    <input type="hidden" id="item_id" name="item_id">
                    <input type="hidden" id="item_type" name="item_type">
                    
                    <div class="form-group" style="margin-bottom: 24px;">
                        <label for="item_name" style="display: block; margin-bottom: 10px; font-weight: 600; color: var(--text-primary, #1a202c); font-size: 14px;">
                            <i class="fas fa-tag" style="margin-left: 6px; color: var(--primary-color, #02c0ce);"></i>
                            {{ __('messages.item_name') ?? 'اسم العنصر' }}
                        </label>
                        <input type="text" 
                               id="item_name" 
                               class="form-control" 
                               readonly
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; background: #f8f9fa; color: var(--text-secondary, #6b7280);">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 24px;">
                        <label for="points_price" style="display: block; margin-bottom: 10px; font-weight: 600; color: var(--text-primary, #1a202c); font-size: 14px;">
                            <i class="fas fa-coins" style="margin-left: 6px; color: var(--primary-color, #02c0ce);"></i>
                            {{ __('messages.points_price') ?? 'سعر النقاط' }} 
                            <span class="required" style="color: #ef4444;">*</span>
                        </label>
                        <input type="number" 
                               name="points_price" 
                               id="points_price" 
                               class="form-control" 
                               step="0.01" 
                               min="0" 
                               required
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; transition: all 0.3s;">
                        <small class="form-text text-muted" style="display: block; margin-top: 8px; font-size: 13px; color: var(--text-secondary, #6b7280);">
                            <i class="fas fa-info-circle" style="margin-left: 4px;"></i>
                            {{ __('messages.points_price_desc') ?? 'عدد النقاط المطلوبة لشراء هذه الخدمة/الاستشارة' }}
                        </small>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="checkbox-label" style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 12px; background: #f8f9fa; border-radius: 8px; transition: all 0.3s;">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   checked
                                   style="width: 20px; height: 20px; cursor: pointer;">
                            <span style="font-weight: 500; color: var(--text-primary, #1a202c); font-size: 14px;">
                                <i class="fas fa-toggle-on" style="margin-left: 6px; color: var(--primary-color, #02c0ce);"></i>
                                {{ __('messages.activate') ?? 'تفعيل' }}
                            </span>
                        </label>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditPricingModal()">
                            {{ __('messages.cancel') ?? 'إلغاء' }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save') ?? 'حفظ' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Settings Grid Layout */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .setting-cell {
            background: #ffffff;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .setting-cell:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
            border-color: var(--primary-color);
        }

        .setting-cell::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .setting-cell:hover::after {
            opacity: 1;
        }

        .setting-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }

        .setting-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            transition: all 0.3s;
        }

        .setting-cell:hover .setting-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        .setting-icon.primary {
            background: rgba(2, 192, 206, 0.1);
            color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(2, 192, 206, 0.1);
        }

        .setting-icon.success {
            background: rgba(26, 188, 156, 0.1);
            color: #1abc9c;
            box-shadow: 0 4px 12px rgba(26, 188, 156, 0.1);
        }

        .setting-info {
            flex: 1;
        }

        .setting-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 6px 0;
            font-family: 'Cairo', sans-serif;
        }

        .setting-description {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin: 0;
        }

        /* Modern Input Styling */
        .modern-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .modern-input {
            width: 100%;
            padding: 14px 16px;
            padding-left: 70px; /* Space for suffix in RTL */
            border: 2px solid #eef2f6;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            background: #f8fafc;
            transition: all 0.3s;
            font-family: 'Cairo', sans-serif;
        }

        html[dir='ltr'] .modern-input {
            padding-left: 16px;
            padding-right: 70px;
        }

        .modern-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(2, 192, 206, 0.1);
        }

        .input-suffix-label {
            position: absolute;
            left: 14px;
            background: #ffffff;
            padding: 5px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            pointer-events: none;
            border: 1px solid #eef2f6;
        }

        html[dir='ltr'] .input-suffix-label {
            left: auto;
            right: 14px;
        }

        /* Modern Toggle Styling (Professional Switch) */
        .modern-toggle {
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            user-select: none;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 14px;
            border: 2px solid #eef2f6;
            transition: all 0.3s;
        }

        .modern-toggle:hover {
            background: #f1f5f9;
            border-color: #e2e8f0;
        }

        .toggle-switch {
            position: relative;
            width: 54px;
            height: 28px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        input:checked + .toggle-slider {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .toggle-label {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 14px;
        }

        /* Save Bar - Premium Floating Look */
        .settings-save-bar {
            background: #ffffff;
            padding: 24px 32px;
            border-radius: 20px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-top: 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }

        .btn-save {
            padding: 14px 40px;
            font-size: 15px;
            font-weight: 800;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            box-shadow: 0 8px 20px rgba(2, 192, 206, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(2, 192, 206, 0.35);
        }

        /* Dark Mode Support */
        [data-theme="dark"] .setting-cell {
            background: #1e1f27;
            border-color: #2a2d3a;
        }
        [data-theme="dark"] .setting-title {
            color: #f1f5f9;
        }
        [data-theme="dark"] .modern-input, 
        [data-theme="dark"] .modern-toggle {
            background: #15171d;
            border-color: #2a2d3a;
            color: #f1f5f9;
        }
        [data-theme="dark"] .input-suffix-label {
            background: #1e1f27;
            border-color: #2a2d3a;
        }
        [data-theme="dark"] .settings-save-bar {
            background: #1e1f27;
            border-color: #2a2d3a;
        }

        /* Form Input Focus Styles for Modal */
        #points_price:focus {
            outline: none !important;
            border-color: var(--primary-color, #02c0ce) !important;
            box-shadow: 0 0 0 3px rgba(2, 192, 206, 0.1) !important;
        }

        #points_price:hover {
            border-color: #d1d5db !important;
        }

        /* Table Row Hover */
        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Empty State Styling */
        .empty-state {
            padding: 60px 20px !important;
        }

        /* Dark Mode Support */
        [data-theme="dark"] .settings-form .form-control {
            background: var(--card-bg, #1f2937);
            border-color: var(--border-color, #374151);
            color: var(--text-primary, #f9fafb);
        }

        [data-theme="dark"] .settings-form .form-control:focus {
            background: var(--card-bg, #1f2937);
            border-color: var(--primary-color, #02c0ce);
        }

        [data-theme="dark"] .switch-container {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border-color: var(--border-color, #374151);
        }

        [data-theme="dark"] .switch-container:hover {
            border-color: var(--primary-color, #02c0ce);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function openEditPricingModal(itemId, itemType, itemName, pointsPrice, isActive) {
            document.getElementById('item_id').value = itemId;
            document.getElementById('item_type').value = itemType;
            document.getElementById('item_name').value = itemName;
            document.getElementById('points_price').value = pointsPrice || '';
            document.getElementById('is_active').checked = isActive !== false;
            
            // Set form action
            const route = itemType === 'service' 
                ? '{{ route("admin.points.services.pricing", ":id") }}'.replace(':id', itemId)
                : '{{ route("admin.points.consultations.pricing", ":id") }}'.replace(':id', itemId);
            document.getElementById('editPricingForm').action = route;
            
            document.getElementById('editPricingModal').classList.add('show');
        }

        function closeEditPricingModal() {
            document.getElementById('editPricingModal').classList.remove('show');
        }

        // Close modal on outside click
        document.getElementById('editPricingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditPricingModal();
            }
        });
    </script>
    @endpush
@endsection


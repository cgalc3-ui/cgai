@extends('layouts.dashboard')

@section('title', __('messages.time_slots'))
@section('page-title', __('messages.time_slots'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.time_slots_list') }}</h2>
            <p>{{ __('messages.manage_time_slots_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.time-slots.schedules') }}" class="btn btn-info">
                <i class="fas fa-calendar-alt"></i> {{ __('messages.recurring_appointments') }}
            </a>
            <button type="button" class="btn btn-primary" onclick="openCreateModal('{{ route('admin.time-slots.create') }}', 'createTimeSlotModal', '{{ __('messages.add_time_slot') }}')">
                <i class="fas fa-plus"></i> {{ __('messages.add_time_slot') }}
            </button>
            <span class="total-count">{{ __('messages.total_time_slots') }}: {{ $timeSlots->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.time-slots') }}" class="filter-form" id="filterForm">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="employee_id"><i class="fas fa-user-tie"></i> {{ __('messages.employee') }}:</label>
                    <select name="employee_id" id="employee_id" class="filter-select">
                        <option value="all" {{ $employeeFilter == 'all' ? 'selected' : '' }}>
                            {{ __('messages.all_employees') }}
                        </option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $employeeFilter == $employee->id ? 'selected' : '' }}>
                                {{ $employee->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date"><i class="fas fa-calendar-day"></i> {{ __('messages.date') }}:</label>
                    <input type="date" name="date" id="date"
                        value="{{ $dateFilter ?? \Carbon\Carbon::today()->format('Y-m-d') }}" class="filter-input">
                </div>
            </div>
            <div class="filter-actions" style="margin-inline-start: 24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> {{ __('messages.apply_filter') }}
                </button>
                <a href="{{ route('admin.time-slots') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="time-slots-layout">
        <!-- Calendar Sidebar -->
        <div class="calendar-sidebar">
            <div class="calendar-container">
                <div class="calendar-header">
                    <button type="button" class="calendar-nav-btn" id="prevMonth">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <h3 id="calendarMonthYear"></h3>
                    <button type="button" class="calendar-nav-btn" id="nextMonth">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>
                <div class="calendar-weekdays">
                    <div class="weekday">ح</div>
                    <div class="weekday">ن</div>
                    <div class="weekday">ث</div>
                    <div class="weekday">ر</div>
                    <div class="weekday">خ</div>
                    <div class="weekday">ج</div>
                    <div class="weekday">س</div>
                </div>
                <div class="calendar-days" id="calendarDays"></div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container-wrapper">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.employee') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.from_hour') }}</th>
                            <th>{{ __('messages.to_hour') }}</th>
                            <th class="text-center">{{ __('messages.status') }}</th>
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timeSlots as $timeSlot)
                            @php
                                $startTime = \Carbon\Carbon::parse($timeSlot->start_time);
                                $endTime = \Carbon\Carbon::parse($timeSlot->end_time);

                                // البدء من الساعة الكاملة الأولى (مثلاً 10:30 -> 10:00)
                                $currentHour = $startTime->copy()->startOfHour();
                                $hours = [];

                                // تقسيم الوقت إلى ساعات كاملة منفصلة
                                while ($currentHour->lt($endTime)) {
                                    $hourStart = $currentHour->copy();
                                    $hourEnd = $currentHour->copy()->addHour();

                                    // التأكد من أن الساعة تتقاطع مع الوقت الفعلي
                                    if ($hourStart->lt($endTime) && $hourEnd->gt($startTime)) {
                                        $hours[] = [
                                            'start' => $hourStart,
                                            'end' => $hourEnd,
                                        ];
                                    }

                                    $currentHour->addHour();
                                }
                            @endphp

                            @foreach($hours as $hour)
                                <tr>
                                    <td data-label="{{ __('messages.employee') }}"><strong>{{ $timeSlot->employee->user->name }}</strong></td>
                                    <td data-label="{{ __('messages.date') }}">{{ \Carbon\Carbon::parse($timeSlot->date)->format('Y-m-d') }}</td>
                                    <td data-label="{{ __('messages.from_hour') }}">{{ $hour['start']->format('h:i A') }}</td>
                                    <td data-label="{{ __('messages.to_hour') }}">{{ $hour['end']->format('h:i A') }}</td>
                                    <td class="text-center" data-label="{{ __('messages.status') }}">
                                        @if($timeSlot->is_available)
                                            <span class="status-pill completed">{{ __('messages.available') }}</span>
                                        @else
                                            <span class="status-pill cancelled">{{ __('messages.unavailable') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center" data-label="{{ __('messages.actions') }}">
                                        <div style="display: flex; gap: 8px; justify-content: center;">
                                            <button type="button" class="calm-action-btn warning" title="{{ __('messages.edit') }}"
                                                onclick="openEditModal('{{ route('admin.time-slots.edit', $timeSlot) }}', 'editTimeSlotModal', '{{ __('messages.edit_time_slot') }}')">
                                                <i class="far fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.time-slots.delete', $timeSlot) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="event.preventDefault(); Confirm.delete({{ json_encode(__('messages.delete_time_slot_confirm')) }}, {{ json_encode(__('messages.confirm_delete_title')) }}).then(confirmed => { if(confirmed) this.submit(); }); return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="calm-action-btn danger"
                                                    title="{{ __('messages.delete') }}">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('messages.no_time_slots') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $timeSlots->links('vendor.pagination.custom', ['itemName' => 'time_slots']) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal-overlay" id="createTimeSlotModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.add_time_slot') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('createTimeSlotModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="createTimeSlotModalBody" style="overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editTimeSlotModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.edit_time_slot') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('editTimeSlotModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="editTimeSlotModalBody" style="overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <style>
        .modal-container::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
    </style>

    @push('styles')
        <style>
            .time-slots-layout {
                display: flex;
                gap: 20px;
                align-items: flex-start;
            }

            .calendar-sidebar {
                width: 350px;
                flex-shrink: 0;
            }

            .calendar-container {
                background: white;
                border-radius: 12px;
                box-shadow: var(--card-shadow);
                padding: 20px;
                position: sticky;
                top: 20px;
            }

            .calendar-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 2px solid var(--border-color);
            }

            .calendar-header h3 {
                font-size: 18px;
                font-weight: 700;
                color: var(--text-primary);
                margin: 0;
            }

            .calendar-nav-btn {
                background: var(--bg-light);
                border: 2px solid var(--border-color);
                border-radius: 8px;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s;
                color: var(--text-primary);
            }

            .calendar-nav-btn:hover {
                background: var(--primary-color);
                color: white;
                border-color: var(--primary-color);
            }

            .calendar-weekdays {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 5px;
                margin-bottom: 10px;
            }

            .weekday {
                text-align: center;
                font-weight: 600;
                font-size: 14px;
                color: var(--text-secondary);
                padding: 8px 0;
            }

            .calendar-days {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 5px;
            }

            .calendar-day {
                aspect-ratio: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid var(--border-color);
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s;
                font-weight: 600;
                font-size: 14px;
                background: white;
                color: var(--text-primary);
            }

            .calendar-day:hover {
                background: var(--bg-light);
                border-color: var(--primary-color);
            }

            .calendar-day.other-month {
                color: #d1d5db;
                background: #f9fafb;
            }

            .calendar-day.today {
                background: var(--primary-color);
                color: white;
                border-color: var(--primary-color);
            }

            .calendar-day.selected {
                background: var(--secondary-color);
                color: white;
                border-color: var(--secondary-color);
            }

            .calendar-day.has-slots {
                position: relative;
            }

            .calendar-day.has-slots::after {
                content: '';
                position: absolute;
                bottom: 4px;
                left: 50%;
                transform: translateX(-50%);
                width: 4px;
                height: 4px;
                background: var(--success-color);
                border-radius: 50%;
            }

            .table-container-wrapper {
                flex: 1;
                min-width: 0;
            }

            @media (max-width: 1024px) {
                .time-slots-layout {
                    flex-direction: column;
                }

                .calendar-sidebar {
                    width: 100%;
                }

                .calendar-container {
                    position: relative;
                    top: 0;
                }
            }

            /* Dark Mode Styles for Time Slots */
            [data-theme="dark"] .calendar-container {
                background: var(--card-bg);
                box-shadow: none;
                border: 1px solid var(--border-color);
            }

            [data-theme="dark"] .calendar-header {
                border-bottom-color: var(--border-color);
            }

            [data-theme="dark"] .calendar-header h3 {
                color: var(--text-primary);
            }

            [data-theme="dark"] .calendar-nav-btn {
                background: var(--sidebar-active-bg);
                border-color: var(--border-color);
                color: var(--text-primary);
            }

            [data-theme="dark"] .calendar-nav-btn:hover {
                background: var(--primary-color);
                color: white;
                border-color: var(--primary-color);
            }

            [data-theme="dark"] .weekday {
                color: var(--text-secondary);
            }

            [data-theme="dark"] .calendar-day {
                background: var(--card-bg);
                border-color: var(--border-color);
                color: var(--text-primary);
            }

            [data-theme="dark"] .calendar-day:hover {
                background: var(--sidebar-active-bg);
                border-color: var(--primary-color);
            }

            [data-theme="dark"] .calendar-day.other-month {
                color: var(--text-secondary);
                background: var(--sidebar-active-bg);
            }

            [data-theme="dark"] .calendar-day.today {
                background: var(--primary-color);
                color: white;
                border-color: var(--primary-color);
            }

            [data-theme="dark"] .calendar-day.selected {
                background: var(--secondary-color);
                color: white;
                border-color: var(--secondary-color);
            }

            [data-theme="dark"] .table-container-wrapper {
                background: transparent;
            }

            /* Mobile: Make table full width like customers page */
            @media (max-width: 767px) {
                .time-slots-layout {
                    flex-direction: column !important;
                    gap: 15px !important;
                }

                .calendar-sidebar {
                    width: 100% !important;
                    order: 1 !important;
                }

                .table-container-wrapper {
                    width: 100% !important;
                    order: 2 !important;
                    flex: none !important;
                }

                .table-container {
                    width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    background: transparent !important;
                    box-shadow: none !important;
                    border: none !important;
                }

                .data-table {
                    width: 100% !important;
                    font-size: 13px !important;
                    min-width: auto !important;
                }

                .data-table thead {
                    display: none !important;
                }

                .data-table tbody,
                .data-table tr,
                .data-table td {
                    display: block !important;
                    width: 100% !important;
                }

                .data-table tr {
                    margin-bottom: 20px !important;
                    background: white !important;
                    border-radius: 20px !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
                    padding: 15px !important;
                    border: none !important;
                }

                [data-theme="dark"] .data-table tr {
                    background: var(--card-bg, #1e1f27) !important;
                    border: none !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4) !important;
                }

                .data-table td {
                    display: grid !important;
                    grid-template-columns: 110px 1fr !important;
                    gap: 12px !important;
                    padding: 14px 20px !important;
                    border: none !important;
                    border-bottom: none !important;
                    text-align: start !important;
                    align-items: center !important;
                    font-size: 14px !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    color: var(--text-primary) !important;
                    font-weight: 500 !important;
                    word-wrap: break-word !important;
                    overflow-wrap: break-word !important;
                }

                html[dir='rtl'] .data-table td {
                    grid-template-columns: 1fr 110px !important;
                    text-align: end !important;
                }

                [data-theme="dark"] .data-table td {
                    border: none !important;
                    border-bottom: none !important;
                    color: var(--text-primary, #f1f5f9) !important;
                }

                .data-table td:last-child {
                    border: none !important;
                    border-bottom: none !important;
                }

                .data-table .status-pill {
                    padding: 4px 12px !important;
                    font-size: 11px !important;
                    font-weight: 800 !important;
                    border-radius: 30px !important;
                    display: inline-flex !important;
                    width: fit-content !important;
                    margin: 0 !important;
                }

                .data-table td::before {
                    content: attr(data-label) !important;
                    font-weight: 800 !important;
                    color: var(--primary-color) !important;
                    font-size: 10px !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.5px !important;
                    opacity: 0.6 !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }

                html[dir='rtl'] .data-table td::before {
                    order: 2 !important;
                    text-align: right !important;
                }

                .data-table td > * {
                    order: 1 !important;
                }

                .data-table td.text-center {
                    text-align: start !important;
                }

                html[dir='rtl'] .data-table td.text-center {
                    text-align: end !important;
                }

                /* Actions column */
                .data-table td:last-child {
                    display: flex !important;
                    justify-content: flex-start !important;
                    background: #f8fafc !important;
                    padding: 14px 20px !important;
                    margin: 0 -15px -15px -15px !important;
                    border-radius: 0 0 20px 20px !important;
                    border-bottom: none !important;
                    grid-template-columns: 1fr !important;
                    width: calc(100% + 30px) !important;
                    max-width: calc(100% + 30px) !important;
                }

                [data-theme="dark"] .data-table td:last-child {
                    background: var(--sidebar-active-bg, #15171d) !important;
                }

                html[dir='rtl'] .data-table td:last-child {
                    justify-content: flex-end !important;
                }

                .data-table td:last-child::before {
                    display: none !important;
                }

                .data-table td:last-child > div {
                    display: flex !important;
                    gap: 8px !important;
                    flex-wrap: wrap !important;
                    justify-content: flex-start !important;
                    width: 100% !important;
                    align-items: center !important;
                }

                html[dir='rtl'] .data-table td:last-child > div {
                    justify-content: flex-end !important;
                }

                /* Smaller action buttons */
                .data-table .calm-action-btn {
                    width: 36px !important;
                    height: 36px !important;
                    min-width: 36px !important;
                    padding: 0 !important;
                    border-radius: 10px !important;
                    font-size: 14px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }

                .data-table .calm-action-btn i {
                    font-size: 14px !important;
                    margin: 0 !important;
                }

                .data-table td:last-child > div form {
                    display: inline-block !important;
                    margin: 0 !important;
                }

                /* Pagination - Mobile */
                .pagination-wrapper {
                    padding: 15px 10px !important;
                    margin-top: 20px !important;
                    width: 100% !important;
                }

                .pagination-wrapper .custom-pagination {
                    display: flex !important;
                    flex-direction: row !important;
                    justify-content: space-between !important;
                    align-items: center !important;
                    width: 100% !important;
                    gap: 15px !important;
                }

                .pagination-wrapper .pagination-info {
                    position: static !important;
                    flex: 0 0 auto !important;
                    text-align: right !important;
                }

                html[dir='ltr'] .pagination-wrapper .pagination-info {
                    text-align: left !important;
                }

                .pagination-wrapper .pagination-text {
                    font-size: 12px !important;
                    color: var(--text-secondary, #6b7280) !important;
                    white-space: nowrap !important;
                }

                [data-theme="dark"] .pagination-wrapper .pagination-text {
                    color: var(--text-secondary, #94a3b8) !important;
                }

                .pagination-wrapper .pagination-controls {
                    flex: 0 0 auto !important;
                    display: flex !important;
                    gap: 4px !important;
                    flex-wrap: wrap !important;
                    justify-content: flex-start !important;
                }

                html[dir='rtl'] .pagination-wrapper .pagination-controls {
                    justify-content: flex-end !important;
                }

                .pagination-wrapper .pagination-btn {
                    min-width: 36px !important;
                    height: 36px !important;
                    padding: 6px 8px !important;
                    font-size: 12px !important;
                    border-radius: 8px !important;
                }

                [data-theme="dark"] .pagination-wrapper {
                    background: transparent !important;
                }

                [data-theme="dark"] .pagination-wrapper .pagination-btn {
                    background: var(--card-bg, #1e1f27) !important;
                    border-color: var(--border-color, #2a2d3a) !important;
                    color: var(--text-primary, #f1f5f9) !important;
                }

                [data-theme="dark"] .pagination-wrapper .pagination-btn:hover:not(.disabled):not(.active) {
                    background: var(--sidebar-active-bg, #15171d) !important;
                    color: var(--primary-color, #02c0ce) !important;
                }

                [data-theme="dark"] .pagination-wrapper .pagination-btn.active {
                    background: var(--primary-color, #02c0ce) !important;
                    color: white !important;
                    border-color: var(--primary-color, #02c0ce) !important;
                }
            }

            @media (max-width: 575px) {
                .data-table {
                    font-size: 13px !important;
                }

                .data-table tr {
                    padding: 12px !important;
                    border-radius: 16px !important;
                }

                .data-table td {
                    grid-template-columns: 90px 1fr !important;
                    padding: 12px 16px !important;
                    font-size: 13px !important;
                    gap: 10px !important;
                }

                html[dir='rtl'] .data-table td {
                    grid-template-columns: 1fr 90px !important;
                }

                .data-table td::before {
                    font-size: 9px !important;
                }

                .data-table .status-pill {
                    padding: 4px 10px !important;
                    font-size: 10px !important;
                    font-weight: 800 !important;
                    border-radius: 30px !important;
                    display: inline-flex !important;
                    width: fit-content !important;
                    margin: 0 !important;
                }

                .data-table .calm-action-btn {
                    width: 32px !important;
                    height: 32px !important;
                    min-width: 32px !important;
                    font-size: 13px !important;
                }

                .data-table .calm-action-btn i {
                    font-size: 13px !important;
                }

                .data-table td:last-child {
                    padding: 12px 20px !important;
                    margin: 0 -12px -12px -12px !important;
                    width: calc(100% + 24px) !important;
                    max-width: calc(100% + 24px) !important;
                }

                .data-table td:last-child > div {
                    gap: 6px !important;
                }

                /* Pagination - Extra Small */
                .pagination-wrapper {
                    padding: 12px 8px !important;
                }

                .pagination-wrapper .custom-pagination {
                    gap: 10px !important;
                }

                .pagination-wrapper .pagination-text {
                    font-size: 11px !important;
                }

                .pagination-wrapper .pagination-btn {
                    min-width: 32px !important;
                    height: 32px !important;
                    padding: 5px 6px !important;
                    font-size: 11px !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarDays = document.getElementById('calendarDays');
                const calendarMonthYear = document.getElementById('calendarMonthYear');
                const prevMonthBtn = document.getElementById('prevMonth');
                const nextMonthBtn = document.getElementById('nextMonth');
                const dateInput = document.getElementById('date');
                const filterForm = document.getElementById('filterForm');

                let currentDate = new Date();
                let selectedDate = dateInput.value ? new Date(dateInput.value) : new Date();

                // Get dates with time slots from server
                const timeSlotDates = new Set(@json($datesWithSlots ?? []));

                // If no date filter in URL, set today's date and submit form
                if (!window.location.search.includes('date=')) {
                    const today = new Date();
                    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
                    dateInput.value = todayStr;
                    selectedDate = today;

                    // Submit form to filter by today's date
                    filterForm.submit();
                }

                function renderCalendar() {
                    const year = currentDate.getFullYear();
                    const month = currentDate.getMonth();

                    // Set month/year header
                    const monthNames = {
                        'ar': ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                        'en': ['January', 'February', 'March', 'April', 'May', 'June',
                            'July', 'August', 'September', 'October', 'November', 'December']
                    };
                    const locale = '{{ app()->getLocale() }}';
                    const monthNamesArray = monthNames[locale] || monthNames['ar'];
                    calendarMonthYear.textContent = `${monthNamesArray[month]} ${year}`;

                    // Get first day of month and number of days
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
                    const daysInMonth = lastDay.getDate();
                    const startingDayOfWeek = firstDay.getDay(); // 0 = Sunday, 6 = Saturday

                    // Convert to Arabic week (Saturday = 0, Friday = 6)
                    const arabicStartingDay = (startingDayOfWeek + 1) % 7;

                    calendarDays.innerHTML = '';

                    // Add empty cells for days before the first day of the month
                    for (let i = 0; i < arabicStartingDay; i++) {
                        const emptyDay = document.createElement('div');
                        emptyDay.className = 'calendar-day other-month';
                        calendarDays.appendChild(emptyDay);
                    }

                    // Add days of the month
                    for (let day = 1; day <= daysInMonth; day++) {
                        const dayElement = document.createElement('div');
                        dayElement.className = 'calendar-day';
                        dayElement.textContent = day;

                        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                        // Check if today
                        const today = new Date();
                        if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                            dayElement.classList.add('today');
                        }

                        // Check if selected
                        if (selectedDate &&
                            year === selectedDate.getFullYear() &&
                            month === selectedDate.getMonth() &&
                            day === selectedDate.getDate()) {
                            dayElement.classList.add('selected');
                        }

                        // Check if has time slots
                        if (timeSlotDates.has(dateStr)) {
                            dayElement.classList.add('has-slots');
                        }

                        // Add click event
                        dayElement.addEventListener('click', function () {
                            // Remove previous selection
                            document.querySelectorAll('.calendar-day.selected').forEach(el => {
                                el.classList.remove('selected');
                            });

                            // Add selection to clicked day
                            dayElement.classList.add('selected');

                            // Update date input and submit form
                            dateInput.value = dateStr;
                            filterForm.submit();
                        });

                        calendarDays.appendChild(dayElement);
                    }

                    // Add empty cells for days after the last day of the month
                    const totalCells = arabicStartingDay + daysInMonth;
                    const remainingCells = 42 - totalCells; // 6 rows * 7 days
                    for (let i = 0; i < remainingCells && i < 7; i++) {
                        const emptyDay = document.createElement('div');
                        emptyDay.className = 'calendar-day other-month';
                        calendarDays.appendChild(emptyDay);
                    }
                }

                // Navigation
                prevMonthBtn.addEventListener('click', function () {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar();
                });

                nextMonthBtn.addEventListener('click', function () {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar();
                });

                // Initialize calendar
                if (selectedDate) {
                    currentDate = new Date(selectedDate);
                }
                renderCalendar();
            });

            // Modal Functions
            function openCreateModal(url, modalId, title) {
                const modal = document.getElementById(modalId);
                const modalBody = document.getElementById(modalId + 'Body');
                
                modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #3b82f6;"></i></div>';
                modal.style.display = 'flex';
                setTimeout(() => modal.classList.add('show'), 10);
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    modalBody.innerHTML = data.html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="alert alert-error">{{ __('messages.error') }}</div>';
                });
            }

            function openEditModal(url, modalId, title) {
                const modal = document.getElementById(modalId);
                const modalBody = document.getElementById(modalId + 'Body');
                
                modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #3b82f6;"></i></div>';
                modal.style.display = 'flex';
                setTimeout(() => modal.classList.add('show'), 10);
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    modalBody.innerHTML = data.html;
                    // Update form action and add AJAX handler
                    const form = modalBody.querySelector('form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            handleFormSubmit(form, modalId);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="alert alert-error">{{ __('messages.error') }}</div>';
                });
            }

            function handleFormSubmit(form, modalId) {
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';
                
                fetch(form.action, {
                    method: form.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal(modalId);
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            // Clear previous errors
                            form.querySelectorAll('.error-message').forEach(el => el.remove());
                            form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
                            
                            Object.keys(data.errors).forEach(key => {
                                const input = form.querySelector(`[name="${key}"]`);
                                if (input) {
                                    input.classList.add('error');
                                    const errorMsg = document.createElement('span');
                                    errorMsg.className = 'error-message';
                                    errorMsg.textContent = data.errors[key][0];
                                    input.parentNode.appendChild(errorMsg);
                                }
                            });
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('show');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                }
            }

            // Close modal when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    closeModal(e.target.id);
                }
            });
        </script>
    @endpush
@endsection
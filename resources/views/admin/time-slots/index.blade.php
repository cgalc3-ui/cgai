@extends('layouts.dashboard')

@section('title', 'إدارة الأوقات المتاحة')
@section('page-title', 'إدارة الأوقات المتاحة')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>قائمة الأوقات المتاحة</h2>
            <p>إدارة جميع الأوقات المتاحة للموظفين</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.time-slots.schedules') }}" class="btn btn-info">
                <i class="fas fa-calendar-alt"></i> المواعيد المتكررة
            </a>
            <a href="{{ route('admin.time-slots.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة وقت متاح
            </a>
            <span class="total-count">إجمالي الأوقات: {{ $timeSlots->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('admin.time-slots') }}" class="filter-form" id="filterForm">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="employee_id"><i class="fas fa-user-tie"></i> الموظف:</label>
                    <select name="employee_id" id="employee_id" class="filter-select">
                        <option value="all" {{ $employeeFilter == 'all' ? 'selected' : '' }}>جميع الموظفين</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $employeeFilter == $employee->id ? 'selected' : '' }}>
                                {{ $employee->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date"><i class="fas fa-calendar-day"></i> التاريخ:</label>
                    <input type="date" name="date" id="date"
                        value="{{ $dateFilter ?? \Carbon\Carbon::today()->format('Y-m-d') }}" class="filter-input">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> فلترة
                </button>
                <a href="{{ route('admin.time-slots') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> مسح
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
                            <th>الموظف</th>
                            <th>التاريخ</th>
                            <th>من</th>
                            <th>إلى</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">الإجراءات</th>
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
                                    <td><strong>{{ $timeSlot->employee->user->name }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($timeSlot->date)->format('Y-m-d') }}</td>
                                    <td>{{ $hour['start']->format('h:i A') }}</td>
                                    <td>{{ $hour['end']->format('h:i A') }}</td>
                                    <td class="text-center">
                                        @if($timeSlot->is_available)
                                            <span class="status-pill completed">متاح</span>
                                        @else
                                            <span class="status-pill cancelled">غير متاح</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div style="display: flex; gap: 8px; justify-content: center;">
                                            <a href="{{ route('admin.time-slots.edit', $timeSlot) }}"
                                                class="calm-action-btn warning" title="تعديل">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.time-slots.delete', $timeSlot) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الوقت؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="calm-action-btn danger" title="حذف">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">لا توجد أوقات متاحة مسجلة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $timeSlots->links() }}
                </div>
            </div>
        </div>
    </div>

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
                    const monthNames = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                        'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                    calendarMonthYear.textContent = `${monthNames[month]} ${year}`;

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
        </script>
    @endpush
@endsection
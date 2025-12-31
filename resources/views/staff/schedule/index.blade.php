@extends('layouts.dashboard')

@section('title', 'أيام العمل')
@section('page-title', 'أيام العمل')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>جدول أوقات العمل</h2>
            <p>عرض الأوقات المتاحة والحجوزات لليوم المحدد</p>
        </div>
        <div class="page-header-right">
            <span class="total-count">عدد الفترات: {{ $timeSlots->count() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('staff.my-schedule') }}" class="filter-form" id="filterForm">
            <label>أيام العمل:</label>
            <div class="checkbox-group">
                @foreach($weekDays as $day)
                    <label class="checkbox-label">
                        <input type="checkbox" name="day_names[]" value="{{ $day['name_en'] }}" {{ in_array($day['name_en'], $selectedDayNames) ? 'checked' : '' }}
                            onchange="document.getElementById('date_picker').value=''; this.form.submit()">
                        <span class="custom-checkbox">{{ $day['name_ar'] }}</span>
                    </label>
                @endforeach
            </div>

            <!-- Hidden/Secondary Date Picker -->
            <input type="date" name="date" id="date_picker"
                value="{{ $selectedDate ? $selectedDate->format('Y-m-d') : '' }}" class="filter-input-small"
                style="display:none;" onchange="
                        var checkboxes = document.getElementsByName('day_names[]');
                        for(var i=0; i<checkboxes.length; i++) checkboxes[i].checked = false;
                        this.form.submit();
                    ">
            <button type="button" class="btn btn-sm btn-outline-secondary"
                onclick="document.getElementById('date_picker').click()" style="margin-right: auto;">
                <i class="fas fa-calendar-alt"></i> اختيار تاريخ محدد
            </button>
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
            <div class="section-container" style="margin-top: 0; height: 100%;">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-calendar-day"></i>
                        @if(count($selectedDayNames) > 0)
                                            الأيام المحددة: {{ implode('، ', array_map(function ($d) use ($weekDays) {
                                return collect($weekDays)->where('name_en', $d)->first()['name_ar'] ?? $d;
                            }, $selectedDayNames)) }}
                        @elseif($selectedDate)
                            {{ $selectedDate->format('Y-m-d') }}
                            <span class="day-badge">{{ $selectedDate->locale('ar')->dayName }}</span>
                        @else
                            جميع الأوقات القادمة
                        @endif
                    </h3>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <!-- Show Date column if not a specific date selected (i.e. day name filter or default) -->
                                @if(!$selectedDate)
                                    <th>التاريخ</th>
                                @endif
                                <th>من الساعة</th>
                                <th>إلى الساعة</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($timeSlots as $slot)
                                <tr>
                                    @if(!$selectedDate)
                                        <td>
                                            <div class="datetime-info">
                                                <span class="date">{{ \Carbon\Carbon::parse($slot->date)->format('Y-m-d') }}</span>
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($slot->date)->locale('ar')->dayName }}</small>
                                            </div>
                                        </td>
                                    @endif
                                    <td>
                                        <span class="time-badge start-time">
                                            <i class="far fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="time-badge end-time">
                                            <i class="far fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!$slot->is_available)
                                            <span class="status-pill cancelled">محجوز</span>
                                        @else
                                            <span class="status-pill completed">متاح</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ !$selectedDate ? 4 : 3 }}" class="text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-calendar-times"></i>
                                            <h3>لا توجد فترات عمل</h3>
                                            <p>لا توجد فترات عمل مطابقة للبحث.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Filter Styles */
        .filter-form {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .checkbox-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .checkbox-label {
            cursor: pointer;
            user-select: none;
            margin-bottom: 0;
        }

        .checkbox-label input {
            display: none;
        }

        .custom-checkbox {
            display: inline-block;
            padding: 6px 16px;
            background: white;
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 20px;
            font-size: 14px;
            color: var(--text-secondary, #64748b);
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .checkbox-label:hover .custom-checkbox {
            border-color: var(--primary-color, #3b82f6);
            color: var(--primary-color, #3b82f6);
        }

        .checkbox-label input:checked+.custom-checkbox {
            background: var(--primary-color, #3b82f6);
            color: white;
            border-color: var(--primary-color, #3b82f6);
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        }

        .filter-input-small {
            padding: 6px 12px;
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 8px;
            font-size: 14px;
        }

        /* Custom Calendar Styles from Admin Dashboard */
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
            background: #10b981;
            /* Green dot */
            border-radius: 50%;
        }

        .table-container-wrapper {
            flex: 1;
            min-width: 0;
        }

        .section-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .section-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-header h3 {
            margin: 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .day-badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        /* Common Badges */
        .time-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 14px;
            color: #4b5563;
            font-weight: 500;
        }

        .section-container .table-container {
            box-shadow: none;
            border-radius: 0;
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
            // ID was updated to date_picker in manual filter form
            const dateInput = document.getElementById('date_picker') || document.getElementById('date');
            const filterForm = document.getElementById('filterForm');

            let currentDate = new Date();
            // Initialize selectedDate from input if present
            let selectedDate = (dateInput && dateInput.value) ? new Date(dateInput.value) : null;

            // If no selected date (e.g. checkbox mode), use today for the *view* month but don't select it
            if (!selectedDate) {
                // If we have "day_names" selected, we still might want to show the current month
                // currentDate is already new Date() (Today)
            } else {
                currentDate = new Date(selectedDate);
                // Reset to first day to avoid overflow issues when setting month later
                currentDate.setDate(1);
            }

            // Get dates with time slots from server (Using workDays variable passed from controller)
            const timeSlotDates = new Set(@json($workDays ?? []));

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

                // Convert to Arabic week week starting Saturday (Standard grid usually starts Sunday (0) in our view)
                // HTML Headers: ح (Sun) ...

                calendarDays.innerHTML = '';

                // Add empty cells for days before the first day of the month
                for (let i = 0; i < startingDayOfWeek; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'calendar-day other-month';
                    calendarDays.appendChild(emptyDay);
                }

                // Add days of the month
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';
                    dayElement.textContent = day;

                    // Format format YYYY-MM-DD
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
                        // When clicking a date on calendar, we switch to Specific Date mode
                        // So we should check if there are unrelated checkboxes and clear them?
                        // But the server handles preference for date if present.

                        // We need to set the date input
                        if (dateInput) {
                            dateInput.value = dateStr;
                            // Clear checkboxes to ensure date filter takes precedence visually/logically
                            const checkboxes = document.getElementsByName('day_names[]');
                            checkboxes.forEach(cb => cb.checked = false);

                            filterForm.submit();
                        }
                    });

                    calendarDays.appendChild(dayElement);
                }

                // Fill remaining grid cells to keep height consistent (optional)
                const totalCells = startingDayOfWeek + daysInMonth;
                const remainingCells = 7 - (totalCells % 7);
                if (remainingCells < 7) {
                    for (let i = 0; i < remainingCells; i++) {
                        const emptyDay = document.createElement('div');
                        emptyDay.className = 'calendar-day other-month';
                        calendarDays.appendChild(emptyDay);
                    }
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

            renderCalendar();
        });
    </script>
@endpush
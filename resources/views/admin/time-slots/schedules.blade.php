@extends('layouts.dashboard')

@section('title', __('messages.recurring_appointments'))
@section('page-title', __('messages.recurring_appointments'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.recurring_appointments') }}</h2>
            <p>{{ __('messages.manage_recurring_appointments_desc') }}</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary" onclick="openCreateModal('{{ route('admin.time-slots.schedules.create') }}', 'createScheduleModal', '{{ __('messages.add_recurring_appointments') }}')">
                <i class="fas fa-plus"></i> {{ __('messages.add_recurring_appointments') }}
            </button>
            <span class="total-count">{{ __('messages.total_appointments') }}: {{ $schedules->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.time-slots.schedules') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="employee_id"><i class="fas fa-user-tie"></i> {{ __('messages.employee') }}:</label>
                    <select name="employee_id" id="employee_id" class="filter-select">
                        <option value="all">{{ __('messages.all') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> {{ __('messages.apply_filter') }}
                </button>
                <a href="{{ route('admin.time-slots.schedules') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.reset') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.employee') }}</th>
                    <th>{{ __('messages.weekdays') }}</th>
                    <th>{{ __('messages.from_hour') }}</th>
                    <th>{{ __('messages.to_hour') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->employee->user->name ?? __('messages.unspecified') }}</td>
                        <td>
                            <div class="days-badges">
                                @php
                                    $days = is_string($schedule->days_of_week) ? json_decode($schedule->days_of_week, true) : $schedule->days_of_week;
                                    $dayNames = [
                                        0 => __('messages.sunday'),
                                        1 => __('messages.monday'),
                                        2 => __('messages.tuesday'),
                                        3 => __('messages.wednesday'),
                                        4 => __('messages.thursday'),
                                        5 => __('messages.friday'),
                                        6 => __('messages.saturday')
                                    ];
                                @endphp
                                @foreach($days as $day)
                                    <span class="status-pill active" style="font-size: 11px;">{{ $dayNames[$day] ?? $day }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>{{ $schedule->start_time }}</td>
                        <td>{{ $schedule->end_time }}</td>
                        <td>
                            @if($schedule->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $schedule->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.time-slots.schedules.edit', $schedule) }}"
                                    class="calm-action-btn warning" title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.time-slots.schedules.delete', $schedule) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('{{ __('messages.delete_recurring_appointments_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="calm-action-btn danger" title="{{ __('messages.delete') }}">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>{{ __('messages.no_recurring_appointments') }}</h3>
                                <p>{{ __('messages.no_recurring_appointments_desc') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $schedules->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal-overlay" id="createScheduleModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.add_recurring_appointments') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('createScheduleModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="createScheduleModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
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

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transform: scale(0.9);
            transition: transform 0.3s ease;
            /* Hide scrollbar but keep functionality */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .modal-container::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .modal-overlay.show .modal-container {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            color: #6b7280;
            cursor: pointer;
            padding: 5px;
            border-radius: 6px;
            transition: all 0.2s;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .modal-body {
            padding: 24px;
        }

        @media (max-width: 768px) {
            .modal-container {
                width: 95%;
                max-height: 95vh;
            }
        }

        /* Dark Mode Styles */
        [data-theme="dark"] .modal-overlay {
            background: rgba(0, 0, 0, 0.7);
        }

        [data-theme="dark"] .modal-container {
            background: var(--card-bg, #1e1f27);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        [data-theme="dark"] .modal-header {
            background: var(--card-bg, #1e1f27);
            border-bottom-color: var(--border-color, #2a2d3a);
        }

        [data-theme="dark"] .modal-title {
            color: var(--text-primary, #f1f5f9);
        }

        [data-theme="dark"] .modal-close {
            color: var(--text-secondary, #94a3b8);
            background: transparent;
        }

        [data-theme="dark"] .modal-close:hover {
            background: var(--sidebar-active-bg, #15171d);
            color: var(--text-primary, #f1f5f9);
        }

        [data-theme="dark"] .modal-body {
            background: var(--card-bg, #1e1f27);
            color: var(--text-primary, #f1f5f9);
        }

        .days-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
    </style>

    @push('styles')
        <style>
            .days-badges {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
        </style>
    @endpush
@endsection
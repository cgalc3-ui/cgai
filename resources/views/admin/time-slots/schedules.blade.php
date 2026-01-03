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
            <a href="{{ route('admin.time-slots.schedules.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_recurring_appointments') }}
            </a>
            <span class="total-count">{{ __('messages.total_appointments') }}: {{ $schedules->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.time-slots.schedules') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="employee_id">{{ __('messages.employee') }}:</label>
                    <select name="employee_id" id="employee_id" class="filter-input">
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
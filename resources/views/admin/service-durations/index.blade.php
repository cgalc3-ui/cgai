@extends('layouts.dashboard')

@section('title', __('messages.service_durations'))
@section('page-title', __('messages.service_durations_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.service_durations_list') }}</h2>
            <p>{{ __('messages.manage_service_durations_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.service-durations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_service_duration') }}
            </a>
            <span class="total-count">{{ __('messages.total_service_durations') }}: {{ $durations->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.service') }}</th>
                    <th>{{ __('messages.duration_type') }}</th>
                    <th>{{ __('messages.duration_value') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($durations as $duration)
                    <tr>
                        <td>{{ $duration->service->name }}</td>
                        <td>
                            @if($duration->duration_type == 'hour')
                                {{ __('messages.hour') }}
                            @elseif($duration->duration_type == 'day')
                                {{ __('messages.day') }}
                            @elseif($duration->duration_type == 'week')
                                {{ __('messages.week') }}
                            @endif
                        </td>
                        <td>{{ $duration->duration_value }}</td>
                        <td>{{ number_format($duration->price, 2) }} {{ __('messages.sar') }}</td>
                        <td>
                            @if($duration->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $duration->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.service-durations.edit', $duration) }}" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.service-durations.destroy', $duration) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('{{ __('messages.delete_service_duration_confirm') }}')">
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
                        <td colspan="7" class="text-center">{{ __('messages.no_service_durations') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $durations->links('vendor.pagination.custom', ['itemName' => 'service_durations']) }}
        </div>
    </div>
@endsection
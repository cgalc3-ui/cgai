@extends('layouts.dashboard')

@section('title', __('messages.services'))
@section('page-title', __('messages.services_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.services_list') }}</h2>
            <p>{{ __('messages.manage_services_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_service') }}
            </a>
            <span class="total-count">{{ __('messages.total_services') }}: {{ $services->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.sub_category') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>{{ $service->trans('name') }}</td>
                        <td>{{ $service->subCategory->category->trans('name') }}</td>
                        <td>{{ $service->subCategory->trans('name') }}</td>
                        <td>
                            @if($service->price)
                                <span style="color: #28a745; font-weight: 600;">{{ number_format($service->price, 2) }}
                                    {{ __('messages.sar') }}</span>
                            @else
                                <span style="color: #999; font-style: italic;">{{ __('messages.not_specified') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($service->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $service->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.services.edit', $service) }}" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_service_confirm') }}')">
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
                        <td colspan="7" class="text-center">{{ __('messages.no_services') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $services->links() }}
        </div>
    </div>
@endsection
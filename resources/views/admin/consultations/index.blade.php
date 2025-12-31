@extends('layouts.dashboard')

@section('title', __('messages.consultations'))
@section('page-title', __('messages.consultations_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.consultations_list') }}</h2>
            <p>{{ __('messages.manage_consultations_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.consultations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_consultation') }}
            </a>
            <span class="total-count">{{ __('messages.total_consultations') }}: {{ $consultations->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.fixed_price') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultations as $consultation)
                    <tr>
                        <td>{{ $consultation->trans('name') }}</td>
                        <td>{{ $consultation->category->trans('name') }}</td>
                        <td>
                            <span style="color: #28a745; font-weight: 600;">{{ number_format($consultation->fixed_price, 2) }}
                                {{ __('messages.sar') }}</span>
                        </td>
                        <td class="text-center">
                            @if($consultation->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $consultation->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.consultations.edit', $consultation) }}" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.consultations.destroy', $consultation) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_consultation_confirm') }}')">
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
                        <td colspan="6" class="text-center">{{ __('messages.no_consultations') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $consultations->links() }}
        </div>
    </div>
@endsection
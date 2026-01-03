@extends('layouts.dashboard')

@section('title', __('messages.help_guides_management'))
@section('page-title', __('messages.help_guides_management'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.help_guides_management') }}</h2>
            <p>{{ __('messages.manage_help_guides_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('help-guide.index') }}" class="btn btn-secondary" style="margin-left: 10px;">
                <i class="fas fa-eye"></i> {{ __('messages.view_as_user') }}
            </a>
            <a href="{{ route('admin.help-guides.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_help_guide') }}
            </a>
            <span class="total-count">{{ __('messages.total_help_guides') }}: {{ $helpGuides->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.role') }}</th>
                    <th>{{ __('messages.title') }}</th>
                    <th class="text-center">{{ __('messages.icon') }}</th>
                    <th class="text-center">{{ __('messages.sort_order') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($helpGuides as $helpGuide)
                    <tr>
                        <td>
                            @if($helpGuide->role === 'admin')
                                <span class="status-pill active">{{ __('messages.admin_role') }}</span>
                            @elseif($helpGuide->role === 'staff')
                                <span class="status-pill active">{{ __('messages.staff_role') }}</span>
                            @else
                                <span class="status-pill active">{{ __('messages.customer_role') }}</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($helpGuide->trans('title'), 60) }}</td>
                        <td class="text-center">
                            @if($helpGuide->icon)
                                <i class="{{ $helpGuide->icon }}"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $helpGuide->sort_order }}</td>
                        <td class="text-center">
                            @if($helpGuide->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.help-guides.edit', $helpGuide) }}" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.help-guides.destroy', $helpGuide) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_help_guide_confirm') }}')">
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
                        <td colspan="6" class="text-center">{{ __('messages.no_help_guides') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $helpGuides->links() }}
        </div>
    </div>
@endsection


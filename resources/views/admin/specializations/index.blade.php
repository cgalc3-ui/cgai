@extends('layouts.dashboard')

@section('title', __('messages.specializations'))
@section('page-title', __('messages.specializations'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.specializations_list') }}</h2>
            <p>{{ __('messages.manage_specializations_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.specializations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_specialization') }}
            </a>
            <span class="total-count">{{ __('messages.total_specializations') }}: {{ $specializations->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.specializations') }}" class="filter-form">
            <div class="filter-inputs">
                <div class="filter-group">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search_specializations_label') }}:</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}"
                        placeholder="{{ __('messages.search_specializations_placeholder') }}" class="filter-input">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> {{ __('messages.search') }}
                </button>
                <a href="{{ route('admin.specializations') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.employees_count') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($specializations as $specialization)
                    <tr>
                        <td>
                            <strong>{{ $specialization->name }}</strong>
                            <br><small class="text-muted">Slug: {{ $specialization->slug }}</small>
                        </td>
                        <td>
                            @if($specialization->description)
                                {{ Str::limit($specialization->description, 50) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $specialization->employees()->count() }} {{ __('messages.employee') }}</span>
                        </td>
                        <td>
                            @if($specialization->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.specializations.edit', $specialization) }}"
                                    class="calm-action-btn warning" title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.specializations.delete', $specialization) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('{{ __('messages.delete_specialization_confirm') }}')">
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
                        <td colspan="5" class="text-center">{{ __('messages.no_specializations') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $specializations->links() }}
        </div>
    </div>
@endsection
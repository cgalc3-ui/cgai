@extends('layouts.dashboard')

@section('title', __('messages.sub_categories'))
@section('page-title', __('messages.sub_categories_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.sub_categories_list') }}</h2>
            <p>{{ __('messages.manage_sub_categories_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.sub-categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_sub_category') }}
            </a>
            <span class="total-count">{{ __('messages.total_sub_categories') }}: {{ $subCategories->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subCategories as $subCategory)
                    <tr>
                        <td>{{ $subCategory->trans('name') }}</td>
                        <td>{{ $subCategory->category->trans('name') }}</td>
                        <td>{{ Str::limit($subCategory->trans('description') ?? '-', 50) }}</td>
                        <td class="text-center">
                            @if($subCategory->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $subCategory->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.sub-categories.edit', $subCategory) }}" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.sub-categories.destroy', $subCategory) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_sub_category_confirm') }}')">
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
                        <td colspan="6" class="text-center">{{ __('messages.no_sub_categories') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $subCategories->links() }}
        </div>
    </div>
@endsection
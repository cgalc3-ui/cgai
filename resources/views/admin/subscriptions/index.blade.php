@extends('layouts.dashboard')

@section('title', __('messages.subscriptions'))
@section('page-title', __('messages.subscriptions_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.subscriptions_list') }}</h2>
            <p>{{ __('messages.manage_subscriptions_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_subscription') }}
            </a>
            <span class="total-count">{{ __('messages.total_subscriptions') }}: {{ $subscriptions->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th>{{ __('messages.duration_type') }}</th>
                    <th>{{ __('messages.max_debtors') }}</th>
                    <th>{{ __('messages.max_messages') }}</th>
                    <th class="text-center">{{ __('messages.ai_enabled') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->trans('name') }}</td>
                        <td>{{ number_format($subscription->price, 2) }} {{ __('messages.sar') }}</td>
                        <td>{{ $subscription->trans('duration_text') ?? $subscription->duration_text }}</td>
                        <td>{{ $subscription->max_debtors == 0 ? __('messages.unlimited') : $subscription->max_debtors }}</td>
                        <td>{{ $subscription->max_messages == 0 ? __('messages.unlimited') : $subscription->max_messages }}</td>
                        <td class="text-center">
                            @if($subscription->ai_enabled)
                                <span class="status-pill completed">{{ __('messages.enabled') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.disabled') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($subscription->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="calm-action-btn info"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_subscription_confirm') }}')">
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
                        <td colspan="8" class="text-center">{{ __('messages.no_subscriptions') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $subscriptions->links() }}
        </div>
    </div>
@endsection
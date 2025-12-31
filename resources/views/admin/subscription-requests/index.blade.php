@extends('layouts.dashboard')

@section('title', __('messages.subscription_requests'))
@section('page-title', __('messages.subscription_requests_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.subscription_requests_list') }}</h2>
            <p>{{ __('messages.manage_subscription_requests_desc') }}</p>
        </div>
        <div class="page-header-right">
            <span class="total-count">{{ __('messages.total_requests') }}: {{ $requests->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.user') }}</th>
                    <th>{{ __('messages.package') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.request_date') }}</th>
                    <th>{{ __('messages.processed_by') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->subscription->name }}</td>
                        <td class="text-center">
                            @if($request->status == 'pending')
                                <span class="status-pill pending">{{ __('messages.pending') }}</span>
                            @elseif($request->status == 'approved')
                                <span class="status-pill completed">{{ __('messages.approved') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.rejected') }}</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $request->approver ? $request->approver->name : '-' }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.subscription-requests.show', $request) }}" class="calm-action-btn info"
                                    title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('messages.no_subscription_requests') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
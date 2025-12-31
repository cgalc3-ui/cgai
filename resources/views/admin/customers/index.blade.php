@extends('layouts.dashboard')

@section('title', __('messages.customers_list'))
@section('page-title', __('messages.customers_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.customers_list') }}</h2>
            <p>{{ __('messages.manage_customers_all_desc') }}</p>
        </div>
        <div class="page-header-right">
            <span class="total-count">{{ __('messages.total_customers') }}: {{ $customers->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.phone') }}</th>
                    <th>{{ __('messages.email') }}</th>
                    <th>{{ __('messages.registration_date') }}</th>
                    <th>{{ __('messages.verification_status') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email ?? __('messages.not_available') }}</td>
                        <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                        <td>
                            @if($customer->phone_verified_at)
                                <span class="status-pill completed">{{ __('messages.verified') }}</span>
                            @else
                                <span class="status-pill pending">{{ __('messages.not_verified') }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="calm-action-btn" title="{{ __('messages.view') }}">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.customers.delete', $customer) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_customer_confirm') }}')">
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
                        <td colspan="6" class="text-center">{{ __('messages.no_customers_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $customers->links() }}
        </div>
    </div>
@endsection
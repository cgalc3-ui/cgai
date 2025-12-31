@extends('layouts.dashboard')

@section('title', __('messages.edit_customer'))
@section('page-title', __('messages.edit_customer'))

@section('content')
<div class="form-container">
    <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">{{ __('messages.name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
        </div>

        <div class="form-group">
            <label for="phone">{{ __('messages.phone') }}</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
            </button>
            <a href="{{ route('admin.customers') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection


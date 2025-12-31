@extends('layouts.dashboard')

@section('title', __('messages.edit_admin'))
@section('page-title', __('messages.edit_admin') . ': ' . $user->name)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>{{ __('messages.edit_admin') }}</h2>
        <p>{{ __('messages.edit_admin_desc') }}</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.users.admins.show', $user) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.users.admins.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.basic_information') }}</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="name">{{ __('messages.name') }} *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">{{ __('messages.email') }} *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">{{ __('messages.phone') }} *</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" required>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">{{ __('messages.password') }} ({{ __('messages.password_help_edit') }})</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
            </button>
            <a href="{{ route('admin.users.admins.show', $user) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection


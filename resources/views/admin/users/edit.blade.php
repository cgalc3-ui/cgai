@extends('layouts.dashboard')

@section('title', __('messages.edit_user'))
@section('page-title', __('messages.edit_user') . ': ' . $user->name)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>{{ __('messages.edit_user') }}</h2>
        <p>{{ __('messages.edit_user_desc') }}</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="form-container">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
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
                    <label for="role">{{ __('messages.role') }} *</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">{{ __('messages.select_role') }}</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>{{ __('messages.admin_role') }}</option>
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>{{ __('messages.staff_role') }}</option>
                        <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>{{ __('messages.customer_role') }}</option>
                    </select>
                    @error('role')
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

        @if($user->isStaff() && $user->employee)
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('messages.employee_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="specializations">{{ __('messages.specializations') }}</label>
                        <select id="specializations" name="employee[specializations][]" class="form-control" multiple style="min-height: 120px;">
                            @foreach($specializations as $specialization)
                                <option value="{{ $specialization->id }}" 
                                    {{ $user->employee->specializations->contains($specialization->id) ? 'selected' : '' }}>
                                    {{ $specialization->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ __('messages.specializations_help') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="bio">{{ __('messages.bio') }}</label>
                        <textarea id="bio" name="employee[bio]" rows="4" class="form-control" placeholder="{{ __('messages.bio_placeholder') }}">{{ old('employee.bio', $user->employee->bio) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="hourly_rate">{{ __('messages.hourly_rate') }} ({{ __('messages.sar') }})</label>
                        <input type="number" id="hourly_rate" name="employee[hourly_rate]" value="{{ old('employee.hourly_rate', $user->employee->hourly_rate) }}" step="0.01" min="0" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="employee[is_available]" value="1" {{ old('employee.is_available', $user->employee->is_available) ? 'checked' : '' }}>
                            <span>{{ __('messages.employee_available') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        @endif

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
            </button>
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>

<style>
.form-container {
    max-width: 800px;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.card-header {
    padding: 20px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
}

.card-body {
    padding: 20px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}
</style>
@endsection

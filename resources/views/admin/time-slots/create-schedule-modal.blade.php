<form action="{{ route('admin.time-slots.schedules.store') }}" method="POST" id="scheduleCreateForm" class="modal-form">
    @csrf

    <div class="form-group">
        <label for="employee_id">{{ __('messages.employee') }} <span class="required">*</span></label>
        <select id="employee_id" name="employee_id" class="form-control" required>
            <option value="">{{ __('messages.select_employee') }}</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                    {{ $employee->user->name }}
                </option>
            @endforeach
        </select>
        @error('employee_id')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label>{{ __('messages.weekdays_label') }} <span class="required">*</span></label>
        <div class="days-checkboxes">
            @php
                $days = [
                    0 => ['name' => __('messages.sunday'), 'value' => 0],
                    1 => ['name' => __('messages.monday'), 'value' => 1],
                    2 => ['name' => __('messages.tuesday'), 'value' => 2],
                    3 => ['name' => __('messages.wednesday'), 'value' => 3],
                    4 => ['name' => __('messages.thursday'), 'value' => 4],
                    5 => ['name' => __('messages.friday'), 'value' => 5],
                    6 => ['name' => __('messages.saturday'), 'value' => 6],
                ];
                $oldDays = old('days_of_week', []);
            @endphp
            @foreach($days as $day)
                <label class="day-checkbox">
                    <input type="checkbox" name="days_of_week[]" value="{{ $day['value'] }}" 
                           {{ in_array($day['value'], $oldDays) ? 'checked' : '' }}>
                    <span>{{ $day['name'] }}</span>
                </label>
            @endforeach
        </div>
        @error('days_of_week')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="start_time">{{ __('messages.from_hour') }} <span class="required">*</span></label>
            <input type="time" id="start_time" name="start_time" value="{{ old('start_time', '10:00') }}" class="form-control" required>
            @error('start_time')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="end_time">{{ __('messages.to_hour') }} <span class="required">*</span></label>
            <input type="time" id="end_time" name="end_time" value="{{ old('end_time', '18:00') }}" class="form-control" required>
            @error('end_time')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
            <span>{{ __('messages.appointments_active') }}</span>
        </label>
    </div>

    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        <p>{{ __('messages.auto_create_time_slots_info') }}</p>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('messages.save') }}
        </button>
        <button type="button" class="btn btn-secondary" onclick="closeModal('createScheduleModal'); return false;">
            <i class="fas fa-times"></i> {{ __('messages.cancel') }}
        </button>
    </div>
</form>

<style>
    .modal-form .form-group {
        margin-bottom: 20px;
    }

    .modal-form label {
        color: #374151;
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
    }

    .modal-form .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: white;
        color: #1f2937;
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .modal-form .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modal-form .form-control:hover {
        border-color: #9ca3af;
    }

    .modal-form .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .modal-form .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .modal-form .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #374151;
        cursor: pointer;
    }

    .modal-form .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #3b82f6;
    }

    .modal-form .days-checkboxes {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .modal-form .day-checkbox {
        display: flex;
        align-items: center;
        padding: 10px;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        gap: 8px;
    }

    .modal-form .day-checkbox:hover {
        background: #e9ecef;
        border-color: #3b82f6;
    }

    .modal-form .day-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #3b82f6;
        margin: 0;
    }

    .modal-form .day-checkbox input[type="checkbox"]:checked + span {
        font-weight: 600;
        color: #3b82f6;
    }

    .modal-form .day-checkbox:has(input[type="checkbox"]:checked) {
        background: #e7f3ff;
        border-color: #3b82f6;
    }

    .modal-form .info-box {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 8px;
        padding: 12px;
        margin: 20px 0;
        display: flex;
        align-items: start;
        gap: 10px;
    }

    .modal-form .info-box i {
        color: #3b82f6;
        font-size: 18px;
        margin-top: 2px;
    }

    .modal-form .info-box p {
        margin: 0;
        color: #004085;
        font-size: 13px;
    }

    .modal-form .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .modal-form .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .modal-form .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .modal-form .btn-primary:hover {
        background: #2563eb;
    }

    .modal-form .btn-secondary {
        background: #e5e7eb;
        color: #6b7280;
    }

    .modal-form .btn-secondary:hover {
        background: #d1d5db;
    }

    /* Dark Mode Styles */
    [data-theme="dark"] .modal-form label {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .form-control {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--border-color, #2a2d3a) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .form-control:focus {
        border-color: var(--primary-color, #6658dd) !important;
        box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1) !important;
    }

    [data-theme="dark"] .modal-form .form-control:hover {
        border-color: var(--border-color, #2a2d3a) !important;
    }

    [data-theme="dark"] .modal-form select.form-control {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--border-color, #2a2d3a) !important;
        color: var(--text-primary, #f1f5f9) !important;
        background-image: none !important;
        padding-right: 16px !important;
        padding-left: 16px !important;
    }

    [data-theme="dark"] .modal-form select.form-control:focus {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--primary-color, #6658dd) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form select.form-control option {
        background: var(--sidebar-active-bg, #15171d) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .error-message {
        color: var(--danger-color, #ef4444) !important;
    }

    [data-theme="dark"] .modal-form .checkbox-label {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .checkbox-label span {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .checkbox-label input[type="checkbox"] {
        accent-color: var(--primary-color, #6658dd) !important;
        filter: brightness(1.2);
    }

    [data-theme="dark"] .modal-form .day-checkbox {
        background: var(--sidebar-active-bg, #15171d) !important;
        border-color: var(--border-color, #2a2d3a) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .day-checkbox span {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .day-checkbox:hover {
        background: var(--bg-light, #15171d) !important;
        border-color: var(--primary-color, #6658dd) !important;
    }

    [data-theme="dark"] .modal-form .day-checkbox:hover span {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .day-checkbox input[type="checkbox"] {
        accent-color: var(--primary-color, #6658dd) !important;
        filter: brightness(1.2);
    }

    [data-theme="dark"] .modal-form .day-checkbox input[type="checkbox"]:checked + span {
        color: var(--primary-color, #6658dd) !important;
        font-weight: 600 !important;
    }

    [data-theme="dark"] .modal-form .day-checkbox:has(input[type="checkbox"]:checked) {
        background: rgba(102, 88, 221, 0.15) !important;
        border-color: var(--primary-color, #6658dd) !important;
    }

    [data-theme="dark"] .modal-form .day-checkbox:has(input[type="checkbox"]:checked) span {
        color: var(--primary-color, #6658dd) !important;
    }

    [data-theme="dark"] .modal-form .info-box {
        background: rgba(102, 88, 221, 0.1) !important;
        border-color: var(--primary-color, #6658dd) !important;
    }

    [data-theme="dark"] .modal-form .info-box i {
        color: var(--primary-color, #6658dd) !important;
    }

    [data-theme="dark"] .modal-form .info-box p {
        color: var(--text-primary, #f1f5f9) !important;
    }

    [data-theme="dark"] .modal-form .form-actions {
        border-top-color: var(--border-color, #2a2d3a) !important;
    }

    [data-theme="dark"] .modal-form .btn-primary {
        background: var(--primary-color, #6658dd) !important;
        color: white !important;
    }

    [data-theme="dark"] .modal-form .btn-primary:hover {
        background: var(--primary-dark, #564ab1) !important;
    }

    [data-theme="dark"] .modal-form .btn-secondary {
        background: var(--sidebar-active-bg, #15171d) !important;
        color: var(--text-secondary, #94a3b8) !important;
        border: 1px solid var(--border-color, #2a2d3a) !important;
    }

    [data-theme="dark"] .modal-form .btn-secondary:hover {
        background: var(--bg-light, #15171d) !important;
        color: var(--text-primary, #f1f5f9) !important;
    }
</style>

<script>
    (function() {
        const form = document.getElementById('scheduleCreateForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal('createScheduleModal');
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        // Handle validation errors
                        form.querySelectorAll('.error-message').forEach(el => el.remove());
                        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
                        
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const input = form.querySelector(`[name="${key}"]`);
                                if (input) {
                                    input.classList.add('error');
                                    const errorMsg = document.createElement('span');
                                    errorMsg.className = 'error-message';
                                    errorMsg.textContent = data.errors[key][0];
                                    input.parentNode.appendChild(errorMsg);
                                }
                            });
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }
    })();
</script>


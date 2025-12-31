@extends('layouts.dashboard')

@section('title', __('messages.edit_staff'))
@section('page-title', __('messages.edit_staff') . ': ' . $user->name)

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.edit_staff') }}</h2>
            <p>{{ __('messages.view_edit_staff_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.users.staff.show', $user) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.users.staff.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">
                    <h3>{{ __('messages.basic_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('messages.name') }} *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control"
                            required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('messages.email') }} *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="form-control" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">{{ __('messages.phone') }} *</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="form-control" required>
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">{{ __('messages.password') }}
                            ({{ __('messages.password_help_edit') }})</label>
                        <input type="password" id="password" name="password" class="form-control" minlength="8">
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>{{ __('messages.employee_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="specialization_select">{{ __('messages.specializations') }}</label>
                        <select id="specialization_select" class="form-control">
                            <option value="">{{ __('messages.select_category_placeholder') }}</option>
                            @foreach($specializations as $specialization)
                                <option value="{{ $specialization->id }}" data-name="{{ $specialization->name }}">
                                    {{ $specialization->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="selected_specializations"
                            style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;">
                            @if($user->employee && $user->employee->specializations->count() > 0)
                                @foreach($user->employee->specializations as $spec)
                                    <div class="specialization-tag" data-id="{{ $spec->id }}">
                                        <input type="hidden" name="employee[specializations][]" value="{{ $spec->id }}">
                                        <span>{{ $spec->name }}</span>
                                        <button type="button" class="remove-spec" onclick="removeSpecialization(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                            @if(old('employee.specializations'))
                                @foreach(old('employee.specializations') as $specId)
                                    @php
                                        $spec = $specializations->firstWhere('id', $specId);
                                        $alreadyAdded = $user->employee && $user->employee->specializations->contains($specId);
                                    @endphp
                                    @if($spec && !$alreadyAdded)
                                        <div class="specialization-tag" data-id="{{ $spec->id }}">
                                            <input type="hidden" name="employee[specializations][]" value="{{ $spec->id }}">
                                            <span>{{ $spec->name }}</span>
                                            <button type="button" class="remove-spec" onclick="removeSpecialization(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio">{{ __('messages.bio') }}</label>
                        <textarea id="bio" name="employee[bio]" rows="4" class="form-control"
                            placeholder="{{ __('messages.bio_placeholder') }}">{{ old('employee.bio', $user->employee->bio ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="hourly_rate">{{ __('messages.hourly_rate') }} ({{ __('messages.sar') }})</label>
                        <input type="number" id="hourly_rate" name="employee[hourly_rate]"
                            value="{{ old('employee.hourly_rate', $user->employee->hourly_rate ?? '') }}" step="0.01"
                            min="0" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="employee[is_available]" value="1" {{ old('employee.is_available', $user->employee->is_available ?? true) ? 'checked' : '' }}>
                            <span>{{ __('messages.employee_available') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                </button>
                <a href="{{ route('admin.users.staff.show', $user) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            .specialization-tag {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background-color: #007bff;
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 14px;
            }

            .specialization-tag .remove-spec {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 0;
                margin: 0;
                font-size: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                transition: background-color 0.2s;
            }

            .specialization-tag .remove-spec:hover {
                background-color: rgba(255, 255, 255, 0.2);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.getElementById('specialization_select').addEventListener('change', function () {
                const select = this;
                const selectedId = select.value;
                const selectedOption = select.options[select.selectedIndex];

                if (!selectedId) return;

                const selectedName = selectedOption.getAttribute('data-name');
                const container = document.getElementById('selected_specializations');

                // Check if already selected
                const existingTags = container.querySelectorAll('.specialization-tag');
                for (let tag of existingTags) {
                    if (tag.getAttribute('data-id') === selectedId) {
                        select.value = '';
                        return;
                    }
                }

                // Create new tag
                const tagDiv = document.createElement('div');
                tagDiv.className = 'specialization-tag';
                tagDiv.setAttribute('data-id', selectedId);
                tagDiv.innerHTML = `
                    <input type="hidden" name="employee[specializations][]" value="${selectedId}">
                    <span>${selectedName}</span>
                    <button type="button" class="remove-spec" onclick="removeSpecialization(this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                container.appendChild(tagDiv);
                select.value = '';
            });

            function removeSpecialization(button) {
                const tag = button.closest('.specialization-tag');
                tag.remove();
            }
        </script>
    @endpush
@endsection
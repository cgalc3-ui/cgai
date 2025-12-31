@extends('layouts.dashboard')

@section('title', __('messages.add_new_staff'))
@section('page-title', __('messages.add_new_staff'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.add_new_staff') }}</h2>
            <p>{{ __('messages.add_new_staff_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.users.staff') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.users.staff.store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-header">
                    <h3>{{ __('messages.basic_information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('messages.name') }} *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('messages.email') }} *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control"
                            required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">{{ __('messages.phone') }} *</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control" required>
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">{{ __('messages.password') }} *</label>
                        <input type="password" id="password" name="password" class="form-control" required minlength="8">
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
                        <label for="category_select">{{ __('messages.categories_specializations') }}</label>
                        <select id="category_select" class="form-control">
                            <option value="">{{ __('messages.select_category_placeholder') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-name="{{ $category->name }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="selected_categories" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;">
                            @if(old('employee.categories'))
                                @foreach(old('employee.categories') as $catId)
                                    @php
                                        $cat = $categories->firstWhere('id', $catId);
                                    @endphp
                                    @if($cat)
                                        <div class="category-tag" data-id="{{ $cat->id }}">
                                            <input type="hidden" name="employee[categories][]" value="{{ $cat->id }}">
                                            <span>{{ $cat->name }}</span>
                                            <button type="button" class="remove-cat" onclick="removeCategory(this)">
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
                            placeholder="{{ __('messages.bio_placeholder') }}">{{ old('employee.bio') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="hourly_rate">{{ __('messages.hourly_rate') }} ({{ __('messages.sar') }})</label>
                        <input type="number" id="hourly_rate" name="employee[hourly_rate]"
                            value="{{ old('employee.hourly_rate') }}" step="0.01" min="0" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="employee[is_available]" value="1" {{ old('employee.is_available', true) ? 'checked' : '' }}>
                            <span>{{ __('messages.employee_available') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
                <a href="{{ route('admin.users.staff') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            .category-tag {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background-color: #007bff;
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 14px;
            }

            .category-tag .remove-cat {
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

            .category-tag .remove-cat:hover {
                background-color: rgba(255, 255, 255, 0.2);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const select = document.getElementById('category_select');
                if (!select) return;

                select.addEventListener('change', function () {
                    const selectedId = this.value;
                    if (!selectedId) return;

                    const selectedOption = this.options[this.selectedIndex];
                    const selectedName = selectedOption.getAttribute('data-name');
                    const container = document.getElementById('selected_categories');

                    if (!container) return;

                    // Check if already selected
                    const existingTags = container.querySelectorAll('.category-tag');
                    for (let tag of existingTags) {
                        if (tag.getAttribute('data-id') === selectedId) {
                            this.value = '';
                            return;
                        }
                    }

                    // Create new tag
                    const tagDiv = document.createElement('div');
                    tagDiv.className = 'category-tag';
                    tagDiv.setAttribute('data-id', selectedId);
                    tagDiv.innerHTML = `
                        <input type="hidden" name="employee[categories][]" value="${selectedId}">
                        <span>${selectedName}</span>
                        <button type="button" class="remove-cat" onclick="removeCategory(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    `;

                    container.appendChild(tagDiv);
                    this.value = '';
                });
            });

            function removeCategory(button) {
                const tag = button.closest('.category-tag');
                if (tag) {
                    tag.remove();
                }
            }
        </script>
    @endpush
@endsection
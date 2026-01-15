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
                        <label for="name">{{ __('messages.name') }} <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control"
                            required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('messages.email') }} <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="form-control" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">{{ __('messages.phone') }} <span class="required">*</span></label>
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
                        <label for="category_select">{{ __('messages.categories_specializations') }}</label>
                        <select id="category_select" class="form-control">
                            <option value="">{{ __('messages.select_category_placeholder') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-name="{{ app()->getLocale() === 'en' && $category->name_en ? $category->name_en : $category->name }}" data-name-ar="{{ $category->name }}" data-name-en="{{ $category->name_en ?? $category->name }}">
                                    {{ app()->getLocale() === 'en' && $category->name_en ? $category->name_en : $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="selected_categories"
                            style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;">
                            @if($user->employee && $user->employee->categories->count() > 0)
                                    @foreach($user->employee->categories as $category)
                                        <div class="category-tag" data-id="{{ $category->id }}">
                                            <input type="hidden" name="employee[categories][]" value="{{ $category->id }}">
                                            <span>{{ app()->getLocale() === 'en' && $category->name_en ? $category->name_en : $category->name }}</span>
                                            <button type="button" class="remove-cat" onclick="removeCategory(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                            @endif
                            @if(old('employee.categories'))
                                @foreach(old('employee.categories') as $catId)
                                    @php
                                        $cat = $categories->firstWhere('id', $catId);
                                        $alreadyAdded = $user->employee && $user->employee->categories->contains($catId);
                                    @endphp
                                    @if($cat && !$alreadyAdded)
                                        <div class="category-tag" data-id="{{ $cat->id }}">
                                            <input type="hidden" name="employee[categories][]" value="{{ $cat->id }}">
                                            <span>{{ app()->getLocale() === 'en' && $cat->name_en ? $cat->name_en : $cat->name }}</span>
                                            <button type="button" class="remove-cat" onclick="removeCategory(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="form-group" id="subcategories_group" style="display: none;">
                        <label for="subcategory_select">{{ __('messages.sub_categories') }}</label>
                        <select id="subcategory_select" class="form-control">
                            <option value="">{{ __('messages.select_subcategory_placeholder') }}</option>
                        </select>
                        <div id="selected_subcategories"
                            style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;">
                            @if($user->employee && $user->employee->subCategories->count() > 0)
                                    @foreach($user->employee->subCategories as $subCategory)
                                        <div class="category-tag" data-id="{{ $subCategory->id }}">
                                            <input type="hidden" name="employee[sub_categories][]" value="{{ $subCategory->id }}">
                                            <span>{{ app()->getLocale() === 'en' && $subCategory->name_en ? $subCategory->name_en : $subCategory->name }}</span>
                                            <button type="button" class="remove-cat" onclick="removeCategory(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
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
            // Load subcategories when category is selected
            function loadSubCategories(categoryId) {
                if (!categoryId) {
                    document.getElementById('subcategory_select').innerHTML = '<option value="">{{ __('messages.select_subcategory_placeholder') }}</option>';
                    document.getElementById('subcategories_group').style.display = 'none';
                    return;
                }

                fetch(`/admin/api/categories/${categoryId}/subcategories`)
                    .then(response => response.json())
                    .then(data => {
                        const select = document.getElementById('subcategory_select');
                        select.innerHTML = '<option value="">{{ __('messages.select_subcategory_placeholder') }}</option>';

                        if (data.subcategories && data.subcategories.length > 0) {
                            document.getElementById('subcategories_group').style.display = 'block';
                            data.subcategories.forEach(subCat => {
                                const option = document.createElement('option');
                                option.value = subCat.id;
                                option.textContent = subCat.name;
                                option.setAttribute('data-name', subCat.name);
                                select.appendChild(option);
                            });
                        } else {
                            document.getElementById('subcategories_group').style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading subcategories:', error);
                    });
            }

            document.addEventListener('DOMContentLoaded', function () {
                // Categories
                const categorySelect = document.getElementById('category_select');
                if (categorySelect) {
                    categorySelect.addEventListener('change', function () {
                        const selectedId = this.value;
                        const selectedOption = this.options[this.selectedIndex];

                        if (!selectedId) {
                            document.getElementById('subcategories_group').style.display = 'none';
                            return;
                        }

                        // Show subcategories group and load subcategories
                        loadSubCategories(selectedId);

                        const selectedName = selectedOption.getAttribute('data-name');
                        const container = document.getElementById('selected_categories');

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
                }

                // SubCategories
                const subcategorySelect = document.getElementById('subcategory_select');
                if (subcategorySelect) {
                    subcategorySelect.addEventListener('change', function () {
                        const selectedId = this.value;
                        const selectedOption = this.options[this.selectedIndex];

                        if (!selectedId) return;

                        const selectedName = selectedOption.getAttribute('data-name');
                        const container = document.getElementById('selected_subcategories');

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
                            <input type="hidden" name="employee[sub_categories][]" value="${selectedId}">
                            <span>${selectedName}</span>
                            <button type="button" class="remove-cat" onclick="removeCategory(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        `;

                        container.appendChild(tagDiv);
                        this.value = '';
                    });
                }

                // Load subcategories for existing categories
                const existingCategories = document.querySelectorAll('#selected_categories .category-tag');
                if (existingCategories.length > 0) {
                    const firstCategoryId = existingCategories[0].getAttribute('data-id');
                    loadSubCategories(firstCategoryId);
                }
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
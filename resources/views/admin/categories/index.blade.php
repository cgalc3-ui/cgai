@extends('layouts.dashboard')

@section('title', __('messages.categories'))
@section('page-title', __('messages.categories_list'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.categories_list') }}</h2>
            <p>{{ __('messages.manage_categories_desc') }}</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary" onclick="openCreateModal('{{ route('admin.categories.create') }}', 'createCategoryModal', '{{ __('messages.add_category') }}')">
                <i class="fas fa-plus"></i> {{ __('messages.add_category') }}
            </button>
            <span class="total-count">{{ __('messages.total_categories') }}: {{ $categories->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="filter-form">
            <div class="filter-inputs" style="gap: 30px;">
                <div class="filter-group">
                    <label for="status"><i class="fas fa-toggle-on"></i> {{ __('messages.status') }}:</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                            {{ __('messages.active') }}
                        </option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            {{ __('messages.inactive') }}
                        </option>
                    </select>
                </div>
                <div class="filter-group" style="margin-inline-start: 40px;">
                    <label for="search"><i class="fas fa-search"></i> {{ __('messages.search') }}:</label>
                    <input type="text" name="search" id="search" class="filter-input"
                        placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="filter-actions" style="margin-inline-start: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> {{ __('messages.apply_filter') }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.image') }}</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->trans('name') }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            @else
                                <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>{{ $category->trans('name') }}</td>
                        <td>{{ Str::limit($category->trans('description') ?? '-', 50) }}</td>
                        <td class="text-center">
                            @if($category->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $category->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" onclick="openEditModal('{{ route('admin.categories.edit', $category) }}', 'editCategoryModal', '{{ __('messages.edit_category') }}')" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline"
                                    onsubmit="event.preventDefault(); Confirm.delete({{ json_encode(__('messages.delete_category_confirm')) }}, {{ json_encode(__('messages.confirm_delete_title')) }}).then(confirmed => { if(confirmed) this.submit(); }); return false;">
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
                        <td colspan="6" class="text-center">{{ __('messages.no_categories') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $categories->links('vendor.pagination.custom', ['itemName' => 'categories']) }}
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal-overlay" id="createCategoryModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.add_category') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('createCategoryModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="createCategoryModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Edit Modal (dynamic - will be created on demand) -->
    <div class="modal-overlay" id="editCategoryModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.edit_category') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('editCategoryModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="editCategoryModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        function openCreateModal(url, modalId, title) {
            const modal = document.getElementById(modalId);
            const modalBody = document.getElementById(modalId + 'Body');
            
            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #3b82f6;"></i></div>';
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                modalBody.innerHTML = data.html;
                // Re-execute scripts in the loaded content
                const scripts = modalBody.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                    } else {
                        newScript.textContent = oldScript.textContent;
                    }
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
                
                if (modalId === 'createCategoryModal') {
                    setTimeout(() => {
                        const form = modalBody.querySelector('form');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();
                                handleFormSubmit(form, modalId);
                            });
                        }
                    }, 100);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = '<div class="alert alert-error">{{ __('messages.error') }}</div>';
            });
        }

        function openEditModal(url, modalId, title) {
            const modal = document.getElementById(modalId);
            const modalBody = document.getElementById(modalId + 'Body');
            
            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #3b82f6;"></i></div>';
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                modalBody.innerHTML = data.html;
                // Re-execute scripts in the loaded content
                const scripts = modalBody.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                    } else {
                        newScript.textContent = oldScript.textContent;
                    }
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
                
                // Update form action and add AJAX handler
                setTimeout(() => {
                    const form = modalBody.querySelector('form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            handleFormSubmit(form, modalId);
                        });
                    }
                }, 100);
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = '<div class="alert alert-error">{{ __('messages.error') }}</div>';
            });
        }

        function handleFormSubmit(form, modalId) {
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Ensure _method is set for PUT request
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput && methodInput.value === 'PUT') {
                if (!formData.has('_method')) {
                    formData.append('_method', 'PUT');
                }
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch(form.action, {
                method: 'POST', // Always use POST for FormData with method spoofing
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
                    closeModal(modalId);
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        // Clear previous errors
                        form.querySelectorAll('.error-message').forEach(el => el.remove());
                        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
                        
                        Object.keys(data.errors).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            const container = input ? input.closest('.form-group') : form;
                            if (input) input.classList.add('error');
                            
                            const errorMsg = document.createElement('span');
                            errorMsg.className = 'error-message';
                            errorMsg.textContent = data.errors[key][0];
                            container.appendChild(errorMsg);
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
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeModal(e.target.id);
            }
        });
    </script>

    <style>
        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center;
            z-index: 10000; opacity: 0; visibility: hidden; transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        .modal-overlay.show { opacity: 1; visibility: visible; }
        .modal-container {
            background: white; border-radius: 20px; width: 90%; max-width: 700px;
            max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.95); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            scrollbar-width: none; border: 1px solid #e5e7eb;
        }
        .modal-container::-webkit-scrollbar { display: none; }
        .modal-overlay.show .modal-container { transform: scale(1); }
        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 24px 30px; border-bottom: 1px solid #e5e7eb;
        }
        .modal-title { font-size: 22px; font-weight: 700; color: #1f2937; margin: 0; }
        .modal-close {
            background: none; border: none; font-size: 18px; color: #6b7280;
            cursor: pointer; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s; border-radius: 10px;
        }
        .modal-close:hover { background: #f3f4f6; color: #1f2937; }
        .modal-body { padding: 30px; }

        /* Dark Mode Styles */
        [data-theme="dark"] .modal-overlay {
            background: rgba(0, 0, 0, 0.7);
        }
        [data-theme="dark"] .modal-container {
            background: #1a1d21;
            border-color: rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        [data-theme="dark"] .modal-header {
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }
        [data-theme="dark"] .modal-title {
            color: #ffffff;
        }
        [data-theme="dark"] .modal-close {
            color: rgba(255, 255, 255, 0.5);
        }
        [data-theme="dark"] .modal-close:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }
        
        .error-message { color: #ef4444; font-size: 12px; margin-top: 8px; text-align: right; display: block; }
        .error { border-color: #ef4444 !important; }
    </style>
@endsection
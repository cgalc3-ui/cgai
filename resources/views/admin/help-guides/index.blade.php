@extends('layouts.dashboard')

@section('title', __('messages.help_guides_management'))
@section('page-title', __('messages.help_guides_management'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.help_guides_management') }}</h2>
            <p>{{ __('messages.manage_help_guides_desc') }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('help-guide.index') }}" class="btn btn-secondary" style="margin-left: 10px;">
                <i class="fas fa-eye"></i> {{ __('messages.view_as_user') }}
            </a>
            <button type="button" class="btn btn-primary" onclick="openCreateModal('{{ route('admin.help-guides.create') }}', 'createHelpGuideModal', '{{ __('messages.add_help_guide') }}')">
                <i class="fas fa-plus"></i> {{ __('messages.add_help_guide') }}
            </button>
            <span class="total-count">{{ __('messages.total_help_guides') }}: {{ $helpGuides->total() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.role') }}</th>
                    <th>{{ __('messages.title') }}</th>
                    <th class="text-center">{{ __('messages.icon') }}</th>
                    <th class="text-center">{{ __('messages.sort_order') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($helpGuides as $helpGuide)
                    <tr>
                        <td>
                            @if($helpGuide->role === 'admin')
                                <span class="status-pill active">{{ __('messages.admin_role') }}</span>
                            @elseif($helpGuide->role === 'staff')
                                <span class="status-pill active">{{ __('messages.staff_role') }}</span>
                            @else
                                <span class="status-pill active">{{ __('messages.customer_role') }}</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($helpGuide->trans('title'), 60) }}</td>
                        <td class="text-center">
                            @if($helpGuide->icon)
                                <i class="{{ $helpGuide->icon }}"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $helpGuide->sort_order }}</td>
                        <td class="text-center">
                            @if($helpGuide->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" onclick="openEditModal('{{ route('admin.help-guides.edit', $helpGuide) }}', 'editHelpGuideModal', '{{ __('messages.edit_help_guide') }}')" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.help-guides.destroy', $helpGuide) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('messages.delete_help_guide_confirm') }}')">
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
                        <td colspan="6" class="text-center">{{ __('messages.no_help_guides') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $helpGuides->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal-overlay" id="createHelpGuideModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.add_help_guide') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('createHelpGuideModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="createHelpGuideModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editHelpGuideModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.edit_help_guide') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('editHelpGuideModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="editHelpGuideModalBody">
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
                // Update form action and add AJAX handler
                const form = modalBody.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        handleFormSubmit(form, modalId);
                    });
                }
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
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}...';
            
            fetch(form.action, {
                method: form.method,
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
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transform: scale(0.9);
            transition: transform 0.3s ease;
            /* Hide scrollbar but keep functionality */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .modal-container::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .modal-overlay.show .modal-container {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 24px 24px 0 0;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            color: #6b7280;
            cursor: pointer;
            padding: 5px;
            border-radius: 6px;
            transition: all 0.2s;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .modal-body {
            padding: 24px;
            border-radius: 0 0 24px 24px;
        }

        @media (max-width: 768px) {
            .modal-container {
                width: 95%;
                max-height: 95vh;
            }
        }

        /* Dark Mode Styles */
        [data-theme="dark"] .modal-overlay {
            background: rgba(0, 0, 0, 0.7);
        }

        [data-theme="dark"] .modal-container {
            background: var(--card-bg, #1e1f27);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border-radius: 24px;
        }

        [data-theme="dark"] .modal-header {
            background: var(--card-bg, #1e1f27);
            border-bottom-color: var(--border-color, #2a2d3a);
            border-radius: 24px 24px 0 0;
        }

        [data-theme="dark"] .modal-body {
            border-radius: 0 0 24px 24px;
        }

        [data-theme="dark"] .modal-title {
            color: var(--text-primary, #f1f5f9);
        }

        [data-theme="dark"] .modal-close {
            color: var(--text-secondary, #94a3b8);
            background: transparent;
        }

        [data-theme="dark"] .modal-close:hover {
            background: var(--sidebar-active-bg, #15171d);
            color: var(--text-primary, #f1f5f9);
        }

        [data-theme="dark"] .modal-body {
            background: var(--card-bg, #1e1f27);
            color: var(--text-primary, #f1f5f9);
        }
    </style>
@endsection


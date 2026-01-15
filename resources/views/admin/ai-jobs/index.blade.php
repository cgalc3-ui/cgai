@extends('layouts.dashboard')

@section('title', __('messages.ai_jobs'))
@section('page-title', __('messages.ai_jobs'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.ai_jobs') }}</h2>
            <p>{{ __('messages.manage_ai_jobs_desc') }}</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary"
                onclick="openCreateModal('{{ route('admin.ai-jobs.create') }}', 'createAiJobModal', '{{ __('messages.add_ai_job') }}')">
                <i class="fas fa-plus"></i> {{ __('messages.add_ai_job') }}
            </button>
            <span class="total-count">{{ __('messages.total') }}: {{ $jobs->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.ai-jobs.index') }}" class="filter-form">
            <div class="filter-inputs" style="gap: 30px;">
                <div class="filter-group">
                    <label for="job_type"><i class="fas fa-briefcase"></i> {{ __('messages.job_type') }}:</label>
                    <select name="job_type" id="job_type" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="full_time" {{ request('job_type') === 'full_time' ? 'selected' : '' }}>
                            {{ __('messages.full_time') }}
                        </option>
                        <option value="part_time" {{ request('job_type') === 'part_time' ? 'selected' : '' }}>
                            {{ __('messages.part_time') }}
                        </option>
                        <option value="contract" {{ request('job_type') === 'contract' ? 'selected' : '' }}>
                            {{ __('messages.contract') }}
                        </option>
                        <option value="freelance" {{ request('job_type') === 'freelance' ? 'selected' : '' }}>
                            {{ __('messages.freelance') }}
                        </option>
                        <option value="internship" {{ request('job_type') === 'internship' ? 'selected' : '' }}>
                            {{ __('messages.internship') }}
                        </option>
                    </select>
                </div>
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
                <a href="{{ route('admin.ai-jobs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.company') }}</th>
                    <th>{{ __('messages.location') }}</th>
                    <th>{{ __('messages.job_type') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>{{ app()->getLocale() === 'en' && $job->title_en ? $job->title_en : $job->title }}</td>
                        <td>{{ app()->getLocale() === 'en' && $job->company_en ? $job->company_en : $job->company }}</td>
                        <td>{{ app()->getLocale() === 'en' && $job->location_en ? $job->location_en : $job->location }}</td>
                        <td>
                            @if($job->job_type === 'full_time')
                                <span class="status-pill info">{{ __('messages.full_time') }}</span>
                            @elseif($job->job_type === 'part_time')
                                <span class="status-pill warning">{{ __('messages.part_time') }}</span>
                            @elseif($job->job_type === 'contract')
                                <span class="status-pill teal">{{ __('messages.contract') }}</span>
                            @elseif($job->job_type === 'freelance')
                                <span class="status-pill purple">{{ __('messages.freelance') }}</span>
                            @else
                                <span class="status-pill blue">{{ __('messages.internship') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($job->is_active && (!$job->expires_at || $job->expires_at > now()))
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $job->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" onclick="openEditModal('{{ route('admin.ai-jobs.edit', $job) }}', 'editAiJobModal', '{{ __('messages.edit_ai_job') }}')" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.ai-jobs.destroy', $job) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="event.preventDefault(); Confirm.delete({{ json_encode(__('messages.delete_confirm')) }}, {{ json_encode(__('messages.confirm_delete_title')) }}).then(confirmed => { if(confirmed) this.submit(); }); return false;">
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
                        <td colspan="7" class="text-center">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $jobs->links('vendor.pagination.custom', ['itemName' => 'jobs']) }}
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal-overlay" id="createAiJobModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.add_ai_job') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('createAiJobModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="createAiJobModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editAiJobModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.edit_ai_job') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('editAiJobModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="editAiJobModalBody">
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

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
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
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = '<div class="alert alert-error">{{ __('messages.error') }}</div>';
            });
        }
    </script>
@endsection


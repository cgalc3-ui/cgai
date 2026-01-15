@extends('layouts.dashboard')

@section('title', __('messages.ai_articles'))
@section('page-title', __('messages.ai_articles'))

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.ai_articles') }}</h2>
            <p>{{ __('messages.manage_ai_articles_desc') }}</p>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-primary"
                onclick="openCreateModal('{{ route('admin.ai-articles.create') }}', 'createAiArticleModal', '{{ __('messages.add_ai_article') }}')">
                <i class="fas fa-plus"></i> {{ __('messages.add_ai_article') }}
            </button>
            <span class="total-count">{{ __('messages.total') }}: {{ $articles->total() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container" data-filter-title="{{ __('messages.filter_options') }}">
        <form method="GET" action="{{ route('admin.ai-articles.index') }}" class="filter-form">
            <div class="filter-inputs" style="gap: 30px;">
                <div class="filter-group">
                    <label for="category_id"><i class="fas fa-tags"></i> {{ __('messages.category') }}:</label>
                    <select name="category_id" id="category_id" class="filter-input">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'en' && $category->name_en ? $category->name_en : $category->name }}
                            </option>
                        @endforeach
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
                <a href="{{ route('admin.ai-articles.index') }}" class="btn btn-secondary">
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
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.author') }}</th>
                    <th class="text-center">{{ __('messages.views') }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.published_at') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                    <tr>
                        <td>{{ app()->getLocale() === 'en' && $article->title_en ? $article->title_en : $article->title }}</td>
                        <td>{{ $article->category ? (app()->getLocale() === 'en' && $article->category->name_en ? $article->category->name_en : $article->category->name) : '-' }}</td>
                        <td>{{ $article->author ? $article->author->name : '-' }}</td>
                        <td class="text-center">{{ number_format($article->views_count) }}</td>
                        <td class="text-center">
                            @if($article->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>{{ $article->published_at ? $article->published_at->format('Y-m-d') : '-' }}</td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" onclick="openEditModal('{{ route('admin.ai-articles.edit', $article) }}', 'editAiArticleModal', '{{ __('messages.edit_ai_article') }}')" class="calm-action-btn warning"
                                    title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.ai-articles.destroy', $article) }}" method="POST"
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
            {{ $articles->links('vendor.pagination.custom', ['itemName' => 'articles']) }}
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal-overlay" id="createAiArticleModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.add_ai_article') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('createAiArticleModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="createAiArticleModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editAiArticleModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.edit_ai_article') }}</h3>
                <button type="button" class="modal-close" onclick="closeModal('editAiArticleModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="editAiArticleModalBody">
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


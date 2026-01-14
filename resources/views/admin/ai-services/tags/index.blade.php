@extends('layouts.dashboard')

@section('title', __('messages.ai_service_tags') ?? 'التقنيات الأكثر استخداماً')
@section('page-title', __('messages.ai_service_tags') ?? 'التقنيات الأكثر استخداماً')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.ai_service_tags') ?? 'التقنيات الأكثر استخداماً' }}</h2>
            <p>{{ __('messages.manage_ai_service_tags_desc') ?? 'إدارة التقنيات الأكثر استخداماً في خدمات الذكاء الاصطناعي' }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.ai-services.tags.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_tag') ?? 'إضافة تقنية' }}
            </a>
            <span class="total-count">{{ __('messages.total') ?? 'الإجمالي' }}: {{ $tags->count() }}</span>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.image') }}</th>
                    <th class="text-center">{{ __('messages.services_count') ?? 'عدد الخدمات' }}</th>
                    <th class="text-center">{{ __('messages.status') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tags as $tag)
                    <tr>
                        <td>
                            <span>{{ $tag->trans('name') }}</span>
                        </td>
                        <td>
                            @if($tag->image)
                                <img src="{{ strpos($tag->image, '/storage/') === 0 ? $tag->image : asset('storage/' . $tag->image) }}" 
                                     alt="{{ $tag->trans('name') }}" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $tag->services_count ?? 0 }}</span>
                        </td>
                        <td class="text-center">
                            @if($tag->is_active)
                                <span class="status-pill completed">{{ __('messages.active') }}</span>
                            @else
                                <span class="status-pill cancelled">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('admin.ai-services.tags.edit', $tag) }}"
                                    class="calm-action-btn warning" title="{{ __('messages.edit') }}">
                                    <i class="far fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.ai-services.tags.destroy', $tag) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="event.preventDefault(); Confirm.delete({{ json_encode(__('messages.delete_tag_confirm') ?? 'هل أنت متأكد من حذف هذه التقنية؟') }}, {{ json_encode(__('messages.confirm_delete_title')) }}).then(confirmed => { if(confirmed) this.submit(); }); return false;">
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
                        <td colspan="5" class="text-center">
                            <p class="text-muted" style="padding: 40px;">
                                {{ __('messages.no_tags_found') ?? 'لا توجد تقنيات مسجلة' }}
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection


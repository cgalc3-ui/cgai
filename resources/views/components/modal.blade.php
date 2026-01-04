@props(['modalId', 'title', 'formId' => null])

<div class="modal-overlay" id="{{ $modalId }}">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">{{ $title }}</h3>
            <button type="button" class="modal-close" onclick="closeModal('{{ $modalId }}')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" @if($formId) id="{{ $formId }}_body" @endif>
            {{ $slot }}
        </div>
        @if($formId)
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('{{ $modalId }}')">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </button>
                <button type="submit" form="{{ $formId }}" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        @endif
    </div>
</div>
<div class="modal-overlay" id="{{ $modalId }}">
    <div class="modal">
        <div class="modal-header">
            <h3>{{ $title }}</h3>
        </div>
        <div class="modal-body">
            {{ $slot }}
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('{{ $modalId }}')">
                <i class="fas fa-times"></i> إلغاء
            </button>
            <button type="submit" form="{{ $formId ?? 'modal-form' }}" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ
            </button>
        </div>
    </div>
</div>


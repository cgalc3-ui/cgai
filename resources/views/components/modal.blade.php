<div class="modal-overlay" id="{{ $modalId ?? 'defaultModal' }}" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">{{ $title ?? __('messages.form') }}</h3>
            <button type="button" class="modal-close" onclick="closeModal('{{ $modalId ?? 'defaultModal' }}')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            {{ $slot }}
        </div>
    </div>
</div>

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
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        transform: scale(0.9);
        transition: transform 0.3s ease;
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
    }

    @media (max-width: 768px) {
        .modal-container {
            width: 95%;
            max-height: 95vh;
        }
    }
</style>

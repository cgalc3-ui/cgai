/**
 * Professional Confirmation Modal System
 * Usage: Confirm.show('message', callback)
 */

class Confirm {
    static modal = null;
    static overlay = null;
    static currentCallback = null;

    static init() {
        if (this.modal) return;

        // Create overlay
        this.overlay = document.createElement('div');
        this.overlay.className = 'confirm-overlay';
        this.overlay.id = 'confirmOverlay';

        // Create modal
        this.modal = document.createElement('div');
        this.modal.className = 'confirm-modal';
        this.modal.id = 'confirmModal';
        // Get default translations
        const defaultCancel = window.translations?.cancel || 'إلغاء';
        const defaultOk = window.translations?.ok || 'موافق';
        
        this.modal.innerHTML = `
            <div class="confirm-icon-wrapper">
                <i class="fas fa-question-circle"></i>
            </div>
            <h3 class="confirm-title"></h3>
            <p class="confirm-message"></p>
            <div class="confirm-actions">
                <button class="confirm-btn confirm-btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>${this.escapeHtml(defaultCancel)}</span>
                </button>
                <button class="confirm-btn confirm-btn-ok">
                    <i class="fas fa-check"></i>
                    <span>${this.escapeHtml(defaultOk)}</span>
                </button>
            </div>
        `;

        this.overlay.appendChild(this.modal);
        document.body.appendChild(this.overlay);

        // Event listeners
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.hide();
            }
        });

        const cancelBtn = this.modal.querySelector('.confirm-btn-cancel');
        const okBtn = this.modal.querySelector('.confirm-btn-ok');

        cancelBtn.addEventListener('click', () => {
            this.hide();
        });

        okBtn.addEventListener('click', () => {
            if (this.currentCallback) {
                this.currentCallback(true);
            }
            this.hide();
        });

        // ESC key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('show')) {
                this.hide();
            }
        });
    }

    static show(message, title = null, options = {}) {
        this.init();

        // Get default translations
        const defaultTitle = window.translations?.confirm || 'تأكيد';
        const defaultOk = window.translations?.ok || 'موافق';
        const defaultCancel = window.translations?.cancel || 'إلغاء';

        const {
            okText = defaultOk,
            cancelText = defaultCancel,
            okClass = 'primary',
            cancelClass = 'secondary',
            icon = 'question',
            type = 'info' // info, warning, danger, success
        } = options;

        // Use provided title or default
        const finalTitle = title || defaultTitle;

        // Set content
        const titleEl = this.modal.querySelector('.confirm-title');
        const messageEl = this.modal.querySelector('.confirm-message');
        const iconEl = this.modal.querySelector('.confirm-icon-wrapper i');
        const okBtn = this.modal.querySelector('.confirm-btn-ok');
        const cancelBtn = this.modal.querySelector('.confirm-btn-cancel');

        // Use innerHTML to support HTML entities and preserve formatting
        titleEl.innerHTML = this.escapeHtml(finalTitle);
        messageEl.innerHTML = this.escapeHtml(message);

        // Set icon based on type
        const icons = {
            question: 'fa-question-circle',
            warning: 'fa-exclamation-triangle',
            danger: 'fa-exclamation-circle',
            info: 'fa-info-circle',
            success: 'fa-check-circle'
        };

        iconEl.className = `fas ${icons[icon] || icons.question}`;
        this.modal.className = `confirm-modal confirm-${type}`;

        // Set button texts
        okBtn.innerHTML = `<i class="fas fa-check"></i> <span>${okText}</span>`;
        cancelBtn.innerHTML = `<i class="fas fa-times"></i> <span>${cancelText}</span>`;

        // Show modal
        this.overlay.classList.add('show');
        setTimeout(() => {
            this.modal.classList.add('show');
        }, 10);

        // Return promise
        return new Promise((resolve) => {
            this.currentCallback = (confirmed) => {
                resolve(confirmed);
            };
        });
    }

    static hide() {
        if (!this.modal) return;

        this.modal.classList.remove('show');
        setTimeout(() => {
            this.overlay.classList.remove('show');
            this.currentCallback = null;
        }, 300);
    }

    static confirm(message = 'هل أنت متأكد؟', title = null) {
        // Get translations from page if available
        const okText = window.translations?.ok || 'موافق';
        const cancelText = window.translations?.cancel || 'إلغاء';
        const confirmTitle = title || window.translations?.confirm || 'تأكيد';
        
        return this.show(message, confirmTitle, {
            type: 'info',
            icon: 'question',
            okText: okText,
            cancelText: cancelText
        });
    }

    static delete(message = 'هل أنت متأكد من حذف هذا العنصر؟', title = null) {
        // Get translations from page if available
        const deleteText = window.translations?.delete || 'حذف';
        const cancelText = window.translations?.cancel || 'إلغاء';
        const deleteTitle = title || window.translations?.confirm_delete_title || 'تأكيد الحذف';
        
        return this.show(message, deleteTitle, {
            type: 'danger',
            icon: 'danger',
            okText: deleteText,
            cancelText: cancelText
        });
    }

    static warning(message, title = 'تحذير') {
        const okText = window.translations?.ok || 'موافق';
        const cancelText = window.translations?.cancel || 'إلغاء';
        const warningTitle = window.translations?.warning || 'تحذير';
        
        return this.show(message, title || warningTitle, {
            type: 'warning',
            icon: 'warning',
            okText: okText,
            cancelText: cancelText
        });
    }

    static info(message, title = 'معلومة') {
        const okText = window.translations?.ok || 'موافق';
        const cancelText = window.translations?.cancel || 'إلغاء';
        const infoTitle = window.translations?.info || 'معلومة';
        
        return this.show(message, title || infoTitle, {
            type: 'info',
            icon: 'info',
            okText: okText,
            cancelText: cancelText
        });
    }

    static escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => Confirm.init());
} else {
    Confirm.init();
}

// Make Confirm available globally
window.Confirm = Confirm;

// Helper function for form submission with confirm
window.confirmSubmit = function(event, message, title = 'تأكيد الحذف') {
    event.preventDefault();
    const form = event.target;
    
    Confirm.delete(message, title).then((confirmed) => {
        if (confirmed) {
            form.submit();
        }
    });
    
    return false;
};

// Helper function for button click with confirm
window.confirmDelete = function(message, title, callback) {
    Confirm.delete(message, title).then((confirmed) => {
        if (confirmed && callback) {
            callback();
        }
    });
};

// Auto-convert all onsubmit="return confirm(...)" to use new system
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[onsubmit*="confirm"]');
    forms.forEach(form => {
        const onsubmitAttr = form.getAttribute('onsubmit');
        if (onsubmitAttr && onsubmitAttr.includes('confirm')) {
            // Extract message from onsubmit - handle both single and double quotes
            const match = onsubmitAttr.match(/confirm\(['"]([^'"]+)['"]\)/) || 
                         onsubmitAttr.match(/confirm\(([^)]+)\)/);
            if (match) {
                // Decode HTML entities
                const message = match[1]
                    .replace(/&quot;/g, '"')
                    .replace(/&#039;/g, "'")
                    .replace(/&amp;/g, '&')
                    .replace(/&lt;/g, '<')
                    .replace(/&gt;/g, '>');
                const deleteTitle = window.translations?.confirm_delete_title || 'تأكيد الحذف';
                
                form.removeAttribute('onsubmit');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Confirm.delete(message, deleteTitle).then((confirmed) => {
                        if (confirmed) {
                            form.submit();
                        }
                    });
                });
            }
        }
    });

    // Auto-convert onclick="return confirm(...)"
    const buttons = document.querySelectorAll('[onclick*="confirm"]');
    buttons.forEach(button => {
        const onclickAttr = button.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('confirm') && onclickAttr.includes('return')) {
            const match = onclickAttr.match(/confirm\(['"]([^'"]+)['"]\)/) ||
                         onclickAttr.match(/confirm\(([^)]+)\)/);
            if (match) {
                // Decode HTML entities
                const message = match[1]
                    .replace(/&quot;/g, '"')
                    .replace(/&#039;/g, "'")
                    .replace(/&amp;/g, '&')
                    .replace(/&lt;/g, '<')
                    .replace(/&gt;/g, '>');
                const deleteTitle = window.translations?.confirm_delete_title || 'تأكيد الحذف';
                
                button.removeAttribute('onclick');
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    Confirm.delete(message, deleteTitle).then((confirmed) => {
                        if (confirmed) {
                            const form = button.closest('form');
                            if (form) {
                                form.submit();
                            }
                        }
                    });
                });
            }
        }
    });
});


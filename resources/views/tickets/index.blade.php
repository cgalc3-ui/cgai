@extends('layouts.dashboard')

@section('title', 'الدعم والتذاكر')
@section('page-title', 'الدعم والتذاكر')

@section('content')
    <div class="tickets-container">
        <!-- Header -->
        <div class="tickets-header">
            <div class="header-left">
                <h2>الدعم والتذاكر</h2>
                <p>جميع تذاكر الدعم الخاصة بك</p>
            </div>
            <div class="header-right">
                <button onclick="openCreateTicketModal()" class="btn-create-ticket">
                    <i class="fas fa-plus"></i>
                    إنشاء تذكرة جديدة
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-container">
            <form method="GET" action="{{ route('tickets.index') }}" class="filters-form">
                <div class="filter-group">
                    <label>الحالة:</label>
                    <select name="status" class="filter-select">
                        <option value="">الكل</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوحة</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>قيد المعالجة
                        </option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>محلولة</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>مغلقة</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>الأولوية:</label>
                    <select name="priority" class="filter-select">
                        <option value="">الكل</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>عاجلة</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">تطبيق</button>
                <a href="{{ route('tickets.index') }}" class="btn-clear">مسح</a>
            </form>
        </div>

        <!-- Tickets List -->
        <div class="tickets-list">
            @forelse($tickets as $ticket)
                <div class="ticket-card {{ $ticket->status }}">
                    <div class="ticket-header">
                        <div class="ticket-title-section">
                            <h3 class="ticket-title">
                                <a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->subject }}</a>
                            </h3>
                            <div class="ticket-meta">
                                <span class="ticket-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $ticket->created_at->format('Y-m-d') }}
                                </span>
                            </div>
                        </div>
                        <div class="ticket-status-section">
                            @if($ticket->status === 'open')
                                <span class="badge badge-warning">مفتوحة</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="badge badge-info">قيد المعالجة</span>
                            @elseif($ticket->status === 'resolved')
                                <span class="badge badge-success">محلولة</span>
                            @else
                                <span class="badge badge-secondary">مغلقة</span>
                            @endif
                            @if($ticket->priority === 'urgent')
                                <span class="badge badge-danger">عاجلة</span>
                            @elseif($ticket->priority === 'high')
                                <span class="badge badge-warning">عالية</span>
                            @elseif($ticket->priority === 'medium')
                                <span class="badge badge-info">متوسطة</span>
                            @else
                                <span class="badge badge-secondary">منخفضة</span>
                            @endif
                        </div>
                    </div>
                    <div class="ticket-body">
                        <p class="ticket-description">{{ \Illuminate\Support\Str::limit($ticket->description, 150) }}</p>
                        @if($ticket->latestMessage)
                            <div class="ticket-last-message">
                                <i class="fas fa-comment"></i>
                                آخر رد: {{ $ticket->latestMessage->created_at->diffForHumans() }}
                            </div>
                        @endif
                    </div>
                    <div class="ticket-footer">
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn-view-ticket">
                            <i class="fas fa-eye"></i>
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-headset"></i>
                    <h3>لا توجد تذاكر</h3>
                    <p>لم يتم العثور على أي تذاكر دعم حالياً</p>
                    <button onclick="openCreateTicketModal()" class="btn-create-ticket">
                        <i class="fas fa-plus"></i>
                        إنشاء تذكرة جديدة
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
            <div class="pagination-wrapper">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .tickets-container {
                background: white;
                border-radius: 12px;
                box-shadow: var(--card-shadow);
                padding: 24px;
            }

            .tickets-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
                padding-bottom: 16px;
                border-bottom: 2px solid #e5e7eb;
            }

            .header-left h2 {
                font-size: 24px;
                font-weight: 700;
                color: #1f2937;
                margin: 0 0 4px 0;
            }

            .header-left p {
                color: #6b7280;
                font-size: 14px;
                margin: 0;
            }

            .btn-create-ticket {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                background: #3b82f6;
                color: white;
                border-radius: 8px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                transition: all 0.2s;
                border: none;
                cursor: pointer;
            }

            .btn-create-ticket:hover {
                background: #2563eb;
                transform: translateY(-1px);
            }

            .filters-container {
                background: #f9fafb;
                padding: 16px;
                border-radius: 8px;
                margin-bottom: 24px;
            }

            .filters-form {
                display: flex;
                gap: 16px;
                align-items: flex-end;
            }

            .filter-group {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .filter-group label {
                font-size: 12px;
                font-weight: 600;
                color: #4b5563;
            }

            .filter-select {
                padding: 8px 12px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                font-size: 14px;
                background: white;
            }

            .btn-filter,
            .btn-clear {
                padding: 8px 16px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                border: none;
                text-decoration: none;
                display: inline-block;
            }

            .btn-filter {
                background: #3b82f6;
                color: white;
            }

            .btn-clear {
                background: #e5e7eb;
                color: #4b5563;
            }

            .tickets-list {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .ticket-card {
                background: #f9fafb;
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                padding: 20px;
                transition: all 0.2s;
            }

            .ticket-card:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .ticket-card.open {
                border-color: #f59e0b;
            }

            .ticket-card.in_progress {
                border-color: #3b82f6;
            }

            .ticket-card.resolved {
                border-color: #10b981;
            }

            .ticket-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 12px;
            }

            .ticket-title {
                font-size: 18px;
                font-weight: 700;
                margin: 0 0 8px 0;
            }

            .ticket-title a {
                color: #1f2937;
                text-decoration: none;
            }

            .ticket-title a:hover {
                color: #3b82f6;
            }

            .ticket-meta {
                display: flex;
                gap: 12px;
                font-size: 12px;
                color: #6b7280;
            }

            .ticket-status-section {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            .ticket-body {
                margin-bottom: 16px;
            }

            .ticket-description {
                color: #4b5563;
                font-size: 14px;
                line-height: 1.6;
                margin: 0 0 8px 0;
            }

            .ticket-last-message {
                font-size: 12px;
                color: #9ca3af;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .ticket-footer {
                display: flex;
                justify-content: flex-end;
            }

            .btn-view-ticket {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 16px;
                background: #3b82f6;
                color: white;
                border-radius: 6px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 600;
                transition: all 0.2s;
            }

            .btn-view-ticket:hover {
                background: #2563eb;
            }

            .empty-state {
                text-align: center;
                padding: 60px 20px;
            }

            .empty-state i {
                font-size: 64px;
                color: #d1d5db;
                margin-bottom: 16px;
            }

            .empty-state h3 {
                font-size: 20px;
                font-weight: 700;
                color: #4b5563;
                margin: 0 0 8px 0;
            }

            .empty-state p {
                color: #9ca3af;
                font-size: 14px;
                margin: 0 0 24px 0;
            }

            .pagination-wrapper {
                margin-top: 24px;
                display: flex;
                justify-content: center;
            }

            /* Modal Styles */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.6);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .modal-container {
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                max-width: 700px;
                width: 100%;
                max-height: 90vh;
                display: flex;
                flex-direction: column;
            }

            .modal-header {
                padding: 20px 24px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-header h2 {
                margin: 0;
                font-size: 20px;
                color: #1f2937;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 24px;
                color: #6b7280;
                cursor: pointer;
                padding: 0;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 6px;
                transition: all 0.2s;
            }

            .modal-close:hover {
                background: #f3f4f6;
                color: #1f2937;
            }

            .modal-body {
                padding: 24px;
                overflow-y: auto;
                flex: 1;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
            }

            .required {
                color: #ef4444;
            }

            .form-input,
            .form-select,
            .form-textarea {
                width: 100%;
                padding: 12px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 14px;
                font-family: inherit;
                transition: all 0.2s;
                box-sizing: border-box;
            }

            .form-input:focus,
            .form-select:focus,
            .form-textarea:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .form-textarea {
                resize: vertical;
            }

            .form-help {
                display: block;
                font-size: 12px;
                color: #6b7280;
                margin-top: 4px;
            }

            .error-message {
                display: block;
                font-size: 12px;
                color: #ef4444;
                margin-top: 4px;
                min-height: 16px;
            }

            .form-actions {
                display: flex;
                gap: 12px;
                margin-top: 24px;
            }

            .btn-submit {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                background: #3b82f6;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-submit:hover:not(:disabled) {
                background: #2563eb;
            }

            .btn-submit:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            .btn-cancel {
                display: inline-flex;
                align-items: center;
                padding: 12px 24px;
                background: #f3f4f6;
                color: #4b5563;
                border: none;
                border-radius: 8px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-cancel:hover {
                background: #e5e7eb;
            }
        </style>
    @endpush

    <!-- Create Ticket Modal -->
    <div id="createTicketModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> إنشاء تذكرة جديدة</h2>
                <button class="modal-close" onclick="closeCreateTicketModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="createTicketForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="ticketSubject">الموضوع <span class="required">*</span></label>
                        <input type="text" name="subject" id="ticketSubject" class="form-input" required>
                        <span class="error-message" id="subjectError"></span>
                    </div>

                    <div class="form-group">
                        <label for="ticketPriority">الأولوية</label>
                        <select name="priority" id="ticketPriority" class="form-select">
                            <option value="low">منخفضة</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="high">عالية</option>
                            <option value="urgent">عاجلة</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ticketDescription">الوصف <span class="required">*</span></label>
                        <textarea name="description" id="ticketDescription" class="form-textarea" rows="6"
                            required></textarea>
                        <span class="error-message" id="descriptionError"></span>
                    </div>

                    <div class="form-group">
                        <label for="ticketAttachments">المرفقات (صور)</label>
                        <input type="file" name="attachments[]" id="ticketAttachments" class="form-input" multiple
                            accept="image/*">
                        <small class="form-help">يمكنك إرفاق حتى 5 صور (حجم كل صورة حتى 5MB)</small>
                        <span class="error-message" id="attachmentsError"></span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="submitTicketBtn">
                            <i class="fas fa-paper-plane"></i>
                            إرسال التذكرة
                        </button>
                        <button type="button" class="btn-cancel" onclick="closeCreateTicketModal()">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openCreateTicketModal() {
                document.getElementById('createTicketModal').style.display = 'flex';
                // Clear form
                document.getElementById('createTicketForm').reset();
                // Clear errors
                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            }

            function closeCreateTicketModal() {
                document.getElementById('createTicketModal').style.display = 'none';
                // Clear form
                document.getElementById('createTicketForm').reset();
                // Clear errors
                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            }

            // Handle create ticket form submission
            document.getElementById('createTicketForm')?.addEventListener('submit', function (e) {
                e.preventDefault();

                const form = this;
                const submitBtn = document.getElementById('submitTicketBtn');
                const formData = new FormData(form);

                // Clear previous errors
                document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';

                fetch('{{ route("tickets.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw { errors: data.errors || {}, message: data.message || 'حدث خطأ' };
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success || !data.errors) {
                            // Success - close modal and reload page
                            closeCreateTicketModal();
                            // Reload page to show new ticket
                            window.location.reload();
                        } else {
                            // Show validation errors
                            if (data.errors) {
                                if (data.errors.subject) {
                                    document.getElementById('subjectError').textContent = data.errors.subject[0];
                                }
                                if (data.errors.description) {
                                    document.getElementById('descriptionError').textContent = data.errors.description[0];
                                }
                                if (data.errors.attachments) {
                                    document.getElementById('attachmentsError').textContent = Array.isArray(data.errors.attachments)
                                        ? data.errors.attachments[0]
                                        : data.errors.attachments;
                                }
                                if (data.errors['attachments.*']) {
                                    document.getElementById('attachmentsError').textContent = Array.isArray(data.errors['attachments.*'])
                                        ? data.errors['attachments.*'][0]
                                        : data.errors['attachments.*'];
                                }
                            } else {
                                alert('حدث خطأ: ' + (data.message || 'فشل إنشاء التذكرة'));
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (error.errors) {
                            // Show validation errors
                            if (error.errors.subject) {
                                document.getElementById('subjectError').textContent = error.errors.subject[0];
                            }
                            if (error.errors.description) {
                                document.getElementById('descriptionError').textContent = error.errors.description[0];
                            }
                            if (error.errors.attachments) {
                                document.getElementById('attachmentsError').textContent = Array.isArray(error.errors.attachments)
                                    ? error.errors.attachments[0]
                                    : error.errors.attachments;
                            }
                        } else {
                            alert('حدث خطأ أثناء إنشاء التذكرة. يرجى المحاولة مرة أخرى.');
                        }
                    })
                    .finally(() => {
                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال التذكرة';
                    });
            });

            // Close modal when clicking outside
            document.getElementById('createTicketModal')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeCreateTicketModal();
                }
            });

            // Close modal on ESC key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('createTicketModal');
                    if (modal && modal.style.display !== 'none') {
                        closeCreateTicketModal();
                    }
                }
            });
        </script>
    @endpush
@endsection
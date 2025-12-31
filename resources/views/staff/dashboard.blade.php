@extends('layouts.dashboard')

@section('title', 'لوحة تحكم الموظفين')
@section('page-title', 'لوحة تحكم الموظفين')

@section('content')
    <div class="dashboard-description">
        <h2>لوحة تحكم الموظفين</h2>
        <p>إدارة العملاء ومتابعة الطلبات والمعاملات. الوصول السريع إلى المعلومات المهمة وإدارة العمليات اليومية.</p>
    </div>

    <!-- Statistics Cards -->
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <!-- Total Customers -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">إجمالي العملاء</h3>
                <i class="fas fa-ellipsis-h stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle purple">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ $stats['total_customers'] ?? 0 }}</h2>
                        <span class="stat-card-trend up">
                            <i class="fas fa-arrow-up"></i> +{{ number_format(5.2, 1) }}%
                        </span>
                    </div>
                    <span class="stat-card-subtitle">العملاء المسجلين في النظام</span>
                </div>
            </div>
            <div class="stat-card-chart">
                <svg viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="M0,25 C10,20 20,28 30,22 C40,16 50,18 60,12 C70,6 80,10 90,8 C100,6 100,5 100,5" fill="none"
                        stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">إجمالي الحجوزات</h3>
                <i class="fas fa-ellipsis-h stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle blue">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ $stats['total_bookings'] ?? 0 }}</h2>
                        <span class="stat-card-trend up">
                            <i class="fas fa-arrow-up"></i> +{{ number_format(3.8, 1) }}%
                        </span>
                    </div>
                    <span class="stat-card-subtitle">{{ $stats['today_bookings'] ?? 0 }} حجز اليوم</span>
                </div>
            </div>
            <div class="stat-card-chart">
                <svg viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="M0,20 C10,25 20,15 30,18 C40,21 50,10 60,15 C70,20 80,5 90,10 C100,15 100,10 100,10"
                        fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="stat-card">
            <div class="stat-card-title-row">
                <h3 class="stat-card-title">حجوزات قيد الانتظار</h3>
                <i class="fas fa-ellipsis-h stat-card-more"></i>
            </div>
            <div class="stat-card-main">
                <div class="stat-card-icon-circle orange">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value-container">
                        <h2 class="stat-card-value">{{ $stats['pending_bookings'] ?? 0 }}</h2>
                        <span class="stat-card-trend down">
                            <i class="fas fa-clock"></i>
                        </span>
                    </div>
                    <span class="stat-card-subtitle">تحتاج إلى مراجعة وتأكيد</span>
                </div>
            </div>
            <div class="stat-card-chart">
                <svg viewBox="0 0 100 30" preserveAspectRatio="none">
                    <path d="M0,28 C10,22 20,25 30,20 C40,15 50,22 60,18 C70,14 80,18 90,12 C100,6 100,5 100,5" fill="none"
                        stroke="#f59e0b" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Section -->
    <div class="section-container">
        <div class="section-header">
            <h3>الحجوزات الخاصة بي</h3>
            <p>آخر الحجوزات المخصصة لك</p>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>العميل</th>
                        <th>الخدمة</th>
                        <th>التاريخ والوقت</th>
                        <th>السعر</th>
                        <th>حالة الحجز</th>
                        <th>حالة الدفع</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings ?? [] as $booking)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">{{ $booking->customer->name ?? 'غير محدد' }}</div>
                                    <div class="user-details">
                                        @if($booking->customer->phone)
                                            <span><i class="fas fa-phone"></i> {{ $booking->customer->phone }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="service-info">
                                    <div class="service-name">{{ $booking->service->name ?? 'غير محدد' }}</div>
                                    <div class="service-duration">
                                        <i class="fas fa-clock"></i> {{ $booking->formatted_duration }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="datetime-info">
                                    <div class="date">
                                        <i class="fas fa-calendar"></i> {{ $booking->booking_date->format('Y-m-d') }}
                                    </div>
                                    <div class="time">
                                        <i class="fas fa-clock"></i> {{ $booking->start_time }} - {{ $booking->end_time }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong class="price">{{ number_format($booking->total_price, 2) }} ر.س</strong>
                            </td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-hourglass-half"></i> قيد الانتظار
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge badge-info">
                                        <i class="fas fa-check-circle"></i> مؤكد
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-double"></i> مكتمل
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times-circle"></i> ملغي
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($booking->payment_status === 'paid')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> مدفوع
                                    </span>
                                @elseif($booking->payment_status === 'unpaid')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-exclamation-triangle"></i> غير مدفوع
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-undo"></i> مسترد
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <h3>لا توجد حجوزات</h3>
                                    <p>لم يتم العثور على أي حجوزات مخصصة لك</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="actions-grid">
        <button class="action-btn" onclick="window.location.href='{{ route('staff.my-schedule') }}'">
            <i class="fas fa-calendar-alt"></i>
            <span>أيام العمل</span>
        </button>
        <button class="action-btn" onclick="openTicketsModal()">
            <i class="fas fa-headset"></i>
            <span>التذاكر والدعم</span>
        </button>
        <button class="action-btn" onclick="window.location.href='#'">
            <i class="fas fa-chart-line"></i>
            <span>التقارير</span>
        </button>
    </div>

    <!-- Tickets Modal -->
    <div id="ticketsModal" class="modal-overlay" style="display: none;">
        <div class="modal-container tickets-modal">
            <div class="modal-header">
                <h2><i class="fas fa-headset"></i> التذاكر والدعم</h2>
                <button class="modal-close" onclick="closeTicketsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="tickets-filters">
                    <select id="ticketStatusFilter" onchange="loadTickets()">
                        <option value="">جميع الحالات</option>
                        <option value="open">مفتوحة</option>
                        <option value="in_progress">قيد المعالجة</option>
                        <option value="resolved">محلولة</option>
                        <option value="closed">مغلقة</option>
                    </select>
                    <select id="ticketPriorityFilter" onchange="loadTickets()">
                        <option value="">جميع الأولويات</option>
                        <option value="low">منخفضة</option>
                        <option value="medium">متوسطة</option>
                        <option value="high">عالية</option>
                        <option value="urgent">عاجلة</option>
                    </select>
                    <button class="btn btn-primary" onclick="openCreateTicketModal()">
                        <i class="fas fa-plus"></i> تذكرة جديدة
                    </button>
                </div>
                <div id="ticketsList" class="tickets-list-modal">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i> جاري التحميل...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Ticket Modal -->
    <div id="createTicketModal" class="modal-overlay" style="display: none;">
        <div class="modal-container create-ticket-modal">
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

    @push('styles')
        <style>
            .stat-card-icon.orange {
                background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
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
                max-width: 900px;
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

            .tickets-filters {
                display: flex;
                gap: 12px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }

            .tickets-filters select {
                padding: 10px 16px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 14px;
                background: white;
                min-width: 150px;
            }

            .tickets-filters .btn {
                padding: 10px 20px;
                border-radius: 8px;
                border: none;
                cursor: pointer;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .tickets-list-modal {
                min-height: 300px;
            }

            .ticket-item-modal {
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 16px;
                margin-bottom: 12px;
                cursor: pointer;
                transition: all 0.2s;
            }

            .ticket-item-modal:hover {
                background: #f3f4f6;
                border-color: #d1d5db;
            }

            .ticket-item-header {
                display: flex;
                justify-content: space-between;
                align-items: start;
                margin-bottom: 12px;
            }

            .ticket-item-title {
                font-weight: 600;
                color: #1f2937;
                font-size: 16px;
                margin: 0 0 4px 0;
            }

            .ticket-item-meta {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                font-size: 12px;
                color: #6b7280;
            }

            .ticket-item-description {
                color: #4b5563;
                font-size: 14px;
                margin-top: 8px;
                line-height: 1.5;
            }

            .badge {
                padding: 4px 12px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 500;
            }

            .badge-open {
                background: #fef3c7;
                color: #92400e;
            }

            .badge-in_progress {
                background: #dbeafe;
                color: #1e40af;
            }

            .badge-resolved {
                background: #d1fae5;
                color: #065f46;
            }

            .badge-closed {
                background: #e5e7eb;
                color: #374151;
            }

            .badge-urgent {
                background: #fee2e2;
                color: #991b1b;
            }

            .badge-high {
                background: #fed7aa;
                color: #9a3412;
            }

            .badge-medium {
                background: #dbeafe;
                color: #1e40af;
            }

            .badge-low {
                background: #e5e7eb;
                color: #6b7280;
            }

            .loading-spinner {
                text-align: center;
                padding: 40px;
                color: #6b7280;
            }

            .empty-state-modal {
                text-align: center;
                padding: 60px 20px;
                color: #6b7280;
            }

            .empty-state-modal i {
                font-size: 48px;
                margin-bottom: 16px;
                color: #d1d5db;
            }

            /* Create Ticket Modal Styles */
            .create-ticket-modal {
                max-width: 700px;
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

    @push('scripts')
        <script>
            function openTicketsModal() {
                document.getElementById('ticketsModal').style.display = 'flex';
                loadTickets();
            }

            function closeTicketsModal() {
                document.getElementById('ticketsModal').style.display = 'none';
            }

            function loadTickets() {
                const ticketsList = document.getElementById('ticketsList');
                const status = document.getElementById('ticketStatusFilter').value;
                const priority = document.getElementById('ticketPriorityFilter').value;

                ticketsList.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>';

                let url = '{{ route("staff.tickets") }}?';
                if (status) url += 'status=' + status + '&';
                if (priority) url += 'priority=' + priority + '&';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.data && data.data.data.length > 0) {
                            let html = '';
                            data.data.data.forEach(ticket => {
                                const statusBadge = getStatusBadge(ticket.status);
                                const priorityBadge = getPriorityBadge(ticket.priority);
                                const createdAt = new Date(ticket.created_at).toLocaleDateString('ar-SA');

                                const ticketUrl = '{{ url("/tickets") }}/' + ticket.id;
                                html += `
                                                <div class="ticket-item-modal" onclick="window.location.href='${ticketUrl}'">
                                                    <div class="ticket-item-header">
                                                        <div>
                                                            <h3 class="ticket-item-title">${ticket.subject}</h3>
                                                            <div class="ticket-item-meta">
                                                                <span>#${ticket.id}</span>
                                                                <span><i class="fas fa-calendar"></i> ${createdAt}</span>
                                                                ${statusBadge}
                                                                ${priorityBadge}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="ticket-item-description">${ticket.description ? ticket.description.substring(0, 100) + '...' : ''}</p>
                                                </div>
                                            `;
                            });
                            ticketsList.innerHTML = html;
                        } else {
                            ticketsList.innerHTML = `
                                            <div class="empty-state-modal">
                                                <i class="fas fa-inbox"></i>
                                                <h3>لا توجد تذاكر</h3>
                                                <p>لم يتم العثور على أي تذاكر دعم</p>
                                            </div>
                                        `;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading tickets:', error);
                        ticketsList.innerHTML = `
                                        <div class="empty-state-modal">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <h3>حدث خطأ</h3>
                                            <p>فشل تحميل التذاكر. يرجى المحاولة مرة أخرى.</p>
                                        </div>
                                    `;
                    });
            }

            function getStatusBadge(status) {
                const badges = {
                    'open': '<span class="badge badge-open">مفتوحة</span>',
                    'in_progress': '<span class="badge badge-in_progress">قيد المعالجة</span>',
                    'resolved': '<span class="badge badge-resolved">محلولة</span>',
                    'closed': '<span class="badge badge-closed">مغلقة</span>'
                };
                return badges[status] || '';
            }

            function getPriorityBadge(priority) {
                const badges = {
                    'urgent': '<span class="badge badge-urgent">عاجلة</span>',
                    'high': '<span class="badge badge-high">عالية</span>',
                    'medium': '<span class="badge badge-medium">متوسطة</span>',
                    'low': '<span class="badge badge-low">منخفضة</span>'
                };
                return badges[priority] || '';
            }

            // Close modal when clicking outside
            document.getElementById('ticketsModal')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeTicketsModal();
                }
            });

            // Close modal on ESC key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const ticketsModal = document.getElementById('ticketsModal');
                    const createModal = document.getElementById('createTicketModal');
                    if (createModal && createModal.style.display !== 'none') {
                        closeCreateTicketModal();
                    } else if (ticketsModal && ticketsModal.style.display !== 'none') {
                        closeTicketsModal();
                    }
                }
            });

            // Create Ticket Modal Functions
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
                            // Success - close modal and reload tickets
                            closeCreateTicketModal();
                            if (document.getElementById('ticketsModal').style.display !== 'none') {
                                loadTickets();
                            } else {
                                // If tickets modal is closed, open it and show the new ticket
                                openTicketsModal();
                                setTimeout(() => loadTickets(), 300);
                            }
                            // Show success message
                            alert('تم إنشاء التذكرة بنجاح');
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
                        alert('حدث خطأ أثناء إنشاء التذكرة. يرجى المحاولة مرة أخرى.');
                    })
                    .finally(() => {
                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال التذكرة';
                    });
            });

            // Close create modal when clicking outside
            document.getElementById('createTicketModal')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeCreateTicketModal();
                }
            });
        </script>
    @endpush
@endsection
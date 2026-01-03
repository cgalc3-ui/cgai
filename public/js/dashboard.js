// Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // Sidebar collapse toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const dashboardContainer = document.getElementById('dashboardContainer');

    // Check for saved sidebar state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        if (dashboardContainer) dashboardContainer.classList.add('sidebar-collapsed');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            if (dashboardContainer) {
                dashboardContainer.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', dashboardContainer.classList.contains('sidebar-collapsed'));
            }
        });
    }

    // Ensure active nav group is open on load
    const activeHeaders = document.querySelectorAll('.nav-group-header.active');
    activeHeaders.forEach(header => {
        const navGroup = header.closest('.nav-group');
        const navGroupItems = navGroup.querySelector('.nav-group-items');
        const arrow = header.querySelector('.nav-arrow');
        if (navGroupItems) {
            navGroupItems.classList.add('expanded');
            if (arrow) arrow.classList.add('rotated');
        }
    });

    // Also check for sub-items that are active to keep parent open
    const activeSubItems = document.querySelectorAll('.nav-group-items .nav-item.active');
    activeSubItems.forEach(item => {
        const navGroupItems = item.closest('.nav-group-items');
        const navGroup = item.closest('.nav-group');
        const header = navGroup ? navGroup.querySelector('.nav-group-header') : null;
        const arrow = header ? header.querySelector('.nav-arrow') : null;

        if (navGroupItems) {
            navGroupItems.classList.add('expanded');
            if (header) header.classList.add('active');
            if (arrow) arrow.classList.add('rotated');
        }
    });

    // Language toggle
    const languageToggle = document.getElementById('languageToggle');
    const languageMenu = document.getElementById('languageMenu');

    if (languageToggle && languageMenu) {
        languageToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            languageMenu.classList.toggle('show');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!languageToggle.contains(e.target) && !languageMenu.contains(e.target)) {
                languageMenu.classList.remove('show');
            }
        });
    }

    // Modal functionality
    initModals();
});

// Toggle navigation group
function toggleNavGroup(element) {
    const navGroup = element.closest('.nav-group');
    const navGroupItems = navGroup.querySelector('.nav-group-items');
    const arrow = element.querySelector('.nav-arrow');

    navGroupItems.classList.toggle('expanded');
    arrow.classList.toggle('rotated');
}

// Modal Functions
function initModals() {
    // Close modal on overlay click
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeModal(e.target);
        }
    });

    // Close modal on close button click
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('modal-close') || e.target.closest('.modal-close')) {
            const modal = e.target.closest('.modal-overlay');
            if (modal) {
                closeModal(modal);
            }
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal-overlay.show');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Focus first input
        const firstInput = modal.querySelector('input, select, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
}

function closeModal(modalElement) {
    if (typeof modalElement === 'string') {
        modalElement = document.getElementById(modalElement);
    }

    if (modalElement) {
        modalElement.classList.remove('show');
        document.body.style.overflow = '';

        // Reset form if exists
        const form = modalElement.querySelector('form');
        if (form) {
            form.reset();
            // Clear validation errors
            const errorMessages = form.querySelectorAll('.error-message');
            errorMessages.forEach(error => error.remove());
            const errorInputs = form.querySelectorAll('.error');
            errorInputs.forEach(input => input.classList.remove('error'));
        }
    }
}

// Global function to open modal from links
window.openModal = openModal;
window.closeModal = closeModal;


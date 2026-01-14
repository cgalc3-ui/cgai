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

    // Sidebar collapse toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const dashboardContainer = document.getElementById('dashboardContainer');
    const sidebar = document.getElementById('sidebar');

    // Create sidebar overlay for mobile
    let sidebarOverlay = document.querySelector('.sidebar-overlay');
    if (!sidebarOverlay) {
        sidebarOverlay = document.createElement('div');
        sidebarOverlay.className = 'sidebar-overlay';
        document.body.appendChild(sidebarOverlay);
    }

    // Check if we're on mobile
    function isMobile() {
        return window.innerWidth <= 767;
    }

    // Toggle sidebar on mobile
    function toggleSidebarMobile() {
        if (!sidebar) return;

        const isOpen = sidebar.classList.contains('open');
        if (isOpen) {
            closeSidebarMobile();
        } else {
            openSidebarMobile();
        }
    }

    // Open sidebar on mobile
    function openSidebarMobile() {
        if (!sidebar) return;

        // Make sidebar visible first (but still off-screen)
        sidebar.style.visibility = 'visible';
        sidebar.style.opacity = '1';
        sidebar.style.pointerEvents = 'auto';

        // Force reflow to ensure visibility is applied
        sidebar.offsetHeight;

        // Then add open class to trigger slide-in animation
        requestAnimationFrame(() => {
            sidebar.classList.add('open');
        });

        // Show overlay
        if (sidebarOverlay) {
            sidebarOverlay.style.display = 'block';
            sidebarOverlay.style.visibility = 'visible';
            sidebarOverlay.style.opacity = '1';
            sidebarOverlay.style.pointerEvents = 'auto';
            sidebarOverlay.classList.add('show');
        }

        // Prevent body scroll
        document.body.classList.add('sidebar-open');
        document.body.style.overflow = 'hidden';
    }

    // Close sidebar on mobile
    function closeSidebarMobile() {
        if (!sidebar) return;

        // Remove open class to trigger slide-out animation
        sidebar.classList.remove('open');

        // Hide overlay
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('show');
            sidebarOverlay.style.display = 'none';
            sidebarOverlay.style.visibility = 'hidden';
            sidebarOverlay.style.opacity = '0';
            sidebarOverlay.style.pointerEvents = 'none';
        }

        // Restore body scroll
        document.body.classList.remove('sidebar-open');
        document.body.style.overflow = '';

        // Hide sidebar after slide-out animation completes
        setTimeout(() => {
            if (!sidebar.classList.contains('open')) {
                sidebar.style.visibility = 'hidden';
                sidebar.style.opacity = '1'; // Keep opacity at 1
                sidebar.style.pointerEvents = 'none';
            }
        }, 300);
    }

    // Initialize sidebar as hidden on mobile
    if (isMobile() && sidebar) {
        sidebar.style.visibility = 'hidden';
        sidebar.style.opacity = '1'; // Keep opacity at 1, only use visibility
        sidebar.style.pointerEvents = 'none';
        sidebar.classList.remove('open');
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('show');
        }
    }

    // Check for saved sidebar state (only on desktop)
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed && !isMobile()) {
        if (dashboardContainer) dashboardContainer.classList.add('sidebar-collapsed');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (isMobile()) {
                toggleSidebarMobile();
            } else {
                if (dashboardContainer) {
                    dashboardContainer.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', dashboardContainer.classList.contains('sidebar-collapsed'));
                }
            }
        });
    }

    // Close sidebar when clicking overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            closeSidebarMobile();
        });
    }

    // Close sidebar when clicking outside on mobile
    if (sidebar) {
        document.addEventListener('click', (e) => {
            if (isMobile() && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) &&
                    sidebarToggle && !sidebarToggle.contains(e.target) &&
                    !sidebarOverlay.contains(e.target)) {
                    closeSidebarMobile();
                }
            }
        });
    }

    // Close sidebar when pressing Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isMobile() && sidebar && sidebar.classList.contains('open')) {
            closeSidebarMobile();
        }
    });

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (!isMobile()) {
                closeSidebarMobile();
                // Restore collapsed state on desktop
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (dashboardContainer) {
                    if (isCollapsed) {
                        dashboardContainer.classList.add('sidebar-collapsed');
                    } else {
                        dashboardContainer.classList.remove('sidebar-collapsed');
                    }
                }
            } else {
                // Ensure sidebar is hidden on mobile after resize
                if (sidebar && !sidebar.classList.contains('open')) {
                    sidebar.style.visibility = 'hidden';
                    sidebar.style.opacity = '0';
                }
            }
        }, 250);
    });

    // Initialize on page load
    if (isMobile() && sidebar) {
        // Ensure sidebar starts hidden
        sidebar.style.visibility = 'hidden';
        sidebar.style.opacity = '0';
        sidebar.classList.remove('open');
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('show');
        }
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

    // Make tables responsive on mobile
    makeTablesResponsive();
});

// Make tables responsive by adding data-label attributes
function makeTablesResponsive() {
    const tables = document.querySelectorAll('.data-table');

    tables.forEach(table => {
        const headers = table.querySelectorAll('thead th');
        const rows = table.querySelectorAll('tbody tr');

        headers.forEach((header, index) => {
            const headerText = header.textContent.trim();
            rows.forEach(row => {
                const cell = row.querySelectorAll('td')[index];
                if (cell) {
                    cell.setAttribute('data-label', headerText);
                }
            });
        });
    });
}

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

        // Handle explicit display styles set by some scripts
        setTimeout(() => {
            if (!modalElement.classList.contains('show')) {
                modalElement.style.display = 'none';
            }
        }, 300);

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


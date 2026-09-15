/**
 * BloodLife — Core Application Client Script
 * Handles toast notifications, sidebar toggles, modal dialogs, and AJAX utilities.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Sidebar Toggle Handler
    const sidebarToggle = document.getElementById('blSidebarToggle');
    const sidebar = document.getElementById('blSidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('show') && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }

    // 2. Auto-Dismiss Alerts after 5 seconds
    const autoAlerts = document.querySelectorAll('.bl-alert-auto-dismiss');
    autoAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

/**
 * Toast Notification Utility
 * Renders a standard design-system floating toast alert.
 *
 * @param {string} message Text message to display
 * @param {string} type 'success' | 'danger' | 'warning' | 'info'
 */
function showToast(message, type = 'info') {
    let container = document.getElementById('blToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'blToastContainer';
        container.className = 'bl-toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `bl-toast bl-toast-${type}`;
    
    let iconClass = 'bi-info-circle-fill text-primary';
    if (type === 'success') iconClass = 'bi-check-circle-fill text-success';
    if (type === 'danger') iconClass = 'bi-x-circle-fill text-danger';
    if (type === 'warning') iconClass = 'bi-exclamation-triangle-fill text-warning';

    toast.innerHTML = `
        <i class="bi ${iconClass} fs-5"></i>
        <div class="flex-grow-1 font-semibold text-dark text-sm">${message}</div>
        <button type="button" class="btn-close ms-2" onclick="this.parentElement.remove()"></button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.4s ease';
        setTimeout(() => toast.remove(), 400);
    }, 4500);
}

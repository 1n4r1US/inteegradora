// js/notifications.js
// Sistema de notificaciones toast moderno

let toastContainer = null;
let toastCounter = 0;

/**
 * Inicializa el contenedor de toasts
 */
function initToastContainer() {
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }
}

/**
 * Muestra una notificación toast
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo: 'success', 'error', 'info', 'warning'
 * @param {number} duration - Duración en ms (default: 3000)
 */
export function showToast(message, type = 'info', duration = 3000) {
    initToastContainer();

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.id = `toast-${toastCounter++}`;

    const icon = getIcon(type);

    toast.innerHTML = `
        <div class="toast-icon">${icon}</div>
        <div class="toast-message">${escapeHtml(message)}</div>
        <button class="toast-close" aria-label="Cerrar">&times;</button>
        <div class="toast-progress"></div>
    `;

    toastContainer.appendChild(toast);

    // Animación de entrada
    setTimeout(() => toast.classList.add('toast-show'), 10);

    // Cerrar al hacer clic en X
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => removeToast(toast));

    // Auto-cerrar
    const progressBar = toast.querySelector('.toast-progress');
    progressBar.style.animationDuration = `${duration}ms`;

    setTimeout(() => removeToast(toast), duration);
}

/**
 * Elimina un toast con animación
 */
function removeToast(toast) {
    toast.classList.remove('toast-show');
    toast.classList.add('toast-hide');
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

/**
 * Obtiene el ícono según el tipo
 */
function getIcon(type) {
    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
    };
    return icons[type] || icons.info;
}

/**
 * Escapa HTML para prevenir XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Funciones de conveniencia
export function showSuccess(message, duration) {
    showToast(message, 'success', duration);
}

export function showError(message, duration) {
    showToast(message, 'error', duration);
}

export function showInfo(message, duration) {
    showToast(message, 'info', duration);
}

export function showWarning(message, duration) {
    showToast(message, 'warning', duration);
}

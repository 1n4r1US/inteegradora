// js/modal-manager.js
// Gestor de modales reutilizable

export class Modal {
    constructor(modalId) {
        this.modal = document.getElementById(modalId);
        if (!this.modal) {
            console.error(`Modal with id "${modalId}" not found`);
            return;
        }

        this.setupEventListeners();
    }

    setupEventListeners() {
        // Cerrar al hacer clic en el backdrop
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Cerrar con tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen()) {
                this.close();
            }
        });

        // Cerrar con botón X
        const closeBtn = this.modal.querySelector('.close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }
    }

    open() {
        if (this.modal) {
            this.modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevenir scroll
        }
    }

    close() {
        if (this.modal) {
            this.modal.style.display = 'none';
            document.body.style.overflow = ''; // Restaurar scroll
        }
    }

    isOpen() {
        return this.modal && this.modal.style.display === 'block';
    }

    /**
     * Limpia los campos de un formulario dentro del modal
     */
    resetForm(formId) {
        const form = this.modal.querySelector(`#${formId}`);
        if (form) {
            form.reset();
        }
    }

    /**
     * Muestra un mensaje dentro del modal
     */
    showMessage(message, type = 'info') {
        const msgEl = this.modal.querySelector('.form-msg');
        if (msgEl) {
            msgEl.textContent = message;
            msgEl.style.color = type === 'error' ? 'crimson' : 'green';
        }
    }

    /**
     * Limpia el mensaje del modal
     */
    clearMessage() {
        const msgEl = this.modal.querySelector('.form-msg');
        if (msgEl) {
            msgEl.textContent = '';
        }
    }
}

/**
 * Gestor global de modales
 */
export class ModalManager {
    constructor() {
        this.modals = new Map();
    }

    register(modalId) {
        if (!this.modals.has(modalId)) {
            this.modals.set(modalId, new Modal(modalId));
        }
        return this.modals.get(modalId);
    }

    get(modalId) {
        return this.modals.get(modalId);
    }

    closeAll() {
        this.modals.forEach(modal => modal.close());
    }
}

// Instancia global
export const modalManager = new ModalManager();

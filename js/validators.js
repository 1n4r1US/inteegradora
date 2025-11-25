// js/validators.js
// Utilidades de validación de formularios

/**
 * Valida que una fecha no sea en el pasado
 * @param {string} dateStr - Fecha en formato YYYY-MM-DD
 * @param {string} timeStr - Hora en formato HH:MM (opcional)
 * @returns {boolean}
 */
export function isValidFutureDate(dateStr, timeStr = null) {
    const now = new Date();
    let selectedDate;

    if (timeStr) {
        selectedDate = new Date(`${dateStr}T${timeStr}`);
    } else {
        selectedDate = new Date(dateStr);
        selectedDate.setHours(23, 59, 59); // Fin del día
    }

    return selectedDate > now;
}

/**
 * Valida formato de email
 * @param {string} email
 * @returns {boolean}
 */
export function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Valida formato de teléfono (10 dígitos)
 * @param {string} phone
 * @returns {boolean}
 */
export function isValidPhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    return cleaned.length === 10;
}

/**
 * Formatea un número de teléfono
 * @param {string} phone
 * @returns {string}
 */
export function formatPhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    if (cleaned.length === 10) {
        return `${cleaned.slice(0, 3)}-${cleaned.slice(3, 6)}-${cleaned.slice(6)}`;
    }
    return phone;
}

/**
 * Valida que un campo no esté vacío
 * @param {string} value
 * @returns {boolean}
 */
export function isRequired(value) {
    return value && value.trim().length > 0;
}

/**
 * Valida longitud mínima
 * @param {string} value
 * @param {number} min
 * @returns {boolean}
 */
export function minLength(value, min) {
    return value && value.trim().length >= min;
}

/**
 * Obtiene la fecha mínima para inputs (hoy)
 * @returns {string} Fecha en formato YYYY-MM-DD
 */
export function getMinDate() {
    const today = new Date();
    return today.toISOString().split('T')[0];
}

/**
 * Obtiene la hora mínima si la fecha es hoy
 * @param {string} dateStr - Fecha seleccionada
 * @returns {string|null} Hora mínima o null
 */
export function getMinTime(dateStr) {
    const today = new Date().toISOString().split('T')[0];
    if (dateStr === today) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }
    return null;
}

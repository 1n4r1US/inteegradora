// js/api.js
import { ENDPOINTS } from './config.js';

export async function login(correo, password) {
    const response = await fetch(ENDPOINTS.LOGIN, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ correo, password })
    });
    return response.json();
}

export async function register(userData) {
    const response = await fetch(ENDPOINTS.REGISTER, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(userData)
    });
    return response.json();
}

export async function checkAuth() {
    try {
        const response = await fetch(ENDPOINTS.CHECK_AUTH);
        if (response.ok) {
            return response.json();
        }
        return { success: false };
    } catch (error) {
        return { success: false };
    }
}

export async function logout() {
    const response = await fetch(ENDPOINTS.LOGOUT);
    return response.json();
}

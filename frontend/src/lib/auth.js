// Penyimpanan login admin (token Sanctum + data user) di LocalStorage.
// Dibuat sebagai "store" kecil supaya komponen React ikut ter-render ulang saat login/logout,
// dan supaya interceptor axios (di luar komponen) bisa menghapus token saat 401.
import { useSyncExternalStore } from 'react';

const TOKEN_KEY = 'sutra-token';
const USER_KEY = 'sutra-user';

function load() {
    try {
        const token = localStorage.getItem(TOKEN_KEY);
        const user = JSON.parse(localStorage.getItem(USER_KEY));
        return token ? { token, user, manual: false } : { token: null, user: null, manual: false };
    } catch {
        return { token: null, user: null, manual: false };
    }
}

let state = load();
const listeners = new Set();
const emit = () => listeners.forEach((fn) => fn());

export const authStore = {
    getState: () => state,
    getToken: () => state.token,
    subscribe(fn) {
        listeners.add(fn);
        return () => listeners.delete(fn);
    },
    save(token, user) {
        try {
            localStorage.setItem(TOKEN_KEY, token);
            localStorage.setItem(USER_KEY, JSON.stringify(user));
        } catch {
            /* penyimpanan penuh atau diblokir: login tetap berlaku sampai halaman ditutup */
        }
        state = { token, user, manual: false };
        emit();
    },
    // manual = true kalau admin menekan "Keluar" sendiri. Kalau false (misal token kedaluwarsa),
    // ProtectedRoute mengingat halaman terakhir supaya setelah login ulang kembali ke sana.
    clear({ manual = false } = {}) {
        try {
            localStorage.removeItem(TOKEN_KEY);
            localStorage.removeItem(USER_KEY);
        } catch {
            /* diabaikan */
        }
        state = { token: null, user: null, manual };
        emit();
    },
};

export function useAuth() {
    return useSyncExternalStore(authStore.subscribe, authStore.getState);
}

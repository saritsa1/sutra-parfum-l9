// Satu-satunya tempat yang berbicara dengan backend Laravel.
// Alamat API dibaca dari VITE_API_URL (file .env, atau Environment Variable di Vercel).
import axios from 'axios';
import { authStore } from './auth';

export const API_URL = (import.meta.env.VITE_API_URL || 'http://localhost:8000/api').replace(/\/$/, '');

export const api = axios.create({
    baseURL: API_URL,
    timeout: 15000,
    headers: { Accept: 'application/json' },
});

// Setiap request: tempelkan Bearer token kalau admin sedang login.
// FormData (upload gambar) tidak diberi Content-Type manual, axios yang mengatur boundary-nya.
api.interceptors.request.use((config) => {
    const token = authStore.getToken();
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

// Token kedaluwarsa atau dicabut: hapus login, ProtectedRoute otomatis mengarahkan ke halaman login.
api.interceptors.response.use(
    (res) => res,
    (error) => {
        if (error.response?.status === 401 && authStore.getToken()) {
            authStore.clear();
        }
        return Promise.reject(error);
    }
);

// Pesan error yang ramah untuk ditampilkan ke pengguna (toast atau kotak error).
export function getErrorMessage(error, fallback = 'Terjadi kesalahan. Coba lagi.') {
    if (!error) return fallback;
    // error biasa (bukan dari axios), misalnya "Akun ini bukan admin."
    if (!axios.isAxiosError(error)) return error.message || fallback;
    if (!error.response) {
        return error.code === 'ECONNABORTED'
            ? 'Server terlalu lama merespons. Coba lagi.'
            : 'Tidak bisa terhubung ke server. Pastikan backend sudah dijalankan.';
    }
    const data = error.response.data;
    // error validasi Laravel (422): tampilkan pesan kolom yang pertama
    if (error.response.status === 422 && data?.errors) {
        const first = Object.values(data.errors).flat()[0];
        if (first) return first;
    }
    return data?.message || fallback;
}

// Pesan validasi per kolom dari Laravel: { name: ['Nama wajib diisi.'] }
export const getFieldErrors = (error) => error?.response?.data?.errors ?? {};

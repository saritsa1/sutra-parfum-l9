// Service autentikasi admin (Laravel Sanctum). Menyimpan token ke authStore setelah login.
import { api } from '../lib/axios';
import { authStore } from '../lib/auth';

export const authService = {
    // Hanya akun admin yang boleh masuk. Akun biasa yang valid tetap ditolak dan token-nya langsung dicabut.
    async login({ email, password }) {
        const res = (await api.post('/login', { email, password })).data;

        if (!res.user?.is_admin) {
            await api.post('/logout', null, { headers: { Authorization: `Bearer ${res.token}` } }).catch(() => {});
            const err = new Error('Akun ini bukan admin.');
            err.notAdmin = true;
            throw err;
        }

        authStore.save(res.token, res.user);
        return res.user;
    },

    // Cabut token di server. Menghapus login di browser dilakukan pemanggil (lihat AdminLayout).
    async logout() {
        try {
            await api.post('/logout');
        } catch {
            /* tetap lanjut: token di browser tetap dihapus */
        }
    },
};

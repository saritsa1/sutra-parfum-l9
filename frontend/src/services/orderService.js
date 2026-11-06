// Service pesanan: sisi pembeli (checkout, lihat, bayar) dan sisi admin (daftar, status, statistik).
import { api } from '../lib/axios';

const bersihkan = (params = {}) => Object.fromEntries(Object.entries(params).filter(([, v]) => v !== '' && v != null));

export const orderService = {
    // ===== Pembeli (publik) =====
    // Pengiriman, metode bayar, biaya layanan, dan daftar status pesanan
    async options() {
        return (await api.get('/checkout-options')).data;
    },

    async create(payload) {
        return (await api.post('/orders', payload)).data.data;
    },

    async get(orderNo) {
        return (await api.get(`/orders/${encodeURIComponent(orderNo)}`)).data.data;
    },

    async pay(orderNo) {
        return (await api.post(`/orders/${encodeURIComponent(orderNo)}/pay`)).data.data;
    },

    // ===== Admin =====
    async adminList(params) {
        return (await api.get('/admin/orders', { params: bersihkan(params) })).data;
    },

    async updateStatus(id, status) {
        return (await api.patch(`/admin/orders/${id}/status`, { status })).data.data;
    },

    async stats() {
        return (await api.get('/admin/stats')).data.data;
    },
};

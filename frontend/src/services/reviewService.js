// Service ulasan produk.
import { api } from '../lib/axios';

export const reviewService = {
    async list(productKey) {
        return (await api.get(`/products/${encodeURIComponent(productKey)}/reviews`)).data.data;
    },

    async create(productKey, payload) {
        return (await api.post(`/products/${encodeURIComponent(productKey)}/reviews`, payload)).data.data;
    },

    // Admin
    async remove(id) {
        return (await api.delete(`/reviews/${id}`)).data;
    },
};

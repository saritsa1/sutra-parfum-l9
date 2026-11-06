// Service produk: satu-satunya tempat yang tahu alamat endpoint /products.
// Hanya fungsi biasa yang mengembalikan data (tanpa React), sehingga mudah dites dan dipakai ulang.
import { api } from '../lib/axios';

// Buang parameter kosong supaya URL bersih: { s: '', page: 2 } -> ?page=2
const bersihkan = (params = {}) => Object.fromEntries(Object.entries(params).filter(([, v]) => v !== '' && v != null));

export const productService = {
    // params: { s, category_id, page, per_page, sort_by, order, min_price, max_price }, hasil { data, links, meta }
    async list(params) {
        return (await api.get('/products', { params: bersihkan(params) })).data;
    },

    // key = slug atau id
    async get(key) {
        return (await api.get(`/products/${encodeURIComponent(key)}`)).data.data;
    },

    // Admin. Body berupa FormData karena ada upload gambar.
    async create(formData) {
        return (await api.post('/products', formData)).data.data;
    },

    async update(id, formData) {
        // PHP tidak membaca multipart lewat PUT, jadi dikirim POST + _method=PUT (method spoofing Laravel)
        formData.append('_method', 'PUT');
        return (await api.post(`/products/${id}`, formData)).data.data;
    },

    async remove(id) {
        return (await api.delete(`/products/${id}`)).data;
    },
};

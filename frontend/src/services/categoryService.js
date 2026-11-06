// Service kategori: satu-satunya tempat yang tahu alamat endpoint /categories.
// Dipisah dari productService supaya tiap sumber data punya modul sendiri.
import { api } from '../lib/axios';

export const categoryService = {
    // Daftar kategori: [{ id, name }]
    async list() {
        return (await api.get('/categories')).data.data;
    },
};

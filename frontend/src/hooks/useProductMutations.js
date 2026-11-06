// Tambah, ubah, dan hapus produk (admin). Setelah berhasil, cache daftar produk dan
// statistik dashboard dinyatakan basi supaya otomatis dimuat ulang.
import { useMutation, useQueryClient } from '@tanstack/react-query';
import toast from 'react-hot-toast';
import { productService } from '../services';

function useRefreshProducts() {
    const qc = useQueryClient();
    return () => {
        qc.invalidateQueries({ queryKey: ['products'] });
        qc.invalidateQueries({ queryKey: ['product'] });
        qc.invalidateQueries({ queryKey: ['admin', 'stats'] });
    };
}

// variables: { id, formData }. id kosong = tambah baru, id ada = ubah.
export function useSaveProduct() {
    const refresh = useRefreshProducts();
    return useMutation({
        mutationFn: ({ id, formData }) => (id ? productService.update(id, formData) : productService.create(formData)),
        onSuccess: (_data, { id }) => {
            refresh();
            toast.success(id ? 'Produk berhasil diperbarui.' : 'Produk berhasil ditambahkan.');
        },
    });
}

export function useDeleteProduct() {
    const refresh = useRefreshProducts();
    return useMutation({
        mutationFn: (id) => productService.remove(id),
        onSuccess: () => {
            refresh();
            toast.success('Produk berhasil dihapus.');
        },
    });
}

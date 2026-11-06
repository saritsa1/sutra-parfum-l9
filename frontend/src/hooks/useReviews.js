// Ulasan produk: daftar dan tambah (publik), hapus (admin).
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import toast from 'react-hot-toast';
import { reviewService } from '../services';

export const useReviews = (productKey) =>
    useQuery({
        queryKey: ['reviews', String(productKey)],
        queryFn: () => reviewService.list(productKey),
        enabled: !!productKey,
    });

export const useCreateReview = (productKey) => {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: (payload) => reviewService.create(productKey, payload),
        onSuccess: () => {
            qc.invalidateQueries({ queryKey: ['reviews', String(productKey)] });
            qc.invalidateQueries({ queryKey: ['product'] }); // rata-rata rating berubah
            qc.invalidateQueries({ queryKey: ['products'] });
            toast.success('Terima kasih, ulasan Anda sudah dikirim.');
        },
    });
};

export const useDeleteReview = () => {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: reviewService.remove,
        onSuccess: () => {
            qc.invalidateQueries({ queryKey: ['reviews'] });
            qc.invalidateQueries({ queryKey: ['admin'] });
            qc.invalidateQueries({ queryKey: ['product'] });
            qc.invalidateQueries({ queryKey: ['products'] });
            toast.success('Ulasan dihapus.');
        },
    });
};

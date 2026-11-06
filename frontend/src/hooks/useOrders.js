// Pesanan: checkout dan lihat pesanan (publik), kelola pesanan dan statistik (admin).
import { keepPreviousData, useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import toast from 'react-hot-toast';
import { orderService } from '../services';

// ===== Publik =====
// Pengiriman, metode bayar, biaya layanan, dan daftar status
export const useCheckoutOptions = () =>
    useQuery({ queryKey: ['checkout-options'], queryFn: orderService.options, staleTime: 5 * 60 * 1000 });

export const useCreateOrder = () => {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: orderService.create,
        onSuccess: () => {
            // stok produk sudah berkurang di server, jadi katalog perlu dimuat ulang
            qc.invalidateQueries({ queryKey: ['products'] });
            qc.invalidateQueries({ queryKey: ['product'] });
        },
    });
};

export const useOrder = (orderNo) =>
    useQuery({
        queryKey: ['order', orderNo],
        queryFn: () => orderService.get(orderNo),
        enabled: !!orderNo,
        retry: (count, error) => error?.response?.status !== 404 && count < 1,
    });

export const usePayOrder = (orderNo) => {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: () => orderService.pay(orderNo),
        onSuccess: (order) => {
            qc.setQueryData(['order', orderNo], order);
            toast.success('Pembayaran berhasil (simulasi).');
        },
    });
};

// ===== Admin =====
export const useAdminStats = () => useQuery({ queryKey: ['admin', 'stats'], queryFn: orderService.stats });

// params: { status, s, page, per_page }
export const useAdminOrders = (params = {}) =>
    useQuery({
        queryKey: ['admin', 'orders', params],
        queryFn: () => orderService.adminList(params),
        placeholderData: keepPreviousData,
    });

export const useUpdateOrderStatus = () => {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: ({ id, status }) => orderService.updateStatus(id, status),
        onSuccess: (order) => {
            qc.invalidateQueries({ queryKey: ['admin'] });
            // membatalkan pesanan mengembalikan stok, jadi katalog ikut dimuat ulang
            qc.invalidateQueries({ queryKey: ['products'] });
            toast.success(`Status ${order.order_no} menjadi ${order.status}.`);
        },
    });
};

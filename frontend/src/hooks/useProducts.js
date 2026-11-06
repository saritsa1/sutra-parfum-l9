// Hooks produk. Pemanggilan API ada di services/productService.js, hook ini hanya membungkusnya
// dengan React Query supaya ada status loading/error dan cache.
import { keepPreviousData, useQuery } from '@tanstack/react-query';
import { productService } from '../services';

// params: { s, category_id, page, per_page, ... }; options: opsi tambahan React Query, contoh { enabled: false }
export const useProducts = (params = {}, options = {}) =>
    useQuery({
        queryKey: ['products', params],
        queryFn: () => productService.list(params),
        placeholderData: keepPreviousData, // daftar lama tetap tampil selagi data baru dimuat
        ...options,
    });

// key = slug atau id
export const useProduct = (key) =>
    useQuery({
        queryKey: ['product', String(key)],
        queryFn: () => productService.get(key),
        enabled: !!key,
        retry: (count, error) => error?.response?.status !== 404 && count < 1,
    });

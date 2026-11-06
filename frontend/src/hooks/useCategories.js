// Hook kategori (membungkus categoryService dengan React Query).
import { useQuery } from '@tanstack/react-query';
import { categoryService } from '../services';

export const useCategories = () =>
    useQuery({
        queryKey: ['categories'],
        queryFn: categoryService.list,
        staleTime: 5 * 60 * 1000, // kategori jarang berubah: cukup diambil ulang tiap 5 menit
    });

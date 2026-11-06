// CategoryContext: daftar kategori, dipakai bersama oleh banyak halaman (chip filter di beranda,
// dropdown filter dan form di admin). Sengaja dipisah dari ProductContext: kategori punya
// service, hook, dan context sendiri, dan tidak bergantung pada data produk.
//
// Alur: komponen -> CategoryContext -> useCategories (React Query) -> categoryService -> Axios -> GET /api/categories
//
// Provider dipasang satu kali di main.jsx karena toko dan admin memakai daftar yang sama.
import { createContext, useCallback, useContext, useMemo } from 'react';
import { useCategories } from '../hooks/useCategories';

const CategoryContext = createContext(null);

export function CategoryProvider({ children }) {
    const query = useCategories();

    // cari satu kategori dari id (id bisa angka atau teks, misal dari <select>)
    const findCategory = useCallback(
        (id) => (query.data ?? []).find((c) => String(c.id) === String(id)),
        [query.data]
    );

    const value = useMemo(
        () => ({
            categories: query.data ?? [],
            isLoading: query.isLoading,
            error: query.error,
            refetch: query.refetch,
            findCategory,
        }),
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [query.data, query.isLoading, query.error, findCategory]
    );

    return <CategoryContext.Provider value={value}>{children}</CategoryContext.Provider>;
}

export function useCategoryContext() {
    const ctx = useContext(CategoryContext);
    if (!ctx) throw new Error('useCategoryContext harus dipakai di dalam CategoryProvider');
    return ctx;
}

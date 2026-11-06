// ProductContext: state produk yang dipakai bersama oleh banyak komponen.
//
// Isinya: daftar produk hasil filter, filter yang sedang aktif (kata kunci, kategori, halaman),
// dan aksi simpan/hapus produk. Daftar kategori TIDAK ada di sini, lihat CategoryContext.
//
// Dengan Context, kolom pencarian di navbar dan daftar produk di halaman bisa berbagi
// filter yang sama tanpa saling mengoper props.
//
// Alur: komponen -> ProductContext -> hooks (React Query) -> services -> Axios -> API Laravel
//
// Provider dipasang dua kali dengan jumlah data per halaman berbeda:
//   ShopLayout  -> <ProductProvider perPage={50}> (katalog toko)
//   AdminLayout -> <ProductProvider perPage={5}>  (tabel produk admin)
// Karena instance-nya terpisah, filter di toko tidak ikut berubah saat admin memfilter tabelnya.
import { createContext, useCallback, useContext, useMemo, useState } from 'react';
import { useProducts } from '../hooks/useProducts';
import { useDeleteProduct, useSaveProduct } from '../hooks/useProductMutations';

const ProductContext = createContext(null);

const FILTER_AWAL = { s: '', category_id: '', page: 1 };

export function ProductProvider({ perPage = 8, children }) {
    const [filters, setFiltersState] = useState(FILTER_AWAL);

    // Ubah sebagian filter. Kecuali yang diubah memang halaman, kembali ke halaman 1
    // (hasil pencarian baru tidak boleh tertinggal di halaman 3).
    const setFilters = useCallback((patch) => {
        setFiltersState((prev) => {
            const next = { ...prev, ...patch };
            if (!('page' in patch)) next.page = 1;
            return next;
        });
    }, []);

    const resetFilters = useCallback(() => setFiltersState(FILTER_AWAL), []);

    const query = useProducts({ ...filters, per_page: perPage });
    const save = useSaveProduct();
    const remove = useDeleteProduct();

    const value = useMemo(
        () => ({
            // data
            products: query.data?.data ?? [],
            meta: query.data?.meta,
            isLoading: query.isLoading,
            error: query.error,
            refetch: query.refetch,

            // filter
            filters,
            setFilters,
            resetFilters,

            // aksi admin
            saveProduct: save.mutate, // saveProduct({ id, formData }, { onSuccess })
            isSaving: save.isPending,
            saveError: save.error,
            resetSave: save.reset,
            deleteProduct: remove.mutate, // deleteProduct(id, { onSuccess })
            isDeleting: remove.isPending,
        }),
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [query.data, query.isLoading, query.error, filters, setFilters, resetFilters,
         save.isPending, save.error, remove.isPending]
    );

    return <ProductContext.Provider value={value}>{children}</ProductContext.Provider>;
}

export function useProductContext() {
    const ctx = useContext(ProductContext);
    if (!ctx) throw new Error('useProductContext harus dipakai di dalam ProductProvider');
    return ctx;
}

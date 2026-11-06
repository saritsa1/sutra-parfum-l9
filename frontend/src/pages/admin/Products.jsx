import { useState } from 'react';
import { Link } from 'react-router-dom';
import ErrorBox from '../../components/ErrorBox';
import Icon from '../../components/Icon';
import Pagination from '../../components/Pagination';
import ProductImage from '../../components/ProductImage';
import { useCategoryContext } from '../../context/CategoryContext';
import { useProductContext } from '../../context/ProductContext';
import { rupiah } from '../../lib/format';

// Daftar produk admin: cari, filter kategori, paginasi 5 data per halaman.
// Produk dan filter dari ProductContext (dipasang di AdminLayout), kategori dari CategoryContext.
export default function Products() {
    const { products: list, meta, filters, setFilters, isLoading, error, refetch, deleteProduct, isDeleting } = useProductContext();
    const { categories } = useCategoryContext();
    const [keyword, setKeyword] = useState(filters.s);

    const hapus = (item) => {
        if (!window.confirm(`Hapus "${item.name}"?`)) return;
        deleteProduct(item.id, {
            // yang dihapus data terakhir di halaman ini: mundur satu halaman
            onSuccess: () => { if (list.length === 1 && filters.page > 1) setFilters({ page: filters.page - 1 }); },
        });
    };

    return (
        <div className="max-w-5xl">
            <div className="mb-6 flex items-center justify-between">
                <h1 className="font-serif text-4xl">Daftar Produk</h1>
            </div>

            {error && <ErrorBox error={error} onRetry={refetch} className="mb-4" />}

            <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                <Link to="/admin/produk/baru" className="btn btn-solid px-4 py-2 text-sm">
                    <Icon name="plus" size={15} /> Tambah
                </Link>

                {/* Pencarian dan filter kategori (dikirim ke API sebagai ?s= dan ?category_id=) */}
                <form
                    onSubmit={(e) => { e.preventDefault(); setFilters({ s: keyword.trim() }); }}
                    className="flex flex-wrap items-center gap-2"
                >
                    <select
                        value={filters.category_id}
                        onChange={(e) => setFilters({ category_id: e.target.value })}
                        aria-label="Filter kategori"
                        className="rounded-lg border border-line bg-white px-3 py-2 text-sm focus:border-line"
                    >
                        <option value="">Semua Kategori</option>
                        {categories.map((c) => (
                            <option key={c.id} value={c.id}>{c.name}</option>
                        ))}
                    </select>
                    <div className="flex">
                        <input
                            type="text"
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                            placeholder="Cari produk"
                            aria-label="Cari produk"
                            className="rounded-l-lg border border-line bg-white px-4 py-2 text-sm focus:border-line"
                        />
                        <button type="submit" className="rounded-r-lg border border-l-0 border-line bg-white px-4 py-2 text-sm text-mute hover:bg-soft">Cari</button>
                    </div>
                </form>
            </div>

            <div className="overflow-x-auto rounded-xl border border-line bg-white">
                <table className="w-full text-left text-sm">
                    <thead className="border-b border-line text-mute">
                        <tr>
                            <th className="p-3">Nama</th>
                            <th className="p-3">Harga</th>
                            <th className="p-3">Kategori</th>
                            <th className="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {isLoading && (
                            <tr><td colSpan={4} className="p-6 text-center text-mute">Memuat data...</td></tr>
                        )}
                        {!isLoading && !error && list.length === 0 && (
                            <tr><td colSpan={4} className="p-6 text-center text-mute">Tidak ada data produk yang ditemukan.</td></tr>
                        )}
                        {list.map((item) => (
                            <tr key={item.id} className="border-b border-line last:border-0">
                                <td className="p-3">
                                    <div className="flex items-center gap-3">
                                        <div className="h-10 w-10 shrink-0 overflow-hidden rounded"><ProductImage item={item} pad="p-0.5" /></div>
                                        <div>
                                            <div className="font-medium">{item.name}</div>
                                            <div className="text-xs text-mute">{item.slug}</div>
                                        </div>
                                    </div>
                                </td>
                                <td className="p-3">
                                    <div>{rupiah(item.price)}</div>
                                    <div className={`text-xs ${item.stock <= 5 ? 'text-red-700' : 'text-mute'}`}>{`Stok: ${item.stock}`}</div>
                                </td>
                                <td className="p-3">
                                    {/* klik nama kategori = filter produk di kategori itu */}
                                    <button type="button" className="font-medium text-acc hover:underline" onClick={() => setFilters({ category_id: item.category.id })}>
                                        {item.category.name || '-'}
                                    </button>
                                </td>
                                <td className="p-3 text-right">
                                    <span className="inline-flex items-center gap-3">
                                        <Link to={`/admin/produk/${item.id}/edit`} className="text-acc hover:underline">Edit</Link>
                                        <button type="button" onClick={() => hapus(item)} disabled={isDeleting} className="text-red-700 hover:underline disabled:opacity-50">Del</button>
                                    </span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <Pagination meta={meta} onPage={(n) => setFilters({ page: n })} label="produk" />
        </div>
    );
}

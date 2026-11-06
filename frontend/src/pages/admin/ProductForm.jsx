import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import ErrorBox from '../../components/ErrorBox';
import { useCategoryContext } from '../../context/CategoryContext';
import { useProductContext } from '../../context/ProductContext';
import { useProduct } from '../../hooks/useProducts';
import { getFieldErrors } from '../../lib/axios';

const KOSONG = { name: '', category_id: '', price: '', stock: 0, notes: '', description: '' };
const inputClass = 'w-full rounded-lg border border-line bg-white px-4 py-2 text-sm focus:border-line';

// Form tambah (/admin/produk/baru) dan edit (/admin/produk/:id/edit)
export default function ProductForm() {
    const { id } = useParams();
    const isEdit = !!id;
    const navigate = useNavigate();

    // aksi simpan dari ProductContext, daftar kategori dari CategoryContext; data satu produk (mode edit) diambil terpisah
    const { saveProduct, isSaving, saveError, resetSave } = useProductContext();
    const { categories } = useCategoryContext();
    const product = useProduct(isEdit ? id : null);

    const [form, setForm] = useState(KOSONG);
    const [file, setFile] = useState(null);

    // buang sisa error dari penyimpanan sebelumnya
    useEffect(() => resetSave(), [resetSave]);

    // mode edit: isi form dengan data produk begitu selesai dimuat
    useEffect(() => {
        const p = product.data;
        if (!p) return;
        setForm({
            name: p.name,
            category_id: p.category.id,
            price: Math.round(p.price),
            stock: p.stock,
            notes: p.notes ?? '',
            description: p.description ?? '',
        });
    }, [product.data]);

    const set = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));
    const errors = getFieldErrors(saveError); // pesan validasi per kolom dari Laravel

    const submit = (e) => {
        e.preventDefault();
        // FormData dipakai karena ada upload file
        const fd = new FormData();
        Object.entries(form).forEach(([k, v]) => fd.append(k, v ?? ''));
        if (file) fd.append('img', file);
        saveProduct({ id, formData: fd }, { onSuccess: () => navigate('/admin/produk') });
    };

    const memuat = isEdit && product.isLoading;
    const tidakAda = isEdit && product.error?.response?.status === 404;

    const Pesan = ({ name }) => <div className="mt-1 text-xs text-red-800">{errors[name]?.[0]}</div>;

    return (
        <div className="max-w-4xl">
            <div className="mb-6 flex items-center justify-between">
                <h1 className="font-serif text-4xl">{isEdit ? 'Edit Produk' : 'Input Produk'}</h1>
                <Link to="/admin/produk" className="btn btn-ghost px-4 py-2 text-sm">&larr; Kembali ke Daftar</Link>
            </div>

            {memuat && <p className="text-mute">Memuat data...</p>}
            {tidakAda && <ErrorBox message="Produk tidak ditemukan." className="mb-4" />}
            {product.error && !tidakAda && <ErrorBox error={product.error} onRetry={product.refetch} className="mb-4" />}
            {saveError && <ErrorBox message="Periksa kembali isian yang bertanda merah." className="mb-4" />}

            {!memuat && !tidakAda && (
                <div className="rounded-xl border border-line bg-white p-6 shadow-sm">
                    <form onSubmit={submit} noValidate>
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {/* Nama Produk */}
                            <div>
                                <label htmlFor="name" className="mb-1 block text-sm font-medium text-ink">Nama Produk</label>
                                <input type="text" id="name" value={form.name} onChange={set('name')} placeholder="Nama Produk Lengkap" className={inputClass} />
                                <Pesan name="name" />
                            </div>

                            {/* Kategori Produk (daftar dari /api/categories) */}
                            <div>
                                <label htmlFor="category_id" className="mb-1 block text-sm font-medium text-ink">Kategori Produk</label>
                                <select id="category_id" value={form.category_id} onChange={set('category_id')} className={inputClass}>
                                    <option value="">Pilih Kategori</option>
                                    {categories.map((c) => (
                                        <option key={c.id} value={c.id}>{c.name}</option>
                                    ))}
                                </select>
                                <Pesan name="category_id" />
                            </div>

                            {/* Harga */}
                            <div>
                                <label htmlFor="price" className="mb-1 block text-sm font-medium text-ink">Harga (Rp)</label>
                                <input type="number" id="price" value={form.price} onChange={set('price')} placeholder="450000" step="1" min="0" className={inputClass} />
                                <Pesan name="price" />
                            </div>

                            {/* Stok */}
                            <div>
                                <label htmlFor="stock" className="mb-1 block text-sm font-medium text-ink">Jumlah Stok</label>
                                <input type="number" id="stock" value={form.stock} onChange={set('stock')} placeholder="10" min="0" className={inputClass} />
                                <Pesan name="stock" />
                            </div>

                            {/* Aroma / Catatan */}
                            <div>
                                <label htmlFor="notes" className="mb-1 block text-sm font-medium text-ink">Catatan Aroma / Spesifikasi</label>
                                <input type="text" id="notes" value={form.notes} onChange={set('notes')} placeholder="Mawar, peony, musk" className={inputClass} />
                                <Pesan name="notes" />
                            </div>

                            {/* Foto Produk (upload file) */}
                            <div>
                                <label htmlFor="img" className="mb-1 block text-sm font-medium text-ink">Foto Produk</label>
                                <input
                                    type="file"
                                    id="img"
                                    accept="image/png,image/jpeg,image/webp"
                                    onChange={(e) => setFile(e.target.files[0] ?? null)}
                                    className="w-full rounded-lg border border-line bg-white px-4 py-1.5 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-soft file:px-3 file:py-1 file:text-sm file:font-medium file:text-ink hover:file:bg-line"
                                />
                                {product.data?.img_url && !file && (
                                    <div className="mt-2 flex items-center gap-2 text-xs text-mute">
                                        <span>File saat ini:</span>
                                        <img src={product.data.img_url} alt="" className="h-8 w-8 rounded border border-line bg-soft object-contain" />
                                    </div>
                                )}
                                <Pesan name="img" />
                            </div>

                            {/* Deskripsi */}
                            <div className="md:col-span-2">
                                <label htmlFor="description" className="mb-1 block text-sm font-medium text-ink">Deskripsi Produk</label>
                                <textarea id="description" value={form.description} onChange={set('description')} rows={3} placeholder="Tuliskan keterangan lengkap produk di sini..." className={inputClass} />
                                <Pesan name="description" />
                            </div>
                        </div>

                        <hr className="my-6 border-line" />

                        <div className="flex items-center justify-end gap-3">
                            <Link to="/admin/produk" className="btn btn-ghost px-5 py-2 text-sm">Batal</Link>
                            <button type="submit" disabled={isSaving} className="btn btn-solid px-6 py-2 text-sm disabled:opacity-60">
                                {isSaving ? 'Menyimpan...' : isEdit ? 'Update' : 'Save'}
                            </button>
                        </div>
                    </form>
                </div>
            )}
        </div>
    );
}

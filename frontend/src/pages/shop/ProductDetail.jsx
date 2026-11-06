import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import ErrorBox from '../../components/ErrorBox';
import Icon from '../../components/Icon';
import ProductImage from '../../components/ProductImage';
import Stars from '../../components/Stars';
import { useProduct } from '../../hooks/useProducts';
import { useCreateReview, useReviews } from '../../hooks/useReviews';
import { getFieldErrors } from '../../lib/axios';
import { useCart } from '../../lib/cart';
import { rupiah, tanggal } from '../../lib/format';

function ReviewForm({ slug }) {
    const create = useCreateReview(slug);
    const [rating, setRating] = useState(0);
    const [nama, setNama] = useState('');
    const [comment, setComment] = useState('');
    const [formError, setFormError] = useState('');

    const serverErrors = getFieldErrors(create.error);

    const submit = (e) => {
        e.preventDefault();
        if (!rating || !comment.trim()) {
            setFormError('Pilih rating bintang dan tulis ulasan Anda.');
            return;
        }
        setFormError('');
        create.mutate(
            { name: nama.trim() || undefined, rating, comment: comment.trim() },
            { onSuccess: () => { setRating(0); setNama(''); setComment(''); } }
        );
    };

    return (
        <form onSubmit={submit} noValidate>
            <h2 className="mb-4 font-serif text-3xl">Beri rating</h2>
            <div className="mb-4 flex gap-1" role="group" aria-label="Rating">
                {[1, 2, 3, 4, 5].map((s) => (
                    <button
                        key={s}
                        type="button"
                        onClick={() => setRating(s)}
                        aria-label={`${s} bintang`}
                        aria-pressed={s <= rating}
                        className={`transition-transform hover:scale-110 ${s <= rating ? 'text-acc' : 'text-line'}`}
                    >
                        <Icon name="star" size={30} className={s <= rating ? 'fill-current' : ''} />
                    </button>
                ))}
            </div>
            <label htmlFor="nama" className="mb-1 block font-medium">Nama (opsional)</label>
            <input id="nama" type="text" value={nama} onChange={(e) => setNama(e.target.value)} className="field mb-4" />
            <label htmlFor="review" className="mb-1 block font-medium">Pengalaman Anda</label>
            <textarea id="review" rows={4} value={comment} onChange={(e) => setComment(e.target.value)} className="field mb-2" />
            <small className="mb-2 block min-h-[1.25rem] text-red-700" role="alert">
                {formError || serverErrors.comment?.[0] || serverErrors.rating?.[0]}
            </small>
            <button type="submit" disabled={create.isPending} className="btn btn-solid disabled:opacity-60">
                {create.isPending ? 'Mengirim...' : 'Kirim ulasan'}
            </button>
        </form>
    );
}

export default function ProductDetail() {
    const { slug } = useParams();
    const navigate = useNavigate();
    const cart = useCart();
    const [qty, setQty] = useState(1);

    const { data: p, isLoading, error, refetch } = useProduct(slug);
    const reviews = useReviews(slug);

    useEffect(() => {
        if (p) document.title = `${p.name} - Sutra Parfum`;
        return () => { document.title = 'Sutra Parfum'; };
    }, [p]);

    // pindah ke produk lain: jumlah kembali ke 1
    useEffect(() => setQty(1), [slug]);

    const notFound = error?.response?.status === 404;

    return (
        <div>
            <Link to="/" className="inline-flex items-center gap-1 text-mute hover:text-ink">
                <Icon name="arrow-left" size={16} />Kembali ke koleksi
            </Link>

            {isLoading && <p className="py-16 text-center text-mute" aria-live="polite">Memuat produk...</p>}
            {error && !notFound && <ErrorBox error={error} onRetry={refetch} className="mt-6" />}
            {notFound && (
                <div className="py-16 text-center text-mute">
                    <p>Produk tidak ditemukan.</p>
                    <Link to="/" className="btn btn-ghost mt-5">Kembali ke koleksi</Link>
                </div>
            )}

            {p && (
                <div>
                    <div className="mt-6 grid gap-12 md:grid-cols-2">
                        <div className="aspect-square overflow-hidden rounded-xl border border-line shadow">
                            <ProductImage item={p} pad="p-3" />
                        </div>
                        <div>
                            <h1 className="font-serif text-5xl">{p.name}</h1>
                            <p className="mt-1 flex items-center gap-2 text-sm text-mute">
                                <Stars value={p.rating} size={16} />
                                {`${Number(p.rating).toFixed(1)} (${p.reviews_count} ulasan)`}
                            </p>
                            <p className="my-2 text-2xl">{rupiah(p.price)}</p>
                            <p>{p.description}</p>
                            <dl className="my-6 border-t border-line">
                                {[
                                    ['Aroma', p.notes || '-'],
                                    ['Golongan', p.category.name],
                                    ['Ukuran', '50 ml'],
                                    ['Stok', p.stock],
                                ].map(([k, v]) => (
                                    <div key={k} className="flex justify-between border-b border-line py-2">
                                        <dt className="text-mute">{k}</dt>
                                        <dd>{v}</dd>
                                    </div>
                                ))}
                            </dl>
                            <div className="flex flex-wrap items-center gap-3">
                                <div className="flex items-center rounded-full border border-line">
                                    <button type="button" onClick={() => setQty((v) => Math.max(1, v - 1))} aria-label="Kurangi jumlah" className="p-3 hover:text-acc"><Icon name="minus" size={16} /></button>
                                    <span className="w-8 text-center" aria-live="polite">{qty}</span>
                                    <button type="button" onClick={() => setQty((v) => Math.min(p.stock, v + 1))} aria-label="Tambah jumlah" className="p-3 hover:text-acc"><Icon name="plus" size={16} /></button>
                                </div>
                                <button type="button" onClick={() => cart.add(p, qty)} disabled={p.stock < 1} className="btn btn-ghost disabled:opacity-50">
                                    <Icon name="shopping-bag" size={18} />Tambah ke keranjang
                                </button>
                                <button type="button" onClick={() => { cart.add(p, qty); navigate('/checkout'); }} disabled={p.stock < 1} className="btn btn-solid disabled:opacity-50">
                                    <Icon name="zap" size={18} />Beli sekarang
                                </button>
                            </div>
                            {p.stock < 1 && <p className="mt-3 text-sm text-red-700">Stok habis.</p>}
                        </div>
                    </div>

                    <section className="mt-14 grid gap-10 md:grid-cols-2">
                        <div>
                            <h2 className="mb-4 font-serif text-3xl">Ulasan</h2>
                            {reviews.isLoading && <p className="text-mute">Memuat ulasan...</p>}
                            {reviews.error && <ErrorBox error={reviews.error} onRetry={reviews.refetch} />}
                            {reviews.isSuccess && reviews.data.length === 0 && (
                                <p className="text-mute">Belum ada ulasan. Jadilah yang pertama memberi rating.</p>
                            )}
                            <ul className="space-y-4">
                                {(reviews.data ?? []).map((r) => (
                                    <li key={r.id} className="rounded-xl border border-line bg-white p-4">
                                        <Stars value={r.rating} size={14} />
                                        <p className="text-sm text-mute">{`${r.name}, ${tanggal(r.created_at)}`}</p>
                                        <p className="mt-1">{r.comment}</p>
                                    </li>
                                ))}
                            </ul>
                        </div>

                        <ReviewForm slug={slug} />
                    </section>
                </div>
            )}
        </div>
    );
}

import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useCart } from '../lib/cart';
import { rupiah } from '../lib/format';
import Icon from './Icon';
import ProductImage from './ProductImage';
import Stars from './Stars';

// Kartu dua sisi: depan = gambar produk, belakang = keterangan dan aksi. Terbalik saat diklik.
export default function ProductCard({ item }) {
    const [flipped, setFlipped] = useState(false);
    const cart = useCart();
    const navigate = useNavigate();

    const buyNow = () => {
        cart.add(item);
        navigate('/checkout');
    };

    return (
        <article className="perspective">
            <div className={`flip relative aspect-[3/4.3] ${flipped ? 'is-flipped' : ''}`}>
                {/* Sisi depan */}
                <button
                    type="button"
                    inert={flipped ? '' : undefined}
                    onClick={() => setFlipped(true)}
                    aria-label={`Lihat info ${item.name}`}
                    className="face absolute inset-0 flex flex-col overflow-hidden rounded-xl border border-line bg-white text-left shadow"
                >
                    <div className="min-h-0 flex-1">
                        <ProductImage item={item} pad="p-3" />
                    </div>
                    <div className="w-full p-4">
                        <div className="flex items-center justify-between text-xs text-mute">
                            <span>{item.category.name}</span>
                            <span className="flex items-center gap-1">
                                <Stars value={item.rating} size={12} />
                                {Number(item.rating).toFixed(1)}
                            </span>
                        </div>
                        {/* text-xl: judul terpanjang tetap satu baris, jadi tinggi gambar tidak berubah-ubah */}
                        <h3 className="font-serif text-xl leading-tight">{item.name}</h3>
                        <div className="mt-1 flex items-center justify-between">
                            <span className="font-medium">{rupiah(item.price)}</span>
                            <Icon name="info" size={18} className="text-mute" />
                        </div>
                    </div>
                </button>

                {/* Sisi belakang */}
                <div inert={!flipped ? '' : undefined} className="face face-back absolute inset-0 flex flex-col rounded-xl border border-line bg-white p-5 shadow">
                    <div className="flex items-start justify-between">
                        <span className="rounded-full bg-soft px-3 py-0.5 text-xs">{item.category.name}</span>
                        <button type="button" onClick={() => setFlipped(false)} aria-label="Balik kartu" className="text-mute hover:text-ink">
                            <Icon name="rotate-ccw" size={18} />
                        </button>
                    </div>
                    <h3 className="mt-3 font-serif text-2xl leading-tight">{item.name}</h3>
                    <p className="mt-1 text-sm text-mute">{item.notes}</p>
                    <p className="mt-3 line-clamp-4 text-sm">{item.description}</p>
                    <p className="mt-auto pb-3 pt-2 text-sm text-mute">{`50 ml, stok ${item.stock}`}</p>
                    <div className="grid gap-2">
                        <button type="button" onClick={buyNow} disabled={item.stock < 1} className="btn btn-solid py-2 text-sm disabled:opacity-50">
                            <Icon name="zap" size={16} />Beli sekarang
                        </button>
                        <div className="grid grid-cols-2 gap-2">
                            <button type="button" onClick={() => cart.add(item)} disabled={item.stock < 1} className="btn btn-ghost px-3 py-2 text-sm disabled:opacity-50">
                                <Icon name="shopping-bag" size={16} />Keranjang
                            </button>
                            <Link to={`/produk/${encodeURIComponent(item.slug)}`} className="btn btn-ghost px-3 py-2 text-sm">
                                <Icon name="eye" size={16} />Detail
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    );
}

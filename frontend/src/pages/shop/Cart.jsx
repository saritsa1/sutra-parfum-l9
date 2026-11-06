import { Link } from 'react-router-dom';
import Icon from '../../components/Icon';
import ProductImage from '../../components/ProductImage';
import { useCart } from '../../lib/cart';
import { rupiah } from '../../lib/format';

export default function Cart() {
    const cart = useCart();

    // Keranjang kosong
    if (cart.items.length === 0) {
        return (
            <div>
                <h1 className="mb-3 font-serif text-4xl">Keranjang</h1>
                <p className="text-mute">
                    Keranjang masih kosong. <Link to="/" className="underline">Pilih parfum</Link>
                </p>
            </div>
        );
    }

    return (
        <div>
            <h1 className="mb-6 font-serif text-4xl">Keranjang</h1>
            <ul className="border-t border-line">
                {cart.items.map((i) => (
                    <li key={i.id} className="flex flex-wrap items-center gap-4 border-b border-line py-4">
                        <div className="w-16">
                            <div className="aspect-square overflow-hidden rounded-md"><ProductImage item={i} pad="p-3" /></div>
                        </div>
                        <div className="flex-1">
                            <Link to={`/produk/${encodeURIComponent(i.slug)}`} className="font-serif text-xl hover:underline">{i.name}</Link>
                            <p className="text-mute">{rupiah(i.price)}</p>
                        </div>
                        <label className="text-sm">
                            <span className="sr-only">{`Jumlah ${i.name}`}</span>
                            <input
                                type="number"
                                min="1"
                                max={i.stock}
                                value={i.qty}
                                onChange={(e) => cart.updateQty(i.id, parseInt(e.target.value, 10))}
                                className="w-16 border border-mute/50 bg-white p-1 text-center focus:border-mute/50"
                            />
                        </label>
                        <button type="button" onClick={() => cart.remove(i.id)} aria-label={`Hapus ${i.name}`} className="p-2 text-mute hover:text-red-700">
                            <Icon name="trash-2" size={18} />
                        </button>
                    </li>
                ))}
            </ul>
            <div className="mt-6 flex flex-wrap items-center justify-between gap-4">
                <p className="text-xl">Total: <strong className="font-medium">{rupiah(cart.totalPrice)}</strong></p>
                <Link to="/checkout" className="btn btn-solid">Lanjut ke checkout<Icon name="arrow-right" size={18} /></Link>
            </div>
        </div>
    );
}

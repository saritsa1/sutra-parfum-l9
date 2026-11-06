import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import ErrorBox from '../../components/ErrorBox';
import Icon from '../../components/Icon';
import PriceSummary from '../../components/PriceSummary';
import ProductImage from '../../components/ProductImage';
import { useCheckoutOptions, useCreateOrder } from '../../hooks/useOrders';
import { getFieldErrors } from '../../lib/axios';
import { useCart } from '../../lib/cart';
import { rupiah } from '../../lib/format';

export default function Checkout() {
    const cart = useCart();
    const navigate = useNavigate();
    const options = useCheckoutOptions();
    const createOrder = useCreateOrder();

    const [form, setForm] = useState({ nama: '', telepon: '', alamat: '' });
    const [shipId, setShipId] = useState('');
    const [payId, setPayId] = useState('');
    const [errors, setErrors] = useState({});

    const set = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));

    if (cart.items.length === 0 && !createOrder.isPending) {
        return <p>Keranjang kosong. <Link to="/" className="underline">Pilih parfum dulu</Link></p>;
    }
    if (options.isLoading) return <p className="py-10 text-center text-mute" aria-live="polite">Memuat opsi pembayaran...</p>;
    if (options.error) return <ErrorBox error={options.error} onRetry={options.refetch} />;

    // Opsi pengiriman dan metode bayar datang dari backend
    const { shipping, payment: groups, service_fee: layanan } = options.data;
    const ship = shipping.find((s) => s.id === shipId) ?? shipping[0];
    const methods = groups.flatMap((g) => g.methods.map((m) => ({ ...m, type: g.type })));
    const method = methods.find((m) => m.id === payId);
    const biayaMetode = method?.fee ?? 0;
    const total = cart.totalPrice + ship.cost + layanan + biayaMetode;

    const submit = (e) => {
        e.preventDefault();
        const err = {};
        if (form.nama.trim().length < 3) err.nama = 'Nama minimal 3 karakter.';
        if (!/^08\d{8,12}$/.test(form.telepon)) err.telepon = 'Nomor HP harus diawali 08 dan 10-14 digit.';
        if (form.alamat.trim().length < 10) err.alamat = 'Alamat minimal 10 karakter.';
        if (!method) err.bayar = 'Pilih metode pembayaran.';
        setErrors(err);
        if (Object.keys(err).length) return;

        // Yang dikirim hanya id produk dan jumlah. Harga dan total dihitung ulang oleh server.
        createOrder.mutate(
            {
                customer_name: form.nama.trim(),
                phone: form.telepon,
                address: form.alamat.trim(),
                shipping_id: ship.id,
                payment_id: method.id,
                items: cart.items.map((i) => ({ product_id: i.id, qty: i.qty })),
            },
            {
                onSuccess: (order) => {
                    // replace: tombol Back tidak kembali ke checkout yang sudah kosong
                    navigate(`/pesanan/${order.order_no}`, { replace: true });
                    cart.clear();
                },
            }
        );
    };

    // pesan validasi dari server (kalau lolos validasi browser tapi ditolak backend)
    const server = getFieldErrors(createOrder.error);
    const kolom = {
        nama: errors.nama || server.customer_name?.[0],
        telepon: errors.telepon || server.phone?.[0],
        alamat: errors.alamat || server.address?.[0],
        bayar: errors.bayar || server.payment_id?.[0],
    };
    const errorItems = server.items?.[0] || server['items.0.product_id']?.[0];

    return (
        <form onSubmit={submit} noValidate className="grid gap-8 md:grid-cols-[1fr_340px]">
            <div>
                {/* Tombol kembali: pengguna bisa membatalkan checkout tanpa kehilangan isi keranjang */}
                <Link to="/keranjang" className="mb-4 inline-flex items-center gap-1 text-mute hover:text-ink">
                    <Icon name="arrow-left" size={16} />Kembali ke keranjang
                </Link>
                <h1 className="mb-6 font-serif text-4xl">Checkout</h1>

                <h2 className="mb-3 font-serif text-2xl">Alamat pengiriman</h2>
                <div className="mb-3">
                    <label htmlFor="nama" className="mb-1 block font-medium">Nama penerima</label>
                    <input id="nama" type="text" value={form.nama} onChange={set('nama')} className="field" />
                    <small className="block min-h-[1.25rem] text-red-700">{kolom.nama}</small>
                </div>
                <div className="mb-3">
                    <label htmlFor="telepon" className="mb-1 block font-medium">Nomor HP</label>
                    <input id="telepon" type="text" inputMode="numeric" value={form.telepon} onChange={set('telepon')} className="field" />
                    <small className="block min-h-[1.25rem] text-red-700">{kolom.telepon}</small>
                </div>
                <div className="mb-8">
                    <label htmlFor="alamat" className="mb-1 block font-medium">Alamat lengkap</label>
                    <textarea id="alamat" rows={3} value={form.alamat} onChange={set('alamat')} className="field" />
                    <small className="block min-h-[1.25rem] text-red-700">{kolom.alamat}</small>
                </div>

                <fieldset className="mb-8">
                    <legend className="mb-3 font-serif text-2xl">Pengiriman</legend>
                    <div className="grid gap-2">
                        {shipping.map((s) => (
                            <label key={s.id} className="choice">
                                <input type="radio" name="kurir" value={s.id} checked={ship.id === s.id} onChange={() => setShipId(s.id)} />
                                <span className="flex-1">{s.name} <span className="text-sm text-mute">{`estimasi ${s.est}`}</span></span>
                                <span>{rupiah(s.cost)}</span>
                            </label>
                        ))}
                    </div>
                </fieldset>

                <fieldset>
                    <legend className="mb-3 font-serif text-2xl">Metode pembayaran</legend>
                    {groups.map((g) => (
                        <div key={g.group} className="mb-4">
                            <p className="mb-2 text-sm text-mute">{g.group}</p>
                            <div className="grid gap-2">
                                {g.methods.map((m) => (
                                    <label key={m.id} className="choice">
                                        <input type="radio" name="bayar" value={m.id} checked={payId === m.id} onChange={() => setPayId(m.id)} />
                                        <span className="flex-1">{m.name}</span>
                                        {m.fee > 0 && <span className="text-sm text-mute">{'+' + rupiah(m.fee)}</span>}
                                    </label>
                                ))}
                            </div>
                        </div>
                    ))}
                    <small className="block min-h-[1.25rem] text-red-700">{kolom.bayar}</small>
                </fieldset>
            </div>

            {/* Ringkasan belanja */}
            <aside className="self-start rounded-xl border border-line bg-white p-5 md:sticky md:top-24">
                <h2 className="mb-3 font-serif text-2xl">Rincian belanja</h2>
                <ul className="mb-4 space-y-3 border-b border-line pb-4">
                    {cart.items.map((i) => (
                        <li key={i.id} className="flex items-center gap-3 text-sm">
                            <div className="h-12 w-12 shrink-0 overflow-hidden rounded"><ProductImage item={i} pad="p-1" /></div>
                            <span className="flex-1">{i.name}<br /><span className="text-mute">{`${i.qty} x ${rupiah(i.price)}`}</span></span>
                            <span>{rupiah(i.qty * i.price)}</span>
                        </li>
                    ))}
                </ul>
                <PriceSummary subtotal={cart.totalPrice} ongkir={ship.cost} layanan={layanan} biayaMetode={biayaMetode} total={total} />
                {errorItems && <p className="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-800" role="alert">{errorItems}</p>}
                <button type="submit" disabled={createOrder.isPending} className="btn btn-solid mt-5 w-full disabled:opacity-60">
                    {createOrder.isPending ? 'Memproses...' : 'Buat pesanan'}
                </button>
                {/* Tombol batal: kembali ke keranjang, isi keranjang tidak dihapus */}
                <Link to="/keranjang" className="btn btn-ghost mt-2 w-full">Batal</Link>
            </aside>
        </form>
    );
}

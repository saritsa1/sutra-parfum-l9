import { useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import ErrorBox from '../../components/ErrorBox';
import Icon from '../../components/Icon';
import PriceSummary from '../../components/PriceSummary';
import ProductImage from '../../components/ProductImage';
import { useOrder, usePayOrder } from '../../hooks/useOrders';
import { rupiah, tanggal } from '../../lib/format';

export default function Order() {
    const { orderNo } = useParams();
    const { data: o, isLoading, error, refetch } = useOrder(orderNo);
    const pay = usePayOrder(orderNo);
    const [copied, setCopied] = useState(false);

    if (isLoading) return <p className="py-10 text-center text-mute" aria-live="polite">Memuat pesanan...</p>;
    if (error?.response?.status === 404) {
        return <p>Pesanan tidak ditemukan. <Link to="/" className="underline">Kembali ke koleksi</Link></p>;
    }
    if (error) return <ErrorBox error={error} onRetry={refetch} />;

    const menunggu = o.status === 'Menunggu Pembayaran';

    const copy = async () => {
        try {
            await navigator.clipboard.writeText(String(o.payment.va_number));
            setCopied(true);
            setTimeout(() => setCopied(false), 1500);
        } catch {
            /* clipboard diblokir browser: abaikan */
        }
    };

    return (
        <div className="mx-auto max-w-2xl">
            <div className="mb-6 text-center">
                <Icon name="circle-check" size={44} className="mx-auto mb-2 text-acc" />
                <h1 className="font-serif text-4xl">Pesanan berhasil dibuat</h1>
                <p className="text-mute">Nomor pesanan <strong className="font-medium text-ink">{o.order_no}</strong>, {tanggal(o.created_at)}</p>
                <span className="mt-2 inline-block rounded-full bg-soft px-4 py-1 text-sm">{o.status}</span>
            </div>

            {/* Instruksi pembayaran sesuai jenis metode */}
            <section className="mb-6 rounded-xl border border-line bg-white p-5">
                <h2 className="mb-2 font-serif text-2xl">Pembayaran</h2>
                <p className="text-sm text-mute">{o.payment.name}</p>
                {o.payment.type === 'va' && (
                    <div className="mt-3">
                        <p className="text-sm">Nomor Virtual Account</p>
                        <p className="flex items-center gap-3 font-mono text-2xl tracking-wider">
                            <span>{o.payment.va_number}</span>
                            <button type="button" onClick={copy} className="btn btn-ghost px-3 py-1 text-sm">
                                <Icon name="copy" size={14} />{copied ? 'Tersalin' : 'Salin'}
                            </button>
                        </p>
                    </div>
                )}
                {o.payment.type === 'ewallet' && (
                    <p className="mt-3">{`Buka aplikasi ${o.payment.name} Anda, lalu selesaikan pembayaran sebesar ${rupiah(o.total)}.`}</p>
                )}
                {o.payment.type === 'cod' && <p className="mt-3">{`Siapkan uang pas ${rupiah(o.total)} saat paket tiba.`}</p>}
                {menunggu && (
                    <div>
                        <p className="mt-3 text-sm text-mute">Bayar sebelum {tanggal(o.expires_at)}</p>
                        <button type="button" onClick={() => pay.mutate()} disabled={pay.isPending} className="btn btn-solid mt-4 disabled:opacity-60">
                            {pay.isPending ? 'Memproses...' : 'Simulasikan pembayaran'}
                        </button>
                    </div>
                )}
            </section>

            <section className="mb-6 rounded-xl border border-line bg-white p-5">
                <h2 className="mb-2 font-serif text-2xl">Info pengiriman</h2>
                <p>{`${o.customer_name}, ${o.phone}`}</p>
                <p className="text-mute">{o.address}</p>
                <p className="mt-2 text-sm">{`Kurir: ${o.shipping.name} (${o.shipping.est})`}</p>
            </section>

            <section className="mb-6 rounded-xl border border-line bg-white p-5">
                <h2 className="mb-3 font-serif text-2xl">Rincian belanja</h2>
                <ul className="mb-4 space-y-3 border-b border-line pb-4">
                    {o.items.map((i) => (
                        <li key={i.id} className="flex items-center gap-3 text-sm">
                            <div className="h-12 w-12 shrink-0 overflow-hidden rounded"><ProductImage item={{ id: i.product_id ?? i.id, name: i.name, img_url: i.img_url, color: i.color }} pad="p-1" /></div>
                            <span className="flex-1">{i.name}<br /><span className="text-mute">{`${i.qty} x ${rupiah(i.price)}`}</span></span>
                            <span>{rupiah(i.subtotal)}</span>
                        </li>
                    ))}
                </ul>
                <PriceSummary subtotal={o.subtotal} ongkir={o.shipping.cost} layanan={o.service_fee} biayaMetode={o.payment_fee} total={o.total} />
            </section>

            <div className="text-center"><Link to="/" className="btn btn-ghost">Lanjut belanja</Link></div>
        </div>
    );
}

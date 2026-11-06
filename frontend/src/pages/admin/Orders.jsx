import { useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import ErrorBox from '../../components/ErrorBox';
import Pagination from '../../components/Pagination';
import { useAdminOrders, useCheckoutOptions, useUpdateOrderStatus } from '../../hooks/useOrders';
import { rupiah, tanggal } from '../../lib/format';

const FINAL = ['Selesai', 'Dibatalkan']; // sama dengan aturan di backend (Order::STATUS_FINAL)

// Kelola pesanan: filter status, cari, ubah status. Membatalkan pesanan mengembalikan stok (diurus backend).
export default function Orders() {
    const [params, setParams] = useSearchParams();
    const status = params.get('status') ?? '';
    const s = params.get('s') ?? '';
    const page = Number(params.get('page')) || 1;

    const [keyword, setKeyword] = useState(s);

    const options = useCheckoutOptions();
    const orders = useAdminOrders({ status, s, page, per_page: 8 });
    const update = useUpdateOrderStatus();

    const ubah = (perubahan, kePertama = true) => {
        const next = new URLSearchParams(params);
        Object.entries(perubahan).forEach(([k, v]) => (v ? next.set(k, v) : next.delete(k)));
        if (kePertama) next.delete('page');
        setParams(next, { replace: true });
    };

    const gantiStatus = (order, baru) => {
        if (baru === order.status) return;
        if (baru === 'Dibatalkan' && !window.confirm(`Batalkan pesanan ${order.order_no}? Stok produk akan dikembalikan dan status tidak bisa diubah lagi.`)) return;
        if (baru === 'Selesai' && !window.confirm(`Tandai pesanan ${order.order_no} selesai? Status tidak bisa diubah lagi.`)) return;
        update.mutate({ id: order.id, status: baru });
    };

    const list = orders.data?.data ?? [];
    const statuses = options.data?.statuses ?? [];

    return (
        <div className="max-w-5xl">
            <h1 className="mb-6 font-serif text-4xl">Pesanan</h1>

            {orders.error && <ErrorBox error={orders.error} onRetry={orders.refetch} className="mb-4" />}

            <div className="mb-4 flex flex-wrap items-center justify-end gap-2">
                <select value={status} onChange={(e) => ubah({ status: e.target.value })} aria-label="Filter status" className="rounded-lg border border-line bg-white px-3 py-2 text-sm focus:border-line">
                    <option value="">Semua Status</option>
                    {statuses.map((st) => <option key={st} value={st}>{st}</option>)}
                </select>
                <form onSubmit={(e) => { e.preventDefault(); ubah({ s: keyword.trim() }); }} className="flex">
                    <input type="text" value={keyword} onChange={(e) => setKeyword(e.target.value)} placeholder="Cari nomor / nama" aria-label="Cari pesanan" className="rounded-l-lg border border-line bg-white px-4 py-2 text-sm focus:border-line" />
                    <button type="submit" className="rounded-r-lg border border-l-0 border-line bg-white px-4 py-2 text-sm text-mute hover:bg-soft">Cari</button>
                </form>
            </div>

            <div className="overflow-x-auto rounded-xl border border-line bg-white">
                <table className="w-full text-left text-sm">
                    <thead className="border-b border-line text-mute">
                        <tr><th className="p-3">Pesanan</th><th className="p-3">Pembeli</th><th className="p-3">Item</th><th className="p-3">Total</th><th className="p-3">Status</th></tr>
                    </thead>
                    <tbody>
                        {orders.isLoading && <tr><td colSpan={5} className="p-6 text-center text-mute">Memuat data...</td></tr>}
                        {orders.isSuccess && list.length === 0 && <tr><td colSpan={5} className="p-6 text-center text-mute">Tidak ada pesanan yang cocok.</td></tr>}
                        {list.map((o) => (
                            <tr key={o.id} className="border-b border-line align-top last:border-0">
                                <td className="p-3">{o.order_no}<br /><span className="text-mute">{tanggal(o.created_at)}</span></td>
                                <td className="p-3">{o.customer_name}<br /><span className="text-mute">{o.phone}</span><br /><span className="text-mute">{o.payment.name}</span></td>
                                <td className="p-3">{o.items.map((i) => `${i.name} x${i.qty}`).join(', ')}</td>
                                <td className="p-3">{rupiah(o.total)}</td>
                                <td className="p-3">
                                    <select
                                        value={o.status}
                                        onChange={(e) => gantiStatus(o, e.target.value)}
                                        disabled={FINAL.includes(o.status) || update.isPending}
                                        aria-label={`Status pesanan ${o.order_no}`}
                                        className="rounded border border-line bg-white p-1 focus:border-line disabled:bg-soft"
                                    >
                                        {statuses.map((st) => <option key={st} value={st}>{st}</option>)}
                                    </select>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <Pagination meta={orders.data?.meta} onPage={(n) => ubah({ page: n > 1 ? String(n) : '' }, false)} label="pesanan" />
        </div>
    );
}

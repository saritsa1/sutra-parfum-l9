import { Link } from 'react-router-dom';
import ErrorBox from '../../components/ErrorBox';
import Stars from '../../components/Stars';
import Icon from '../../components/Icon';
import { useAdminStats } from '../../hooks/useOrders';
import { useDeleteReview } from '../../hooks/useReviews';
import { rupiah, tanggal } from '../../lib/format';

export default function Dashboard() {
    const { data, isLoading, error, refetch } = useAdminStats();
    const deleteReview = useDeleteReview();

    if (isLoading) return <p className="text-mute">Memuat dashboard...</p>;
    if (error) return <ErrorBox error={error} onRetry={refetch} />;

    const kartu = [
        ['Pesanan', data.total_orders],
        ['Pendapatan', rupiah(data.revenue)],
        ['Ulasan', data.total_reviews],
        ['Produk', data.total_products],
    ];

    const hapusUlasan = (r) => {
        if (window.confirm(`Hapus ulasan dari ${r.name}?`)) deleteReview.mutate(r.id);
    };

    return (
        <div className="max-w-4xl">
            <h1 className="mb-6 font-serif text-4xl">Dashboard</h1>

            <div className="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
                {kartu.map(([label, nilai]) => (
                    <div key={label} className="rounded-xl border border-line bg-white p-4">
                        <p className="text-sm text-mute">{label}</p>
                        <p className="font-serif text-3xl">{nilai}</p>
                    </div>
                ))}
            </div>

            <div className="mb-10 grid gap-6 md:grid-cols-2">
                <section>
                    <h2 className="mb-3 font-serif text-2xl">Pesanan per status</h2>
                    <ul className="rounded-xl border border-line bg-white">
                        {Object.entries(data.orders_by_status).map(([status, jumlah]) => (
                            <li key={status} className="flex justify-between border-b border-line px-4 py-2 last:border-0">
                                <Link to={`/admin/pesanan?status=${encodeURIComponent(status)}`} className="hover:underline">{status}</Link>
                                <span className="font-medium">{jumlah}</span>
                            </li>
                        ))}
                    </ul>
                </section>

                <section>
                    <h2 className="mb-3 font-serif text-2xl">Stok menipis</h2>
                    {data.low_stock.length === 0 ? (
                        <p className="text-mute">{`Semua produk punya stok di atas ${data.low_stock_limit}.`}</p>
                    ) : (
                        <ul className="rounded-xl border border-line bg-white">
                            {data.low_stock.map((p) => (
                                <li key={p.id} className="flex items-center justify-between gap-3 border-b border-line px-4 py-2 last:border-0">
                                    <span className="flex items-center gap-2">
                                        <Icon name="triangle-alert" size={16} className={p.stock === 0 ? 'text-red-700' : 'text-acc'} />
                                        {p.name}
                                    </span>
                                    <span className="flex items-center gap-3">
                                        <span className={p.stock === 0 ? 'text-red-700' : ''}>{p.stock === 0 ? 'Habis' : `Sisa ${p.stock}`}</span>
                                        <Link to={`/admin/produk/${p.id}/edit`} className="text-acc hover:underline">Edit</Link>
                                    </span>
                                </li>
                            ))}
                        </ul>
                    )}
                </section>
            </div>

            <section className="mb-10">
                <div className="mb-3 flex items-center justify-between">
                    <h2 className="font-serif text-2xl">Pesanan terbaru</h2>
                    <Link to="/admin/pesanan" className="text-sm text-acc hover:underline">Lihat semua</Link>
                </div>
                {data.latest_orders.length === 0 ? (
                    <p className="text-mute">Belum ada pesanan. Pesanan dari halaman checkout akan muncul di sini.</p>
                ) : (
                    <div className="overflow-x-auto rounded-xl border border-line bg-white">
                        <table className="w-full text-left text-sm">
                            <thead className="border-b border-line text-mute">
                                <tr><th className="p-3">Pesanan</th><th className="p-3">Pembeli</th><th className="p-3">Total</th><th className="p-3">Status</th></tr>
                            </thead>
                            <tbody>
                                {data.latest_orders.map((o) => (
                                    <tr key={o.id} className="border-b border-line last:border-0">
                                        <td className="p-3">{o.order_no}<br /><span className="text-mute">{tanggal(o.created_at)}</span></td>
                                        <td className="p-3">{o.customer_name}<br /><span className="text-mute">{o.payment.name}</span></td>
                                        <td className="p-3">{rupiah(o.total)}</td>
                                        <td className="p-3"><span className="rounded-full bg-soft px-3 py-0.5 text-xs">{o.status}</span></td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </section>

            <section>
                <h2 className="mb-3 font-serif text-2xl">Ulasan pelanggan</h2>
                {data.latest_reviews.length === 0 ? (
                    <p className="text-mute">Belum ada ulasan.</p>
                ) : (
                    <ul className="space-y-3">
                        {data.latest_reviews.map((r) => (
                            <li key={r.id} className="rounded-xl border border-line bg-white p-4">
                                <div className="flex flex-wrap items-center justify-between gap-2">
                                    <span className="font-medium">{r.product}</span>
                                    <span className="flex items-center gap-3">
                                        <Stars value={r.rating} size={14} />
                                        <button type="button" onClick={() => hapusUlasan(r)} aria-label="Hapus ulasan" className="text-mute hover:text-red-700">
                                            <Icon name="trash-2" size={16} />
                                        </button>
                                    </span>
                                </div>
                                <p className="text-sm text-mute">{`${r.name}, ${tanggal(r.created_at)}`}</p>
                                <p>{r.comment}</p>
                            </li>
                        ))}
                    </ul>
                )}
            </section>
        </div>
    );
}

import { useState } from 'react';
import { NavLink, Link, Outlet } from 'react-router-dom';
import Icon from '../components/Icon';
import { ProductProvider } from '../context/ProductContext';
import { useLogout } from '../hooks/useAuthActions';
import { authStore } from '../lib/auth';

const menuClass = ({ isActive }) =>
    `flex items-center gap-2 rounded-md px-3 py-2 transition-colors ${isActive ? 'bg-soft text-ink' : 'text-mute hover:bg-soft'}`;

export default function AdminLayout() {
    const [open, setOpen] = useState(false);
    const logout = useLogout();

    // Hapus login di browser setelah token dicabut di server. ProtectedRoute otomatis mengalihkan ke halaman login.
    const keluar = () => logout.mutate(undefined, { onSettled: () => authStore.clear({ manual: true }) });
    const tutup = () => setOpen(false);

    return (
        // ProductContext khusus admin: tabel produk 5 data per halaman, filternya terpisah dari toko
        <ProductProvider perPage={5}>
            {/* font-serif = Times New Roman untuk seluruh area admin, lining-nums = bentuk angka seragam */}
            <div className="relative flex h-screen bg-paper font-serif lining-nums">
                {/* Sidebar */}
                <aside className={`${open ? 'block' : 'hidden'} w-64 shrink-0 border-r border-line bg-white max-md:absolute max-md:inset-y-0 max-md:z-10 max-md:shadow-lg md:block`}>
                    <div className="flex items-center gap-2 p-5 font-serif text-2xl">
                        <Icon name="sparkles" size={20} className="text-acc" />Sutra Admin
                    </div>
                    <nav aria-label="Menu admin" className="flex flex-col gap-1 px-3">
                        <NavLink to="/admin" end onClick={tutup} className={menuClass}><Icon name="layout-dashboard" size={17} />Dashboard</NavLink>
                        <NavLink to="/admin/produk" onClick={tutup} className={menuClass}><Icon name="package" size={17} />Produk</NavLink>
                        <NavLink to="/admin/pesanan" onClick={tutup} className={menuClass}><Icon name="clipboard-list" size={17} />Pesanan</NavLink>
                        <NavLink to="/admin/about" onClick={tutup} className={menuClass}><Icon name="info" size={17} />About</NavLink>
                        <Link to="/" className="mt-4 flex items-center gap-2 rounded-md px-3 py-2 text-mute hover:bg-soft"><Icon name="store" size={17} />Lihat toko</Link>
                        {/* Keluar: hapus token di server (Sanctum) dan di browser */}
                        <button type="button" onClick={keluar} className="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-mute hover:bg-soft">
                            <Icon name="log-out" size={17} />Keluar
                        </button>
                    </nav>
                </aside>

                <div className="flex flex-1 flex-col overflow-hidden">
                    {/* Top bar khusus mobile */}
                    <div className="flex items-center justify-between border-b border-line bg-white p-4 md:hidden">
                        <h1 className="font-serif text-xl">Sutra Admin</h1>
                        <button type="button" onClick={() => setOpen((v) => !v)} aria-label="Buka menu" className="rounded border border-line p-2">
                            <Icon name="menu" size={18} />
                        </button>
                    </div>
                    <main className="flex-1 overflow-y-auto p-6">
                        <Outlet />
                    </main>
                    <footer className="border-t border-line bg-white p-4 text-center text-sm text-mute">&copy; 2025 Sutra Admin v2.0.0</footer>
                </div>
            </div>
        </ProductProvider>
    );
}

import { useEffect, useState } from 'react';
import { NavLink, Link, Outlet, useLocation, useNavigate } from 'react-router-dom';
import Icon from '../components/Icon';
import { ProductProvider, useProductContext } from '../context/ProductContext';
import { useCart } from '../lib/cart';

const menuClass = ({ isActive }) =>
    `flex items-center gap-1.5 border-b py-1 transition-colors ${isActive ? 'border-ink text-ink' : 'border-transparent text-mute hover:text-ink'}`;

function Navbar() {
    const { totalQty } = useCart();

    return (
        <header className="sticky top-0 z-20 border-b border-line bg-paper/90 backdrop-blur">
            <nav aria-label="Menu utama" className="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-x-6 gap-y-3 px-5 py-4">
                <Link to="/" className="flex items-center gap-2 font-serif text-3xl tracking-wide">
                    <Icon name="sparkles" size={22} className="text-acc" />Sutra Parfum
                </Link>

                {/* menu yang aktif ditandai garis bawah */}
                <div className="flex flex-wrap gap-x-5 gap-y-1 text-sm md:text-base">
                    <NavLink to="/" end className={menuClass}>
                        <Icon name="layout-dashboard" size={17} />Dashboard
                    </NavLink>
                    <NavLink to="/keranjang" className={menuClass}>
                        <Icon name="shopping-bag" size={17} />Keranjang
                        {totalQty > 0 && (
                            // key = jumlah: animasi "bump" diputar ulang setiap jumlah berubah
                            <span key={totalQty} className="bump rounded-full bg-acc px-2 text-xs text-white">{totalQty}</span>
                        )}
                    </NavLink>
                    <NavLink to="/checkout" className={menuClass}>
                        <Icon name="credit-card" size={17} />Checkout
                    </NavLink>
                    <Link to="/admin" className="flex items-center gap-1.5 border-b border-transparent py-1 text-mute transition-colors hover:text-ink">
                        <Icon name="settings" size={17} />Admin
                    </Link>
                </div>
            </nav>
        </header>
    );
}

// Kolom cari: kata kunci disimpan di ProductContext, jadi daftar produk di beranda ikut berubah.
function SearchBar() {
    const { filters, setFilters } = useProductContext();
    const location = useLocation();
    const navigate = useNavigate();

    const [q, setQ] = useState(filters.s);

    // filter berubah dari tempat lain (misal tombol "Tampilkan semua"): samakan isi kolom
    useEffect(() => setQ(filters.s), [filters.s]);

    const go = (value) => {
        setFilters({ s: value });
        if (location.pathname !== '/') navigate('/'); // dari halaman lain, hasil pencarian ditampilkan di beranda
    };

    // Mengetik: hasil dicari otomatis setelah berhenti mengetik; Enter juga bisa
    useEffect(() => {
        if (q === filters.s) return undefined;
        const timer = setTimeout(() => go(q), 500);
        return () => clearTimeout(timer);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [q]);

    return (
        <header className="border-b border-line bg-soft">
            <div className="mx-auto flex max-w-5xl flex-col items-center justify-between gap-2 px-5 py-4 md:flex-row">
                <form
                    role="search"
                    className="relative w-full md:w-1/3"
                    onSubmit={(e) => {
                        e.preventDefault();
                        go(q);
                    }}
                >
                    <Icon name="search" size={17} className="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-mute" />
                    <input
                        type="text"
                        value={q}
                        onChange={(e) => setQ(e.target.value)}
                        placeholder="Cari nama, aroma, golongan"
                        aria-label="Cari parfum"
                        className="w-full rounded-lg border border-line bg-white py-2 pl-10 pr-9 focus:border-line"
                    />
                    {q !== '' && (
                        <button type="button" onClick={() => { setQ(''); go(''); }} aria-label="Hapus pencarian" className="absolute right-3 top-1/2 -translate-y-1/2 text-mute hover:text-ink">
                            <Icon name="x" size={16} />
                        </button>
                    )}
                </form>
            </div>
        </header>
    );
}

export default function ShopLayout() {
    return (
        // seluruh area toko berbagi satu ProductContext (kolom cari di navbar dan daftar di beranda)
        <ProductProvider perPage={50}>
            <div className="flex min-h-screen flex-col">
                <Navbar />
                <SearchBar />
                <main className="page mx-auto w-full max-w-5xl flex-1 px-5 py-10">
                    <Outlet />
                </main>
                <footer className="border-t border-line py-6 text-center text-sm text-mute">
                    &copy; 2025 Sutra Parfum | Version 2.0
                </footer>
            </div>
        </ProductProvider>
    );
}

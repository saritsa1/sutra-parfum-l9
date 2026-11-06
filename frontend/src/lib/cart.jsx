// Keranjang belanja: disimpan di LocalStorage (pembeli tidak perlu akun).
// Harga di sini hanya untuk tampilan; saat checkout server menghitung ulang dari database.
import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { Link } from 'react-router-dom';
import toast from 'react-hot-toast';

const KEY = 'sutra-cart';
const CartContext = createContext(null);

function load() {
    try {
        const data = JSON.parse(localStorage.getItem(KEY));
        return Array.isArray(data) ? data : [];
    } catch {
        return [];
    }
}

export function CartProvider({ children }) {
    const [items, setItems] = useState(load);

    // setiap isi keranjang berubah, simpan ulang
    useEffect(() => {
        try {
            localStorage.setItem(KEY, JSON.stringify(items));
        } catch {
            /* diabaikan */
        }
    }, [items]);

    const add = useCallback((product, qty = 1) => {
        setItems((prev) => {
            const found = prev.find((i) => i.id === product.id);
            if (found) {
                return prev.map((i) => (i.id === product.id ? { ...i, qty: Math.min(i.qty + qty, i.stock) } : i));
            }
            // simpan seperlunya saja, bukan seluruh data produk dari API
            return [
                ...prev,
                {
                    id: product.id,
                    name: product.name,
                    slug: product.slug,
                    price: product.price,
                    stock: product.stock,
                    img_url: product.img_url,
                    color: product.color,
                    qty: Math.min(qty, product.stock),
                },
            ];
        });
        toast.success(
            (t) => (
                <span>
                    {product.name} masuk keranjang{' '}
                    <Link to="/keranjang" onClick={() => toast.dismiss(t.id)} className="underline underline-offset-4">
                        Lihat keranjang
                    </Link>
                </span>
            ),
            { id: 'cart-add' }
        );
    }, []);

    const updateQty = useCallback((id, qty) => {
        setItems((prev) =>
            prev.map((i) => (i.id === id ? { ...i, qty: Math.min(Math.max(1, qty || 1), i.stock) } : i))
        );
    }, []);

    const remove = useCallback((id) => setItems((prev) => prev.filter((i) => i.id !== id)), []);
    const clear = useCallback(() => setItems([]), []);

    const value = useMemo(
        () => ({
            items,
            add,
            updateQty,
            remove,
            clear,
            totalQty: items.reduce((s, i) => s + i.qty, 0),
            totalPrice: items.reduce((s, i) => s + i.qty * i.price, 0),
        }),
        [items, add, updateQty, remove, clear]
    );

    return <CartContext.Provider value={value}>{children}</CartContext.Provider>;
}

export function useCart() {
    const ctx = useContext(CartContext);
    if (!ctx) throw new Error('useCart harus dipakai di dalam CartProvider');
    return ctx;
}

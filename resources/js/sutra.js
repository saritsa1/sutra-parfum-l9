// Logika sisi browser untuk toko Sutra Parfum (Alpine.js).
// Keranjang, ulasan, dan pesanan disimpan di LocalStorage, sama seperti versi React.
// Data produk datang dari server (Blade/Controller), dikirim ke sini lewat @js(...).

// Kunci LocalStorage yang dipakai aplikasi
const KEYS = { cart: 'sutra-cart', reviews: 'sutra-reviews', orders: 'sutra-orders' };
const SERVICE_FEE = 1000;

// Baca dari LocalStorage (aman jika kosong/rusak)
function read(key, initial) {
    try {
        const raw = localStorage.getItem(key);
        return raw === null ? initial : JSON.parse(raw);
    } catch {
        return initial;
    }
}

// Simpan ke LocalStorage (abaikan jika penyimpanan penuh/diblokir)
function write(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
    } catch {
        /* diabaikan */
    }
}

const rupiah = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

// Rata-rata rating: dari ulasan pengguna jika ada, jika tidak pakai rating awal produk
function ratingInfo(p) {
    const list = read(KEYS.reviews, {})[p.id] ?? [];
    if (!list.length) return { avg: Number(p.rating), count: 0 };
    return { avg: list.reduce((s, r) => s + r.rating, 0) / list.length, count: list.length };
}

// Nomor VA tiruan: prefix bank + 10 angka acak
const makeVA = (prefix) => prefix + Math.floor(1e9 + Math.random() * 9e9);

// Fungsi bantu yang boleh dipanggil dari atribut Blade, contoh: x-text="sutra.rupiah(i.price)"
window.sutra = { rupiah, ratingInfo, KEYS };

document.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine;

    // ===== Keranjang (dipakai Navbar, Toast, Cart, Checkout) =====
    Alpine.store('cart', {
        items: read(KEYS.cart, []),
        toast: null,
        timer: null,

        init() {
            // Setiap isi keranjang berubah, simpan ulang ke LocalStorage
            Alpine.effect(() => write(KEYS.cart, this.items));
        },

        add(product, qty = 1) {
            const found = this.items.find((i) => i.id === product.id);
            if (found) found.qty = Math.min(found.qty + qty, found.stock);
            else this.items.push({ ...product, qty: Math.min(qty, product.stock) });
            this.notify(`${product.name} masuk keranjang`);
        },

        updateQty(id, qty) {
            const item = this.items.find((i) => i.id === id);
            if (item) item.qty = Math.min(Math.max(1, qty || 1), item.stock);
        },

        remove(id) {
            this.items = this.items.filter((i) => i.id !== id);
        },

        clear() {
            this.items = [];
        },

        // Notifikasi kecil "... masuk keranjang"
        notify(text) {
            clearTimeout(this.timer);
            this.toast = null;
            setTimeout(() => (this.toast = { id: Date.now(), text }), 0);
            this.timer = setTimeout(() => (this.toast = null), 2600);
        },

        get totalQty() {
            return this.items.reduce((s, i) => s + i.qty, 0);
        },
        get totalPrice() {
            return this.items.reduce((s, i) => s + i.qty * i.price, 0);
        },
    });

    // ===== Kartu produk (bisa dibalik) =====
    Alpine.data('productCard', (p) => ({
        p,
        flipped: false,
        rating: ratingInfo(p),
        buyNow() {
            Alpine.store('cart').add(p);
            location.href = window.sutraRoutes.checkout;
        },
    }));

    // ===== Halaman detail produk: jumlah, ulasan, rating =====
    Alpine.data('detailPage', (p) => ({
        p,
        qty: 1,
        allReviews: read(KEYS.reviews, {}),
        rating: 0,
        nama: '',
        review: '',
        error: '',

        get reviews() {
            return this.allReviews[p.id] ?? [];
        },
        get avg() {
            return this.reviews.length
                ? this.reviews.reduce((s, r) => s + r.rating, 0) / this.reviews.length
                : Number(p.rating);
        },

        buyNow() {
            Alpine.store('cart').add(p, this.qty);
            location.href = window.sutraRoutes.checkout;
        },

        submit() {
            if (!this.rating || !this.review.trim()) {
                this.error = 'Pilih rating bintang dan tulis ulasan Anda.';
                return;
            }
            const r = {
                id: Date.now(),
                nama: this.nama.trim() || 'Pembeli',
                rating: this.rating,
                review: this.review.trim(),
                tanggal: new Date().toLocaleDateString('id-ID'),
            };
            this.allReviews = { ...this.allReviews, [p.id]: [r, ...this.reviews] };
            write(KEYS.reviews, this.allReviews);
            this.rating = 0;
            this.nama = '';
            this.review = '';
            this.error = '';
        },

        remove(id) {
            this.allReviews = { ...this.allReviews, [p.id]: this.reviews.filter((r) => r.id !== id) };
            write(KEYS.reviews, this.allReviews);
        },
    }));

    // ===== Halaman checkout =====
    Alpine.data('checkoutPage', ({ shipping, groups }) => ({
        shipping,
        groups,
        form: { nama: '', telepon: '', alamat: '' },
        shipId: 'reguler',
        payId: '',
        errors: {},
        SERVICE_FEE,

        // Daftar datar semua metode bayar, ditambah type dari grupnya
        get allMethods() {
            return this.groups.flatMap((g) => g.methods.map((m) => ({ ...m, type: g.type })));
        },
        get ship() {
            return this.shipping.find((s) => s.id === this.shipId);
        },
        get method() {
            return this.allMethods.find((m) => m.id === this.payId);
        },
        get biayaMetode() {
            return this.method?.fee ?? 0;
        },
        get total() {
            return Alpine.store('cart').totalPrice + this.ship.cost + SERVICE_FEE + this.biayaMetode;
        },

        submit() {
            const err = {};
            if (this.form.nama.trim().length < 3) err.nama = 'Nama minimal 3 karakter.';
            if (!/^08\d{8,12}$/.test(this.form.telepon)) err.telepon = 'Nomor HP harus diawali 08 dan 10-14 digit.';
            if (this.form.alamat.trim().length < 10) err.alamat = 'Alamat minimal 10 karakter.';
            if (!this.method) err.bayar = 'Pilih metode pembayaran.';
            this.errors = err;
            if (Object.keys(err).length) return;

            const cart = Alpine.store('cart');
            const sekarang = Date.now();
            const order = {
                id: sekarang,
                no: 'SP' + String(sekarang).slice(-8),
                tanggal: new Date(sekarang).toLocaleString('id-ID'),
                batas: new Date(sekarang + 24 * 60 * 60 * 1000).toLocaleString('id-ID'),
                nama: this.form.nama.trim(),
                telepon: this.form.telepon,
                alamat: this.form.alamat.trim(),
                kurir: `${this.ship.name} (${this.ship.est})`,
                bayar: this.method.name, // nama metode, dipakai tabel admin
                tipe: this.method.type, // va / ewallet / cod
                va: this.method.prefix ? makeVA(this.method.prefix) : null,
                items: cart.items.map(({ id, name, qty, price, img }) => ({ id, name, qty, price, img })),
                subtotal: cart.totalPrice,
                ongkir: this.ship.cost,
                layanan: SERVICE_FEE,
                biayaMetode: this.biayaMetode,
                total: this.total,
                status: this.method.type === 'cod' ? 'Diproses' : 'Menunggu Pembayaran',
            };
            write(KEYS.orders, [...read(KEYS.orders, []), order]);
            cart.clear();
            // replace: tombol Back tidak kembali ke checkout yang sudah kosong
            location.replace(window.sutraRoutes.order.replace('__ID__', order.id));
        },
    }));

    // ===== Halaman pesanan setelah checkout =====
    Alpine.data('orderPage', (id) => ({
        orders: read(KEYS.orders, []),
        copied: false,

        get o() {
            return this.orders.find((x) => x.id === id);
        },
        get menunggu() {
            return this.o?.status === 'Menunggu Pembayaran';
        },

        async copy() {
            await navigator.clipboard.writeText(String(this.o.va));
            this.copied = true;
            setTimeout(() => (this.copied = false), 1500);
        },

        // Simulasi: di aplikasi asli status berubah dari notifikasi payment gateway
        bayar() {
            this.orders = this.orders.map((x) => (x.id === id ? { ...x, status: 'Dibayar' } : x));
            write(KEYS.orders, this.orders);
        },
    }));

    // ===== Dashboard admin: membaca pesanan dan ulasan dari toko =====
    Alpine.data('adminDashboard', (products) => ({
        products,
        orders: read(KEYS.orders, []),
        reviews: Object.entries(read(KEYS.reviews, {})).flatMap(([id, list]) =>
            list.map((r) => ({ ...r, produk: products.find((p) => p.id === +id)?.name }))
        ),

        get stats() {
            return [
                ['Pesanan', this.orders.length],
                ['Pendapatan', rupiah(this.orders.reduce((s, o) => s + o.total, 0))],
                ['Ulasan', this.reviews.length],
                ['Produk', this.products.length],
            ];
        },

        save() {
            write(KEYS.orders, this.orders);
        },

        reset() {
            if (!confirm('Hapus semua data demo (keranjang, pesanan, ulasan)?')) return;
            Object.values(KEYS).forEach((k) => localStorage.removeItem(k));
            location.reload();
        },
    }));
});
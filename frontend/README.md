# Sutra Parfum - Frontend (React SPA)

Cara menjalankan dan deploy ada di `../README.md`. Semua data (produk, pesanan, ulasan, login) diambil dari API Laravel.
Hanya keranjang belanja yang disimpan di LocalStorage browser pembeli.

Stack: React 18, React Router 6, Axios, TanStack React Query 5, React Hot Toast, Tailwind CSS 3, Vite.

```
src/
  main.jsx            titik masuk: QueryClientProvider, BrowserRouter, CartProvider, Toaster
  App.jsx             daftar route (toko publik dan /admin yang dilindungi)
  services/           LAPISAN SERVICE: satu-satunya tempat yang memanggil API (fungsi biasa, tanpa React)
    productService.js       list, get, create, update, remove produk        -> /products
    categoryService.js      list kategori                                   -> /categories
    orderService.js         opsi checkout, checkout, lihat, bayar, kelola   -> /orders, /admin/*
    reviewService.js        daftar, tambah, hapus ulasan                    -> /reviews
    authService.js          login (khusus admin) dan logout                 -> /login, /logout
    index.js                pintu masuk: import { productService } from '../services'
  context/            CONTEXT: state yang dibagikan ke banyak komponen
    ProductContext.jsx      produk: daftar, filter (cari, kategori, halaman), aksi simpan/hapus
    CategoryContext.jsx     kategori: daftar kategori (terpisah dari produk)
  lib/
    axios.js          instance Axios: baseURL dari VITE_API_URL, token Bearer, penanganan 401, pesan error
    auth.js           penyimpanan token dan data admin
    cart.jsx          CartContext: keranjang (Context + LocalStorage)
    format.js         format rupiah dan tanggal
  hooks/              pembungkus React Query (useQuery/useMutation + toast) di atas services
    useProducts.js, useCategories.js, useProductMutations.js, useOrders.js, useReviews.js, useAuthActions.js
  components/         Icon, Bottle, ProductImage, ProductCard, Stars, Pagination, PriceSummary, ErrorBox, ProtectedRoute
  layouts/            ShopLayout (navbar, pencarian, ProductProvider), AdminLayout (sidebar, ProductProvider)
  pages/shop/         Home, ProductDetail, Cart, Checkout, Order
  pages/admin/        Login, Dashboard, Products, ProductForm, Orders, About
  css/app.css         Tailwind dan gaya dasar
```

Alur data (tiap lapisan punya satu tugas):

```
Komponen/halaman -> Context -> hooks (React Query) -> services -> lib/axios -> API Laravel

Produk:    ProductContext  -> useProducts / useProductMutations -> productService  -> /products
Kategori:  CategoryContext -> useCategories                     -> categoryService -> /categories
```

- `services/` tahu alamat endpoint dan bentuk request. Tidak ada React di dalamnya.
- `hooks/` mengurus cache, status loading/error, dan toast.
- `context/ProductContext` menyimpan filter dan membagikan data produk ke banyak komponen sekaligus
  (kolom cari di navbar dan daftar di beranda memakai filter yang sama tanpa saling mengoper props).
  Dipasang dua kali (toko 50 data/halaman, admin 5 data/halaman) sehingga filternya terpisah.
- `context/CategoryContext` khusus kategori, dipasang satu kali di `main.jsx` karena toko dan admin memakai
  daftar yang sama. Halaman yang butuh produk dan kategori memakai kedua context sekaligus
  (contoh: `pages/shop/Home.jsx`, `pages/admin/ProductForm.jsx`).
- Contoh hapus produk: `deleteProduct(id)` dari context -> `useDeleteProduct` -> `productService.remove(id)`
  -> `api.delete('/products/{id}')` (token ditempel interceptor) -> berhasil: `invalidateQueries(['products'])`
  memuat ulang daftar dan toast sukses tampil; gagal: `MutationCache` di `main.jsx` menampilkan toast error.

`vercel.json` mengarahkan semua alamat ke `index.html` supaya refresh di route React tidak 404.

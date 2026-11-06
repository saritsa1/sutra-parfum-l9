# Sutra Parfum (backend dan frontend dipisah)

```
backend/    Laravel 9, REST API saja (Sanctum, MySQL)
frontend/   React 18 SPA (Vite, Tailwind, Axios, React Query, React Hot Toast)
```

Fitur utama
- Toko: katalog, cari dan filter, detail produk, ulasan, keranjang, checkout, halaman pesanan
- Admin: login, dashboard statistik, kelola produk (CRUD + upload foto), kelola pesanan
- Fitur tambahan di luar modul dasar: **modul Pesanan** (stok berkurang otomatis di dalam transaksi),
  **ulasan produk**, **role admin**, dan **dashboard statistik**

## Menjalankan di komputer sendiri

Backend (terminal 1)
```
cd backend
composer install
copy .env.example .env      # lalu isi DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan migrate --seed  # jangan pakai --seed kalau database sudah berisi data
php artisan storage:link    # supaya foto upload admin bisa dibuka
php artisan serve           # http://localhost:8000
```

Frontend (terminal 2)
```
cd frontend
npm install
copy .env.example .env      # VITE_API_URL = alamat API backend
npm run dev                 # http://localhost:5173
```

Login admin: `admin@example.com` / `password` (dari seeder). Akun lain (misalnya `test@example.com`
atau yang mendaftar lewat `/api/register`) bisa login ke API tetapi **tidak** bisa membuka halaman admin.
Kalau frontend jalan di alamat lain, ubah `FRONTEND_URL` di `.env` backend (untuk CORS).

## Deploy ke Vercel (dua project dari satu repo)

### 0. Kalau database online sudah berisi data dari versi sebelumnya
Cukup tambahkan tabel baru (pesanan, isi pesanan, ulasan, kolom `is_admin`), **jangan** pakai `--seed`:
```
php artisan migrate --force
php artisan db:seed --class=DemoDataSeeder --force   # opsional: pesanan dan ulasan contoh untuk demo
```
Migration `add_is_admin_to_users_table` otomatis menjadikan `admin@example.com` sebagai admin.

### 1. Persiapan
- **Database MySQL online** (Aiven, TiDB Cloud, Railway, dll). Isi dari komputermu: arahkan `.env` backend ke database online, lalu `php artisan migrate --seed --force` (atau impor SQL dari database lama).
- **Supabase Storage** untuk foto produk (filesystem Vercel read-only): buat project, buat bucket bernama `products` dengan akses **Public**, lalu catat `Project URL` dan `service_role key` (Settings, API).
- Buat `APP_KEY`: `php artisan key:generate --show`

### 2. Project backend
- Add New Project, pilih repo ini, **Root Directory: `backend`**
- Environment Variables:

| Nama | Isi |
|---|---|
| APP_NAME | Sutra Parfum API |
| APP_ENV | production |
| APP_DEBUG | false |
| APP_KEY | hasil `key:generate --show` |
| APP_URL | https://NAMA-BACKEND.vercel.app |
| FRONTEND_URL | https://NAMA-FRONTEND.vercel.app (tanpa garis miring di akhir) |
| DB_CONNECTION | mysql |
| DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD | dari database online |
| IMAGE_DRIVER | supabase |
| SUPABASE_URL | https://xxxx.supabase.co |
| SUPABASE_SERVICE_KEY | service_role key |
| SUPABASE_BUCKET | products |

Pengaturan lain (log, cache, folder /tmp, https) sudah diatur otomatis di `backend/api/index.php`.
Setelah deploy, buka `https://NAMA-BACKEND.vercel.app/api/products` dan harus muncul JSON.

### 3. Project frontend
- Add New Project, repo yang sama, **Root Directory: `frontend`**, Framework: Vite
- File `frontend/vercel.json` sudah ada: memastikan alamat seperti `/keranjang` atau `/admin/produk` tetap terbuka saat halaman di-refresh
- Environment Variable: `VITE_API_URL` = `https://NAMA-BACKEND.vercel.app/api`
- Setelah deploy, isi `FRONTEND_URL` di backend dengan alamat frontend, lalu **Redeploy backend**.
- Kalau `VITE_API_URL` diubah, frontend juga harus di-Redeploy (nilainya dibaca saat build).

## Endpoint API

| Method | URL | Akses |
|---|---|---|
| GET | /api/products?s=&category_id=&min_price=&max_price=&sort_by=&order=&per_page=&page= | publik |
| GET | /api/products/{id atau slug} | publik |
| GET | /api/categories | publik |
| GET | /api/checkout-options | publik (pengiriman, metode bayar, biaya layanan, status pesanan) |
| POST | /api/login, /api/register | publik |
| GET | /api/products/{id atau slug}/reviews | publik |
| POST | /api/products/{id atau slug}/reviews | publik (dibatasi 20 per menit) |
| POST | /api/orders | publik (checkout: harga dan total dihitung server) |
| GET | /api/orders/{order_no} | publik |
| POST | /api/orders/{order_no}/pay | publik (simulasi pembayaran) |
| GET | /api/profile, POST /api/logout | token |
| POST | /api/products | token admin |
| PUT/PATCH | /api/products/{id} | token admin |
| DELETE | /api/products/{id} | token admin |
| GET | /api/admin/stats | token admin |
| GET | /api/admin/orders?status=&s=&per_page=&page= | token admin |
| PATCH | /api/admin/orders/{id}/status | token admin |
| DELETE | /api/reviews/{id} | token admin |

Token dikirim lewat header `Authorization: Bearer <token>` dan kedaluwarsa setelah 24 jam.
Update produk yang ada upload gambarnya: kirim `POST` dengan field `_method=PUT`.
Akses ditolak: **401** kalau belum login, **403** kalau sudah login tetapi bukan admin.

Aturan pesanan
- Browser hanya mengirim id produk dan jumlah. Harga, ongkir, biaya layanan, dan total dihitung ulang server.
- Cek stok, simpan pesanan, dan kurangi stok berjalan dalam satu transaksi database (gagal satu, batal semua).
- Status: Menunggu Pembayaran, Dibayar, Diproses, Dikirim, Selesai, Dibatalkan. COD langsung Diproses.
- Status Selesai dan Dibatalkan tidak bisa diubah lagi. Membatalkan pesanan mengembalikan stok.
- Menghapus produk tidak menghapus riwayat pesanan (nama dan harga disalin ke isi pesanan).

## Tes backend
```
cd backend && php artisan test      # 24 tes: ApiTest (produk, auth) dan OrderTest (pesanan, ulasan, admin)
```

Frontend: `cd frontend && npm run build` harus selesai tanpa error.

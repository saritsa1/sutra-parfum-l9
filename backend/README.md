# Sutra Parfum - Backend (Laravel 9 API)

Hanya API (JSON), tidak ada Blade. Cara menjalankan dan deploy ada di `../README.md`.

- `routes/api.php`                       daftar endpoint (publik, login, login + admin)
- `app/Http/Controllers/Api/`            Auth, Product, Catalog, Order, Review, Dashboard
- `app/Models/`                          Product, Category, Order, OrderItem, Review, User
- `app/Http/Resources/`                  bentuk JSON (ProductResource, OrderResource, ReviewResource)
- `app/Http/Requests/`                   validasi input (FormRequest)
- `app/Http/Middleware/EnsureAdmin.php`  alias `admin`: hanya user dengan `is_admin = true` (selain itu 403)
- `app/Support/Katalog.php`              data tetap: pengiriman, metode bayar, status pesanan, biaya layanan
- `app/Support/ImageStorage.php`         simpan/hapus foto produk (local atau Supabase)
- `database/migrations/`                 categories, products, orders, order_items, reviews, kolom is_admin
- `database/seeders/`                    kategori, produk, admin, dan DemoDataSeeder (pesanan + ulasan contoh)
- `config/cors.php`                      alamat frontend yang diizinkan (`FRONTEND_URL`, boleh lebih dari satu dipisah koma)
- `api/index.php` dan `vercel.json`      pintu masuk dan konfigurasi untuk Vercel
- `tests/Feature/`                       ApiTest (produk, auth) dan OrderTest (pesanan, ulasan, role admin, statistik)

Relasi tabel: categories 1-N products, products 1-N reviews, orders 1-N order_items, products 1-N order_items
(`order_items.product_id` boleh NULL supaya riwayat pesanan tetap ada saat produk dihapus).

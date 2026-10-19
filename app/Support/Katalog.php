<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;

/**
 * Data toko dan katalog parfum.
 * Mengintegrasikan Eloquent ORM Product & Category dari database,
 * dengan fallback aman ke data bawaan.
 */
class Katalog
{
    public static function produk()
    {
        try {
            $products = Product::with('category')->get();
            if ($products->isNotEmpty()) {
                return $products;
            }
        } catch (\Throwable $e) {
            // fallback jika belum migrasi
        }

        return collect([
            (object) ['id' => 1, 'product_id' => 1, 'name' => "Rose d'Amour", 'slug' => 'rose-damour', 'price' => 485000, 'stock' => 12, 'category_id' => 1, 'category_name' => 'Floral', 'rating' => 4.6, 'color' => '#2f5a47', 'img' => asset('images/rose-damour.webp'), 'notes' => 'Mawar, peony, musk', 'desc' => 'Mawar yang lembut dan romantis, hangat di kulit. Cocok untuk acara santai maupun formal.'],
            (object) ['id' => 2, 'product_id' => 2, 'name' => 'Osmanthus Crystal', 'slug' => 'osmanthus-crystal', 'price' => 530000, 'stock' => 10, 'category_id' => 1, 'category_name' => 'Floral', 'rating' => 4.5, 'color' => '#e07b5a', 'img' => asset('images/osmanthus-crystal.webp'), 'notes' => 'Osmanthus, persik, musk bening', 'desc' => 'Bunga osmanthus yang cerah dengan sentuhan buah. Ringan, manis, dan mudah disukai.'],
            (object) ['id' => 3, 'product_id' => 3, 'name' => 'Hinoki Sublime', 'slug' => 'hinoki-sublime', 'price' => 560000, 'stock' => 8, 'category_id' => 2, 'category_name' => 'Woody', 'rating' => 4.4, 'color' => '#7a8a3a', 'img' => asset('images/hinoki-sublime.webp'), 'notes' => 'Hinoki, cedar, lumut', 'desc' => 'Kayu hinoki yang tenang dan segar seperti hutan setelah hujan. Tahan hingga sore.'],
            (object) ['id' => 4, 'product_id' => 4, 'name' => 'Alexandra', 'slug' => 'alexandra', 'price' => 610000, 'stock' => 6, 'category_id' => 2, 'category_name' => 'Woody', 'rating' => 4.8, 'color' => '#7f98b8', 'img' => asset('images/alexandra.webp'), 'notes' => 'Iris, cendana, amber lembut', 'desc' => 'Aroma bertekstur halus dengan dasar kayu yang hangat. Elegan untuk malam hari.'],
            (object) ['id' => 5, 'product_id' => 5, 'name' => 'Chronic Tonic', 'slug' => 'chronic-tonic', 'price' => 420000, 'stock' => 15, 'category_id' => 3, 'category_name' => 'Fresh', 'rating' => 4.2, 'color' => '#1f8a85', 'img' => asset('images/chronic-tonic.webp'), 'notes' => 'Jeruk, jahe, herbal', 'desc' => 'Segar dan bersemangat, seperti tonik herbal dingin. Pilihan untuk dipakai sehari-hari.'],
            (object) ['id' => 6, 'product_id' => 6, 'name' => 'High Tide Low Tide', 'slug' => 'high-tide-low-tide', 'price' => 450000, 'stock' => 9, 'category_id' => 3, 'category_name' => 'Fresh', 'rating' => 4.3, 'color' => '#6b4a36', 'img' => asset('images/high-tide-low-tide.webp'), 'notes' => 'Garam laut, driftwood, musk', 'desc' => 'Aroma pantai saat air pasang surut, dengan akhir kayu apung yang bersih.'],
        ]);
    }

    public static function kategori()
    {
        try {
            $cats = Category::pluck('category')->toArray();
            if (!empty($cats)) {
                return array_merge(['Semua'], $cats);
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return ['Semua', 'Floral', 'Woody', 'Fresh'];
    }

    public static function pengiriman()
    {
        return [
            ['id' => 'reguler', 'name' => 'Reguler', 'est' => '3-5 hari', 'cost' => 15000],
            ['id' => 'express', 'name' => 'Express', 'est' => '1-2 hari', 'cost' => 30000],
        ];
    }

    // type menentukan instruksi yang tampil di halaman pesanan
    public static function pembayaran()
    {
        return [
            ['group' => 'Transfer Bank (Virtual Account)', 'type' => 'va', 'methods' => [
                ['id' => 'bca', 'name' => 'BCA Virtual Account', 'prefix' => '39201', 'fee' => 0],
                ['id' => 'bni', 'name' => 'BNI Virtual Account', 'prefix' => '8808', 'fee' => 0],
                ['id' => 'mandiri', 'name' => 'Mandiri Virtual Account', 'prefix' => '88608', 'fee' => 0],
            ]],
            ['group' => 'E-Wallet', 'type' => 'ewallet', 'methods' => [
                ['id' => 'gopay', 'name' => 'GoPay', 'fee' => 0],
                ['id' => 'ovo', 'name' => 'OVO', 'fee' => 0],
                ['id' => 'shopeepay', 'name' => 'ShopeePay', 'fee' => 0],
            ]],
            ['group' => 'Bayar di Tempat', 'type' => 'cod', 'methods' => [
                ['id' => 'cod', 'name' => 'COD (Bayar di Tempat)', 'fee' => 5000],
            ]],
        ];
    }

    public static function statusPesanan()
    {
        return ['Menunggu Pembayaran', 'Dibayar', 'Diproses', 'Dikirim', 'Selesai'];
    }
}
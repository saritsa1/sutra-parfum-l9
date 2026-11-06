<?php

namespace App\Support;

/**
 * Data tetap untuk checkout (pengiriman, metode bayar, status pesanan).
 * Produk dan kategori tidak ada di sini lagi, semuanya diambil dari database.
 */
class Katalog
{
    // Biaya layanan tetap per pesanan (dihitung ulang oleh server saat checkout)
    public const BIAYA_LAYANAN = 1000;

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
        return ['Menunggu Pembayaran', 'Dibayar', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
    }

    // Cari satu opsi pengiriman berdasarkan id, null kalau tidak ada
    public static function cariPengiriman(string $id): ?array
    {
        foreach (self::pengiriman() as $p) {
            if ($p['id'] === $id) {
                return $p;
            }
        }
        return null;
    }

    // Cari satu metode pembayaran berdasarkan id. Hasilnya ditambah 'type' dari grupnya.
    public static function cariPembayaran(string $id): ?array
    {
        foreach (self::pembayaran() as $grup) {
            foreach ($grup['methods'] as $m) {
                if ($m['id'] === $id) {
                    return $m + ['type' => $grup['type']];
                }
            }
        }
        return null;
    }
}

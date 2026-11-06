<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

/**
 * Data contoh pesanan dan ulasan supaya dashboard admin tidak kosong saat demo.
 * Aman dijalankan ulang (tidak menambah data kalau sudah ada), dan bisa dijalankan
 * sendiri di database yang sudah berisi produk:  php artisan db:seed --class=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::orderBy('product_id')->take(4)->get();

        if ($products->count() < 3) {
            return; // produk belum ada, tidak ada yang bisa dipesan
        }

        if (Order::count() === 0) {
            $contoh = [
                ['SP261101DEMO1', 'Ayu Lestari',  '081234567801', 'Jl. Sunset Road No. 12, Kuta, Badung, Bali', 'reguler', 15000, 'bca',  'BCA Virtual Account', 'va',   '39201', 0,    'Selesai',             [[0, 1], [1, 1]]],
                ['SP261103DEMO2', 'Made Wirawan', '081234567802', 'Jl. Raya Ubud No. 8, Ubud, Gianyar, Bali',     'express', 30000, 'gopay','GoPay',              'ewallet', null,  0,    'Dikirim',             [[2, 2]]],
                ['SP261105DEMO3', 'Komang Sari',  '081234567803', 'Jl. Gatot Subroto Barat No. 3, Denpasar',       'reguler', 15000, 'cod',  'COD (Bayar di Tempat)', 'cod', null,   5000, 'Diproses',           [[1, 1]]],
                ['SP261106DEMO4', 'Putu Adi',     '081234567804', 'Jl. Teuku Umar No. 77, Denpasar, Bali',         'reguler', 15000, 'bni',  'BNI Virtual Account', 'va',   '8808',  0,    'Menunggu Pembayaran', [[0, 1]]],
            ];

            foreach ($contoh as [$no, $nama, $hp, $alamat, $kurir, $ongkir, $bayarId, $bayarNama, $tipe, $prefix, $biayaMetode, $status, $baris]) {
                $subtotal = 0;
                foreach ($baris as [$i, $qty]) {
                    $subtotal += (float) $products[$i]->price * $qty;
                }

                $order = Order::create([
                    'order_no'      => $no,
                    'customer_name' => $nama,
                    'phone'         => $hp,
                    'address'       => $alamat,
                    'shipping_name' => $kurir === 'express' ? 'Express' : 'Reguler',
                    'shipping_est'  => $kurir === 'express' ? '1-2 hari' : '3-5 hari',
                    'shipping_cost' => $ongkir,
                    'payment_id'    => $bayarId,
                    'payment_name'  => $bayarNama,
                    'payment_type'  => $tipe,
                    'va_number'     => $prefix ? $prefix . '1234567890' : null,
                    'subtotal'      => $subtotal,
                    'service_fee'   => 1000,
                    'payment_fee'   => $biayaMetode,
                    'total'         => $subtotal + $ongkir + 1000 + $biayaMetode,
                    'status'        => $status,
                    'expires_at'    => $tipe === 'cod' ? null : now()->addDay(),
                ]);

                foreach ($baris as [$i, $qty]) {
                    $p = $products[$i];
                    $order->items()->create([
                        'product_id' => $p->product_id,
                        'name'       => $p->name,
                        'price'      => (float) $p->price,
                        'qty'        => $qty,
                        'img'        => $p->img,
                        'color'      => $p->getRawOriginal('color'),
                    ]);
                }
            }
        }

        if (Review::count() === 0) {
            $ulasan = [
                [0, 'Ayu',   5, 'Wanginya lembut dan tahan seharian. Suka sekali.'],
                [0, 'Made',  4, 'Aroma mawarnya tidak menyengat, cocok untuk kantor.'],
                [1, 'Komang', 5, 'Segar dan manis, banyak yang bertanya parfum apa ini.'],
                [2, 'Putu',  4, 'Kemasan rapi, pengiriman cepat. Aromanya hangat.'],
            ];

            foreach ($ulasan as [$i, $nama, $rating, $isi]) {
                $products[$i]->reviews()->create(['name' => $nama, 'rating' => $rating, 'comment' => $isi]);
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\Katalog;

class CatalogController extends Controller
{
    // Daftar golongan aroma, dipakai filter di toko dan dropdown di form admin
    public function categories()
    {
        $data = Category::orderBy('category_id')->get()->map(fn ($c) => [
            'id'   => $c->category_id,
            'name' => $c->category,
        ]);

        return response()->json(['data' => $data]);
    }

    // Opsi pengiriman, metode pembayaran, dan status pesanan untuk halaman checkout
    public function checkoutOptions()
    {
        return response()->json([
            'shipping' => Katalog::pengiriman(),
            'payment'  => Katalog::pembayaran(),
            'statuses' => Katalog::statusPesanan(),
            'service_fee' => Katalog::BIAYA_LAYANAN,
        ]);
    }
}

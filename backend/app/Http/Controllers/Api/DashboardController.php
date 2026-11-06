<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ReviewResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Support\Katalog;

class DashboardController extends Controller
{
    // Batas stok dianggap menipis
    private const STOK_MENIPIS = 5;

    /**
     * Ringkasan untuk halaman Dashboard admin: angka utama, jumlah pesanan per status,
     * produk dengan stok menipis, serta pesanan dan ulasan terbaru.
     */
    public function stats()
    {
        $perStatus = Order::selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        // semua status selalu muncul, walau jumlahnya 0
        $ordersByStatus = collect(Katalog::statusPesanan())
            ->mapWithKeys(fn ($s) => [$s => (int) ($perStatus[$s] ?? 0)]);

        $stokMenipis = Product::with('category')
            ->where('stock', '<=', self::STOK_MENIPIS)
            ->orderBy('stock')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'id'       => $p->product_id,
                'name'     => $p->name,
                'slug'     => $p->slug,
                'stock'    => (int) $p->stock,
                'category' => $p->category?->category,
            ]);

        return response()->json([
            'data' => [
                'total_products'  => Product::count(),
                'total_orders'    => Order::count(),
                'total_reviews'   => Review::count(),
                'revenue'         => (float) Order::whereIn('status', Order::STATUS_PENDAPATAN)->sum('total'),
                'low_stock_limit' => self::STOK_MENIPIS,
                'low_stock'       => $stokMenipis,
                'orders_by_status' => $ordersByStatus,
                'latest_orders'   => OrderResource::collection(Order::with('items')->latest('id')->limit(5)->get()),
                'latest_reviews'  => ReviewResource::collection(Review::with('product')->latest('id')->limit(5)->get()),
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Support\Katalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /* ===================== PUBLIK (dipakai halaman checkout) ===================== */

    /**
     * Buat pesanan baru.
     * Browser hanya mengirim id produk dan jumlah. Harga, ongkir, biaya layanan, dan total
     * dihitung ulang di sini, jadi tidak bisa dimanipulasi dari sisi klien.
     * Semua langkah (cek stok, simpan pesanan, kurangi stok) berjalan dalam satu transaksi:
     * kalau salah satu gagal, semuanya dibatalkan.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $ship = Katalog::cariPengiriman($data['shipping_id']);
        $pay  = Katalog::cariPembayaran($data['payment_id']);

        if (!$ship) {
            throw ValidationException::withMessages(['shipping_id' => ['Opsi pengiriman tidak dikenal.']]);
        }
        if (!$pay) {
            throw ValidationException::withMessages(['payment_id' => ['Metode pembayaran tidak dikenal.']]);
        }

        $order = DB::transaction(function () use ($data, $ship, $pay) {
            // lockForUpdate: dua pembeli yang checkout bersamaan tidak bisa sama-sama mengambil stok terakhir
            $products = Product::whereIn('product_id', collect($data['items'])->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            $subtotal = 0;
            $lines = [];

            foreach ($data['items'] as $row) {
                $product = $products[$row['product_id']];

                if ($row['qty'] > $product->stock) {
                    throw ValidationException::withMessages([
                        'items' => ["Stok {$product->name} tidak cukup (tersisa {$product->stock})."],
                    ]);
                }

                $price = (float) $product->price;
                $subtotal += $price * $row['qty'];

                $lines[] = [
                    'product'    => $product,
                    'attributes' => [
                        'product_id' => $product->product_id,
                        'name'       => $product->name,
                        'price'      => $price,
                        'qty'        => $row['qty'],
                        'img'        => $product->img,
                        'color'      => $product->getRawOriginal('color'),
                    ],
                ];
            }

            $serviceFee = Katalog::BIAYA_LAYANAN;
            $paymentFee = (float) ($pay['fee'] ?? 0);
            $isCod      = $pay['type'] === 'cod';

            $order = Order::create([
                'order_no'      => $this->nomorPesanan(),
                'customer_name' => $data['customer_name'],
                'phone'         => $data['phone'],
                'address'       => $data['address'],
                'shipping_name' => $ship['name'],
                'shipping_est'  => $ship['est'],
                'shipping_cost' => $ship['cost'],
                'payment_id'    => $pay['id'],
                'payment_name'  => $pay['name'],
                'payment_type'  => $pay['type'],
                'va_number'     => isset($pay['prefix']) ? $pay['prefix'] . random_int(1000000000, 9999999999) : null,
                'subtotal'      => $subtotal,
                'service_fee'   => $serviceFee,
                'payment_fee'   => $paymentFee,
                'total'         => $subtotal + $ship['cost'] + $serviceFee + $paymentFee,
                'status'        => $isCod ? 'Diproses' : 'Menunggu Pembayaran',
                'expires_at'    => $isCod ? null : now()->addDay(),
            ]);

            foreach ($lines as $line) {
                $order->items()->create($line['attributes']);
                $line['product']->decrement('stock', $line['attributes']['qty']);
            }

            return $order;
        });

        return (new OrderResource($order->load('items')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Lihat satu pesanan lewat nomor pesanan (dipakai halaman "Pesanan berhasil dibuat").
     */
    public function show(string $orderNo)
    {
        $order = Order::with('items')->where('order_no', $orderNo)->first();

        if (!$order) {
            return $this->tidakDitemukan();
        }

        return new OrderResource($order);
    }

    /**
     * Simulasi pembayaran. Di aplikasi sungguhan status ini berubah lewat notifikasi
     * payment gateway; di sini cukup tombol "Simulasikan pembayaran".
     */
    public function pay(string $orderNo)
    {
        $order = Order::with('items')->where('order_no', $orderNo)->first();

        if (!$order) {
            return $this->tidakDitemukan();
        }

        if ($order->status !== 'Menunggu Pembayaran') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan ini tidak sedang menunggu pembayaran.',
            ], 422);
        }

        $order->update(['status' => 'Dibayar']);

        return new OrderResource($order);
    }

    /* ===================== ADMIN (auth:sanctum + admin) ===================== */

    /**
     * Daftar pesanan: filter status (?status=), cari nomor/nama (?s=), paginasi.
     */
    public function index(Request $request)
    {
        $query = Order::with('items')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('s')) {
            $keyword = $request->query('s');
            $query->where(function ($q) use ($keyword) {
                $q->where('order_no', 'like', "%{$keyword}%")
                  ->orWhere('customer_name', 'like', "%{$keyword}%");
            });
        }

        $perPage = max(1, min(50, (int) $request->query('per_page', 10)));

        return OrderResource::collection($query->paginate($perPage));
    }

    /**
     * Ubah status pesanan.
     * Aturan: status "Selesai" dan "Dibatalkan" sudah final. Membatalkan pesanan
     * mengembalikan stok produk (dalam transaksi yang sama).
     */
    public function updateStatus(UpdateOrderStatusRequest $request, string $id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return $this->tidakDitemukan();
        }

        if ($order->isFinal()) {
            return response()->json([
                'status'  => 'error',
                'message' => "Pesanan berstatus {$order->status} tidak bisa diubah lagi.",
            ], 422);
        }

        $baru = $request->validated()['status'];

        DB::transaction(function () use ($order, $baru) {
            if ($baru === 'Dibatalkan') {
                foreach ($order->items as $item) {
                    // product_id NULL = produknya sudah dihapus, tidak ada stok yang dikembalikan
                    if ($item->product_id) {
                        Product::where('product_id', $item->product_id)->increment('stock', $item->qty);
                    }
                }
            }

            $order->update(['status' => $baru]);
        });

        return new OrderResource($order->refresh()->load('items'));
    }

    /* ===================== bantu ===================== */

    // Contoh: SP261106K3X9Q (SP + tanggal yymmdd + 5 karakter acak), dijamin unik
    private function nomorPesanan(): string
    {
        do {
            $no = 'SP' . now()->format('ymd') . strtoupper(Str::random(5));
        } while (Order::where('order_no', $no)->exists());

        return $no;
    }

    private function tidakDitemukan()
    {
        return response()->json([
            'status'  => 'error',
            'message' => 'Pesanan tidak ditemukan.',
        ], 404);
    }
}

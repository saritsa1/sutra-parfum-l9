<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes fitur tambahan: Pesanan, Ulasan, role admin, dan statistik dashboard.
 */
class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // admin, kategori, 8 produk, plus data contoh pesanan dan ulasan
    }

    private function adminHeaders(): array
    {
        $token = $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'password'])
            ->assertOk()->json('token');

        return ['Authorization' => 'Bearer ' . $token];
    }

    private function userBiasaHeaders(): array
    {
        $token = $this->postJson('/api/login', ['email' => 'test@example.com', 'password' => 'password'])
            ->assertOk()->json('token');

        return ['Authorization' => 'Bearer ' . $token];
    }

    // Payload checkout yang valid; $items = [[product_id, qty], ...]
    private function payload(array $items, array $ubah = []): array
    {
        return array_merge([
            'customer_name' => 'Budi Santoso',
            'phone'         => '081234567890',
            'address'       => 'Jl. Merdeka No. 10, Denpasar',
            'shipping_id'   => 'reguler',
            'payment_id'    => 'bca',
            'items'         => array_map(fn ($i) => ['product_id' => $i[0], 'qty' => $i[1]], $items),
        ], $ubah);
    }

    /* ---------- checkout ---------- */

    public function test_checkout_menghitung_total_di_server_dan_mengurangi_stok()
    {
        $produk = Product::find(1);
        $stokAwal = $produk->stock;
        $harga = (float) $produk->price;

        // harga palsu dari browser harus diabaikan
        $body = $this->payload([[1, 2]]);
        $body['items'][0]['price'] = 1;
        $body['total'] = 1;

        $res = $this->postJson('/api/orders', $body)->assertCreated();

        // subtotal + ongkir reguler 15000 + layanan 1000 + biaya BCA 0
        $this->assertEquals($harga * 2 + 15000 + 1000, $res->json('data.total'));
        $this->assertEquals($harga * 2, $res->json('data.subtotal'));
        $this->assertSame('Menunggu Pembayaran', $res->json('data.status'));
        $this->assertStringStartsWith('39201', $res->json('data.payment.va_number'));
        $this->assertStringStartsWith('SP', $res->json('data.order_no'));
        $this->assertSame($stokAwal - 2, $produk->fresh()->stock);
    }

    public function test_checkout_cod_langsung_diproses_dan_ada_biaya_metode()
    {
        $res = $this->postJson('/api/orders', $this->payload([[1, 1]], ['payment_id' => 'cod']))->assertCreated();

        $this->assertSame('Diproses', $res->json('data.status'));
        $this->assertNull($res->json('data.payment.va_number'));
        $this->assertEquals(5000, $res->json('data.payment_fee'));
    }

    public function test_checkout_ditolak_kalau_stok_kurang_dan_stok_tidak_berubah()
    {
        $produk = Product::find(2);
        $stokAwal = $produk->stock;
        $jumlahPesanan = Order::count();

        // produk 1 cukup, produk 2 melebihi stok: seluruh pesanan harus batal (transaksi)
        $this->postJson('/api/orders', $this->payload([[1, 1], [2, $stokAwal + 1]]))
            ->assertStatus(422)->assertJsonValidationErrors('items');

        $this->assertSame(Order::count(), $jumlahPesanan);
        $this->assertSame($stokAwal, $produk->fresh()->stock);
        $this->assertSame(12, Product::find(1)->stock);
    }

    public function test_checkout_validasi_input()
    {
        $this->postJson('/api/orders', [])->assertStatus(422)
            ->assertJsonValidationErrors(['customer_name', 'phone', 'address', 'items']);
        $this->postJson('/api/orders', $this->payload([[1, 1]], ['phone' => '12345']))
            ->assertStatus(422)->assertJsonValidationErrors('phone');
        $this->postJson('/api/orders', $this->payload([[1, 1]], ['payment_id' => 'bitcoin']))
            ->assertStatus(422)->assertJsonValidationErrors('payment_id');
        $this->postJson('/api/orders', $this->payload([[1, 1]], ['shipping_id' => 'roket']))
            ->assertStatus(422)->assertJsonValidationErrors('shipping_id');
        $this->postJson('/api/orders', $this->payload([[9999, 1]]))
            ->assertStatus(422)->assertJsonValidationErrors('items.0.product_id');
    }

    public function test_lihat_pesanan_dan_simulasi_bayar()
    {
        $no = $this->postJson('/api/orders', $this->payload([[1, 1]]))->json('data.order_no');

        $this->getJson("/api/orders/$no")->assertOk()
            ->assertJsonPath('data.customer_name', 'Budi Santoso')
            ->assertJsonCount(1, 'data.items');

        $this->postJson("/api/orders/$no/pay")->assertOk()->assertJsonPath('data.status', 'Dibayar');
        // sudah dibayar, tidak bisa dibayar lagi
        $this->postJson("/api/orders/$no/pay")->assertStatus(422);

        $this->getJson('/api/orders/SPTIDAKADA')->assertNotFound();
    }

    /* ---------- admin: pesanan ---------- */

    public function test_endpoint_admin_butuh_login_dan_role_admin()
    {
        $this->getJson('/api/admin/orders')->assertStatus(401);
        $this->getJson('/api/admin/stats')->assertStatus(401);

        // user biasa (bukan admin) ditolak, termasuk saat mengelola produk
        $h = $this->userBiasaHeaders();
        $this->withHeaders($h)->getJson('/api/admin/orders')->assertStatus(403);
        $this->withHeaders($h)->getJson('/api/admin/stats')->assertStatus(403);
        $this->withHeaders($h)->postJson('/api/products', ['name' => 'X'])->assertStatus(403);
        $this->withHeaders($h)->deleteJson('/api/products/1')->assertStatus(403);
    }

    public function test_user_yang_mendaftar_sendiri_bukan_admin()
    {
        $this->postJson('/api/register', [
            'name' => 'Penyusup', 'email' => 'penyusup@example.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'is_admin' => true,
        ])->assertStatus(201);

        $token = $this->postJson('/api/login', ['email' => 'penyusup@example.com', 'password' => 'password123'])
            ->assertOk()->assertJsonPath('user.is_admin', false)->json('token');

        $this->withHeaders(['Authorization' => "Bearer $token"])->getJson('/api/admin/orders')->assertStatus(403);
    }

    public function test_admin_melihat_daftar_pesanan_dengan_filter()
    {
        $h = $this->adminHeaders();

        $semua = $this->withHeaders($h)->getJson('/api/admin/orders?per_page=50')->assertOk();
        $this->assertCount(4, $semua->json('data')); // 4 pesanan contoh dari DemoDataSeeder

        $this->withHeaders($h)->getJson('/api/admin/orders?status=Selesai')->assertOk()->assertJsonCount(1, 'data');
        $this->withHeaders($h)->getJson('/api/admin/orders?s=Komang')->assertOk()->assertJsonCount(1, 'data');
        $this->withHeaders($h)->getJson('/api/admin/orders?s=SP261103DEMO2')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_admin_mengubah_status_dan_status_final_terkunci()
    {
        $h = $this->adminHeaders();
        $order = Order::where('status', 'Diproses')->first();

        $this->withHeaders($h)->patchJson("/api/admin/orders/{$order->id}/status", ['status' => 'Dikirim'])
            ->assertOk()->assertJsonPath('data.status', 'Dikirim');

        $this->withHeaders($h)->patchJson("/api/admin/orders/{$order->id}/status", ['status' => 'Selesai'])->assertOk();

        // sudah Selesai: final
        $this->withHeaders($h)->patchJson("/api/admin/orders/{$order->id}/status", ['status' => 'Dikirim'])
            ->assertStatus(422);

        // status ngawur
        $lain = Order::where('status', 'Dikirim')->first();
        $this->withHeaders($h)->patchJson("/api/admin/orders/{$lain->id}/status", ['status' => 'Terbang'])
            ->assertStatus(422);

        $this->withHeaders($h)->patchJson('/api/admin/orders/9999/status', ['status' => 'Selesai'])->assertNotFound();
    }

    public function test_membatalkan_pesanan_mengembalikan_stok()
    {
        $produk = Product::find(1);
        $stokAwal = $produk->stock;

        $order = $this->postJson('/api/orders', $this->payload([[1, 3]]))->assertCreated();
        $this->assertSame($stokAwal - 3, $produk->fresh()->stock);

        $h = $this->adminHeaders();
        $this->withHeaders($h)->patchJson('/api/admin/orders/' . $order->json('data.id') . '/status', ['status' => 'Dibatalkan'])
            ->assertOk()->assertJsonPath('data.status', 'Dibatalkan');

        $this->assertSame($stokAwal, $produk->fresh()->stock);

        // dibatalkan dua kali tidak boleh menambah stok lagi
        $this->withHeaders($h)->patchJson('/api/admin/orders/' . $order->json('data.id') . '/status', ['status' => 'Dibatalkan'])
            ->assertStatus(422);
        $this->assertSame($stokAwal, $produk->fresh()->stock);
    }

    public function test_hapus_produk_tidak_menghapus_riwayat_pesanan()
    {
        $h = $this->adminHeaders();
        $res = $this->postJson('/api/orders', $this->payload([[3, 1]]))->assertCreated();

        $this->withHeaders($h)->deleteJson('/api/products/3')->assertOk();

        $this->getJson('/api/orders/' . $res->json('data.order_no'))->assertOk()
            ->assertJsonPath('data.items.0.product_id', null)
            ->assertJsonCount(1, 'data.items');
    }

    /* ---------- dashboard ---------- */

    public function test_statistik_dashboard()
    {
        $res = $this->withHeaders($this->adminHeaders())->getJson('/api/admin/stats')->assertOk();

        $this->assertSame(8, $res->json('data.total_products'));
        $this->assertSame(4, $res->json('data.total_orders'));
        $this->assertSame(1, $res->json('data.orders_by_status.Selesai'));
        $this->assertSame(0, $res->json('data.orders_by_status.Dibatalkan'));
        $this->assertGreaterThan(0, $res->json('data.revenue'));
        // pesanan "Menunggu Pembayaran" belum dihitung sebagai pendapatan
        $dibayar = Order::whereIn('status', Order::STATUS_PENDAPATAN)->sum('total');
        $this->assertEquals($dibayar, $res->json('data.revenue'));
        $this->assertCount(4, $res->json('data.latest_reviews'));
    }

    /* ---------- ulasan ---------- */

    public function test_ulasan_dan_rata_rata_rating_produk()
    {
        // produk 1 punya 2 ulasan contoh (5 dan 4) => rata-rata 4.5
        $this->getJson('/api/products/rose-damour/reviews')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/products/1')->assertOk()
            ->assertJsonPath('data.reviews_count', 2)
            ->assertJsonPath('data.rating', 4.5);

        $this->postJson('/api/products/rose-damour/reviews', ['name' => 'Dewi', 'rating' => 3, 'comment' => 'Cukup bagus'])
            ->assertCreated()->assertJsonPath('data.name', 'Dewi');

        // (5 + 4 + 3) / 3 = 4
        $this->getJson('/api/products/1')->assertJsonPath('data.reviews_count', 3)->assertJsonPath('data.rating', 4);

        // nama kosong jadi "Pembeli"
        $this->postJson('/api/products/1/reviews', ['rating' => 5, 'comment' => 'Mantap'])
            ->assertCreated()->assertJsonPath('data.name', 'Pembeli');
    }

    public function test_validasi_ulasan_dan_hapus_hanya_admin()
    {
        $this->postJson('/api/products/1/reviews', ['rating' => 9, 'comment' => 'x'])
            ->assertStatus(422)->assertJsonValidationErrors(['rating', 'comment']);
        $this->postJson('/api/products/tidak-ada/reviews', ['rating' => 5, 'comment' => 'Bagus'])->assertNotFound();

        $id = $this->getJson('/api/products/1/reviews')->json('data.0.id');

        $this->deleteJson("/api/reviews/$id")->assertStatus(401);
        $this->withHeaders($this->userBiasaHeaders())->deleteJson("/api/reviews/$id")->assertStatus(403);

        // Guard Sanctum menyimpan user dari request sebelumnya selama satu tes (limiter 'throttle:api'
        // ikut memanggilnya, termasuk saat login). Ambil token admin dulu, lalu reset guard sebelum ganti akun.
        $adminHeaders = $this->adminHeaders();
        $this->app['auth']->forgetGuards();
        $this->withHeaders($adminHeaders)->deleteJson("/api/reviews/$id")->assertOk();
        $this->getJson('/api/products/1/reviews')->assertJsonCount(1, 'data');
    }
}

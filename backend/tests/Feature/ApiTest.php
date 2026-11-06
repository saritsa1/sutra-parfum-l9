<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // user admin, kategori, dan 8 produk
    }

    // helper: login sebagai admin dan ambil token
    private function token(): string
    {
        return $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertOk()->json('token');
    }

    public function test_server_hidup()
    {
        $this->get('/')->assertOk()->assertJson(['status' => 'ok']);
    }

    public function test_kategori_dan_opsi_checkout_publik()
    {
        $this->getJson('/api/categories')->assertOk()->assertJsonPath('data.0.name', 'Floral');
        $this->getJson('/api/checkout-options')->assertOk()
            ->assertJsonStructure(['shipping', 'payment', 'statuses']);
    }

    public function test_daftar_produk_publik_dan_filter()
    {
        $res = $this->getJson('/api/products?per_page=50')->assertOk();
        $this->assertCount(8, $res->json('data'));
        $this->assertStringStartsWith('http', $res->json('data.0.img_url'));

        // cari berdasarkan nama
        $this->getJson('/api/products?s=hinoki')->assertOk()->assertJsonCount(1, 'data');
        // cari berdasarkan nama kategori
        $this->getJson('/api/products?s=woody')->assertOk()->assertJsonCount(2, 'data');
        // filter category_id (1 = Floral)
        $this->getJson('/api/products?category_id=1')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_detail_produk_pakai_slug_atau_id()
    {
        $this->getJson('/api/products/rose-damour')->assertOk()
            ->assertJsonPath('data.name', "Rose d'Amour")
            ->assertJsonPath('data.category.name', 'Floral');
        $this->getJson('/api/products/1')->assertOk()->assertJsonPath('data.slug', 'rose-damour');
        $this->getJson('/api/products/tidak-ada')->assertNotFound();
    }

    public function test_tulis_data_wajib_login_dan_balasannya_401_bukan_redirect()
    {
        // sengaja tanpa header Accept: application/json
        $this->post('/api/products', ['name' => 'X'])->assertStatus(401);
        $this->delete('/api/products/1')->assertStatus(401);
    }

    public function test_login_salah_ditolak()
    {
        $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'salah'])
            ->assertStatus(422);
    }

    public function test_crud_produk_oleh_admin()
    {
        Storage::fake('public');
        $h = ['Authorization' => 'Bearer ' . $this->token()];

        // create + upload gambar
        $created = $this->withHeaders($h)->post('/api/products', [
            'name' => 'Produk Uji', 'category_id' => 3, 'price' => 100000, 'stock' => 5,
            'description' => 'deskripsi', 'notes' => 'a, b',
            'img' => UploadedFile::fake()->image('botol.jpg'),
        ], ['Accept' => 'application/json'])->assertCreated();

        $id = $created->json('data.id');
        $this->assertSame('produk-uji', $created->json('data.slug'));
        $this->assertStringContainsString('/storage/products/', $created->json('data.img_url'));
        $oldPath = \App\Models\Product::find($id)->getRawOriginal('img');
        Storage::disk('public')->assertExists($oldPath);

        // update via POST + _method=PUT (cara yang dipakai frontend untuk multipart)
        $this->withHeaders($h)->post("/api/products/$id", [
            '_method' => 'PUT', 'name' => 'Produk Uji Baru', 'price' => 120000,
            'img' => UploadedFile::fake()->image('baru.png'),
        ], ['Accept' => 'application/json'])->assertOk()->assertJsonPath('data.name', 'Produk Uji Baru');

        // gambar lama harus ikut terhapus
        Storage::disk('public')->assertMissing($oldPath);

        // validasi
        $this->withHeaders($h)->postJson('/api/products', ['name' => ''])->assertStatus(422);

        // delete
        $this->withHeaders($h)->deleteJson("/api/products/$id")->assertOk();
        $this->getJson("/api/products/$id")->assertNotFound();
    }

    public function test_cors_untuk_frontend()
    {
        $this->withHeaders(['Origin' => 'http://localhost:5173'])
            ->getJson('/api/products')
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
    }

    // Jalur Vercel: foto diunggah ke Supabase Storage lewat HTTP (dipalsukan di tes)
    public function test_foto_produk_ke_supabase()
    {
        config([
            'services.product_images.driver' => 'supabase',
            'services.supabase' => ['url' => 'https://abc.supabase.co', 'key' => 'rahasia', 'bucket' => 'products'],
        ]);
        \Illuminate\Support\Facades\Http::fake(['abc.supabase.co/*' => \Illuminate\Support\Facades\Http::response(['Key' => 'ok'], 200)]);
        $h = ['Authorization' => 'Bearer ' . $this->token()];

        $res = $this->withHeaders($h)->post('/api/products', [
            'name' => 'Produk Supabase', 'category_id' => 1, 'price' => 50000, 'stock' => 3,
            'img' => UploadedFile::fake()->image('a.jpg'),
        ], ['Accept' => 'application/json'])->assertCreated();

        $url = $res->json('data.img_url');
        $this->assertStringStartsWith('https://abc.supabase.co/storage/v1/object/public/products/products/', $url);
        \Illuminate\Support\Facades\Http::assertSent(fn ($r) => $r->method() === 'POST'
            && str_starts_with($r->url(), 'https://abc.supabase.co/storage/v1/object/products/products/')
            && $r->hasHeader('Authorization', 'Bearer rahasia'));

        // hapus produk -> file di Supabase ikut dihapus
        $this->withHeaders($h)->deleteJson('/api/products/' . $res->json('data.id'))->assertOk();
        \Illuminate\Support\Facades\Http::assertSent(fn ($r) => $r->method() === 'DELETE'
            && str_starts_with($r->url(), 'https://abc.supabase.co/storage/v1/object/products/products/'));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar data persisten produk lengkap dengan fitur pencarian,
     * penyaringan (filter), pengurutan (sorting), dan paginasi terstandar.
     */
    public function index(Request $request)
    {
        $query = Product::withRatings()->with('category');

        // Fitur Pencarian (Search by keyword 's' atau 'search')
        if ($request->filled('s') || $request->filled('search')) {
            $keyword = $request->query('s') ?? $request->query('search');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('notes', 'like', "%{$keyword}%")
                  ->orWhereHas('category', function ($c) use ($keyword) {
                      $c->where('category', 'like', "%{$keyword}%");
                  });
            });
        }

        // Fitur Penyaringan (Filter berdasarkan kategori)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        // Fitur Penyaringan Rentang Harga (min_price & max_price)
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->query('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->query('max_price'));
        }

        // Pengurutan (Sorting)
        $sortBy = in_array($request->query('sort_by'), ['name', 'price', 'stock', 'created_at'])
            ? $request->query('sort_by')
            : 'created_at';
        $order = strtolower($request->query('order')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $order);

        // Paginasi dinamis
        $perPage = (int) $request->query('per_page', 10);
        $products = $query->paginate(max(1, min(50, $perPage)));

        return ProductResource::collection($products);
    }

    /**
     * Simpan data produk baru ke penyimpanan persisten.
     * Menggunakan validasi terisolasi (StoreProductRequest),
     * pengamanan mutasi (Database Transaction), dan pengunggahan berkas.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        // Pengamanan mutasi data melalui Database Transaction
        $product = DB::transaction(function () use ($request, $validated) {
            $path = null;
            if ($request->hasFile('img')) {
                // Pengunggahan berkas: nama file unik dibuat otomatis oleh Laravel
                $path = ImageStorage::store($request->file('img'));
            }

            $validated['img'] = $path;
            $validated['slug'] = Product::uniqueSlug($validated['name']);

            return Product::create($validated);
        });

        return (new ProductResource($this->muatRelasi($product)))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Tampilkan detail produk. Parameter bisa berupa id (angka) atau slug,
     * jadi halaman detail di frontend cukup memanggil /api/products/rose-damour.
     */
    public function show(string $product)
    {
        $found = Product::findByIdOrSlug($product);

        if (!$found) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        return new ProductResource($found);
    }

    /**
     * Perbarui data produk pada penyimpanan persisten.
     * Menggunakan validasi terisolasi (UpdateProductRequest),
     * pengamanan mutasi (Database Transaction), dan pembersihan berkas lama saat upload baru.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validated();

        // Pengamanan mutasi data melalui Database Transaction
        $updatedProduct = DB::transaction(function () use ($request, $product, $validated) {
            if ($request->hasFile('img')) {
                // Hapus berkas lama dari storage, lalu simpan berkas baru
                $product->deleteImageFile();
                $validated['img'] = ImageStorage::store($request->file('img'));
            }

            if (isset($validated['name'])) {
                $validated['slug'] = Product::uniqueSlug($validated['name'], $product->product_id);
            }

            $product->update($validated);
            return $product;
        });

        return new ProductResource($this->muatRelasi($updatedProduct));
    }

    /**
     * Hapus data produk dari penyimpanan persisten beserta berkas fisik gambarnya.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        DB::transaction(function () use ($product) {
            // Hapus berkas gambar terkait dari disk publik, lalu hapus datanya
            $product->deleteImageFile();
            $product->delete();
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus secara persisten.',
        ], 200);
    }

    // Muat kategori + jumlah ulasan + rata-rata rating untuk respons setelah simpan/ubah
    private function muatRelasi(Product $product): Product
    {
        return $product->load('category')->loadCount('reviews')->loadAvg('reviews', 'rating');
    }
}

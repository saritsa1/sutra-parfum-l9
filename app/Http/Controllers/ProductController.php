<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Mengimplementasikan Bab III (Eloquent & Pagination) & Bab IV (Filter & Search)
     */
    public function index(Request $request)
    {
        $title = "Daftar Produk";

        // Query builder Eloquent Product dengan relasi category
        $query = Product::with('category');

        // Pencarian berdasarkan kata kunci 's' (Bab IV)
        if ($request->filled('s')) {
            $s = $request->query('s');
            $query->where('name', 'like', "%{$s}%");
        }

        // Filter berdasarkan kategori 'category_id' (Bab IV)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        // Pagination 5 data per halaman (Bab IV)
        $products = $query->paginate(5);

        // Mengambil seluruh kategori untuk filter dropdown
        $categories = Category::all();

        return view('admin.daftarproduk', compact('title', 'products', 'categories'));
    }

    /**
     * Tampilkan data berdasarkan relasi hasMany pada Category (Bab IV Langkah 3-4)
     */
    public function category($id)
    {
        $category = Category::where('category_id', $id)->firstOrFail();
        $title = 'Kategori ' . $category->category;

        // Memanfaatkan relasi hasMany products() dari model Category
        $products = $category->products;
        $categories = Category::all();

        return view('admin.daftarproduk', compact('title', 'products', 'categories', 'category'));
    }

    /**
     * Show the form for creating a new resource (Bab III).
     */
    public function create()
    {
        $title = "Input Produk";
        $categories = Category::all();

        return view('admin.inputproduk', compact('title', 'categories'));
    }

    /**
     * Store a newly created resource in storage (Bab III).
     * Validasi terisolasi (StoreProductRequest), DB Transaction, dan upload file.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated) {
            // Upload file gambar ke storage/app/public/products (nama file dibuat unik oleh Laravel)
            if ($request->hasFile('img')) {
                $validated['img'] = $request->file('img')->store('products', 'public');
            }

            $validated['slug'] = Product::uniqueSlug($validated['name']);

            Product::create($validated);
        });

        return redirect()->route('product.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $title = "Detail Produk " . $product->name;

        return view('frontpage.detail', ['title' => $title, 'p' => $product]);
    }

    /**
     * Show the form for editing the specified resource (Bab III).
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $title = "Edit Data " . $product->name;
        $categories = Category::all();

        return view('admin.inputproduk', compact('title', 'categories', 'product'));
    }

    /**
     * Update the specified resource in storage (Bab III).
     * Validasi terisolasi (UpdateProductRequest), DB Transaction, dan hapus gambar lama.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($request, $product, $validated) {
            if ($request->hasFile('img')) {
                // Hapus foto lama dari storage, lalu simpan foto baru
                $product->deleteImageFile();
                $validated['img'] = $request->file('img')->store('products', 'public');
            }

            $validated['slug'] = Product::uniqueSlug($validated['name'], $product->product_id);

            $product->update($validated);
        });

        return redirect()->route('product.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage (Bab III).
     * Data dihapus beserta berkas gambarnya.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        DB::transaction(function () use ($product) {
            $product->deleteImageFile();
            $product->delete();
        });

        return redirect()->route('product.index')->with('success', 'Produk berhasil dihapus.');
    }
}
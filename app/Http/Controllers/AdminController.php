<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Katalog;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'title' => 'Dashboard',
            // hanya id dan nama yang dibutuhkan untuk menampilkan nama produk pada ulasan
            'products' => Product::select('product_id', 'name')->get()->map(fn ($p) => ['id' => $p->product_id, 'name' => $p->name])->values(),
            'statuses' => Katalog::statusPesanan(),
        ]);
    }

    public function about()
    {
        return view('admin.about', ['title' => 'About']);
    }
}
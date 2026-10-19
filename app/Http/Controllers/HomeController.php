<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\Katalog;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Dashboard toko: daftar produk dengan pencarian (?q=) dan filter kategori (?cat=)
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $cat = (string) $request->query('cat', 'Semua');

        $query = Product::with('category');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('notes', 'like', "%{$q}%")
                    ->orWhereHas('category', function ($c) use ($q) {
                        $c->where('category', 'like', "%{$q}%");
                    });
            });
        }

        if ($cat !== 'Semua') {
            $query->whereHas('category', function ($c) use ($cat) {
                $c->where('category', $cat);
            });
        }

        $products = $query->get();

        // Fallback jika database belum ada data
        if ($products->isEmpty() && $q === '' && $cat === 'Semua') {
            $products = Katalog::produk();
        }

        return view('frontpage.dashboard', [
            'title' => 'Dashboard',
            'q' => $q,
            'cat' => $cat,
            'kategori' => Katalog::kategori(),
            'products' => $products,
        ]);
    }

    // Detail produk berdasarkan slug
    public function detail($slug)
    {
        $p = Product::with('category')->where('slug', $slug)->first();
        if (!$p) {
            $p = Katalog::produk()->firstWhere('slug', $slug);
        }
        abort_if($p === null, 404);

        return view('frontpage.detail', ['title' => $p->name, 'p' => $p]);
    }

    public function cart()
    {
        return view('frontpage.cart', ['title' => 'Keranjang']);
    }

    public function checkout()
    {
        return view('frontpage.checkout', [
            'title' => 'Checkout',
            'shipping' => Katalog::pengiriman(),
            'groups' => Katalog::pembayaran(),
        ]);
    }

    public function order($id)
    {
        return view('frontpage.order', ['title' => 'Pesanan', 'id' => $id]);
    }
}
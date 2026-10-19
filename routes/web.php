<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ===== Halaman toko (tanpa login) =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/koleksi', [HomeController::class, 'index'])->name('produk');
Route::get('/tentang', [AdminController::class, 'about'])->name('tentang');
Route::get('/kontak', function () {
    return redirect()->route('home');
})->name('kontak');
Route::get('/produk/{slug}', [HomeController::class, 'detail'])->name('produk.detail');
Route::get('/cart', [HomeController::class, 'cart'])->name('cart');
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
Route::get('/order/{id}', [HomeController::class, 'order'])->whereNumber('id')->name('order');

// Setelah login, Breeze mengarahkan ke /dashboard; kita teruskan ke dashboard admin
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ===== Halaman admin (wajib login) =====
// Menggunakan middleware group auth sanctum untuk autentikasi
// (autentikasi bekerja pada seluruh class dalam group)
Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/about', [AdminController::class, 'about'])->name('admin.about');
    Route::get('product/category/{id}', [ProductController::class, 'category'])->name('product.category');
    Route::resource('product', ProductController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
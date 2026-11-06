<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefix otomatis: /api)
|--------------------------------------------------------------------------
| Tiga lapis akses:
|   1. Publik        : dipakai halaman toko (katalog, checkout, ulasan)
|   2. Login         : butuh Bearer token Sanctum
|   3. Login + admin : token + user.is_admin = true (halaman admin)
*/

// ===== Publik (tanpa login) =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/categories', [CatalogController::class, 'categories']);
Route::get('/checkout-options', [CatalogController::class, 'checkoutOptions']);
Route::get('/products', [ProductController::class, 'index']);
// {product} bisa berupa id (angka) atau slug, contoh: /api/products/rose-damour
Route::get('/products/{product}', [ProductController::class, 'show']);

// Ulasan produk
Route::get('/products/{product}/reviews', [ReviewController::class, 'index']);
Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->middleware('throttle:20,1');

// Pesanan (checkout dilakukan pembeli tanpa akun)
Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:20,1');
Route::get('/orders/{orderNo}', [OrderController::class, 'show']);
Route::post('/orders/{orderNo}/pay', [OrderController::class, 'pay']);

// ===== Wajib login (Bearer token) =====
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ===== Khusus admin =====
    Route::middleware('admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::patch('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        Route::get('/admin/stats', [DashboardController::class, 'stats']);
        Route::get('/admin/orders', [OrderController::class, 'index']);
        Route::patch('/admin/orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    });
});

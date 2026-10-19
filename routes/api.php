<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth routes (Modul 3: Backend)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Tambahkan Product routes with sanctum auth middleware (Modul 3: Backend)
Route::apiResource('products', ProductController::class)->middleware('auth:sanctum');

// Sanctum Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

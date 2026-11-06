<?php

use Illuminate\Support\Facades\Route;

// Backend hanya API. Route ini sekadar untuk mengecek server sudah jalan.
Route::get('/', function () {
    return response()->json([
        'app'     => 'Sutra Parfum API',
        'status'  => 'ok',
        'api_url' => url('/api'),
    ]);
});

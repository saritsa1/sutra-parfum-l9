<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    // Backend ini murni API, tidak ada halaman login yang bisa dituju.
    // Kalau token tidak valid, balas 401 JSON saja (bukan redirect).
    protected function redirectTo($request)
    {
        return null;
    }
}

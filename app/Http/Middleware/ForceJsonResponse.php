<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Semua request ke /api/* dianggap meminta JSON, walaupun client
     * lupa mengirim header "Accept: application/json".
     * Hasilnya: error validasi menjadi 422 dan tanpa token menjadi 401 (bukan redirect HTML).
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/*')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}

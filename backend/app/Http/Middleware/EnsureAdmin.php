<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    /**
     * Hanya user dengan is_admin = true yang boleh lewat.
     * Dipasang setelah auth:sanctum, jadi user sudah pasti ada (kalau tidak, 401 dari auth).
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()?->is_admin) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akses ditolak. Hanya admin yang boleh melakukan ini.',
            ], 403);
        }

        return $next($request);
    }
}

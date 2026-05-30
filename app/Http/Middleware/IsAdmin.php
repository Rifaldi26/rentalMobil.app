<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan user yang mengakses adalah Admin (pemilik usaha / operator).
 * Middleware ini menggantikan IsPartner setelah refactor domain.
 */
class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk administrator.');
        }

        return $next($request);
    }
}

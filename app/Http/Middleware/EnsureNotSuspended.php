<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya jalankan pengecekan jika user sudah login
        if (auth()->check() && auth()->user()->isSuspended()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Akun Anda telah disuspend. Hubungi admin untuk informasi lebih lanjut.');
        }

        return $next($request);
    }
}

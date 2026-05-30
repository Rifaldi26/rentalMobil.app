<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Jika ada intended URL dari query string ?redirect=... — gunakan itu
        if ($request->query('redirect')) {
            $redirect = urldecode($request->query('redirect'));
            // Pastikan URL internal (keamanan — jangan redirect ke domain eksternal)
            if (str_starts_with($redirect, url('/'))) {
                return redirect($redirect);
            }
        }

        // Jika ada intended URL dari sesi (disimpan oleh middleware 'auth') — gunakan itu
        $intended = session()->pull('url.intended');
        if ($intended && str_starts_with($intended, url('/'))) {
            return redirect($intended);
        }

        // Default: redirect ke dashboard sesuai role
        return match (true) {
            $user->isAdmin()   => redirect()->route('admin.dashboard'),
            default            => redirect()->route('customer.bookings.index'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Setelah logout kembali ke homepage, bukan ke login
        return redirect()->route('home');
    }
}
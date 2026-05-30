<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        Password::sendResetLink($request->only('email'));

        // Selalu tampilkan pesan sukses (keamanan: jangan beri tahu apakah email terdaftar)
        return back()->with('status', 'Jika email Anda terdaftar, link reset password telah dikirimkan. Cek folder inbox/spam.');
    }
}

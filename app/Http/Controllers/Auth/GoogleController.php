<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Redirect ke halaman login Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback setelah login Google berhasil
    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'              => $googleUser->getName(),
                'google_id'         => $googleUser->getId(),
                'password'          => bcrypt(str()->random(24)),
                'role'              => 'pelanggan',
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user);

        return redirect()->intended(
            auth()->user()->role === 'admin'
                ? route('admin.dashboard')
                : route('dashboard')
        );
    }
}
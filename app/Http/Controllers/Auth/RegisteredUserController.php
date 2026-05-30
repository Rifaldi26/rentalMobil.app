<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users'],
            'no_hp'        => ['required', 'string', 'max:20'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'role'         => ['nullable', 'in:customer,partner'],
            // Partner-only fields
            'company_name' => ['nullable', 'string', 'max:150'],
        ]);

        $role = $request->role === 'partner' ? UserRole::Partner : UserRole::Customer;

        $user = DB::transaction(function () use ($request, $role) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'no_hp'    => $request->no_hp,
                'password' => Hash::make($request->password),
                'role'     => $role,
            ]);

            // Jika mendaftar sebagai mitra, buat record Partner (unverified)
            if ($role === UserRole::Partner) {
                Partner::create([
                    'user_id'      => $user->id,
                    'company_name' => $request->company_name,
                    'is_verified'  => false,
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return $role === UserRole::Partner
            ? redirect()->route('partner.dashboard')
                        ->with('success', 'Akun mitra berhasil dibuat! Menunggu verifikasi admin.')
            : redirect()->route('dashboard');
    }
}

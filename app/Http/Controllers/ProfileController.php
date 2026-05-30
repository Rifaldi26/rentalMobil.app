<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'no_hp'        => ['nullable', 'string', 'max:20'],
            'avatar'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password'     => ['nullable', 'min:8', 'confirmed'],
        ]);

        $data = $request->only(['name', 'email', 'no_hp']);

        // Update avatar jika ada
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Reset email verification jika email berubah
        if ($user->email !== $request->email) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = auth()->user();
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Akun berhasil dihapus.');
    }
}

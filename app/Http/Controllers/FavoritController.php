<?php

namespace App\Http\Controllers;

use App\Models\Favorit;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FavoritController;

class FavoritController extends Controller
{
    /**
     * Halaman daftar favorit milik pelanggan yang login.
     */
    public function index()
    {
        $favorits = Favorit::with('mobil')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('users.favorit', compact('favorits'));
    }

    /**
     * Toggle tambah / hapus favorit.
     * Dipanggil via fetch (AJAX) — mengembalikan JSON.
     */
    public function toggle(Mobil $mobil)
    {
        $userId = Auth::id();

        $existing = Favorit::where('user_id', $userId)
            ->where('mobil_id', $mobil->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isFavorit = false;
        } else {
            Favorit::create([
                'user_id'  => $userId,
                'mobil_id' => $mobil->id,
            ]);
            $isFavorit = true;
        }

        return response()->json([
            'favorit' => $isFavorit,
            'message' => $isFavorit
                ? 'Ditambahkan ke favorit'
                : 'Dihapus dari favorit',
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Favorit;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritController extends Controller
{
    /**
     * Toggle favorit: tambah jika belum ada, hapus jika sudah ada.
     * Mengembalikan JSON agar bisa dipakai oleh fetch() di view.
     */
    public function toggle(Mobil $mobil)
    {
        $userId = Auth::id();

        $existing = Favorit::where('user_id', $userId)
                            ->where('mobil_id', $mobil->id)
                            ->first();

        if ($existing) {
            $existing->delete();
            $isFav = false;
        } else {
            Favorit::create([
                'user_id'  => $userId,
                'mobil_id' => $mobil->id,
            ]);
            $isFav = true;
        }

        // Jika request via fetch (AJAX), kembalikan JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'favorited' => $isFav,
                'message'   => $isFav
                    ? 'Ditambahkan ke favorit'
                    : 'Dihapus dari favorit',
            ]);
        }

        // Fallback redirect (form POST biasa)
        return back()->with(
            'success',
            $isFav ? 'Ditambahkan ke favorit!' : 'Dihapus dari favorit!'
        );
    }

    /**
     * Halaman daftar favorit pelanggan.
     */
    public function index()
    {
        $favorits = Favorit::where('user_id', Auth::id())
                           ->with('mobil')
                           ->latest()
                           ->get();

        return view('users.favorit', compact('favorits'));
    }
}
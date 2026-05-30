<?php

namespace App\Http\Controllers;

use App\Events\PesanTerkirim;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Halaman chat admin — daftar semua pelanggan.
     */
    public function adminIndex()
    {
        $pelangganDenganPesan = User::where('role', 'pelanggan')
            ->latest()
            ->get();

        return view('admin.chat', compact('pelangganDenganPesan'));
    }

    /**
     * Halaman chat user — hanya dengan admin.
     */
    public function userIndex()
    {
        $admin = User::where('role', 'admin')->first();

        return view('users.chat', compact('admin'));
    }

    /**
     * Ambil riwayat percakapan antara user yang login dengan lawan bicara.
     * GET /chat/{lawan}/pesan
     */
    public function riwayat(User $lawan)
    {
        $pesans = Pesan::percakapan(Auth::id(), $lawan->id);

        // Tandai pesan masuk sebagai sudah dibaca
        Pesan::where('pengirim_id', $lawan->id)
            ->where('penerima_id', Auth::id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json($pesans->map(fn($p) => [
            'id'          => $p->id,
            'pengirim_id' => $p->pengirim_id,
            'isi'         => $p->isi,
            'waktu'       => $p->created_at->format('H.i'),
        ]));
    }

    /**
     * Kirim pesan baru.
     * POST /chat/{lawan}/kirim
     */
    public function kirim(Request $request, User $lawan)
    {
        $request->validate(['isi' => 'required|string|max:2000']);

        $pesan = Pesan::create([
            'pengirim_id' => Auth::id(),
            'penerima_id' => $lawan->id,
            'isi'         => $request->isi,
        ]);

        // Broadcast real-time ke channel privat
        broadcast(new PesanTerkirim($pesan));

        return response()->json([
            'id'          => $pesan->id,
            'pengirim_id' => $pesan->pengirim_id,
            'isi'         => $pesan->isi,
            'waktu'       => $pesan->created_at->format('H.i'),
        ], 201);
    }

    /**
     * Jumlah pesan belum dibaca (untuk badge notifikasi).
     * GET /chat/unread
     */
    public function unread()
    {
        $count = Pesan::where('penerima_id', Auth::id())
            ->where('dibaca', false)
            ->count();

        return response()->json(['unread' => $count]);
    }
}

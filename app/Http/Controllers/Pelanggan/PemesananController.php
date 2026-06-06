<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    public function index()
    {
        return view('users.pemesanan.index');
    }

    public function create(Request $request)
    {
        $mobil = Mobil::findOrFail($request->mobil_id);

        if ($mobil->status !== 'tersedia') {
            return redirect()->route('dashboard')
                ->with('error', 'Maaf, mobil ini sedang tidak tersedia.');
        }

        return view('users.pemesanan.create', compact('mobil'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mobil_id'        => 'required|exists:mobils,id',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'catatan'         => 'nullable|string|max:500',
        ]);

        $mobil   = Mobil::findOrFail($request->mobil_id);
        $mulai   = \Carbon\Carbon::parse($request->tanggal_mulai);
        $selesai = \Carbon\Carbon::parse($request->tanggal_selesai);

        $konflik = Pemesanan::where('mobil_id', $mobil->id)
            ->whereIn('status', ['pending', 'dikonfirmasi'])
            ->where(function ($q) use ($mulai, $selesai) {
                $q->whereBetween('tanggal_mulai', [$mulai, $selesai])
                  ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])
                  ->orWhere(function ($q2) use ($mulai, $selesai) {
                      $q2->where('tanggal_mulai', '<=', $mulai)
                         ->where('tanggal_selesai', '>=', $selesai);
                  });
            })->exists();

        if ($konflik) {
            return back()->withInput()
                ->withErrors(['tanggal_mulai' => 'Mobil sudah dipesan pada tanggal tersebut.']);
        }

        $durasi     = $mulai->diffInDays($selesai);
        $totalHarga = $durasi * $mobil->harga_per_hari;

        Pemesanan::create([
            'user_id'         => Auth::id(),
            'mobil_id'        => $mobil->id,
            'tanggal_mulai'   => $mulai,
            'tanggal_selesai' => $selesai,
            'total_harga'     => $totalHarga,
            'catatan'         => $request->catatan,
            'status'          => 'pending',
        ]);

        Notifikasi::kirim(
            Auth::id(),
            'Pemesanan Diterima',
            "Pemesanan {$mobil->nama} ({$mulai->format('d M')} – {$selesai->format('d M Y')}) sedang menunggu konfirmasi admin.",
            'info',
            route('user.pemesanan.index')
        );

        return redirect()->route('dashboard')
            ->with('success', 'Pemesanan berhasil dibuat! Menunggu konfirmasi admin.');
    }

    public function cancel(Pemesanan $pemesanan)
    {
        if ($pemesanan->user_id !== Auth::id()) {
            abort(403);
        }

        if ($pemesanan->status !== 'pending') {
            return back()->with('error', 'Pemesanan yang sudah dikonfirmasi tidak dapat dibatalkan.');
        }

        $pemesanan->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Pemesanan berhasil dibatalkan.');
    }
}
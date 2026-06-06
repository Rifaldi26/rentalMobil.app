<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemesanan::with(['user', 'mobil'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $query->whereYear('created_at', $tahun)
                  ->whereMonth('created_at', $bulan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('mobil', fn($m) => $m->where('nama', 'like', "%{$search}%"));
            });
        }

        $pemesanans = $query->paginate(15)->withQueryString();

        return view('admin.pemesanan.index', compact('pemesanans'));
    }

    public function show(Pemesanan $pemesanan)
    {
        $pemesanan->load(['user', 'mobil']);

        return view('admin.pemesanan.show', compact('pemesanan'));
    }

    public function konfirmasi(Pemesanan $pemesanan)
    {
        $pemesanan->update(['status' => 'dikonfirmasi']);
        $pemesanan->mobil->update(['status' => 'disewa']);

        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pemesanan Dikonfirmasi ✅',
            "Pemesanan {$pemesanan->mobil->nama} ({$pemesanan->tanggal_mulai->format('d M')} – {$pemesanan->tanggal_selesai->format('d M Y')}) telah dikonfirmasi.",
            'success',
            route('user.pemesanan.index')
        );

        return back()->with('success', "Pemesanan {$pemesanan->user->name} berhasil dikonfirmasi!");
    }

    public function tolak(Pemesanan $pemesanan)
    {
        $pemesanan->update(['status' => 'dibatalkan']);

        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pemesanan Ditolak',
            "Maaf, pemesanan {$pemesanan->mobil->nama} tidak dapat kami proses. Silakan hubungi kami via chat.",
            'warning',
            route('user.pemesanan.index')
        );

        return back()->with('success', "Pemesanan {$pemesanan->user->name} ditolak.");
    }

    public function selesai(Pemesanan $pemesanan)
    {
        $pemesanan->update(['status' => 'selesai']);
        $pemesanan->mobil->update(['status' => 'tersedia']);

        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pemesanan Selesai 🎉',
            "Terima kasih telah menyewa {$pemesanan->mobil->nama}. Sampai jumpa lagi!",
            'success',
            route('user.pemesanan.index')
        );

        return back()->with('success', "Pemesanan {$pemesanan->user->name} ditandai selesai!");
    }
}
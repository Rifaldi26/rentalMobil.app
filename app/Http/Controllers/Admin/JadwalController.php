<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use App\Models\Pemesanan;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $mobils = Mobil::all();

        $jadwalMobil = $mobils->map(function (Mobil $mobil) use ($bulan, $tahun) {
            $pemesananAktif = $mobil->pemesanans()
                ->where('status', 'dikonfirmasi')
                ->with('user')
                ->orderBy('tanggal_selesai')
                ->first();

            return [
                'mobil'            => $mobil,
                'total_pemesanan'  => $mobil->pemesanans()
                    ->whereMonth('tanggal_mulai', $bulan)
                    ->whereYear('tanggal_mulai', $tahun)
                    ->count(),
                'pemesanan_aktif'  => $pemesananAktif,
                'bebas_berikutnya' => $pemesananAktif
                    ? $pemesananAktif->tanggal_selesai->addDay()
                    : null,
            ];
        });

        return view('admin.jadwal.index', compact('jadwalMobil', 'bulan', 'tahun'))
            ->with([
                'totalMobil'        => $mobils->count(),
                'tersediaHariIni'   => $mobils->where('status', 'tersedia')->count(),
                'sedangDisewa'      => $mobils->where('status', 'disewa')->count(),
                'pemesananBulanIni' => Pemesanan::whereMonth('tanggal_mulai', $bulan)
                                        ->whereYear('tanggal_mulai', $tahun)->count(),
            ]);
    }
}
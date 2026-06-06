<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMobil        = Mobil::count();
        $mobilTersedia     = Mobil::where('status', 'tersedia')->count();
        $mobilDisewa       = Mobil::where('status', 'disewa')->count();
        $totalPemesanan    = Pemesanan::count();
        $pemesananPending  = Pemesanan::where('status', 'pending')->count();
        $pemesananBerjalan = Pemesanan::where('status', 'dikonfirmasi')->count();
        $pendapatanBulanIni = Pemesanan::where('status', 'selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_harga');
        $pendapatanTotal   = Pemesanan::where('status', 'selesai')->sum('total_harga');
        $totalPelanggan    = User::where('role', 'pelanggan')->count();
        $pemesananMenunggu = Pemesanan::with(['user', 'mobil'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
        $sedangBerjalan    = Pemesanan::with(['user', 'mobil'])
            ->where('status', 'dikonfirmasi')
            ->latest()
            ->take(3)
            ->get();

        return view('admin.dashboard', compact(
            'totalMobil', 'mobilTersedia', 'mobilDisewa',
            'totalPemesanan', 'pemesananPending', 'pemesananBerjalan',
            'pendapatanBulanIni', 'pendapatanTotal', 'totalPelanggan',
            'pemesananMenunggu', 'sedangBerjalan'
        ));
    }
}
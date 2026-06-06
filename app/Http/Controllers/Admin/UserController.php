<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'pelanggan')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount('pemesanans')->paginate(20)->withQueryString();

        return view('admin.user.index', compact('users'));
    }

    public function show(User $user)
    {
        $pemesanans       = $user->pemesanans()->with('mobil')->latest()->get();
        $totalPemesanan   = $pemesanans->count();
        $totalPengeluaran = $pemesanans->where('status', 'selesai')->sum('total_harga');
        $totalSelesai     = $pemesanans->where('status', 'selesai')->count();
        $totalBatal       = $pemesanans->where('status', 'dibatalkan')->count();

        return view('admin.user.show', compact(
            'user', 'pemesanans', 'totalPemesanan',
            'totalPengeluaran', 'totalSelesai', 'totalBatal'
        ));
    }
}
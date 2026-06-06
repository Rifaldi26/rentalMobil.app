<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $pelangganDenganPesan = User::where('role', 'pelanggan')
            ->whereHas('pesanTerkirim', function ($q) {
                $q->where('penerima_id', Auth::id());
            })
            ->withCount(['pesanTerkirim as unread_count' => function ($q) {
                $q->where('penerima_id', Auth::id())->where('dibaca', false);
            }])
            ->get();

        return view('admin.chat', compact('pelangganDenganPesan'));
    }
    // Relasi: satu user punya banyak pemesanan
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    // Relasi: pesan yang dikirim user ini
    public function pesanTerkirim()
    {
        return $this->hasMany(Pesan::class, 'pengirim_id');
    }

    // Relasi: pesan yang diterima user ini
    public function pesanDiterima()
    {
        return $this->hasMany(Pesan::class, 'penerima_id');
    }
}
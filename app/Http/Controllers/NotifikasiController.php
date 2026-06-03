<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Halaman daftar notifikasi (User)
     */
    public function userIndex()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        // Tandai semua sebagai dibaca saat halaman dibuka
        Notifikasi::where('user_id', Auth::id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return view('users.notifikasi.index', compact('notifikasis'));
    }

    /**
     * Halaman daftar notifikasi (Admin)
     */
    public function adminIndex()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        Notifikasi::where('user_id', Auth::id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return view('admin.notifikasi.index', compact('notifikasis'));
    }

    /**
     * Jumlah notifikasi belum dibaca (untuk badge — JSON)
     */
    public function unread()
    {
        $count = Notifikasi::where('user_id', Auth::id())
            ->belumDibaca()
            ->count();

        return response()->json(['unread' => $count]);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca & redirect ke link-nya
     */
    public function baca(Notifikasi $notifikasi)
    {
        abort_if($notifikasi->user_id !== Auth::id(), 403);

        $notifikasi->update(['dibaca' => true]);

        return $notifikasi->link
            ? redirect($notifikasi->link)
            : back();
    }

    /**
     * Hapus semua notifikasi milik user yang login
     */
    public function hapusSemua()
    {
        Notifikasi::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Semua notifikasi dihapus.');
    }
}

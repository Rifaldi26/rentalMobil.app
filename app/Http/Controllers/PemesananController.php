<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    /**
     * Form buat pemesanan baru
     */
    // Menampilkan pemesanan milik pelanggan yang sedang login
    public function userIndex()
    {
        return view('users.pemesanan.index');
    }
    
    /**
     * Form buat pemesanan baru
     */
    public function create(Request $request)
    {
        $mobil = Mobil::findOrFail($request->mobil_id);

        // Cek apakah mobil masih tersedia
        if ($mobil->status !== 'tersedia') {
            return redirect()->route('dashboard')
                ->with('error', 'Maaf, mobil ini sedang tidak tersedia.');
        }

        return view('users.pemesanan.create', compact('mobil'));
    }

    /**
     * Simpan pemesanan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'mobil_id'       => 'required|exists:mobils,id',
            'tanggal_mulai'  => 'required|date|after_or_equal:today',
            'tanggal_selesai'=> 'required|date|after:tanggal_mulai',
            'catatan'        => 'nullable|string|max:500',
        ]);

        $mobil   = Mobil::findOrFail($request->mobil_id);
        $mulai   = \Carbon\Carbon::parse($request->tanggal_mulai);
        $selesai = \Carbon\Carbon::parse($request->tanggal_selesai);
        $durasi  = $mulai->diffInDays($selesai);

        // Cek konflik
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
                ->withErrors(['tanggal_mulai' => 'Mobil sudah dipesan pada tanggal tersebut. Pilih tanggal lain.']);
        }

        $totalHarga = $durasi * $mobil->harga_per_hari;

        Pemesanan::create([
            'user_id'        => Auth::id(),
            'mobil_id'       => $mobil->id,
            'tanggal_mulai'  => $mulai,
            'tanggal_selesai'=> $selesai,
            'total_harga'    => $totalHarga,
            'catatan'        => $request->catatan,
            'status'         => 'pending',
        ]);

        // Notifikasi ke user: pesanan berhasil dibuat
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

    /**
     * Batalkan pemesanan oleh pelanggan
     */
    public function cancel(Pemesanan $pemesanan)
    {
        // Pastikan hanya pemilik pemesanan yang bisa batalkan
        if ($pemesanan->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya boleh batalkan jika masih pending
        if ($pemesanan->status !== 'pending') {
            return back()->with('error', 'Pemesanan yang sudah dikonfirmasi tidak dapat dibatalkan.');
        }

        $pemesanan->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Pemesanan berhasil dibatalkan.');
    }

    // ═══ ADMIN METHODS ═══════════════════════════════════════

    /**
     * Daftar semua pemesanan (Admin) — dengan filter & search
     */
    public function index(Request $request)
    {
        $query = Pemesanan::with(['user', 'mobil'])->latest();

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $query->whereYear('created_at', $tahun)
                  ->whereMonth('created_at', $bulan);
        }

        // Search nama pelanggan atau nama mobil
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

    /**
     * Konfirmasi pemesanan oleh admin
     */
    public function konfirmasi(Pemesanan $pemesanan)
    {
        $pemesanan->update(['status' => 'dikonfirmasi']);
        $pemesanan->mobil->update(['status' => 'disewa']);

        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pemesanan Dikonfirmasi ✅',
            "Pemesanan {$pemesanan->mobil->nama} ({$pemesanan->tanggal_mulai->format('d M')} – {$pemesanan->tanggal_selesai->format('d M Y')}) telah dikonfirmasi. Selamat menikmati perjalanan!",
            'success',
            route('user.pemesanan.index')
        );

        return back()->with('success', "Pemesanan {$pemesanan->user->name} berhasil dikonfirmasi!");
    }

    /**
     * Tolak pemesanan oleh admin
     */
    public function tolak(Pemesanan $pemesanan)
    {
        $pemesanan->update(['status' => 'dibatalkan']);

        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pemesanan Ditolak',
            "Maaf, pemesanan {$pemesanan->mobil->nama} ({$pemesanan->tanggal_mulai->format('d M')} – {$pemesanan->tanggal_selesai->format('d M Y')}) tidak dapat kami proses. Silakan hubungi kami via chat untuk informasi lebih lanjut.",
            'warning',
            route('user.pemesanan.index')
        );

        return back()->with('success', "Pemesanan {$pemesanan->user->name} ditolak.");
    }

    /**
     * Tandai pemesanan selesai
     */
    public function selesai(Pemesanan $pemesanan)
    {
        $pemesanan->update(['status' => 'selesai']);
        $pemesanan->mobil->update(['status' => 'tersedia']);

        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pemesanan Selesai 🎉',
            "Terima kasih telah menyewa {$pemesanan->mobil->nama}. Pemesanan Anda telah selesai. Sampai jumpa lagi!",
            'success',
            route('user.pemesanan.index')
        );
        
        return back()->with('success', "Pemesanan {$pemesanan->user->name} ditandai selesai!");
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobilController extends Controller
{
    public function index(Request $request)
    {
        $query = Mobil::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mobils        = $query->paginate(10)->withQueryString();
        $totalMobil    = Mobil::count();
        $tersediaCount = Mobil::where('status', 'tersedia')->count();
        $disewaCount   = Mobil::where('status', 'disewa')->count();

        return view('admin.mobil.index', compact('mobils', 'totalMobil', 'tersediaCount', 'disewaCount'));
    }

    public function create()
    {
        return view('admin.mobil.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'merek'          => 'required|string|max:100',
            'tahun'          => 'required|integer|min:2000|max:' . date('Y'),
            'plat_nomor'     => 'required|string|max:20|unique:mobils,plat_nomor',
            'harga_per_hari' => 'required|numeric|min:50000',
            'status'         => 'required|in:tersedia,disewa',
            'deskripsi'      => 'nullable|string',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        Mobil::create($data);

        return redirect()->route('admin.mobil.index')
            ->with('success', 'Mobil berhasil ditambahkan!');
    }

    public function show(Mobil $mobil)
    {
        $totalDisewa     = $mobil->pemesanans()->where('status', 'selesai')->count();
        $totalPendapatan = $mobil->pemesanans()->where('status', 'selesai')->sum('total_harga');
        $riwayat         = $mobil->pemesanans()->with('user')->latest()->take(10)->get();

        return view('admin.mobil.show', compact('mobil', 'totalDisewa', 'totalPendapatan', 'riwayat'));
    }

    public function edit(Mobil $mobil)
    {
        return view('admin.mobil.edit', compact('mobil'));
    }

    public function update(Request $request, Mobil $mobil)
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'merek'          => 'required|string|max:100',
            'tahun'          => 'required|integer|min:2000|max:' . date('Y'),
            'plat_nomor'     => 'required|string|max:20|unique:mobils,plat_nomor,' . $mobil->id,
            'harga_per_hari' => 'required|numeric|min:50000',
            'status'         => 'required|in:tersedia,disewa',
            'deskripsi'      => 'nullable|string',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            if ($mobil->foto) {
                Storage::disk('public')->delete($mobil->foto);
            }
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        $mobil->update($data);

        return redirect()->route('admin.mobil.index')
            ->with('success', 'Data mobil berhasil diperbarui!');
    }

    public function destroy(Mobil $mobil)
    {
        $pesananAktif = $mobil->pemesanans()
            ->whereIn('status', ['pending', 'dikonfirmasi'])
            ->count();

        if ($pesananAktif > 0) {
            return redirect()->route('admin.mobil.index')
                ->with('error', "{$mobil->nama} tidak dapat dihapus karena masih memiliki {$pesananAktif} pesanan aktif.");
        }

        if ($mobil->foto) {
            Storage::disk('public')->delete($mobil->foto);
        }

        $mobil->delete();

        return redirect()->route('admin.mobil.index')
            ->with('success', "Mobil {$mobil->nama} berhasil dihapus.");
    }

    public function toggleStatus(Mobil $mobil)
    {
        $mobil->update([
            'status' => $mobil->status === 'tersedia' ? 'disewa' : 'tersedia',
        ]);

        return back()->with('success', 'Status mobil diperbarui!');
    }
}
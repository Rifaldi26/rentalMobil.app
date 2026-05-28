<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobilController extends Controller
{
    /**
     * Tampilkan semua mobil (Admin)
     */
    public function index(Request $request)
    {
        $query = Mobil::latest();
 
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
 
        $mobils = $query->paginate(10)->withQueryString();
 
        return view('admin.mobil.index', compact('mobils'));
    }

    /**
     * Form tambah mobil
     */
    public function create()
    {
        return view('admin.mobil.create');
    }

    /**
     * Simpan mobil baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'merek'         => 'required|string|max:100',
            'tahun'         => 'required|integer|min:2000|max:' . date('Y'),
            'plat_nomor'    => 'required|string|max:20|unique:mobils,plat_nomor',
            'harga_per_hari'=> 'required|numeric|min:50000',
            'status'        => 'required|in:tersedia,disewa',
            'deskripsi'     => 'nullable|string',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        Mobil::create($data);

        return redirect()->route('admin.mobil.index')
            ->with('success', 'Mobil berhasil ditambahkan!');
    }

    /**
     * Form edit mobil
     */
    public function edit(Mobil $mobil)
    {
        return view('admin.mobil.edit', compact('mobil'));
    }

    /**
     * Update data mobil
     */
    public function update(Request $request, Mobil $mobil)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'merek'         => 'required|string|max:100',
            'tahun'         => 'required|integer|min:2000|max:' . date('Y'),
            'plat_nomor'    => 'required|string|max:20|unique:mobils,plat_nomor,' . $mobil->id,
            'harga_per_hari'=> 'required|numeric|min:50000',
            'status'        => 'required|in:tersedia,disewa',
            'deskripsi'     => 'nullable|string',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($mobil->foto) {
                Storage::disk('public')->delete($mobil->foto);
            }
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        $mobil->update($data);

        return redirect()->route('admin.mobil.index')
            ->with('success', 'Data mobil berhasil diperbarui!');
    }

    /**
     * Hapus mobil
     */
    public function destroy(Mobil $mobil)
    {
        if ($mobil->foto) {
            Storage::disk('public')->delete($mobil->foto);
        }

        $mobil->delete();

        return redirect()->route('admin.mobil.index')
            ->with('success', 'Mobil berhasil dihapus!');
    }

    /**
     * Toggle status mobil (tersedia/disewa)
     */
    public function toggleStatus(Mobil $mobil)
    {
        $mobil->update([
            'status' => $mobil->status === 'tersedia' ? 'disewa' : 'tersedia'
        ]);

        return back()->with('success', 'Status mobil diperbarui!');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Mobil;
use App\Models\Penarikan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $pendapatanPerBulan = Pemesanan::selectRaw('MONTH(updated_at) as bulan, SUM(total_harga) as total')
            ->where('status', 'selesai')
            ->whereYear('updated_at', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $namaBulan   = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartLabels = [];
        $chartData   = [];

        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = $namaBulan[$i];
            $chartData[]   = (int) ($pendapatanPerBulan[$i]->total ?? 0);
        }

        $statusCount = Pemesanan::selectRaw('status, COUNT(*) as total')
            ->whereYear('created_at', $tahun)
            ->groupBy('status')
            ->pluck('total', 'status');

        $topMobil = Pemesanan::selectRaw('mobil_id, COUNT(*) as total_sewa, SUM(total_harga) as pendapatan')
            ->with('mobil:id,nama,merek')
            ->where('status', 'selesai')
            ->whereYear('updated_at', $tahun)
            ->groupBy('mobil_id')
            ->orderByDesc('total_sewa')
            ->limit(5)
            ->get();

        $ringkasan = [
            'pendapatan_total' => Pemesanan::where('status', 'selesai')->whereYear('updated_at', $tahun)->sum('total_harga'),
            'total_selesai'    => Pemesanan::where('status', 'selesai')->whereYear('created_at', $tahun)->count(),
            'total_pending'    => Pemesanan::where('status', 'pending')->whereYear('created_at', $tahun)->count(),
            'total_batal'      => Pemesanan::where('status', 'dibatalkan')->whereYear('created_at', $tahun)->count(),
        ];

        $daftarTahun = range(now()->year, now()->year - 3);

        return view('admin.laporan.index', compact(
            'chartLabels', 'chartData', 'statusCount',
            'topMobil', 'ringkasan', 'tahun', 'daftarTahun'
        ));
    }

    public function exportCsv(Request $request)
    {
        $tahun  = $request->get('tahun', now()->year);
        $status = $request->get('status', '');

        $query = Pemesanan::with(['user:id,name,email,no_hp', 'mobil:id,nama,merek,plat_nomor'])
            ->whereYear('created_at', $tahun)
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $pemesanans = $query->get();
        $filename   = 'laporan-pemesanan-' . $tahun . ($status ? '-' . $status : '') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate',
        ];

        return response()->stream(function () use ($pemesanans) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, [
                'ID', 'Pelanggan', 'Email', 'No HP',
                'Mobil', 'Merek', 'Plat Nomor',
                'Tgl Mulai', 'Tgl Selesai', 'Durasi (hari)',
                'Total Harga (Rp)', 'Status', 'Catatan', 'Tgl Dibuat',
            ]);
            foreach ($pemesanans as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->user->name        ?? '-',
                    $p->user->email       ?? '-',
                    $p->user->no_hp       ?? '-',
                    $p->mobil->nama       ?? '-',
                    $p->mobil->merek      ?? '-',
                    $p->mobil->plat_nomor ?? '-',
                    $p->tanggal_mulai->format('d/m/Y'),
                    $p->tanggal_selesai->format('d/m/Y'),
                    $p->tanggal_mulai->diffInDays($p->tanggal_selesai),
                    number_format($p->total_harga, 0, ',', '.'),
                    $p->status,
                    $p->catatan ?? '',
                    $p->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function chartData(Request $request)
    {
        $tahun     = $request->get('tahun', now()->year);
        $namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $data = Pemesanan::selectRaw('MONTH(updated_at) as bulan, SUM(total_harga) as total')
            ->where('status', 'selesai')
            ->whereYear('updated_at', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[] = ['bulan' => $namaBulan[$i], 'total' => (int) ($data[$i]->total ?? 0)];
        }

        return response()->json($result);
    }

    public function penarikan()
    {
        $totalPendapatan   = Pemesanan::where('status', 'selesai')->sum('total_harga');
        $totalDitarik      = Penarikan::where('status', 'selesai')->sum('jumlah');
        $saldoTersedia     = $totalPendapatan - $totalDitarik;
        $penarikanBulanIni = Penarikan::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count();
        $riwayatPenarikan  = Penarikan::latest()->paginate(15);

        return view('admin.laporan.penarikan', compact(
            'saldoTersedia', 'totalPendapatan',
            'totalDitarik', 'penarikanBulanIni', 'riwayatPenarikan'
        ));
    }

    public function penarikanStore(Request $request)
    {
        $totalPendapatan = Pemesanan::where('status', 'selesai')->sum('total_harga');
        $totalDitarik    = Penarikan::where('status', 'selesai')->sum('jumlah');
        $saldoTersedia   = $totalPendapatan - $totalDitarik;

        $request->validate([
            'jumlah'     => "required|numeric|min:50000|max:{$saldoTersedia}",
            'rekening'   => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Penarikan::create([
            'jumlah'     => $request->jumlah,
            'rekening'   => $request->rekening,
            'keterangan' => $request->keterangan,
            'status'     => 'pending',
        ]);

        return redirect()->route('admin.laporan.penarikan')
            ->with('success', 'Pengajuan penarikan berhasil diajukan!');
    }

    public function invoicePdf(Pemesanan $pemesanan)
    {
        if (auth()->user()->role !== 'admin' && $pemesanan->user_id !== auth()->id()) {
            abort(403);
        }

        $pemesanan->load(['user', 'mobil']);
        $html = view('admin.laporan.invoice-pdf', compact('pemesanan'))->render();

        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html)->setPaper('A4', 'portrait');

        return $pdf->download('invoice-' . str_pad($pemesanan->id, 5, '0', STR_PAD_LEFT) . '.pdf');
    }
}
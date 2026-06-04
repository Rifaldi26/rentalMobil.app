<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Halaman laporan utama
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        // ── Data chart pendapatan per bulan ─────────────────────────────
        $pendapatanPerBulan = Pemesanan::selectRaw('MONTH(updated_at) as bulan, SUM(total_harga) as total')
            ->where('status', 'selesai')
            ->whereYear('updated_at', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $chartLabels = [];
        $chartData   = [];
        $namaBulan   = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = $namaBulan[$i];
            $chartData[]   = (int) ($pendapatanPerBulan[$i]->total ?? 0);
        }

        // ── Data chart pemesanan per status ─────────────────────────────
        $statusCount = Pemesanan::selectRaw('status, COUNT(*) as total')
            ->whereYear('created_at', $tahun)
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── Top 5 mobil terlaris ────────────────────────────────────────
        $topMobil = Pemesanan::selectRaw('mobil_id, COUNT(*) as total_sewa, SUM(total_harga) as pendapatan')
            ->with('mobil:id,nama,merek')
            ->where('status', 'selesai')
            ->whereYear('updated_at', $tahun)
            ->groupBy('mobil_id')
            ->orderByDesc('total_sewa')
            ->limit(5)
            ->get();

        // ── Ringkasan tahun berjalan ─────────────────────────────────────
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

    /**
     * Export CSV semua pemesanan
     */
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

        $filename = 'laporan-pemesanan-' . $tahun . ($status ? '-' . $status : '') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate',
        ];

        $callback = function () use ($pemesanans) {
            $handle = fopen('php://output', 'w');

            // BOM untuk Excel agar baca UTF-8 dengan benar
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header baris
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
                    $p->durasiHari(),
                    number_format($p->total_harga, 0, ',', '.'),
                    $p->status,
                    $p->catatan ?? '',
                    $p->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Invoice PDF untuk satu pemesanan (user & admin)
     */
    public function invoicePdf(Pemesanan $pemesanan)
    {
        // Hanya pemilik atau admin
        if (auth()->user()->role !== 'admin' && $pemesanan->user_id !== auth()->id()) {
            abort(403);
        }

        $pemesanan->load(['user', 'mobil']);

        $html = view('admin.laporan.invoice-pdf', compact('pemesanan'))->render();

        // Gunakan DomPDF (barryvdh/laravel-dompdf)
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'invoice-' . str_pad($pemesanan->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Data chart JSON (opsional: untuk AJAX refresh tanpa reload)
     */
    public function chartData(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $data = Pemesanan::selectRaw('MONTH(updated_at) as bulan, SUM(total_harga) as total')
            ->where('status', 'selesai')
            ->whereYear('updated_at', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[] = [
                'bulan' => $namaBulan[$i],
                'total' => (int) ($data[$i]->total ?? 0),
            ];
        }

        return response()->json($result);
    }
}

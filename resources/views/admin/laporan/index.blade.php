@extends('layouts.admin')
@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    .summary-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:4px; }
    .summary-card { background:#fff; border-radius:var(--radius-md); border:1px solid var(--gray-100); padding:14px; }
    .summary-card .val { font-size:18px; font-weight:800; color:var(--gray-900); }
    .summary-card .lbl { font-size:11px; color:var(--gray-500); margin-top:2px; }
    .summary-card.accent { background:var(--brand-400); border-color:var(--brand-400); }
    .summary-card.accent .val, .summary-card.accent .lbl { color:#fff; }
    .chart-card { background:#fff; border-radius:var(--radius-md); border:1px solid var(--gray-100); padding:16px; margin-bottom:12px; }
    .chart-card h3 { font-size:13px; font-weight:700; color:var(--gray-700); margin-bottom:14px; }
    .chart-wrap { position:relative; height:200px; }
    .filter-row { display:flex; gap:8px; align-items:center; margin-bottom:16px; flex-wrap:wrap; }
    .filter-row select, .filter-row a { font-size:12px; padding:7px 12px; border-radius:var(--radius-sm); border:1px solid var(--gray-300); background:#fff; color:var(--gray-700); text-decoration:none; }
    .filter-row .btn-export { background:var(--brand-400); color:#fff; border-color:var(--brand-400); font-weight:700; }
    .top-item { display:flex; align-items:center; gap:12px; background:#fff; border-radius:var(--radius-md); border:1px solid var(--gray-100); padding:12px 14px; margin-bottom:8px; }
    .top-rank { width:28px; height:28px; border-radius:50%; background:var(--brand-50); color:var(--brand-400); font-size:12px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .top-rank.gold { background:#fef3c7; color:#d97706; }
    .top-rank.silver { background:#f3f4f6; color:#6b7280; }
    .top-rank.bronze { background:#fef3c7; color:#92400e; }
    .top-info { flex:1; min-width:0; }
    .top-nama { font-size:13px; font-weight:700; color:var(--gray-900); }
    .top-sub  { font-size:11px; color:var(--gray-500); margin-top:1px; }
    .top-total { font-size:13px; font-weight:700; color:var(--brand-400); text-align:right; }
    .top-sewa  { font-size:10px; color:var(--gray-500); text-align:right; }
</style>
@endpush

@section('content')
<div class="admin-content">

    {{-- ── Filter tahun & Export ─────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.laporan.index') }}" class="filter-row">
        <select name="tahun" onchange="this.form.submit()">
            @foreach($daftarTahun as $t)
                <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        <a href="{{ route('admin.laporan.export-csv', ['tahun' => $tahun]) }}" class="btn-export">
            ⬇ Export CSV
        </a>
        <a href="{{ route('admin.laporan.export-csv', ['tahun' => $tahun, 'status' => 'selesai']) }}" class="btn-export-outline">
            CSV Selesai
        </a>
    </form>

    {{-- ── Ringkasan ─────────────────────────────────────────── --}}
    <div class="summary-grid">
        <div class="summary-card accent">
            <div class="val">Rp {{ number_format($ringkasan['pendapatan_total']/1000000, 1, ',', '.') }}jt</div>
            <div class="lbl">Pendapatan {{ $tahun }}</div>
        </div>
        <div class="summary-card">
            <div class="val">{{ $ringkasan['total_selesai'] }}</div>
            <div class="lbl">Pemesanan Selesai</div>
        </div>
        <div class="summary-card">
            <div class="val" style="color:var(--warning);">{{ $ringkasan['total_pending'] }}</div>
            <div class="lbl">Masih Pending</div>
        </div>
        <div class="summary-card">
            <div class="val" style="color:var(--danger);">{{ $ringkasan['total_batal'] }}</div>
            <div class="lbl">Dibatalkan</div>
        </div>
    </div>

    {{-- ── Chart Pendapatan Per Bulan ───────────────────────── --}}
    <p class="section-title">Pendapatan per Bulan</p>
    <div class="chart-card">
        <h3>Pendapatan {{ $tahun }} (Rp)</h3>
        <div class="chart-wrap">
            <canvas id="chartPendapatan"></canvas>
        </div>
    </div>

    {{-- ── Chart Distribusi Status ──────────────────────────── --}}
    <p class="section-title">Distribusi Status Pemesanan</p>
    <div class="chart-card">
        <h3>Status Pemesanan {{ $tahun }}</h3>
        <div class="chart-wrap" style="height:180px;">
            <canvas id="chartStatus"></canvas>
        </div>
    </div>

    {{-- ── Top 5 Mobil Terlaris ─────────────────────────────── --}}
    <p class="section-title">Top 5 Mobil Terlaris</p>
    @forelse($topMobil as $i => $item)
        @php
            $rankClass = match($i) { 0 => 'gold', 1 => 'silver', 2 => 'bronze', default => '' };
        @endphp
        <div class="top-item">
            <div class="top-rank {{ $rankClass }}">{{ $i + 1 }}</div>
            <div class="top-info">
                <div class="top-nama">{{ $item->mobil->nama ?? '-' }}</div>
                <div class="top-sub">{{ $item->mobil->merek ?? '' }} &middot; {{ $item->total_sewa }}x disewa</div>
            </div>
            <div>
                <div class="top-total">Rp {{ number_format($item->pendapatan/1000000, 1, ',', '.') }}jt</div>
                <div class="top-sewa">pendapatan</div>
            </div>
        </div>
    @empty
        <p style="font-size:13px;color:var(--gray-500);text-align:center;padding:20px;">Belum ada data</p>
    @endforelse

</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
    window.laporanChartLabels = @json($chartLabels);
    window.laporanChartData   = @json($chartData);
    window.laporanStatusData  = @json($statusCount);
</script>
@vite(['resources/js/admin/laporan.js'])
@endpush
@endsection
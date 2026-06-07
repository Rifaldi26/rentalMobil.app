@extends('layouts.admin')
@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@push('styles')
    @vite(['resources/css/admin.css'])
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
        <a href="{{ route('admin.laporan.export-csv', ['tahun' => $tahun]) }}"
           class="btn-export">
            ⬇ Export CSV
        </a>
        <a href="{{ route('admin.laporan.export-csv', ['tahun' => $tahun, 'status' => 'selesai']) }}"
           class="btn-export-outline">
            CSV Selesai
        </a>
    </form>

    {{-- ── Ringkasan ─────────────────────────────────────────── --}}
    <div class="summary-grid">
        <div class="summary-card summary-card--accent">
            <div class="summary-card__val">
                Rp {{ number_format($ringkasan['pendapatan_total'] / 1000000, 1, ',', '.') }}jt
            </div>
            <div class="summary-card__lbl">Pendapatan {{ $tahun }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-card__val">{{ $ringkasan['total_selesai'] }}</div>
            <div class="summary-card__lbl">Pemesanan Selesai</div>
        </div>

        <div class="summary-card">
            <div class="summary-card__val summary-card__val--warning">
                {{ $ringkasan['total_pending'] }}
            </div>
            <div class="summary-card__lbl">Masih Pending</div>
        </div>

        <div class="summary-card">
            <div class="summary-card__val summary-card__val--danger">
                {{ $ringkasan['total_batal'] }}
            </div>
            <div class="summary-card__lbl">Dibatalkan</div>
        </div>
    </div>

    {{-- ── Chart Pendapatan Per Bulan ───────────────────────── --}}
    <p class="section-title">Pendapatan per Bulan</p>
    <div class="chart-card">
        <h3 class="chart-card__title">Pendapatan {{ $tahun }} (Rp)</h3>
        <div class="chart-wrap">
            <canvas id="chartPendapatan"></canvas>
        </div>
    </div>

    {{-- ── Chart Distribusi Status ──────────────────────────── --}}
    <p class="section-title">Distribusi Status Pemesanan</p>
    <div class="chart-card">
        <h3 class="chart-card__title">Status Pemesanan {{ $tahun }}</h3>
        <div class="chart-wrap chart-wrap--sm">
            <canvas id="chartStatus"></canvas>
        </div>
    </div>

    {{-- ── Top 5 Mobil Terlaris ─────────────────────────────── --}}
    <p class="section-title">Top 5 Mobil Terlaris</p>
    @forelse($topMobil as $i => $item)
        @php $rankClass = match($i) { 0 => 'top-rank--gold', 1 => 'top-rank--silver', 2 => 'top-rank--bronze', default => '' }; @endphp
        <div class="top-item">
            <div class="top-rank {{ $rankClass }}">{{ $i + 1 }}</div>
            <div class="top-info">
                <div class="top-info__nama">{{ $item->mobil->nama ?? '-' }}</div>
                <div class="top-info__sub">
                    {{ $item->mobil->merek ?? '' }} &middot; {{ $item->total_sewa }}x disewa
                </div>
            </div>
            <div class="top-total">
                <div class="top-total__val">
                    Rp {{ number_format($item->pendapatan / 1000000, 1, ',', '.') }}jt
                </div>
                <div class="top-total__lbl">pendapatan</div>
            </div>
        </div>
    @empty
        <p class="text-empty">Belum ada data</p>
    @endforelse

</div>
@endsection

@push('scripts')
<script>
    window.laporanChartLabels = @json($chartLabels);
    window.laporanChartData   = @json($chartData);
    window.laporanStatusData  = @json($statusCount);
</script>
@vite(['resources/js/admin/laporan.js'])
@endpush
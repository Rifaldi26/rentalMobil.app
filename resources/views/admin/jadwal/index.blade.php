{{-- resources/views/admin/jadwal/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Jadwal & Ketersediaan')
@section('page-title', 'Jadwal & Ketersediaan')
@section('page-subtitle', 'Pantau dan kelola ketersediaan armada per tanggal')

@section('content')
<div class="admin-content">

    {{-- ── Filter bulan & tahun ─────────────────────────────── --}}
    <div class="filter-row mb-20">
        <form method="GET" action="{{ route('admin.jadwal.index') }}" style="display:flex;gap:8px;">
            <select name="bulan" class="form-select form-select--w160" onchange="this.form.submit()">
                @foreach(range(1, 12) as $b)
                    <option value="{{ $b }}" {{ $b == $bulan ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="tahun" class="form-select form-select--w110" onchange="this.form.submit()">
                @foreach(range(now()->year - 1, now()->year + 1) as $t)
                    <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ── Stats ─────────────────────────────────────────────── --}}
    <div class="dashboard-stats mb-24">

        <div class="stat-card stat-card--white">
            <div class="stat-card__label">Total Armada</div>
            <div class="stat-card__value stat-card__value--dark">{{ $totalMobil }}</div>
            <div class="stat-card__sub">unit terdaftar</div>
        </div>

        <div class="stat-card stat-card--white">
            <div class="stat-card__label">Tersedia Hari Ini</div>
            <div class="stat-card__value stat-card__value--dark stat-value--success">{{ $tersediaHariIni }}</div>
            <div class="stat-card__sub">unit siap disewa</div>
        </div>

        <div class="stat-card stat-card--white">
            <div class="stat-card__label">Sedang Disewa</div>
            <div class="stat-card__value stat-card__value--dark stat-value--danger">{{ $sedangDisewa }}</div>
            <div class="stat-card__sub">unit aktif bulan ini</div>
        </div>

        <div class="stat-card stat-card--primary">
            <div class="stat-card__label">Pemesanan Bulan Ini</div>
            <div class="stat-card__value">{{ $pemesananBulanIni }}</div>
            <div class="stat-card__sub">total booking</div>
        </div>

    </div>

    {{-- ── Tabel ketersediaan ───────────────────────────────── --}}
    <div class="card">
        <div class="card-body card-body--table">
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-kendaraan">Kendaraan</th>
                        <th>Status</th>
                        <th>Pemesanan Bulan Ini</th>
                        <th>Pemesanan Aktif</th>
                        <th>Tanggal Bebas Berikutnya</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwalMobil as $item)
                        <tr>
                            <td>
                                <div class="td-mobil-nama">{{ $item['mobil']->nama }}</div>
                                <div class="td-mobil-meta">
                                    {{ $item['mobil']->merek }} ·
                                    <span class="badge-plat">{{ $item['mobil']->plat_nomor }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $item['mobil']->status }}">
                                    {{ $item['mobil']->status === 'tersedia' ? 'Tersedia' : 'Disewa' }}
                                </span>
                            </td>
                            <td><strong>{{ $item['total_pemesanan'] }}</strong> pemesanan</td>
                            <td>
                                @if ($item['pemesanan_aktif'])
                                    <div class="td-aktif-nama">{{ $item['pemesanan_aktif']->user->name }}</div>
                                    <div class="td-aktif-meta">s/d {{ $item['pemesanan_aktif']->tanggal_selesai->translatedFormat('d M Y') }}</div>
                                @else
                                    <span class="td-none">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="td-bebas">
                                    {{ $item['bebas_berikutnya']
                                        ? $item['bebas_berikutnya']->translatedFormat('d M Y')
                                        : 'Sekarang' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.mobil.show', $item['mobil']) }}"
                                   class="btn btn-view btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="td-empty">Belum ada data armada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

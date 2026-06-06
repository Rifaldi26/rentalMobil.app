{{-- resources/views/admin/jadwal/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Jadwal & Ketersediaan')
@section('page-title', 'Jadwal & Ketersediaan')
@section('page-subtitle', 'Pantau dan kelola ketersediaan armada per tanggal')

@section('content')
<div class="admin-content">

    {{-- Filter bulan --}}
    <div class="laporan-period-select">
        <form method="GET" action="{{ route('admin.jadwal.index') }}" style="display:flex;gap:10px;align-items:center;">
            <select name="bulan" class="form-select" style="width:160px;" onchange="this.form.submit()">
                @foreach(range(1, 12) as $b)
                    <option value="{{ $b }}" {{ $b == $bulan ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="tahun" class="form-select" style="width:110px;" onchange="this.form.submit()">
                @foreach(range(now()->year - 1, now()->year + 1) as $t)
                    <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Stats --}}
    <div class="dashboard-stats" style="margin-bottom:28px;">
        <div class="stat-card">
            <div class="stat-label">Total Armada</div>
            <div class="stat-value">{{ $totalMobil }}</div>
            <div class="stat-sub">unit terdaftar</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tersedia Hari Ini</div>
            <div class="stat-value" style="color:var(--success);">{{ $tersediaHariIni }}</div>
            <div class="stat-sub">unit siap disewa</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Sedang Disewa</div>
            <div class="stat-value" style="color:var(--danger);">{{ $sedangDisewa }}</div>
            <div class="stat-sub">unit aktif bulan ini</div>
        </div>
        <div class="stat-card highlight">
            <div class="stat-label">Pemesanan Bulan Ini</div>
            <div class="stat-value">{{ $pemesananBulanIni }}</div>
            <div class="stat-sub">total booking</div>
        </div>
    </div>

    {{-- Tabel ketersediaan per mobil --}}
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="min-width:180px;">Kendaraan</th>
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
                                <div style="font-weight:700;font-size:14px;color:var(--gray-900);">
                                    {{ $item['mobil']->nama }}
                                </div>
                                <div style="font-size:12px;color:var(--gray-500);margin-top:1px;">
                                    {{ $item['mobil']->merek }} ·
                                    <span class="badge-plat">{{ $item['mobil']->plat_nomor }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $item['mobil']->status }}">
                                    {{ $item['mobil']->status === 'tersedia' ? 'Tersedia' : 'Disewa' }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $item['total_pemesanan'] }}</strong> pemesanan
                            </td>
                            <td>
                                @if ($item['pemesanan_aktif'])
                                    <div style="font-size:13px;font-weight:600;color:var(--brand-400);">
                                        {{ $item['pemesanan_aktif']->user->name }}
                                    </div>
                                    <div style="font-size:12px;color:var(--gray-500);">
                                        s/d {{ $item['pemesanan_aktif']->tanggal_selesai->translatedFormat('d M Y') }}
                                    </div>
                                @else
                                    <span style="font-size:13px;color:var(--gray-400);">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($item['bebas_berikutnya'])
                                    <span style="font-size:13px;color:var(--success);font-weight:600;">
                                        {{ $item['bebas_berikutnya']->translatedFormat('d M Y') }}
                                    </span>
                                @else
                                    <span style="font-size:13px;color:var(--success);font-weight:600;">Sekarang</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.mobil.show', $item['mobil']) }}"
                                   class="btn btn-view btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px;color:var(--gray-400);">
                                Belum ada data armada
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . Auth::user()->name . '!')

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush

@section('content')
<div class="admin-content">

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <a href="{{ route('admin.pemesanan.index') }}" class="stat-card stat-card--primary">
            <div class="stat-card__label">Pendapatan Bulan Ini</div>
            <div class="stat-card__value">Rp {{ number_format($pendapatanBulanIni/1000000, 1, ',', '.') }}jt</div>
            <div class="stat-card__sub">Total: Rp {{ number_format($pendapatanTotal/1000000, 1, ',', '.') }}jt</div>
        </a>

        <a href="{{ route('admin.pemesanan.index') }}" class="stat-card stat-card--white">
            <div class="stat-card__label">Pemesanan</div>
            <div class="stat-card__value stat-card__value--dark">{{ $totalPemesanan }}</div>
            <div class="stat-card__sub">
                @if ($pemesananPending > 0)
                    <span class="stat-card__sub--warning">{{ $pemesananPending }} menunggu</span>
                @else
                    <span class="stat-card__sub--success">Semua ditangani</span>
                @endif
            </div>
        </a>

        <a href="{{ route('admin.mobil.index') }}" class="stat-card stat-card--white">
            <div class="stat-card__label">Armada</div>
            <div class="stat-card__value stat-card__value--dark">{{ $totalMobil }} unit</div>
            <div class="stat-card__sub">
                {{ $mobilTersedia }} tersedia ·
                <span class="stat-card__sub--danger">{{ $mobilDisewa }} disewa</span>
            </div>
        </a>

        <a href="{{ route('admin.user.index') }}" class="stat-card stat-card--white">
            <div class="stat-card__label">Pelanggan</div>
            <div class="stat-card__value stat-card__value--dark">{{ $totalPelanggan }}</div>
            <div class="stat-card__sub">Pengguna terdaftar</div>
        </a>
    </div>

    {{-- Konfirmasi Pemesanan --}}
    @php
        $pemesananMenunggu = \App\Models\Pemesanan::with(['user','mobil'])
            ->where('status','pending')->latest()->take(5)->get();
    @endphp
    <div class="card card--mb">
        <div class="card-body card-body--between" style="padding:16px 20px; border-bottom:1px solid var(--gray-100);">
            <span class="card-header__title">Konfirmasi Pemesanan</span>
            @if ($pemesananPending > 0)
                <span class="badge-count badge-count--warning">{{ $pemesananPending }} baru</span>
            @endif
        </div>
        <div class="card-body card-body--col">
            @forelse ($pemesananMenunggu as $p)
                @php $durasi = $p->tanggal_mulai->diffInDays($p->tanggal_selesai); @endphp
                <div class="booking-item">
                    <div class="booking-item-header">
                        <div>
                            <div class="booking-item-name">{{ $p->user->name }}</div>
                            <div class="booking-item-code">
                                {{ $p->mobil->nama }} · {{ $p->tanggal_mulai->format('d M') }} –
                                {{ $p->tanggal_selesai->format('d M') }} · {{ $durasi }} hari
                            </div>
                        </div>
                        <span class="badge badge-pending">Menunggu</span>
                    </div>
                    <div class="booking-item-body">
                        <span style="display:flex;align-items:center;gap:5px;font-size:13px;color:var(--gray-500);">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.11 12 19.79 19.79 0 0 1 1.04 3.33 2 2 0 0 1 3 1h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $p->user->no_hp ?? '-' }}
                        </span>
                        <strong class="text-brand">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                    </div>
                    <div class="booking-item-footer">
                        <form action="{{ route('admin.pemesanan.konfirmasi', $p) }}" method="POST" class="form-flex">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-confirm"
                                    onclick="return confirm('Konfirmasi pemesanan {{ addslashes($p->user->name) }}?')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Konfirmasi
                            </button>
                        </form>
                        <form action="{{ route('admin.pemesanan.tolak', $p) }}" method="POST" class="form-flex">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-reject"
                                    onclick="return confirm('Tolak pemesanan ini?')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div class="empty-state__text">Tidak ada pemesanan pending</div>
                </div>
            @endforelse

            @if ($pemesananPending > 5)
                <a href="{{ route('admin.pemesanan.index') }}" class="link-see-all">
                    Lihat semua {{ $pemesananPending }} pemesanan
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="display:inline;"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            @endif
        </div>
    </div>

    {{-- Sedang Berjalan --}}
    @php
        $sedangBerjalan = \App\Models\Pemesanan::with(['user','mobil'])
            ->where('status','dikonfirmasi')->latest()->take(3)->get();
    @endphp
    <div class="card card--mb">
        <div class="card-body card-body--between" style="padding:16px 20px; border-bottom:1px solid var(--gray-100);">
            <span class="card-header__title">Sedang Berjalan</span>
            <span class="card-header__meta">{{ $pemesananBerjalan }} aktif</span>
        </div>
        <div class="card-body card-body--col">
            @forelse ($sedangBerjalan as $p)
                <div class="booking-item">
                    <div class="booking-item-header">
                        <div>
                            <div class="booking-item-name">{{ $p->user->name }}</div>
                            <div class="booking-item-code">
                                {{ $p->mobil->nama }} · s/d {{ $p->tanggal_selesai->format('d M Y') }}
                            </div>
                        </div>
                        <span class="badge badge-berjalan">Berjalan</span>
                    </div>
                    <div class="booking-item-body">
                        <span style="display:flex;align-items:center;gap:5px;font-size:13px;color:var(--gray-500);">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1l2-3h10l2 3h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/><circle cx="7.5" cy="17" r="2.5"/><circle cx="16.5" cy="17" r="2.5"/></svg>
                            {{ $p->mobil->plat_nomor }}
                        </span>
                        <strong class="text-brand">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                    </div>
                    <div class="booking-item-footer">
                        <form action="{{ route('admin.pemesanan.selesai', $p) }}" method="POST" class="form-flex">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-confirm"
                                    onclick="return confirm('Tandai pemesanan ini selesai?')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Tandai Selesai
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state empty-state--sm">
                    Tidak ada pemesanan berjalan
                </div>
            @endforelse
        </div>
    </div>

    {{-- Ringkasan Bulan Ini --}}
    <div class="card">
        <div class="card-body card-body--between" style="padding:16px 20px; border-bottom:1px solid var(--gray-100);">
            <span class="card-header__title">Ringkasan Bulan Ini</span>
        </div>
        <div class="card-body card-body--col card-body--gap-md">
            <div class="summary-row">
                <span class="summary-row__label">Pemesanan Selesai</span>
                <strong>{{ \App\Models\Pemesanan::where('status','selesai')->whereMonth('updated_at',now()->month)->count() }}</strong>
            </div>
            <div class="summary-row">
                <span class="summary-row__label">Pemesanan Dibatalkan</span>
                <strong>{{ \App\Models\Pemesanan::where('status','dibatalkan')->whereMonth('updated_at',now()->month)->count() }}</strong>
            </div>
            <div class="summary-row">
                <span class="summary-row__label">Pelanggan Baru</span>
                <strong>{{ \App\Models\User::where('role','pelanggan')->whereMonth('created_at',now()->month)->count() }}</strong>
            </div>
            <hr class="divider">
            <div class="summary-row">
                <span class="summary-row__label summary-row__label--bold">Total Pendapatan</span>
                <strong class="summary-row__total">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

</div>
@endsection

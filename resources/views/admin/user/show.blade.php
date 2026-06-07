{{-- resources/views/admin/user/show.blade.php --}}
@extends('layouts.admin')

@section('title', $user->name)
@section('page-title', $user->name)
@section('page-subtitle', 'Detail Pelanggan')

@section('header-actions')
    <a href="{{ route('admin.user.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="admin-content">
    <div class="dashboard-grid">

        {{-- ── Kolom kiri: profil ──────────────────────────────── --}}
        <div>
            <div class="card card--mb">
                <div class="card-body card-body--center">

                    {{-- Avatar --}}
                    <div class="profile-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    {{-- Info --}}
                    <div class="profile-name">{{ $user->name }}</div>
                    <div class="profile-email">{{ $user->email }}</div>

                    @if ($user->no_hp)
                        <div class="profile-phone">{{ $user->no_hp }}</div>
                    @endif

                    <div class="profile-joined">
                        Bergabung {{ $user->created_at->translatedFormat('d M Y') }}
                    </div>
                </div>
            </div>

            {{-- ── Statistik ─────────────────────────────────── --}}
            <div class="card">
                <div class="card-body">
                    <div class="section-title section-title--mb">📊 Statistik</div>
                    <div class="user-detail-stats">
                        <div class="stat-card">
                            <div class="stat-label">Total Pemesanan</div>
                            <div class="stat-value">{{ $totalPemesanan }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Total Pengeluaran</div>
                            <div class="stat-value stat-value--sm">
                                Rp {{ number_format($totalPengeluaran / 1000000, 1, ',', '.') }}jt
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Selesai</div>
                            <div class="stat-value stat-value--success">{{ $totalSelesai }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Dibatalkan</div>
                            <div class="stat-value stat-value--danger">{{ $totalBatal }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Kolom kanan: riwayat ─────────────────────────── --}}
        <div>
            <div class="card">
                <div class="card-body">
                    <div class="section-title section-title--mb">📋 Riwayat Pemesanan</div>
                    <div class="booking-list">
                        @forelse ($pemesanans as $p)
                            @php $durasi = $p->tanggal_mulai->diffInDays($p->tanggal_selesai); @endphp
                            <div class="booking-item">
                                <div class="booking-item-header">
                                    <div>
                                        <div class="booking-item-name">{{ $p->mobil->nama }}</div>
                                        <div class="booking-item-code">
                                            {{ $p->tanggal_mulai->format('d M') }} –
                                            {{ $p->tanggal_selesai->format('d M Y') }}
                                            · {{ $durasi }} hari
                                        </div>
                                    </div>
                                    <span class="badge badge-{{ $p->status }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </div>
                                <div class="booking-item-body">
                                    <span class="badge-plat">{{ $p->mobil->plat_nomor }}</span>
                                    <strong class="text-brand">
                                        Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                                    </strong>
                                </div>
                                <div class="booking-item-action">
                                    <a href="{{ route('admin.pemesanan.show', $p) }}"
                                       class="btn btn-view btn-sm">Lihat Detail</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-empty text-empty--padded">
                                Belum ada riwayat pemesanan
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
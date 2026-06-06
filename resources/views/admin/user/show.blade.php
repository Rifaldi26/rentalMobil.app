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

        {{-- Kolom kiri: profil --}}
        <div>
            <div class="card" style="margin-bottom:20px;">
                <div class="card-body" style="text-align:center;padding:28px;">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--brand-100);
                                color:var(--brand-600);font-size:26px;font-weight:800;
                                display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div style="font-size:18px;font-weight:800;color:var(--gray-900);">{{ $user->name }}</div>
                    <div style="font-size:13px;color:var(--gray-500);margin-top:4px;">{{ $user->email }}</div>
                    @if ($user->no_hp)
                        <div style="font-size:13px;color:var(--gray-500);margin-top:2px;">{{ $user->no_hp }}</div>
                    @endif
                    <div style="font-size:12px;color:var(--brand-400);font-weight:600;margin-top:8px;">
                        Bergabung {{ $user->created_at->translatedFormat('d M Y') }}
                    </div>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="card">
                <div class="card-body">
                    <div class="dashboard-section-title" style="margin-bottom:14px;">📊 Statistik</div>
                    <div class="dashboard-stats" style="grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="stat-card">
                            <div class="stat-label">Total Pemesanan</div>
                            <div class="stat-value">{{ $totalPemesanan }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Total Pengeluaran</div>
                            <div class="stat-value" style="font-size:16px;">
                                Rp {{ number_format($totalPengeluaran / 1000000, 1, ',', '.') }}jt
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Selesai</div>
                            <div class="stat-value" style="color:var(--success);">{{ $totalSelesai }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Dibatalkan</div>
                            <div class="stat-value" style="color:var(--danger);">{{ $totalBatal }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: riwayat --}}
        <div>
            <div class="card">
                <div class="card-body">
                    <div class="dashboard-section-title" style="margin-bottom:14px;">📋 Riwayat Pemesanan</div>
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
                                    <span class="badge badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                                </div>
                                <div class="booking-item-body">
                                    <span class="badge-plat">{{ $p->mobil->plat_nomor }}</span>
                                    <strong style="color:var(--brand-400);">
                                        Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                                    </strong>
                                </div>
                                <div style="margin-top:8px;">
                                    <a href="{{ route('admin.pemesanan.show', $p) }}"
                                       class="btn btn-view btn-sm">Lihat Detail</a>
                                </div>
                            </div>
                        @empty
                            <p style="font-size:13px;color:var(--gray-400);text-align:center;padding:24px 0;">
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
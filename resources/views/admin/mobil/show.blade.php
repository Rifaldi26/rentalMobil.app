{{-- resources/views/admin/mobil/show.blade.php --}}
@extends('layouts.admin')

@section('title', $mobil->nama)
@section('page-title', $mobil->nama)
@section('page-subtitle', $mobil->merek . ' · ' . $mobil->tahun)

@section('header-actions')
    <a href="{{ route('admin.mobil.edit', $mobil) }}" class="btn btn-primary btn-sm">✏️ Edit</a>
    <a href="{{ route('admin.mobil.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="admin-content">
    <div class="dashboard-grid">

        {{-- ── Kolom Kiri ── --}}
        <div>

            {{-- Foto --}}
            <div class="card mb-20">
                @if ($mobil->foto)
                    <img src="{{ asset('storage/' . $mobil->foto) }}"
                         class="mobil-show__foto" alt="{{ $mobil->nama }}">
                @else
                    <div class="mobil-show__foto-placeholder">🚗</div>
                @endif

                <div class="card-body">
                    <div class="mobil-show__status-row">
                        <span class="badge badge-{{ $mobil->status }}">
                            {{ $mobil->status === 'tersedia' ? 'Tersedia' : 'Sedang Disewa' }}
                        </span>
                        <form action="{{ route('admin.mobil.toggle', $mobil) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-secondary btn-sm">
                                Toggle Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Deskripsi --}}
            @if ($mobil->deskripsi)
                <div class="card">
                    <div class="card-body">
                        <div class="section-title mb-10">📝 Deskripsi</div>
                        <p class="mobil-show__deskripsi">{{ $mobil->deskripsi }}</p>
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Kolom Kanan ── --}}
        <div>

            {{-- Spesifikasi --}}
            <div class="card mb-20">
                <div class="card-body">
                    <div class="section-title mb-14">🔧 Spesifikasi</div>
                    <div class="ringkasan-list">
                        <div class="ringkasan-row">
                            <span class="label">Merek</span>
                            <span class="value">{{ $mobil->merek }}</span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">Tahun</span>
                            <span class="value">{{ $mobil->tahun }}</span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">Plat Nomor</span>
                            <span class="value">
                                <span class="badge-plat">{{ $mobil->plat_nomor }}</span>
                            </span>
                        </div>
                        <div class="ringkasan-divider"></div>
                        <div class="ringkasan-row ringkasan-total">
                            <span class="label">Harga / Hari</span>
                            <span class="value">
                                Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="card mb-20">
                <div class="card-body">
                    <div class="section-title mb-14">📊 Statistik</div>
                    <div class="stat-grid stat-grid--2">
                        <div class="stat-card">
                            <div class="stat-label">Total Disewa</div>
                            <div class="stat-value">{{ $totalDisewa }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Pendapatan</div>
                            <div class="stat-value stat-value--sm">
                                Rp {{ number_format($totalPendapatan / 1000000, 1, ',', '.') }}jt
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat Pemesanan --}}
            <div class="card">
                <div class="card-body">
                    <div class="section-title mb-14">📋 Riwayat Pemesanan</div>

                    @forelse ($riwayat as $p)
                        <div class="booking-item mb-8">
                            <div class="booking-item-header">
                                <div>
                                    <div class="booking-item-name">{{ $p->user->name }}</div>
                                    <div class="booking-item-code">
                                        {{ $p->tanggal_mulai->format('d M') }} –
                                        {{ $p->tanggal_selesai->format('d M Y') }}
                                    </div>
                                </div>
                                <span class="badge badge-{{ $p->status }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="empty-text">Belum ada riwayat pemesanan</p>
                    @endforelse

                </div>
            </div>

        </div>
    </div>

    {{-- ── Danger Zone ── --}}
    <div class="danger-zone">
        <form action="{{ route('admin.mobil.destroy', $mobil) }}" method="POST"
              data-confirm="Hapus {{ $mobil->nama }}? Aksi ini tidak dapat dibatalkan.">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">🗑 Hapus Mobil Ini</button>
        </form>
    </div>

</div>
@endsection
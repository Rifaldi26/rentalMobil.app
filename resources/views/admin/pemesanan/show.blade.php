{{-- resources/views/admin/pemesanan/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail Pemesanan')
@section('page-title', 'Detail Pemesanan')
@section('page-subtitle', '#' . $pemesanan->kode_pemesanan)

@section('header-actions')
    <a href="{{ route('admin.pemesanan.index') }}" class="btn btn-secondary btn-sm">
        ← Kembali
    </a>
@endsection

@section('content')
<div class="admin-content">
    <div class="dashboard-grid">

        {{-- ── Kolom Kiri ── --}}
        <div>

            {{-- Status & Aksi --}}
            <div class="card mb-20">
                <div class="card-body">
                    <div class="pemesanan-show__status-row">
                        <span class="badge badge-{{ $pemesanan->status }}">
                            {{ ucfirst($pemesanan->status) }}
                        </span>
                        <span class="pemesanan-show__created-at">
                            {{ $pemesanan->created_at->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pemesanan-show__actions">
                        @if ($pemesanan->status === 'pending')
                            <form action="{{ route('admin.pemesanan.konfirmasi', $pemesanan) }}"
                                  method="POST" class="form-aksi">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-success btn-full">
                                    ✅ Konfirmasi
                                </button>
                            </form>
                            <form action="{{ route('admin.pemesanan.tolak', $pemesanan) }}"
                                  method="POST" class="form-aksi"
                                  data-confirm="Tolak pemesanan ini?">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-full">
                                    ❌ Tolak
                                </button>
                            </form>

                        @elseif ($pemesanan->status === 'dikonfirmasi')
                            <form action="{{ route('admin.pemesanan.selesai', $pemesanan) }}"
                                  method="POST" class="form-aksi"
                                  data-confirm="Tandai selesai?">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-full">
                                    🏁 Tandai Selesai
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Pelanggan --}}
            <div class="card mb-20">
                <div class="card-body">
                    <div class="section-title mb-14">👤 Pelanggan</div>
                    <div class="ringkasan-list">
                        <div class="ringkasan-row">
                            <span class="label">Nama</span>
                            <span class="value">{{ $pemesanan->user->name }}</span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">Email</span>
                            <span class="value">{{ $pemesanan->user->email }}</span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">No. HP</span>
                            <span class="value">{{ $pemesanan->user->no_hp ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Periode Sewa --}}
            <div class="card">
                <div class="card-body">
                    <div class="section-title mb-14">📅 Periode Sewa</div>
                    <div class="ringkasan-list">
                        <div class="ringkasan-row">
                            <span class="label">Tanggal Mulai</span>
                            <span class="value">
                                {{ $pemesanan->tanggal_mulai->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">Tanggal Selesai</span>
                            <span class="value">
                                {{ $pemesanan->tanggal_selesai->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">Durasi</span>
                            <span class="value">
                                {{ $pemesanan->tanggal_mulai->diffInDays($pemesanan->tanggal_selesai) }} hari
                            </span>
                        </div>
                        <div class="ringkasan-divider"></div>
                        <div class="ringkasan-row ringkasan-total">
                            <span class="label">Total Harga</span>
                            <span class="value">
                                Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Kolom Kanan ── --}}
        <div>
            <div class="card">
                <div class="card-body">
                    <div class="section-title mb-14">🚗 Kendaraan</div>

                    @if ($pemesanan->mobil->foto)
                        <img src="{{ asset('storage/' . $pemesanan->mobil->foto) }}"
                             class="pemesanan-show__mobil-foto"
                             alt="{{ $pemesanan->mobil->nama }}">
                    @endif

                    <div class="ringkasan-list">
                        <div class="ringkasan-row">
                            <span class="label">Nama</span>
                            <span class="value">{{ $pemesanan->mobil->nama }}</span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">Merek</span>
                            <span class="value">{{ $pemesanan->mobil->merek }}</span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">Tahun</span>
                            <span class="value">{{ $pemesanan->mobil->tahun }}</span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">Plat Nomor</span>
                            <span class="value">
                                <span class="badge-plat">{{ $pemesanan->mobil->plat_nomor }}</span>
                            </span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="label">Harga/Hari</span>
                            <span class="value">
                                Rp {{ number_format($pemesanan->mobil->harga_per_hari, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-16">
                        <a href="{{ route('admin.mobil.show', $pemesanan->mobil) }}"
                           class="btn btn-outline btn-full">
                            Lihat Detail Mobil
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/admin/pemesanan.js'])
@endpush
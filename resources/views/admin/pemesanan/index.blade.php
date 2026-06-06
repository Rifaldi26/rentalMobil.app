@extends('layouts.admin')
@section('title', 'Pemesanan')
@section('page-title', 'Pemesanan')

@section('content')
<div class="admin-content">

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="filter-chips mb-16">
        <button class="filter-chip active" data-filter="semua">Semua</button>
        <button class="filter-chip" data-filter="pending">Menunggu</button>
        <button class="filter-chip" data-filter="dikonfirmasi">Berjalan</button>
        <button class="filter-chip" data-filter="selesai">Selesai</button>
        <button class="filter-chip" data-filter="dibatalkan">Dibatalkan</button>
    </div>

    {{-- Daftar Pemesanan --}}
    @if ($pemesanans->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" width="48" height="48">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <line x1="9" y1="12" x2="15" y2="12"/>
                    <line x1="9" y1="16" x2="13" y2="16"/>
                </svg>
            </div>
            <div class="empty-state__title">Belum ada pemesanan</div>
        </div>
    @else
        <div class="booking-list" id="booking-list">
            @foreach ($pemesanans as $p)
                @php $durasi = $p->tanggal_mulai->diffInDays($p->tanggal_selesai); @endphp

                <div class="booking-item" data-status="{{ $p->status }}">

                    <div class="booking-item-header">
                        <div>
                            <div class="booking-item-name">{{ $p->user->name }}</div>
                            <div class="booking-item-code">
                                {{ $p->mobil->nama }} · {{ $p->mobil->plat_nomor }} · {{ $durasi }} hari
                            </div>
                        </div>
                        <span class="booking-status {{ $p->status_class }}">
                            {{ $p->status_label }}
                        </span>
                    </div>

                    <div class="booking-item-body">
                        <span>
                            {{ $p->tanggal_mulai->format('d M') }} –
                            {{ $p->tanggal_selesai->format('d M Y') }}
                        </span>
                        <strong class="text-brand">
                            Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                        </strong>
                    </div>

                    {{-- Tombol aksi --}}
                    @if ($p->status === 'pending')
                        <div class="booking-item-footer">
                            <button type="button" class="btn-confirm"
                                    data-aksi="konfirmasi"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->user->name }}"
                                    data-mobil="{{ $p->mobil->nama }}"
                                    data-tanggal="{{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M Y') }}">
                                 Konfirmasi
                            </button>
                            <button type="button" class="btn-reject"
                                    data-aksi="tolak"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->user->name }}"
                                    data-mobil="{{ $p->mobil->nama }}"
                                    data-tanggal="{{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M Y') }}">
                                 Tolak
                            </button>
                        </div>
                    @elseif ($p->status === 'dikonfirmasi')
                        <div class="booking-item-footer">
                            <button type="button" class="btn-confirm"
                                    data-aksi="selesai"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->user->name }}"
                                    data-mobil="{{ $p->mobil->nama }}"
                                    data-tanggal="{{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M Y') }}">
                                 Tandai Selesai
                            </button>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- Hidden forms untuk submit aksi --}}
@foreach ($pemesanans as $p)
    <form id="form-konfirmasi-{{ $p->id }}"
          action="{{ route('admin.pemesanan.konfirmasi', $p) }}"
          method="POST" class="is-hidden">
        @csrf @method('PATCH')
    </form>
    <form id="form-tolak-{{ $p->id }}"
          action="{{ route('admin.pemesanan.tolak', $p) }}"
          method="POST" class="is-hidden">
        @csrf @method('PATCH')
    </form>
    <form id="form-selesai-{{ $p->id }}"
          action="{{ route('admin.pemesanan.selesai', $p) }}"
          method="POST" class="is-hidden">
        @csrf @method('PATCH')
    </form>
@endforeach

{{-- Modal aksi --}}
<div id="modal-aksi" class="modal-aksi-sheet" aria-hidden="true">
    <div class="modal-aksi-sheet__content">
        <div class="modal-aksi-sheet__handle"></div>
        <div id="modal-icon" class="modal-aksi-sheet__icon"></div>
        <div id="modal-title" class="modal-aksi-sheet__title"></div>
        <div id="modal-desc" class="modal-aksi-sheet__desc"></div>
        <button id="modal-submit" class="modal-aksi-sheet__btn-submit"></button>
        <button id="modal-batal" class="modal-aksi-sheet__btn-batal">Batal</button>
    </div>
</div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/pemesanan.js'])
@endpush
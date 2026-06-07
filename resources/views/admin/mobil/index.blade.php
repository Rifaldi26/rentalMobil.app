@extends('layouts.admin')
@section('title', 'Kelola Mobil')
@section('page-title', 'Kelola Mobil')

@section('header-actions')
    <a href="{{ route('admin.mobil.create') }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/>
             <line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Mobil
    </a>
@endsection

@section('content')
<div class="admin-content">

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ── Ringkasan ─────────────────────────────────────────── --}}
    <div class="stat-grid stat-grid--3 mb-20">

        <div class="stat-card stat-card--white" style="text-align:center; padding:16px;">
            <div class="stat-card__label">Total Unit</div>
            <div class="stat-card__value stat-card__value--dark">{{ $totalMobil }}</div>
        </div>

        <div class="stat-card" style="background:var(--success-bg);border:1px solid var(--success-border);border-radius:var(--radius-md);padding:16px;text-align:center;">
            <div class="stat-card__label" style="color:var(--success);">Tersedia</div>
            <div class="stat-card__value" style="color:var(--success);font-size:24px;font-weight:800;">{{ $tersediaCount }}</div>
        </div>

        <div class="stat-card" style="background:var(--danger-bg);border:1px solid var(--danger-border);border-radius:var(--radius-md);padding:16px;text-align:center;">
            <div class="stat-card__label" style="color:var(--danger);">Disewa</div>
            <div class="stat-card__value" style="color:var(--danger);font-size:24px;font-weight:800;">{{ $disewaCount }}</div>
        </div>

    </div>

    {{-- ── Filter chips ──────────────────────────────────────── --}}
    <div class="filter-chips mb-16">
        <a href="{{ route('admin.mobil.index') }}"
           class="filter-chip {{ !request('status') ? 'active' : '' }}">Semua</a>
        <a href="{{ route('admin.mobil.index', ['status' => 'tersedia']) }}"
           class="filter-chip {{ request('status') === 'tersedia' ? 'active' : '' }}">Tersedia</a>
        <a href="{{ route('admin.mobil.index', ['status' => 'disewa']) }}"
           class="filter-chip {{ request('status') === 'disewa' ? 'active' : '' }}">Disewa</a>
    </div>

    {{-- ── Daftar Mobil ──────────────────────────────────────── --}}
    @forelse ($mobils as $mobil)
        <div class="mobil-card mb-10">
            <div class="mobil-card__main">

                <div class="mobil-card__thumb">
                    @if ($mobil->foto)
                        <img src="{{ asset('storage/'.$mobil->foto) }}"
                             class="mobil-card__img" alt="{{ $mobil->nama }}">
                    @else
                        <div class="mobil-card__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                width="32" height="32">
                                <path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1l2-3h10l2 3h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/>
                                <circle cx="7.5" cy="17" r="2.5"/>
                                <circle cx="16.5" cy="17" r="2.5"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="mobil-card__info">
                    <div class="mobil-card__nama">{{ $mobil->nama }}</div>
                    <div class="mobil-card__meta">
                        {{ $mobil->merek }} · {{ $mobil->tahun }} · {{ $mobil->plat_nomor }}
                    </div>
                    <div class="mobil-card__harga">
                        Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}
                        <span class="mobil-card__harga-unit">/hari</span>
                    </div>
                </div>

                <form action="{{ route('admin.mobil.toggle', $mobil) }}" method="POST"
                      class="mobil-card__toggle-form">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="badge {{ $mobil->status === 'tersedia' ? 'badge-tersedia' : 'badge-disewa' }}">
                        {{ $mobil->status === 'tersedia' ? '✓ Tersedia' : '✗ Disewa' }}
                    </button>
                </form>

            </div>

            @if ($mobil->deskripsi)
                <div class="mobil-card__desc">{{ Str::limit($mobil->deskripsi, 80) }}</div>
            @endif

            <div class="mobil-card__actions">
                <a href="{{ route('admin.mobil.show', $mobil) }}"
                   class="btn btn-view btn-sm btn--flex">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/></svg>
                    Detail
                </a>
                <a href="{{ route('admin.mobil.edit', $mobil) }}"
                   class="btn btn-edit btn-sm btn--flex">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('admin.mobil.destroy', $mobil) }}" method="POST"
                      class="btn--flex"
                      data-confirm="Hapus {{ $mobil->nama }}?">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-delete btn-sm w-full">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6M14 11v6"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    width="48" height="48">
                    <path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1l2-3h10l2 3h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/>
                    <circle cx="7.5" cy="17" r="2.5"/>
                    <circle cx="16.5" cy="17" r="2.5"/>
                </svg>
            </div>
            <div class="empty-state__title">Belum ada data mobil</div>
            <a href="{{ route('admin.mobil.create') }}" class="btn btn-primary mt-16">
                + Tambah Mobil Pertama
            </a>
        </div>
    @endforelse

    @if ($mobils->hasPages())
        <div class="pagination-wrapper">{{ $mobils->appends(request()->query())->links() }}</div>
    @endif

</div>
@endsection

@extends('layouts.admin')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="admin-content">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $users = \App\Models\User::where('role', 'pelanggan')
            ->withCount(['pemesanans as total_pemesanan'])
            ->withSum(['pemesanans as total_pengeluaran' => fn($q) => $q->where('status', 'selesai')], 'total_harga')
            ->latest()
            ->get();

        $totalUser    = $users->count();
        $userAktif    = $users->filter(fn($u) => $u->total_pemesanan > 0)->count();
        $userBulanIni = $users->filter(fn($u) => $u->created_at->isCurrentMonth())->count();

        $avatarColors = [
            ['bg' => '#dbeafe', 'text' => '#1d4ed8'],
            ['bg' => '#dcfce7', 'text' => '#15803d'],
            ['bg' => '#fef9c3', 'text' => '#a16207'],
            ['bg' => '#fce7f3', 'text' => '#be185d'],
            ['bg' => '#ede9fe', 'text' => '#6d28d9'],
        ];
    @endphp

    {{-- Stats --}}
    <div class="user-stats-grid">
        <div class="user-stat-card">
            <div class="user-stat-card__value">{{ $totalUser }}</div>
            <div class="user-stat-card__label">Total</div>
        </div>
        <div class="user-stat-card">
            <div class="user-stat-card__value user-stat-card__value--brand">{{ $userAktif }}</div>
            <div class="user-stat-card__label">Pernah Pesan</div>
        </div>
        <div class="user-stat-card">
            <div class="user-stat-card__value user-stat-card__value--success">{{ $userBulanIni }}</div>
            <div class="user-stat-card__label">Baru Bulan Ini</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="search-wrapper">
        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="search-input" class="search-input"
               placeholder="Cari nama atau email...">
    </div>

    {{-- Filter tabs --}}
    <div class="filter-tabs">
        <button class="cat-chip cat-chip--inline active" data-tab="semua">Semua</button>
        <button class="cat-chip cat-chip--inline" data-tab="aktif">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Pernah Pesan
        </button>
        <button class="cat-chip cat-chip--inline" data-tab="baru">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Bulan Ini
        </button>
    </div>

    {{-- User list --}}
    @if ($users->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="empty-state__text">Belum ada pelanggan terdaftar</div>
        </div>
    @else
        <div class="user-list" id="user-list">
            @foreach ($users as $user)
                @php
                    $isAktif = $user->total_pemesanan > 0;
                    $isBaru  = $user->created_at->isCurrentMonth();
                    $inisial = strtoupper(substr($user->name, 0, 1));
                    $color   = $avatarColors[$user->id % 5];
                @endphp
                <div class="booking-item user-card"
                     data-aktif="{{ $isAktif ? 'ya' : 'tidak' }}"
                     data-baru="{{ $isBaru ? 'ya' : 'tidak' }}"
                     data-nama="{{ strtolower($user->name) }}"
                     data-email="{{ strtolower($user->email) }}">

                    <div class="user-card__row">
                        <div class="user-avatar"
                             style="background:{{ $color['bg'] }};color:{{ $color['text'] }};">
                            {{ $inisial }}
                        </div>

                        <div class="user-info">
                            <div class="user-info__name">{{ $user->name }}</div>
                            <div class="user-info__email">{{ $user->email }}</div>
                            <div class="user-info__date" style="display:flex;align-items:center;gap:4px;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Daftar {{ $user->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>

                        <div class="user-badges">
                            @if ($isAktif)
                                <span class="badge-pill badge-pill--blue">Aktif</span>
                            @else
                                <span class="badge-pill badge-pill--gray">Belum pesan</span>
                            @endif
                            @if ($isBaru)
                                <span class="badge-pill badge-pill--green">Baru</span>
                            @endif
                        </div>
                    </div>

                    <div class="user-stats-row">
                        <div class="user-stats-row__cell">
                            <div class="user-stats-row__value">{{ $user->total_pemesanan }}</div>
                            <div class="user-stats-row__label">Pemesanan</div>
                        </div>
                        <div class="user-stats-row__divider"></div>
                        <div class="user-stats-row__cell">
                            <div class="user-stats-row__value user-stats-row__value--brand">
                                {{ $user->total_pengeluaran > 0
                                    ? 'Rp ' . number_format($user->total_pengeluaran / 1000000, 1, ',', '.') . 'jt'
                                    : 'Rp 0' }}
                            </div>
                            <div class="user-stats-row__label">Total Sewa</div>
                        </div>
                        <div class="user-stats-row__divider"></div>
                        <div class="user-stats-row__cell">
                            <div class="user-stats-row__value user-stats-row__value--muted">
                                {{ $user->no_hp ?? '—' }}
                            </div>
                            <div class="user-stats-row__label">No. HP</div>
                        </div>
                    </div>

                    <div class="user-actions">
                        <a href="mailto:{{ $user->email }}"
                           class="btn-user-action btn-user-action--secondary"
                           style="display:flex;align-items:center;justify-content:center;gap:6px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            Email
                        </a>
                        <a href="{{ route('admin.user.show', $user) }}"
                           class="btn-user-action btn-user-action--primary"
                           style="display:flex;align-items:center;justify-content:center;gap:6px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            Riwayat
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="empty-search" class="empty-state" style="display:none;">
            <div class="empty-state__icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </div>
            <div class="empty-state__text">Pelanggan tidak ditemukan</div>
            <div class="empty-state__sub">Coba kata kunci lain</div>
        </div>
    @endif

</div>

@push('scripts')
    @vite(['resources/js/admin/user.js'])
@endpush

@endsection

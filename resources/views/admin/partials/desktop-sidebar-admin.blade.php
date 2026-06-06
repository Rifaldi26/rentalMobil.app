{{-- resources/views/admin/partials/desktop-sidebar-admin.blade.php --}}
@php
    $pemesananPending = \App\Models\Pemesanan::where('status', 'pending')->count();
@endphp

<aside class="admin-sidebar" id="admin-sidebar">

    {{-- Brand --}}
    <div class="admin-sidebar-brand">
        <span>Rental</span>Mobil
    </div>

    {{-- Toggle Button --}}
    <button class="admin-sidebar-toggle" id="admin-sidebar-toggle"
            onclick="toggleAdminSidebar()" aria-label="Toggle sidebar">
        <svg id="admin-icon-open" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
        <svg id="admin-icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             style="display:none;">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </button>

    <nav class="admin-sidebar-nav">

        {{-- ── UTAMA ── --}}
        <div class="admin-nav-label">Utama</div>

        <a href="{{ route('admin.dashboard') }}" data-label="Dashboard"
           class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            <span class="admin-nav-link-label">Dashboard</span>
        </a>

        <a href="{{ route('admin.pemesanan.index') }}" data-label="Pemesanan"
           class="admin-nav-link {{ request()->routeIs('admin.pemesanan.*') ? 'active' : '' }}"
           style="position:relative;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="4" rx="2"/>
                <line x1="16" x2="16" y1="2" y2="6"/>
                <line x1="8" x2="8" y1="2" y2="6"/>
                <line x1="3" x2="21" y1="10" y2="10"/>
                <path d="M8 14h.01M12 14h.01M8 18h.01M12 18h.01"/>
            </svg>
            <span class="admin-nav-link-label">Pemesanan</span>
            @if ($pemesananPending > 0)
                <span class="admin-nav-badge">{{ $pemesananPending }}</span>
            @endif
        </a>

        {{-- ── ARMADA ── --}}
        <div class="admin-nav-label">Armada</div>

        <a href="{{ route('admin.mobil.index') }}" data-label="Kelola Mobil"
           class="admin-nav-link {{ request()->routeIs('admin.mobil.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1l2-3h8l2 3h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2z"/>
                <circle cx="7.5" cy="17" r="1.5"/>
                <circle cx="16.5" cy="17" r="1.5"/>
            </svg>
            <span class="admin-nav-link-label">Kelola Mobil</span>
        </a>

        <a href="{{ route('admin.jadwal.index') }}" data-label="Jadwal & Ketersediaan"
           class="admin-nav-link {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="4" rx="2"/>
                <line x1="16" x2="16" y1="2" y2="6"/>
                <line x1="8" x2="8" y1="2" y2="6"/>
                <line x1="3" x2="21" y1="10" y2="10"/>
                <path d="M8 14h4M8 18h2"/>
            </svg>
            <span class="admin-nav-link-label">Jadwal & Ketersediaan</span>
        </a>

        {{-- ── KOMUNIKASI ── --}}
        <div class="admin-nav-label">Komunikasi</div>

        <a href="{{ route('admin.chat') }}" data-label="Chat Pelanggan"
           class="admin-nav-link {{ request()->routeIs('admin.chat') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <span class="admin-nav-link-label">Chat Pelanggan</span>
        </a>

        {{-- ── LAPORAN ── --}}
        <div class="admin-nav-label">Laporan</div>

        <a href="{{ route('admin.laporan.index') }}" data-label="Laporan Keuangan"
           class="admin-nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
                <line x1="2" y1="20" x2="22" y2="20"/>
            </svg>
            <span class="admin-nav-link-label">Laporan Keuangan</span>
        </a>
        {{-- ── PENGATURAN ── --}}
        <div class="admin-nav-label">Pengaturan</div>

        <a href="{{ route('admin.user.index') }}" data-label="Manajemen User"
           class="admin-nav-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <span class="admin-nav-link-label">Manajemen User</span>
        </a>
    </nav>
</aside>
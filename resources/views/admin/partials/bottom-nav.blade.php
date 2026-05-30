{{-- admin/partials/bottom-nav.blade.php --}}
{{-- Bottom navigation bar untuk tampilan mobile admin pemilik tunggal --}}
@php
    $route = request()->route()?->getName() ?? '';
    $pendingBookings = \App\Models\Booking::where('status', \App\Enums\BookingStatus::Pending)->count();
@endphp

<nav class="bottom-nav" role="navigation" aria-label="Navigasi utama">

    {{-- Dashboard --}}
    <a href="{{ route('admin.dashboard') }}"
       class="bottom-nav-item {{ Str::startsWith($route, 'admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
        </svg>
        Dashboard
    </a>

    {{-- Pemesanan --}}
    <a href="{{ route('admin.bookings.index') }}"
       class="bottom-nav-item {{ Str::startsWith($route, 'admin.bookings') ? 'active' : '' }}"
       style="position:relative;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        @if($pendingBookings > 0)
            <span class="bottom-nav-badge">{{ $pendingBookings }}</span>
        @endif
        Pemesanan
    </a>

    {{-- Tambah Kendaraan (FAB center) --}}
    <div class="bottom-nav-center">
        <a href="{{ route('admin.vehicles.create') }}"
           class="bottom-nav-center-btn"
           aria-label="Tambah kendaraan baru">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
        </a>
    </div>

    {{-- Armada --}}
    <a href="{{ route('admin.vehicles.index') }}"
       class="bottom-nav-item {{ Str::startsWith($route, 'admin.vehicles') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/>
            <rect width="13" height="8" x="8" y="13" rx="2"/>
        </svg>
        Armada
    </a>

    {{-- Lebih Banyak --}}
    <div x-data="{ moreOpen: false }" style="position:relative;flex:1;">
        <button @click="moreOpen = !moreOpen"
                class="bottom-nav-item"
                :class="{ active: moreOpen }"
                style="width:100%;"
                aria-label="Menu lainnya">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/>
            </svg>
            Lainnya
        </button>

        {{-- More Menu Popup --}}
        <div x-show="moreOpen" @click.outside="moreOpen = false" x-cloak x-transition
             style="position:absolute;bottom:calc(100% + 8px);right:0;width:220px;background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);overflow:hidden;z-index:200;">
            <div style="padding:8px;">
                <a href="{{ route('admin.users.index') }}" class="dropdown-item" style="border-radius:var(--radius-sm);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                    Data Pelanggan
                </a>
                <a href="{{ route('admin.schedule.index') }}" class="dropdown-item" style="border-radius:var(--radius-sm);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Jadwal
                </a>
                <a href="{{ route('admin.reports.index') }}" class="dropdown-item" style="border-radius:var(--radius-sm);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Laporan
                </a>
                <a href="{{ route('admin.withdrawals.index') }}" class="dropdown-item" style="border-radius:var(--radius-sm);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Keuangan
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile.edit') }}" class="dropdown-item" style="border-radius:var(--radius-sm);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item danger" style="border-radius:var(--radius-sm);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

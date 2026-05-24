{{-- resources/views/admin/partials/bottom-nav.blade.php --}}
@php $pendingCount = \App\Models\Pemesanan::where('status','pending')->count(); @endphp

<nav class="bottom-nav">
    <a href="{{ route('admin.dashboard') }}"
       class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Beranda
    </a>

    <a href="{{ route('admin.pemesanan.index') }}"
       class="nav-item {{ request()->routeIs('admin.pemesanan.*') ? 'active' : '' }}"
       style="text-decoration:none;position:relative;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" x2="16" y1="2" y2="6"/>
            <line x1="8" x2="8" y1="2" y2="6"/>
            <line x1="3" x2="21" y1="10" y2="10"/>
        </svg>
        @if ($pendingCount > 0)
            <span style="position:absolute;top:6px;right:calc(50% - 22px);background:#dc2626;color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px;line-height:1.4;">
                {{ $pendingCount }}
            </span>
        @endif
        Pemesanan
    </a>

    {{-- Tombol tengah — Tambah Mobil --}}
    <div class="nav-center">
        <a href="{{ route('admin.mobil.create') }}" class="nav-center-btn" style="display:flex;align-items:center;justify-content:center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
        </a>
    </div>

    <a href="{{ route('admin.mobil.index') }}"
       class="nav-item {{ request()->routeIs('admin.mobil.*') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3m4 0a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/>
            <rect width="9" height="7" x="9" y="12" rx="2"/>
        </svg>
        Armada
    </a>

    <a href="{{ route('admin.user.index') }}"
       class="nav-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"/>
            <path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
        </svg>
        Pelanggan
    </a>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>
{{-- resources/views/admin/partials/bottom-nav.blade.php --}}
@php $pemesananPending = \App\Models\Pemesanan::where('status','pending')->count(); @endphp

<nav class="bottom-nav">

    {{-- Beranda --}}
    <a href="{{ route('admin.dashboard') }}"
       class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Beranda
    </a>

    {{-- Pemesanan --}}
    <a href="{{ route('admin.pemesanan.index') }}"
       class="nav-item {{ request()->routeIs('admin.pemesanan.*') ? 'active' : '' }}"
       style="text-decoration:none;position:relative;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" x2="16" y1="2" y2="6"/>
            <line x1="8" x2="8" y1="2" y2="6"/>
            <line x1="3" x2="21" y1="10" y2="10"/>
        </svg>
        @if ($pemesananPending > 0)
            <span style="position:absolute;top:6px;right:calc(50% - 24px);background:var(--danger);color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px;">
                {{ $pemesananPending }}
            </span>
        @endif
        Pemesanan
    </a>

    {{-- Tombol Tengah — Armada --}}
    <div class="nav-center">
        <a href="{{ route('admin.mobil.index') }}" class="nav-center-btn"
           style="display:flex;align-items:center;justify-content:center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/>
                <rect width="13" height="8" x="8" y="13" rx="2"/>
                <path d="M19 17v-4"/>
                <path d="M21 13h-6"/>
            </svg>
        </a>
    </div>

    {{-- Pesan --}}
    <a href="{{ route('admin.chat') }}"
       class="nav-item {{ request()->routeIs('admin.chat') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        Pesan
    </a>

    {{-- Profil --}}
    <a href="{{ route('admin.profil') }}"
       class="nav-item {{ request()->routeIs('admin.profil') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"/>
            <path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
        </svg>
        Profil
    </a>

</nav>

{{-- Logout Form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>

{{-- Confirm Logout Modal --}}
<div id="modal-logout" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:flex-end;">
    <div style="background:#fff;border-radius:24px 24px 0 0;padding:28px 24px;width:100%;">
        <div style="font-size:16px;font-weight:700;margin-bottom:8px;">Keluar dari akun?</div>
        <div style="font-size:14px;color:var(--gray-500);margin-bottom:24px;">
            Anda akan keluar dari akun <strong>{{ Auth::user()->name }}</strong>.
        </div>
        <button onclick="document.getElementById('logout-form').submit()"
            style="width:100%;padding:14px;background:var(--danger);color:#fff;border:none;border-radius:var(--radius-md);font-size:15px;font-weight:700;cursor:pointer;margin-bottom:10px;">
            Ya, Keluar
        </button>
        <button onclick="document.getElementById('modal-logout').style.display='none'"
            style="width:100%;padding:14px;background:var(--gray-100);color:var(--gray-700);border:none;border-radius:var(--radius-md);font-size:15px;font-weight:600;cursor:pointer;">
            Batal
        </button>
    </div>
</div>

<script>
if (typeof confirmLogout === 'undefined') {
    function confirmLogout() {
        document.getElementById('modal-logout').style.display = 'flex';
    }
}
</script>
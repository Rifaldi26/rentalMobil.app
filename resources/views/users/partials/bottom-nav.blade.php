{{-- resources/views/users/partials/bottom-nav.blade.php --}}

<nav class="bottom-nav">

    {{-- Beranda --}}
    <a href="{{ route('dashboard') }}"
       class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Beranda
    </a>

    {{-- Booking --}}
    <a href="{{ route('user.pemesanan.index') }}"
       class="nav-item {{ request()->routeIs('user.pemesanan.*') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" x2="16" y1="2" y2="6"/>
            <line x1="8" x2="8" y1="2" y2="6"/>
            <line x1="3" x2="21" y1="10" y2="10"/>
        </svg>
        Booking
    </a>

    {{-- Tombol Tengah — Favorit --}}
    <div class="nav-center">
        <a href="{{ route('user.favorit') }}"
           class="nav-center-btn"
           style="display:flex;align-items:center;justify-content:center;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </a>
    </div>

    {{-- Pesan --}}
    <a href="{{ route('user.chat') }}"
       class="nav-item {{ request()->routeIs('user.chat') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        Pesan
    </a>

    {{-- Profil --}}
    <a href="{{ route('user.profil') }}"
       class="nav-item {{ request()->routeIs('user.profil') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"/>
            <path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
        </svg>
        Profil
    </a>

</nav>

{{-- Toast --}}
<div class="toast" id="toast"></div>

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
if (typeof showToast === 'undefined') {
    var toastTimer;
    function showToast(msg, type = '') {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className = `toast ${type} show`;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
    }
}
if (typeof confirmLogout === 'undefined') {
    function confirmLogout() {
        document.getElementById('modal-logout').style.display = 'flex';
    }
}
</script>
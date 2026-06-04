{{-- resources/views/admin/partials/desktop-sidebar.blade.php --}}
{{-- Include di setiap halaman admin SEBELUM tag <nav class="nav"> --}}

@php $pemesananPending = \App\Models\Pemesanan::where('status','pending')->count(); @endphp

<aside class="admin-desktop-sidebar" id="admin-desktop-sidebar">

  {{-- Toggle Button --}}
  <button class="admin-sidebar-toggle" id="admin-sidebar-toggle" onclick="toggleAdminSidebar()" aria-label="Toggle sidebar">
    <svg id="admin-icon-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="3" y1="6" x2="21" y2="6"/>
      <line x1="3" y1="12" x2="21" y2="12"/>
      <line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
    <svg id="admin-icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
      <line x1="18" y1="6" x2="6" y2="18"/>
      <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
  </button>

  {{-- Navigasi --}}
  <nav class="admin-sidebar-nav">

    {{-- Beranda --}}
    <a href="{{ route('admin.dashboard') }}"
       data-label="Beranda"
       class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      <span class="admin-sidebar-label">Beranda</span>
    </a>

    {{-- Pemesanan --}}
    <a href="{{ route('admin.pemesanan.index') }}"
       data-label="Pemesanan"
       class="admin-nav-link {{ request()->routeIs('admin.pemesanan.*') ? 'active' : '' }}"
       style="position:relative;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
        <line x1="16" x2="16" y1="2" y2="6"/>
        <line x1="8" x2="8" y1="2" y2="6"/>
        <line x1="3" x2="21" y1="10" y2="10"/>
      </svg>
      <span class="admin-sidebar-label">Pemesanan</span>
      @if ($pemesananPending > 0)
        <span class="admin-nav-badge">{{ $pemesananPending }}</span>
      @endif
    </a>

    {{-- Kelola Mobil --}}
    <a href="{{ route('admin.mobil.index') }}"
       data-label="Kelola Mobil"
       class="admin-nav-link {{ request()->routeIs('admin.mobil.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/>
        <rect width="13" height="8" x="8" y="13" rx="2"/>
        <path d="M19 17v-4"/><path d="M21 13h-6"/>
      </svg>
      <span class="admin-sidebar-label">Kelola Mobil</span>
    </a>

    {{-- Pesan --}}
    <a href="{{ route('admin.chat') }}"
       data-label="Pesan"
       class="admin-nav-link {{ request()->routeIs('admin.chat') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      <span class="admin-sidebar-label">Pesan</span>
    </a>

    {{-- Profil --}}
    <a href="{{ route('admin.profil') }}"
       data-label="Profil"
       class="admin-nav-link {{ request()->routeIs('admin.profil') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="8" r="4"/>
        <path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
      </svg>
      <span class="admin-sidebar-label">Profil</span>
    </a>

  </nav>

  {{-- User info + Logout --}}
  <div class="admin-sidebar-footer admin-sidebar-label-block">
    <div class="admin-sidebar-user">
      <div class="admin-sidebar-avatar">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
      </div>
      <div style="flex:1;min-width:0;">
        <div style="font-size:13px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
          {{ Auth::user()->name }}
        </div>
        <div style="font-size:11px;color:rgba(255,255,255,.45);">Administrator</div>
      </div>
    </div>
    <button onclick="confirmLogout()" class="admin-sidebar-logout">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16 17 21 12 16 7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
      Keluar
    </button>
  </div>

</aside>

<script>
(function () {
  var STORAGE_KEY = 'admin_sidebar_collapsed';

  function applyState(collapsed, animate) {
    var sidebar    = document.getElementById('admin-desktop-sidebar');
    var iconOpen   = document.getElementById('admin-icon-open');
    var iconClose  = document.getElementById('admin-icon-close');
    if (!sidebar) return;

    if (!animate) sidebar.style.transition = 'none';

    if (collapsed) {
      sidebar.classList.add('collapsed');
      iconOpen.style.display  = 'block';
      iconClose.style.display = 'none';
    } else {
      sidebar.classList.remove('collapsed');
      iconOpen.style.display  = 'none';
      iconClose.style.display = 'block';
    }

    if (!animate) {
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          sidebar.style.transition = '';
        });
      });
    }
  }

  window.toggleAdminSidebar = function () {
    var sidebar   = document.getElementById('admin-desktop-sidebar');
    var collapsed = sidebar.classList.contains('collapsed');
    var next      = !collapsed;
    localStorage.setItem(STORAGE_KEY, next ? '1' : '0');
    applyState(next, true);
  };

  var saved = localStorage.getItem(STORAGE_KEY);
  applyState(saved === '1', false);
})();
</script>

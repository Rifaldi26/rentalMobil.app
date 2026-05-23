<aside class="sidebar">
    <div class="sidebar-brand">Rental<span>Mobil</span></div>

    <nav class="sidebar-nav">
        <div class="sidebar-label">Menu Utama</div>

        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="item-icon">📊</span> Dashboard
        </a>

        <a href="{{ route('admin.mobil.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.mobil.*') ? 'active' : '' }}">
            <span class="item-icon">🚗</span> Kelola Mobil
        </a>

        <a href="{{ route('admin.pemesanan.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.pemesanan.*') ? 'active' : '' }}">
            <span class="item-icon">📋</span> Pemesanan
        </a>

        <div class="sidebar-label" style="margin-top:8px;">Pengguna</div>

        <a href="{{ route('admin.user.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
            <span class="item-icon">👥</span> Data Pengguna
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div>
                <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                <div class="sidebar-user-role">{{ ucfirst(Auth::user()->role) }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-item" style="margin-top:4px;color:#f87171;">
                <span class="item-icon">🚪</span> Keluar
            </button>
        </form>
    </div>
</aside>
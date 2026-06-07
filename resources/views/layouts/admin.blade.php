{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Rental Mobil</title>
    @vite(['resources/css/admin.css'])
    @stack('styles')
</head>
<body class="page-admin">

{{-- Sidebar (desktop & tablet) --}}
@include('admin.partials.desktop-sidebar-admin')

{{-- Backdrop (hanya mobile, muncul saat drawer terbuka) --}}
<div class="drawer-backdrop" id="drawer-backdrop"></div>

{{-- Main --}}
<main class="admin-main" id="admin-main-content">

    {{-- Topbar --}}
    <div class="admin-topbar">
        <div class="admin-topbar-left">

            {{-- Hamburger — hanya tampil di mobile --}}
            <button class="btn-hamburger" id="btn-hamburger" aria-label="Buka menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" aria-hidden="true">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <div>
                <div class="admin-page-title">@yield('page-title')</div>
                @hasSection('page-subtitle')
                    <div class="admin-page-subtitle">@yield('page-subtitle')</div>
                @endif
            </div>
        </div>

        <div class="admin-topbar-actions">

            {{-- Slot aksi tambahan per halaman --}}
            @yield('header-actions')

            {{-- Notifikasi --}}
            <div class="topbar-notif-wrap" id="topbar-notif-wrap">
                <button class="topbar-notif-btn" id="topbar-notif-btn"
                        onclick="toggleNotifDropdown()"
                        aria-label="Notifikasi" aria-haspopup="true" aria-expanded="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    @php $pendingCount = \App\Models\Pemesanan::where('status', 'pending')->count(); @endphp
                    @if ($pendingCount > 0)
                        <span class="notif-badge">{{ $pendingCount > 9 ? '9+' : $pendingCount }}</span>
                    @endif
                </button>

                <div class="topbar-notif-dropdown" id="topbar-notif-dropdown" role="menu">
                    <div class="notif-dropdown-header">
                        <span>Notifikasi</span>
                        @if ($pendingCount > 0)
                            <a href="{{ route('admin.pemesanan.index') }}" class="notif-see-all">Lihat semua</a>
                        @endif
                    </div>
                    <div class="notif-dropdown-body">
                        @if ($pendingCount > 0)
                            <a href="{{ route('admin.pemesanan.index') }}" class="notif-item">
                                <div class="notif-item-icon">📋</div>
                                <div class="notif-item-text">
                                    <strong>{{ $pendingCount }} pemesanan pending</strong>
                                    <span>Menunggu konfirmasi Anda</span>
                                </div>
                            </a>
                        @else
                            <div class="notif-empty">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.5" opacity="0.3">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                                <span>Tidak ada notifikasi</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Avatar dropdown --}}
            <div class="topbar-avatar-wrap" id="topbar-avatar-wrap">
                <button class="topbar-avatar" id="topbar-avatar-btn"
                        onclick="toggleAvatarDropdown()"
                        aria-haspopup="true" aria-expanded="false">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </button>

                <div class="topbar-dropdown" id="topbar-dropdown" role="menu">
                    <div class="topbar-dropdown-header">
                        <div class="topbar-dropdown-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="topbar-dropdown-info">
                            <div class="topbar-dropdown-name">{{ Auth::user()->name }}</div>
                            <div class="topbar-dropdown-email">{{ Auth::user()->email }}</div>
                        </div>
                    </div>

                    <div class="topbar-dropdown-divider"></div>

                    <a href="{{ route('profile.edit') }}"
                       class="topbar-dropdown-item" role="menuitem">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
                        </svg>
                        Edit Profil
                    </a>

                    <div class="topbar-dropdown-divider"></div>

                    <button class="topbar-dropdown-item topbar-dropdown-item--danger"
                            onclick="confirmLogout()" role="menuitem">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Keluar
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="admin-content">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif
    @if (session('error'))
        <div class="admin-content">
            <div class="alert alert-danger">{{ session('error') }}</div>
        </div>
    @endif

    {{-- Page content --}}
    @yield('content')

</main>

{{-- Form logout tersembunyi --}}
<form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
    @csrf
</form>

{{-- Modal logout --}}
<div class="modal-overlay" id="modal-logout">
    <div class="modal-box">
        <div class="modal-title">Keluar dari akun?</div>
        <div class="modal-desc">
            Anda akan keluar sebagai <strong>{{ Auth::user()->name }}</strong>.
        </div>
        <div class="modal-actions">
            <button class="btn btn-danger btn-full"
                    onclick="document.getElementById('logout-form').submit()">
                Ya, Keluar
            </button>
            <button class="btn btn-secondary btn-full"
                    onclick="document.getElementById('modal-logout').classList.remove('open')">
                Batal
            </button>
        </div>
    </div>
</div>

@vite(['resources/js/admin/sidebar.js', 'resources/js/admin/topbar.js'])
@stack('scripts')

</body>
</html>
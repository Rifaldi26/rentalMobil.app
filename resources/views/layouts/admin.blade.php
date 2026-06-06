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

{{-- Sidebar --}}
@include('admin.partials.desktop-sidebar-admin')

{{-- Main --}}
<main class="admin-main" id="admin-main-content">

    {{-- Topbar --}}
    <div class="admin-topbar">
        <div>
            <div class="admin-page-title">@yield('page-title')</div>
            @hasSection('page-subtitle')
                <div class="admin-page-subtitle">@yield('page-subtitle')</div>
            @endif
        </div>

        <div class="admin-topbar-actions">

            {{-- Slot aksi tambahan per halaman --}}
            @yield('header-actions')

            {{-- Avatar dropdown --}}
            <div class="topbar-avatar-wrap" id="topbar-avatar-wrap">
                <button class="topbar-avatar" id="topbar-avatar-btn"
                        onclick="toggleAvatarDropdown()" aria-haspopup="true" aria-expanded="false">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </button>

                <div class="topbar-dropdown" id="topbar-dropdown" role="menu">
                    {{-- User info --}}
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

                    <a href="{{ route('profile.edit') }}" class="topbar-dropdown-item" role="menuitem">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
                        </svg>
                        Edit Profil
                    </a>

                    <div class="topbar-dropdown-divider"></div>

                    <button class="topbar-dropdown-item topbar-dropdown-item--danger"
                            onclick="confirmLogout()" role="menuitem">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

{{-- Logout form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

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
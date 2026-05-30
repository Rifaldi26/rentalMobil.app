<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — RentWheels Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

{{-- Mobile Overlay --}}
<div class="overlay" :class="{ show: sidebarOpen }" @click="sidebarOpen = false"></div>

{{-- ═══ SIDEBAR ═════════════════════════════════════════════ --}}
<aside class="sidebar" :class="{ open: sidebarOpen }">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo-text">
            Rent<span>Wheels</span>
        </a>
        <div class="sidebar-logo-role">Admin Panel</div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        @include('layouts.partials.sidebar-admin')
    </nav>

    {{-- User Info --}}
    <div class="sidebar-user">
        <div class="sidebar-user-info">
            <img src="{{ auth()->user()?->avatar_url }}"
                 alt="{{ auth()->user()?->name }}"
                 class="avatar avatar-md">
            <div style="flex:1;min-width:0;">
                <div class="sidebar-user-name">{{ auth()->user()?->name }}</div>
                <div class="sidebar-user-email">{{ auth()->user()?->email }}</div>
            </div>
        </div>
        <a href="{{ route('profile.edit') }}" class="sidebar-link" style="margin-top:4px;">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Edit Profil
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="color:rgba(255,100,100,.65);">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- ═══ TOPBAR ═══════════════════════════════════════════════ --}}
<header class="topbar">
    <div style="display:flex;align-items:center;gap:14px;">
        <button @click="sidebarOpen = !sidebarOpen"
                class="topbar-menu-btn"
                style="display:none;padding:8px;border:none;background:none;cursor:pointer;border-radius:var(--radius-sm);color:var(--gray-600);"
                aria-label="Toggle menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
    </div>

    <div style="display:flex;align-items:center;gap:10px;">
        {{-- Notification Bell --}}
        <div x-data="{ open: false }" style="position:relative;">
            <button @click="open = !open"
                    style="position:relative;padding:9px;border:1.5px solid var(--gray-200);border-radius:var(--radius-md);background:#fff;cursor:pointer;color:var(--gray-600);transition:all .15s;"
                    onmouseover="this.style.background='var(--gray-50)'"
                    onmouseout="this.style.background='#fff'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                @php $pendingCount = \App\Models\Booking::where('status', \App\Enums\BookingStatus::Pending)->count(); @endphp
                @if($pendingCount > 0)
                <span style="position:absolute;top:4px;right:4px;width:8px;height:8px;background:var(--danger);border-radius:50%;border:2px solid #fff;"></span>
                @endif
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                 style="position:absolute;right:0;top:calc(100% + 8px);width:300px;background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius-md);box-shadow:var(--shadow-md);overflow:hidden;z-index:99;">
                <div style="padding:12px 16px;border-bottom:1px solid var(--gray-100);font-weight:700;font-size:.875rem;display:flex;justify-content:space-between;align-items:center;">
                    Notifikasi
                    @if($pendingCount > 0)
                    <span class="badge-dot">{{ $pendingCount }}</span>
                    @endif
                </div>
                @if($pendingCount > 0)
                <a href="{{ route('admin.bookings.index') }}" class="dropdown-item" style="padding:14px 16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--warning)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <div>
                        <div style="font-weight:700;font-size:.85rem;">{{ $pendingCount }} Pemesanan Menunggu</div>
                        <div style="font-size:.75rem;color:var(--gray-500);">Perlu konfirmasi segera</div>
                    </div>
                </a>
                @else
                <div style="padding:24px 16px;text-align:center;color:var(--gray-400);font-size:.875rem;">
                    Tidak ada notifikasi baru
                </div>
                @endif
            </div>
        </div>

        {{-- Avatar Dropdown --}}
        <div x-data="{ open: false }" style="position:relative;">
            <button @click="open = !open" class="user-pill" type="button">
                <img src="{{ auth()->user()?->avatar_url }}" class="avatar avatar-sm" alt="">
                <span class="hide-mobile">{{ Str::before(auth()->user()?->name, ' ') }}</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                 class="dropdown-menu" style="right:0;top:calc(100% + 8px);">
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Edit Profil
                </a>
                <a href="{{ route('admin.reports.index') }}" class="dropdown-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Laporan
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- ═══ MAIN CONTENT ════════════════════════════════════════ --}}
<main class="admin-wrapper">
    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success" data-auto-dismiss="4000">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger" data-auto-dismiss="5000">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning" data-auto-dismiss="5000">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
        {{ session('warning') }}
    </div>
    @endif

    {{ $slot }}
</main>

{{-- ═══ BOTTOM NAV (Mobile) ════════════════════════════════ --}}
@include('admin.partials.bottom-nav')

{{-- Toast --}}
<div id="toast-container" class="toast-container"></div>

<style>
@media (max-width: 1024px) { .topbar-menu-btn { display: flex !important; } }
</style>

@stack('scripts')
</body>
</html>

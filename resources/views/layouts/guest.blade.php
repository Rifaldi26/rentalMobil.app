<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ mobileMenu: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sewa Kendaraan Mudah & Terpercaya' }} — RentWheels</title>
    <meta name="description" content="RentWheels – Platform sewa kendaraan terpercaya. Armada terawat, harga transparan, booking online 24 jam.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="background:#fff;">

{{-- ═══ NAVBAR ══════════════════════════════════════════════ --}}
<header class="navbar">
    <div class="container">
        <div class="navbar-inner">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="navbar-brand">
                Rent<span>Wheels</span>
            </a>

            {{-- Nav Links (desktop) --}}
            <nav class="navbar-links hide-mobile">
                <a href="{{ route('home') }}"
                   class="navbar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('cars.index') }}"
                   class="navbar-link {{ request()->routeIs('cars.*') ? 'active' : '' }}">
                    Kendaraan
                </a>
                <a href="#cara-pemesanan" class="navbar-link">Cara Pemesanan</a>
                <a href="#tentang-kami" class="navbar-link">Tentang Kami</a>
                <a href="#kontak" class="navbar-link">Kontak</a>
            </nav>

            {{-- Actions --}}
            <div class="navbar-actions">
                @auth
                    {{-- Notif bell (customer) --}}
                    @if(auth()->user()->isCustomer())
                    <a href="{{ route('customer.bookings.index') }}"
                       class="btn btn-sm btn-ghost hide-mobile"
                       style="gap:6px;">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Pemesanan Saya
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="btn btn-sm btn-secondary hide-mobile"
                       style="gap:6px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Admin Panel
                    </a>
                    @endif

                    {{-- Avatar Dropdown --}}
                    <div x-data="{ open: false }" style="position:relative;">
                        <button @click="open = !open" class="user-pill" type="button">
                            <img src="{{ auth()->user()->avatar_url }}" class="avatar avatar-sm" alt="">
                            <span>{{ Str::before(auth()->user()->name, ' ') }}</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                             class="dropdown-menu" style="right:0;top:calc(100% + 8px);">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Profil Saya
                            </a>
                            @if(auth()->user()->isCustomer())
                            <a href="{{ route('customer.bookings.index') }}" class="dropdown-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Pemesanan Saya
                            </a>
                            @endif
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

                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-ghost hide-mobile">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        Daftar Gratis
                    </a>
                @endauth

                {{-- Hamburger --}}
                <button @click="mobileMenu = !mobileMenu"
                        class="btn btn-sm btn-ghost"
                        style="padding:8px;display:none;"
                        id="mobile-menu-btn"
                        aria-label="Menu">
                    <svg x-show="!mobileMenu" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg x-show="mobileMenu" x-cloak width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-cloak x-transition
             style="border-top:1px solid var(--gray-100);padding:16px 0 20px;">
            <nav style="display:flex;flex-direction:column;gap:2px;margin-bottom:16px;">
                <a href="{{ route('home') }}" class="navbar-link" style="display:flex;align-items:center;gap:10px;padding:12px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Beranda
                </a>
                <a href="{{ route('cars.index') }}" class="navbar-link" style="display:flex;align-items:center;gap:10px;padding:12px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect width="13" height="8" x="8" y="13" rx="2"/></svg>
                    Kendaraan
                </a>
                <a href="#cara-pemesanan" class="navbar-link" style="display:flex;align-items:center;gap:10px;padding:12px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Cara Pemesanan
                </a>
                <a href="#tentang-kami" class="navbar-link" style="display:flex;align-items:center;gap:10px;padding:12px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Tentang Kami
                </a>
            </nav>
            @auth
                @if(auth()->user()->isCustomer())
                <a href="{{ route('customer.bookings.index') }}" class="btn btn-secondary btn-block" style="margin-bottom:8px;">
                    Pemesanan Saya
                </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-block" style="color:var(--danger);">Keluar</button>
                </form>
            @else
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <a href="{{ route('login') }}" class="btn btn-secondary">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</header>

{{-- Flash Messages --}}
@if(session('success'))
<div class="container" style="padding-top:16px;">
    <div class="alert alert-success" data-auto-dismiss="4000">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
</div>
@endif
@if(session('error'))
<div class="container" style="padding-top:16px;">
    <div class="alert alert-danger" data-auto-dismiss="5000">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
</div>
@endif
@if(session('warning'))
<div class="container" style="padding-top:16px;">
    <div class="alert alert-warning" data-auto-dismiss="5000">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        {{ session('warning') }}
    </div>
</div>
@endif

{{-- Main Content --}}
{{ $slot }}

{{-- ═══ FOOTER ══════════════════════════════════════════════ --}}
<footer id="kontak" style="background:var(--navy-900);color:rgba(255,255,255,.6);padding:64px 0 32px;margin-top:80px;">
    <div class="container">
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:48px;margin-bottom:52px;">

            {{-- Brand --}}
            <div>
                <div style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;color:#fff;margin-bottom:14px;letter-spacing:-0.03em;">
                    Rent<span style="color:var(--brand-400);">Wheels</span>
                </div>
                <p style="font-size:.875rem;line-height:1.8;max-width:280px;color:rgba(255,255,255,.55);">
                    Platform sewa kendaraan terpercaya dengan armada terawat, harga transparan, dan pelayanan prima 24 jam.
                </p>
                <div style="display:flex;gap:10px;margin-top:20px;">
                    {{-- Sosmed icons --}}
                    @foreach([
                        ['label'=>'Facebook','path'=>'M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z'],
                        ['label'=>'Instagram','path'=>'M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37zm1.5-4.87h.01'],
                        ['label'=>'Twitter','path'=>'M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.5 6-3.8 1.1 0 3-1.2 4-2z'],
                        ['label'=>'YouTube','path'=>'M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.5C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z'],
                    ] as $social)
                    <a href="#" aria-label="{{ $social['label'] }}"
                       style="width:36px;height:36px;background:rgba(255,255,255,.08);border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background .2s;"
                       onmouseover="this.style.background='rgba(20,184,166,.3)'"
                       onmouseout="this.style.background='rgba(255,255,255,.08)'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $social['path'] }}"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Layanan --}}
            <div>
                <div style="font-family:'Sora',sans-serif;font-weight:700;color:#fff;margin-bottom:18px;font-size:.9rem;">Layanan</div>
                <div style="display:flex;flex-direction:column;gap:10px;font-size:.875rem;">
                    @foreach([
                        ['label'=>'Cari Kendaraan','route'=>'cars.index'],
                        ['label'=>'Cara Pemesanan','anchor'=>'#cara-pemesanan'],
                        ['label'=>'Daftar Akun','route'=>'register'],
                        ['label'=>'Masuk','route'=>'login'],
                    ] as $link)
                    <a href="{{ isset($link['route']) ? route($link['route']) : $link['anchor'] }}"
                       style="color:rgba(255,255,255,.55);display:flex;align-items:center;gap:8px;transition:color .2s;"
                       onmouseover="this.style.color='#fff'"
                       onmouseout="this.style.color='rgba(255,255,255,.55)'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        {{ $link['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Bantuan --}}
            <div>
                <div style="font-family:'Sora',sans-serif;font-weight:700;color:#fff;margin-bottom:18px;font-size:.9rem;">Bantuan</div>
                <div style="display:flex;flex-direction:column;gap:10px;font-size:.875rem;">
                    @foreach(['Pusat Bantuan','FAQ','Kebijakan Privasi','Syarat & Ketentuan','Kebijakan Pembatalan'] as $item)
                    <a href="#" style="color:rgba(255,255,255,.55);display:flex;align-items:center;gap:8px;transition:color .2s;"
                       onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.55)'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        {{ $item }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Kontak --}}
            <div>
                <div style="font-family:'Sora',sans-serif;font-weight:700;color:#fff;margin-bottom:18px;font-size:.9rem;">Kontak Kami</div>
                <div style="display:flex;flex-direction:column;gap:12px;font-size:.875rem;">
                    <div style="display:flex;align-items:flex-start;gap:10px;color:rgba(255,255,255,.55);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        halo@rentwheels.id
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:10px;color:rgba(255,255,255,.55);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6.08 6.08l.82-.82a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2.01z"/></svg>
                        +62 21 1234 5678
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:10px;color:rgba(255,255,255,.55);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Jl. Sudirman No.1, Jakarta Pusat
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:10px;color:rgba(255,255,255,.55);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Senin–Minggu, 07.00–22.00 WIB
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;font-size:.8rem;">
            <span>© {{ date('Y') }} RentWheels. Semua hak dilindungi.</span>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <span style="background:rgba(255,255,255,.07);padding:4px 12px;border-radius:var(--radius-full);display:flex;align-items:center;gap:6px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    SSL Secured
                </span>
                <span style="background:rgba(255,255,255,.07);padding:4px 12px;border-radius:var(--radius-full);display:flex;align-items:center;gap:6px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Transaksi Aman
                </span>
            </div>
        </div>
    </div>
</footer>

{{-- Toast Container --}}
<div id="toast-container" class="toast-container"></div>

<style>
@media (max-width: 768px) {
    #mobile-menu-btn { display: flex !important; }
    .hide-mobile { display: none !important; }
    footer .container > div:first-child {
        grid-template-columns: 1fr 1fr !important;
        gap: 28px !important;
    }
}
@media (max-width: 480px) {
    footer .container > div:first-child {
        grid-template-columns: 1fr !important;
    }
}
</style>

@stack('scripts')
</body>
</html>

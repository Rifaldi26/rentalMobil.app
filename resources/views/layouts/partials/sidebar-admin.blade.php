{{-- layouts/partials/sidebar-admin.blade.php --}}
{{-- Menu sidebar admin pemilik tunggal RentWheels --}}
@php $route = request()->route()?->getName() ?? ''; @endphp

{{-- ── UTAMA ─────────────────────────────────────────────── --}}
<div class="sidebar-group-label">Utama</div>

<a href="{{ route('admin.dashboard') }}"
   class="sidebar-link {{ Str::startsWith($route, 'admin.dashboard') ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
    </svg>
    Dashboard
</a>

{{-- ── OPERASIONAL ────────────────────────────────────────── --}}
<div class="sidebar-group-label" style="margin-top:16px;">Operasional</div>

<a href="{{ route('admin.bookings.index') }}"
   class="sidebar-link {{ Str::startsWith($route, 'admin.bookings') ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
    </svg>
    Pemesanan
    @php $pending = \App\Models\Booking::where('status', \App\Enums\BookingStatus::Pending)->count(); @endphp
    @if($pending > 0)
        <span class="badge-dot">{{ $pending }}</span>
    @endif
</a>

<a href="{{ route('admin.schedule.index') }}"
   class="sidebar-link {{ Str::startsWith($route, 'admin.schedule') ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
        <line x1="8" y1="14" x2="8" y2="14"/><line x1="12" y1="14" x2="12" y2="14"/>
        <line x1="8" y1="18" x2="8" y2="18"/><line x1="12" y1="18" x2="12" y2="18"/>
    </svg>
    Jadwal & Ketersediaan
</a>

{{-- ── ARMADA ─────────────────────────────────────────────── --}}
<div class="sidebar-group-label" style="margin-top:16px;">Armada</div>

<a href="{{ route('admin.vehicles.index') }}"
   class="sidebar-link {{ Str::startsWith($route, 'admin.vehicles') ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/>
        <rect width="13" height="8" x="8" y="13" rx="2"/>
    </svg>
    Kelola Armada
    @php $unverified = \App\Models\Vehicle::where('is_verified', false)->count(); @endphp
    @if($unverified > 0)
        <span class="badge-dot">{{ $unverified }}</span>
    @endif
</a>

<a href="{{ route('admin.vehicles.create') }}"
   class="sidebar-link {{ $route === 'admin.vehicles.create' ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/>
        <line x1="8" y1="12" x2="16" y2="12"/>
    </svg>
    Tambah Kendaraan
</a>

{{-- ── PELANGGAN ──────────────────────────────────────────── --}}
<div class="sidebar-group-label" style="margin-top:16px;">Pelanggan</div>

<a href="{{ route('admin.users.index') }}"
   class="sidebar-link {{ Str::startsWith($route, 'admin.users') ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    Data Pelanggan
</a>

<a href="{{ route('admin.chat.index', ['tab' => 'chat']) }}"
   class="sidebar-link {{ Str::startsWith($route, 'admin.chat') ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    Pesan Pelanggan
    @php
        $unreadMessages = \App\Models\Message::whereHas('booking', fn($q) =>
            $q->where('status', '!=', 'selesai')
        )->where('sender_id', '!=', auth()->id())
         ->whereNull('read_at')->count();
    @endphp
    @if($unreadMessages > 0)
        <span class="badge-dot">{{ $unreadMessages }}</span>
    @endif
</a>

{{-- ── KEUANGAN ───────────────────────────────────────────── --}}
<div class="sidebar-group-label" style="margin-top:16px;">Keuangan</div>

<a href="{{ route('admin.withdrawals.index') }}"
   class="sidebar-link {{ Str::startsWith($route, 'admin.withdrawals') ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="1" y="4" width="22" height="16" rx="2"/>
        <line x1="1" y1="10" x2="23" y2="10"/>
    </svg>
    Keuangan & Saldo
</a>

<a href="{{ route('admin.reports.index') }}"
   class="sidebar-link {{ Str::startsWith($route, 'admin.reports') ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
        <line x1="6" y1="20" x2="6" y2="14"/>
    </svg>
    Laporan & Analitik
</a>

{{-- ── SISTEM ─────────────────────────────────────────────── --}}
<div class="sidebar-group-label" style="margin-top:16px;">Sistem</div>

<a href="{{ route('admin.audit.index') }}"
   class="sidebar-link {{ Str::startsWith($route, 'admin.audit') ? 'active' : '' }}">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
    </svg>
    Log Aktivitas
</a>

<a href="{{ route('home') }}" target="_blank"
   class="sidebar-link">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
        <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
    </svg>
    Lihat Situs
</a>

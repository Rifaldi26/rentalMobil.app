<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Rental Mobil</title>
    @vite([
        'resources/css/dashboard.css',
        'resources/js/dashboard.js'
    ])
</head>
<body>

{{-- ═══ TOP NAV ═══════════════════════════════════════════ --}}
<nav class="nav">
    <div class="nav-brand">Rental<span>Mobil</span></div>
    <button class="nav-icon" onclick="showToast('Tidak ada notifikasi baru')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <span class="badge"></span>
    </button>
</nav>

{{-- ═══ HALAMAN BERANDA ════════════════════════════════════ --}}
<div class="page active content" id="page-home">

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-greeting">Selamat datang, {{ Auth::user()->name }} 👋</div>
        <div class="hero-title">Mau ke mana <em>hari ini?</em></div>
    </div>

    {{-- Search Card --}}
    <div style="padding: 0 20px; margin-top: -48px; position: relative; z-index: 10;">
        <div class="search-card">
            <div class="search-row">
                <div class="search-field full" onclick="showToast('Pilih kota tujuan')">
                    <div class="sf-label">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        </svg>
                        Kota
                    </div>
                    <div class="sf-value">Jakarta Selatan</div>
                </div>
                <div class="search-field" onclick="showToast('Pilih tanggal mulai')">
                    <div class="sf-label">📅 Mulai</div>
                    <div class="sf-value">{{ now()->format('d M Y') }}</div>
                </div>
                <div class="search-field" onclick="showToast('Pilih tanggal selesai')">
                    <div class="sf-label">📅 Selesai</div>
                    <div class="sf-value">{{ now()->addDays(3)->format('d M Y') }}</div>
                </div>
            </div>
            <button class="search-btn" onclick="showToast('Mencari kendaraan tersedia...')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                Cari Kendaraan
            </button>
        </div>
    </div>

    <div style="height: 24px;"></div>

    {{-- Pemesanan Aktif --}}
    @php
        $pemesananAktif = Auth::user()->pemesanans()
            ->with('mobil')
            ->whereIn('status', ['pending', 'dikonfirmasi'])
            ->latest()
            ->first();
    @endphp

    @if ($pemesananAktif)
        <div class="section" style="padding-top: 0;">
            <div class="booking-banner" onclick="switchPage('page-bookings')">
                <div class="booking-icon">🚗</div>
                <div class="booking-info">
                    <p>Pemesanan aktif</p>
                    <strong>{{ $pemesananAktif->mobil->nama }} {{ $pemesananAktif->mobil->tahun }}</strong>
                    <div style="font-size: 12px; color: var(--gray-500); margin-top: 2px;">
                        {{ $pemesananAktif->tanggal_mulai->format('d M') }} –
                        {{ $pemesananAktif->tanggal_selesai->format('d M Y') }}
                    </div>
                </div>
                <span class="booking-status status-{{ $pemesananAktif->status === 'dikonfirmasi' ? 'progress' : 'pending' }}">
                    {{ $pemesananAktif->status === 'dikonfirmasi' ? 'Berjalan' : 'Menunggu' }}
                </span>
            </div>
        </div>
    @endif

    {{-- Kategori --}}
    <div class="section">
        <div class="section-header">
            <span class="section-title">Jenis Kendaraan</span>
        </div>
        <div class="categories">
            <div class="cat-chip active" onclick="filterCategory(this)"><div class="cat-icon">🚙</div><div class="cat-label">Semua</div></div>
            <div class="cat-chip" onclick="filterCategory(this)"><div class="cat-icon">🚐</div><div class="cat-label">MPV</div></div>
            <div class="cat-chip" onclick="filterCategory(this)"><div class="cat-icon">🛻</div><div class="cat-label">SUV</div></div>
            <div class="cat-chip" onclick="filterCategory(this)"><div class="cat-icon">🚗</div><div class="cat-label">Sedan</div></div>
            <div class="cat-chip" onclick="filterCategory(this)"><div class="cat-icon">🏎️</div><div class="cat-label">City Car</div></div>
            <div class="cat-chip" onclick="filterCategory(this)"><div class="cat-icon">🚚</div><div class="cat-label">Pickup</div></div>
        </div>
    </div>

    {{-- Alert Session --}}
    @if (session('success'))
        <div class="section" style="padding-bottom:0;">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#16a34a;">
                ✅ {{ session('success') }}
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="section" style="padding-bottom:0;">
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#dc2626;">
                ⚠️ {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Daftar Mobil --}}
    <div class="section">
        <div class="section-header">
            <span class="section-title">Mobil Tersedia</span>
            <button class="see-all" onclick="showToast('Lihat semua kendaraan')">Lihat semua</button>
        </div>

        @php
            $mobils = \App\Models\Mobil::where('status', 'tersedia')->latest()->take(6)->get();
        @endphp

        @if ($mobils->isEmpty())
            <div style="text-align:center;padding:40px 20px;color:var(--gray-500);">
                <div style="font-size:48px;margin-bottom:12px;">🚗</div>
                <div style="font-weight:600;">Belum ada mobil tersedia</div>
                <div style="font-size:13px;margin-top:4px;">Silakan cek kembali nanti</div>
            </div>
        @else
            <div class="cars-grid">
                @foreach ($mobils as $mobil)
                    <div class="car-card" onclick="window.location.href='{{ route('pemesanan.create') }}?mobil_id={{ $mobil->id }}'">
                        <div class="car-image-wrap">
                            @if ($mobil->foto)
                                <img src="{{ asset('storage/' . $mobil->foto) }}"
                                     alt="{{ $mobil->nama }}"
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <div class="car-image-placeholder">🚗</div>
                            @endif
                            <span class="car-badge">{{ $mobil->merek }}</span>
                            <button class="car-wishlist" onclick="toggleWishlist(event, this)">🤍</button>
                        </div>
                        <div class="car-body">
                            <div class="car-name">{{ $mobil->nama }}</div>
                            <div class="car-sub">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                </svg>
                                Tahun {{ $mobil->tahun }} · {{ $mobil->plat_nomor }}
                            </div>
                            <div class="car-footer">
                                <div class="car-price-wrap">
                                    <div class="car-price">Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}</div>
                                    <div class="car-price-unit">per hari</div>
                                </div>
                                <button class="btn-book" onclick="bookCar(event, {{ $mobil->id }})">Pesan</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div style="height: 20px;"></div>
</div>{{-- /page-home --}}


{{-- ═══ HALAMAN PEMESANAN ══════════════════════════════════ --}}
<div class="page content" id="page-bookings">
    <div class="section">
        <div class="section-title" style="margin-bottom:16px;">Pemesanan Saya</div>

        {{-- Filter tabs --}}
        <div style="display:flex;gap:8px;margin-bottom:16px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none;">
            <button class="cat-chip active" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBooking('semua', this)">Semua</button>
            <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBooking('dikonfirmasi', this)">Berjalan</button>
            <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBooking('pending', this)">Menunggu</button>
            <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBooking('selesai', this)">Selesai</button>
            <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBooking('dibatalkan', this)">Dibatalkan</button>
        </div>

        @php
            $semuaPemesanan = Auth::user()->pemesanans()->with('mobil')->latest()->get();
        @endphp

        @if ($semuaPemesanan->isEmpty())
            <div style="text-align:center;padding:60px 20px;color:var(--gray-500);">
                <div style="font-size:48px;margin-bottom:12px;">📋</div>
                <div style="font-weight:600;">Belum ada pemesanan</div>
                <div style="font-size:13px;margin-top:4px;">Yuk mulai sewa mobil pertama kamu!</div>
                <button class="btn-book" style="margin-top:16px;padding:10px 24px;"
                    onclick="switchPage('page-home', 'nav-home')">
                    Cari Mobil
                </button>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:12px;" id="booking-list">
                @foreach ($semuaPemesanan as $p)
                    @php
                        $durasi = $p->tanggal_mulai->diffInDays($p->tanggal_selesai);
                        $statusClass = match($p->status) {
                            'dikonfirmasi' => 'status-progress',
                            'pending'      => 'status-pending',
                            'selesai'      => 'status-confirmed',
                            'dibatalkan'   => 'status-cancelled',
                            default        => ''
                        };
                        $statusLabel = match($p->status) {
                            'dikonfirmasi' => 'Berjalan',
                            'pending'      => 'Menunggu',
                            'selesai'      => 'Selesai',
                            'dibatalkan'   => 'Dibatalkan',
                            default        => $p->status
                        };
                    @endphp
                    <div class="booking-item" data-status="{{ $p->status }}">
                        <div class="booking-item-header">
                            <div>
                                <div class="booking-item-name">{{ $p->mobil->nama }}</div>
                                <div class="booking-item-code">
                                    {{ $p->mobil->plat_nomor }} · {{ $durasi }} hari
                                </div>
                            </div>
                            <span class="booking-status {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="booking-item-body">
                            <span>
                                {{ $p->tanggal_mulai->format('d M') }} –
                                {{ $p->tanggal_selesai->format('d M Y') }}
                            </span>
                            <strong style="color:var(--brand-400)">
                                Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                            </strong>
                        </div>
                        {{-- Tombol Batalkan (hanya jika pending) --}}
                        @if ($p->status === 'pending')
                            <div style="margin-top:10px;">
                                <form action="{{ route('pemesanan.cancel', $p) }}" method="POST"
                                    onsubmit="return confirm('Batalkan pemesanan {{ $p->mobil->nama }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        style="width:100%;padding:9px;background:#fef2f2;color:#dc2626;border:none;border-radius:var(--radius-sm);font-family:var(--font);font-size:13px;font-weight:700;cursor:pointer;">
                                        ❌ Batalkan Pemesanan
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>


{{-- ═══ HALAMAN PROFIL ════════════════════════════════════ --}}
<div class="page content" id="page-profile">
    <div style="padding:24px 20px;">
        {{-- Profile Header --}}
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
            <div style="width:64px;height:64px;border-radius:50%;background:var(--brand-100);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:var(--brand-600);">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div>
                <div style="font-size:18px;font-weight:700;">{{ Auth::user()->name }}</div>
                <div style="font-size:13px;color:var(--gray-500);">{{ Auth::user()->email }}</div>
                <div style="font-size:12px;color:var(--gray-500);margin-top:2px;">
                    {{ Auth::user()->no_hp ?? '-' }}
                </div>
                <div style="font-size:12px;color:var(--brand-400);font-weight:600;margin-top:2px;">
                    {{ ucfirst(Auth::user()->role) }} · Bergabung {{ Auth::user()->created_at->translatedFormat('M Y') }}
                </div>
            </div>
        </div>

        {{-- Statistik --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:24px;">
            @php
                $totalPemesanan  = Auth::user()->pemesanans()->count();
                $pemesananSelesai = Auth::user()->pemesanans()->where('status','selesai')->count();
                $totalBelanja    = Auth::user()->pemesanans()->where('status','selesai')->sum('total_harga');
            @endphp
            <div style="background:var(--brand-50);border-radius:var(--radius-md);padding:12px;text-align:center;">
                <div style="font-size:20px;font-weight:800;color:var(--brand-400);">{{ $totalPemesanan }}</div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Total Pesan</div>
            </div>
            <div style="background:#f0fdf4;border-radius:var(--radius-md);padding:12px;text-align:center;">
                <div style="font-size:20px;font-weight:800;color:var(--success);">{{ $pemesananSelesai }}</div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Selesai</div>
            </div>
            <div style="background:#fff7ed;border-radius:var(--radius-md);padding:12px;text-align:center;">
                <div style="font-size:14px;font-weight:800;color:var(--accent-500);">
                    Rp {{ number_format($totalBelanja/1000, 0, ',', '.') }}k
                </div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Total Bayar</div>
            </div>
        </div>

        {{-- Menu --}}
        <div style="background:var(--white);border-radius:var(--radius-md);border:1px solid var(--gray-100);overflow:hidden;">
            <div onclick="showToast('Edit profil')"
                 style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);cursor:pointer;">
                <span style="font-size:14px;font-weight:600;">👤 Edit Profil</span>
                <span style="color:var(--gray-400);">›</span>
            </div>
            <div onclick="showToast('Bantuan & Dukungan')"
                 style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);cursor:pointer;">
                <span style="font-size:14px;font-weight:600;">❓ Bantuan</span>
                <span style="color:var(--gray-400);">›</span>
            </div>
            <div onclick="confirmLogout()"
                 style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;cursor:pointer;">
                <span style="font-size:14px;font-weight:600;color:var(--danger);">🚪 Keluar</span>
                <span style="color:var(--gray-400);">›</span>
            </div>
        </div>
    </div>
</div>


{{-- ═══ BOTTOM NAV ════════════════════════════════════════ --}}
<nav class="bottom-nav" id="bottom-nav">
    <button class="nav-item active" id="nav-home" onclick="switchPage('page-home', 'nav-home')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Beranda
    </button>
    <button class="nav-item" id="nav-bookings" onclick="switchPage('page-bookings', 'nav-bookings')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" x2="16" y1="2" y2="6"/>
            <line x1="8" x2="8" y1="2" y2="6"/>
            <line x1="3" x2="21" y1="10" y2="10"/>
        </svg>
        Booking
    </button>
    <div class="nav-center">
        <button class="nav-center-btn" onclick="switchPage('page-home', 'nav-home')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
        </button>
    </div>
    <a href="{{ route('users.chat') }}"
       class="nav-item {{ request()->routeIs('users.chat') ? 'active' : '' }}"
       style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        Pesan
    </a>
    <button class="nav-item" id="nav-profile" onclick="switchPage('page-profile', 'nav-profile')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"/>
            <path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
        </svg>
        Profil
    </button>
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
let toastTimer;

// ─── Switch Page ──────────────────────────────────────────
function switchPage(pageId, navId) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById(pageId).classList.add('active');
    if (navId) document.getElementById(navId)?.classList.add('active');
}

// ─── Filter Category ──────────────────────────────────────
function filterCategory(el) {
    document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    showToast('Filter: ' + el.querySelector('.cat-label')?.textContent);
}

// ─── Filter Booking ───────────────────────────────────────
function filterBooking(status, el) {
    document.querySelectorAll('#page-bookings .cat-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.booking-item').forEach(item => {
        if (status === 'semua' || item.dataset.status === status) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

// ─── Wishlist Toggle ──────────────────────────────────────
function toggleWishlist(e, btn) {
    e.stopPropagation();
    btn.textContent = btn.textContent === '🤍' ? '❤️' : '🤍';
}

// ─── Book Car ─────────────────────────────────────────────
function bookCar(e, mobilId) {
    e.stopPropagation();
    window.location.href = `{{ url('/pemesanan/create') }}?mobil_id=${mobilId}`;
}

// ─── Logout ───────────────────────────────────────────────
function confirmLogout() {
    const modal = document.getElementById('modal-logout');
    modal.style.display = 'flex';
}

// ─── Toast ────────────────────────────────────────────────
function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = `toast ${type} show`;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
}
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda — Rental Mobil</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/dashboard.css'])
    <style>
        /* [CSS Sebelumnya Tetap Sama] */
        .search-input-wrap { position: relative; margin-bottom: 12px; }
        .search-input-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--gray-400); }
        .search-input-wrap input { width: 100%; padding: 11px 13px 11px 38px; border: 1.5px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font); font-size: 14px; color: var(--gray-900); background: #fff; transition: border-color .15s; box-sizing: border-box; }
        .search-input-wrap input:focus { outline: none; border-color: var(--brand-400); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
        .filter-bar { display: flex; align-items: center; gap: 8px; padding: 0 20px; margin-bottom: 16px; overflow-x: auto; scrollbar-width: none; }
        .filter-bar::-webkit-scrollbar { display: none; }
        .filter-pill { flex-shrink: 0; display: flex; align-items: center; gap: 5px; padding: 8px 14px; background: #fff; border: 1.5px solid var(--gray-200); border-radius: 100px; font-family: var(--font); font-size: 12px; font-weight: 600; color: var(--gray-700); cursor: pointer; transition: all .15s; white-space: nowrap; }
        .filter-pill:hover, .filter-pill.active { border-color: var(--brand-400); background: var(--brand-50); color: var(--brand-400); }
        .filter-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--brand-400); display: none; }
        .filter-pill.active .dot { display: block; }
        .filter-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 200; }
        .filter-overlay.open { display: block; }
        .filter-sheet { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-radius: 24px 24px 0 0; padding: 20px 20px 40px; z-index: 201; transform: translateY(100%); transition: transform .3s cubic-bezier(.32,1,.58,1); max-height: 85vh; overflow-y: auto; }
        .filter-sheet.open { transform: translateY(0); }
        .filter-sheet-handle { width: 40px; height: 4px; background: var(--gray-200); border-radius: 2px; margin: 0 auto 20px; }
        .filter-section-title { font-size: 13px; font-weight: 700; color: var(--gray-700); margin-bottom: 10px; text-transform: uppercase; letter-spacing: .5px; }
        .price-slider-wrap { padding: 4px 0 16px; }
        .price-range-labels { display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; color: var(--brand-400); margin-bottom: 10px; }
        .range-track { position: relative; height: 4px; background: var(--gray-200); border-radius: 2px; margin: 16px 0; }
        .range-fill { position: absolute; height: 4px; background: var(--brand-400); border-radius: 2px; }
        input[type=range] { position: absolute; width: 100%; height: 4px; -webkit-appearance: none; appearance: none; background: transparent; pointer-events: none; top: 0; left: 0; }
        input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 20px; height: 20px; border-radius: 50%; background: var(--brand-400); border: 2px solid #fff; box-shadow: 0 2px 6px rgba(37,99,235,.4); pointer-events: all; cursor: pointer; }
        .merek-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
        .merek-chip { padding: 8px 16px; border-radius: 100px; border: 1.5px solid var(--gray-200); background: #fff; font-family: var(--font); font-size: 12px; font-weight: 600; color: var(--gray-700); cursor: pointer; transition: all .15s; }
        .merek-chip:hover, .merek-chip.active { border-color: var(--brand-400); background: var(--brand-50); color: var(--brand-400); }
        .lokasi-card { display: flex; align-items: center; gap: 14px; background: var(--brand-50); border: 1.5px solid var(--brand-100); border-radius: var(--radius-md); padding: 16px; margin-bottom: 16px; }
        .lokasi-card-icon { width: 44px; height: 44px; border-radius: 50%; background: var(--brand-400); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .lokasi-card-text { flex: 1; min-width: 0; }
        .lokasi-card-label { font-size: 11px; font-weight: 600; color: var(--brand-400); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .lokasi-card-value { font-size: 14px; font-weight: 700; color: var(--gray-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .lokasi-card-sub { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
        .btn-maps { display: flex; align-items: center; gap: 8px; width: 100%; padding: 13px 16px; background: #fff; border: 1.5px solid var(--gray-200); border-radius: var(--radius-md); font-family: var(--font); font-size: 13px; font-weight: 700; color: var(--gray-700); cursor: pointer; transition: all .15s; text-decoration: none; margin-bottom: 8px; }
        .btn-maps:hover { border-color: var(--brand-400); color: var(--brand-400); background: var(--brand-50); }
        .btn-maps-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .lokasi-detecting { text-align: center; padding: 20px; font-size: 13px; color: var(--gray-500); }
        .lokasi-error { background: #fef2f2; border: 1px solid #fecaca; border-radius: var(--radius-sm); padding: 12px 14px; font-size: 13px; color: #dc2626; margin-bottom: 12px; display: none; }
        .btn-apply-filter { width: 100%; padding: 14px; background: var(--brand-400); color: #fff; border: none; border-radius: var(--radius-md); font-family: var(--font); font-size: 15px; font-weight: 700; cursor: pointer; transition: background .15s; margin-top: 8px; }
        .btn-apply-filter:active { background: var(--brand-600); }
        .btn-reset-filter { width: 100%; padding: 12px; background: var(--gray-100); color: var(--gray-700); border: none; border-radius: var(--radius-md); font-family: var(--font); font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 8px; }
        .result-meta { display: flex; align-items: center; justify-content: space-between; padding: 0 20px; margin-bottom: 12px; }
        .result-count { font-size: 13px; color: var(--gray-500); }
        .result-count strong { color: var(--gray-900); }
        #no-result { display: none; text-align: center; padding: 48px 20px; color: var(--gray-500); }
        .badge-terlaris { position: absolute; top: 12px; left: 12px; background: #f97316; color: #fff; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 20px; display: flex; align-items: center; gap: 3px; }

        /* ── CSS Tambahan: Guest Switch Page ── */
        .guest-switch-page {
            display: none; position: fixed; inset: 0; background: #fff; z-index: 9999;
            flex-direction: column; align-items: center; justify-content: center;
            padding: 20px; text-align: center;
        }
        .guest-switch-page.active { display: flex; }
        .guest-icon { font-size: 64px; margin-bottom: 24px; }
        .guest-title { font-size: 20px; font-weight: 800; color: var(--gray-900); margin-bottom: 8px; }
        .guest-desc { font-size: 14px; color: var(--gray-500); margin-bottom: 32px; max-width: 280px; line-height: 1.5; }
        
        .btn-guest-login {
            display: flex; align-items: center; justify-content: center;
            width: 100%; max-width: 300px; padding: 14px; background: var(--brand-400);
            color: #fff; border-radius: var(--radius-md); font-family: var(--font);
            font-size: 15px; font-weight: 700; text-decoration: none; margin-bottom: 12px;
            transition: background .15s;
        }
        .btn-guest-login:active { background: var(--brand-600); }
        
        .btn-guest-home {
            width: 100%; max-width: 300px; padding: 14px; background: #fff;
            color: var(--gray-700); border: 1.5px solid var(--gray-200); border-radius: var(--radius-md);
            font-family: var(--font); font-size: 15px; font-weight: 700; cursor: pointer;
            transition: background .15s;
        }
        .btn-guest-home:active { background: var(--gray-100); }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav-brand">Rental<span>Mobil</span></div>
    
    @auth
    <button class="nav-icon" onclick="showToast('Tidak ada notifikasi baru')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <span class="badge"></span>
    </button>
    @endauth
</nav>

<div class="content" style="padding-bottom:100px;">

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-greeting">
            Selamat datang, {{ Auth::check() ? Auth::user()->name : 'Pengunjung' }} 👋
        </div>
        <div class="hero-title">Mau ke mana <em>hari ini?</em></div>
    </div>

    {{-- Search bar --}}
    <div style="padding:0 20px;margin-top:-28px;position:relative;z-index:10;">
        <div class="search-input-wrap">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="search-input"
                   placeholder="Cari nama atau merek mobil..."
                   oninput="applyFilters()" autocomplete="off">
        </div>
    </div>

    <div style="height:8px;"></div>

    {{-- Filter pills --}}
    <div class="filter-bar">
        <button class="filter-pill" id="pill-lokasi" onclick="openSheet('lokasi')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            </svg>
            <span id="pill-lokasi-label">Lokasi Saya</span>
            <span class="dot"></span>
        </button>
        <button class="filter-pill" id="pill-harga" onclick="openSheet('harga')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="1" x2="12" y2="23"/>
                <path d="M17 5H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6"/>
            </svg>
            <span id="pill-harga-label">Harga</span>
            <span class="dot"></span>
        </button>
        <button class="filter-pill" id="pill-merek" onclick="openSheet('merek')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="2" y="7" width="20" height="14" rx="2"/>
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
            <span id="pill-merek-label">Merek</span>
            <span class="dot"></span>
        </button>
        <button class="filter-pill" id="pill-sort" onclick="openSheet('sort')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="15" y2="12"/>
                <line x1="3" y1="18" x2="9" y2="18"/>
            </svg>
            <span id="pill-sort-label">Urutkan</span>
            <span class="dot"></span>
        </button>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div style="margin:0 20px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#16a34a;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="margin:0 20px 12px;background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#dc2626;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Pemesanan Aktif --}}
    @auth
        @php
            $pemesananAktif = Auth::user()->pemesanans()
                ->with('mobil')
                ->whereIn('status', ['pending', 'dikonfirmasi'])
                ->latest()->first();
        @endphp
        @if ($pemesananAktif)
            <div style="padding:0 20px;margin-bottom:16px;">
                <a href="{{ route('user.pemesanan.index') }}"
                   style="text-decoration:none;display:flex;align-items:center;gap:12px;background:#fff;
                          border-radius:var(--radius-md);padding:14px 16px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                    <div class="booking-icon">🚗</div>
                    <div class="booking-info" style="flex:1;">
                        <p style="font-size:12px;color:var(--gray-500);margin:0;">Pemesanan aktif</p>
                        <strong style="font-size:14px;color:var(--gray-900);">{{ $pemesananAktif->mobil->nama }} {{ $pemesananAktif->mobil->tahun }}</strong>
                        <div style="font-size:12px;color:var(--gray-500);margin-top:2px;">
                            {{ $pemesananAktif->tanggal_mulai->format('d M') }} – {{ $pemesananAktif->tanggal_selesai->format('d M Y') }}
                        </div>
                    </div>
                    <span class="booking-status status-{{ $pemesananAktif->status === 'dikonfirmasi' ? 'progress' : 'pending' }}">
                        {{ $pemesananAktif->status === 'dikonfirmasi' ? 'Berjalan' : 'Menunggu' }}
                    </span>
                </a>
            </div>
        @endif
    @endauth

    {{-- Data mobil --}}
    @php
        $semuaMobil = \App\Models\Mobil::where('status', 'tersedia')
            ->withCount(['pemesanans as total_selesai' => fn($q) => $q->where('status', 'selesai')])
            ->latest()->get();

        $favoritIds  = Auth::check() ? Auth::user()->favorits()->pluck('mobil_id')->toArray() : [];
        $maxHarga    = (int) ($semuaMobil->max('harga_per_hari') ?: 1000000);
        $merekList   = \App\Models\Mobil::where('status', 'tersedia')->distinct()->orderBy('merek')->pluck('merek');
        $terlarisIds = $semuaMobil->sortByDesc('total_selesai')->filter(fn($m) => $m->total_selesai > 0)->take(3)->pluck('id')->toArray();
    @endphp

    {{-- Result count --}}
    <div class="result-meta">
        <div class="result-count">
            <strong id="result-count">{{ $semuaMobil->count() }}</strong> kendaraan tersedia
        </div>
    </div>

    <div style="padding:0 20px 20px;">
        <div style="display:flex;flex-direction:column;gap:16px;" id="cars-grid">
            @forelse ($semuaMobil as $mobil)
                <div class="car-card"
                     data-nama="{{ strtolower($mobil->nama) }}"
                     data-merek="{{ strtolower($mobil->merek) }}"
                     data-harga="{{ (int) $mobil->harga_per_hari }}"
                     data-terlaris="{{ $mobil->total_selesai }}"
                     onclick="window.location.href='{{ route('user.mobil.show', $mobil) }}'">

                    <div class="car-image-wrap">
                        @if ($mobil->foto)
                            <img src="{{ asset('storage/'.$mobil->foto) }}"
                                 alt="{{ $mobil->nama }}"
                                 style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div class="car-image-placeholder">🚗</div>
                        @endif

                        @if (in_array($mobil->id, $terlarisIds))
                            <span class="badge-terlaris">🔥 Terlaris</span>
                        @else
                            <span class="car-badge">{{ $mobil->merek }}</span>
                        @endif

                        <button class="car-wishlist"
                                onclick="event.stopPropagation();toggleFavorit(this,{{ $mobil->id }})"
                                data-fav="{{ in_array($mobil->id, $favoritIds) ? 'true' : 'false' }}"
                                data-id="{{ $mobil->id }}">
                            <svg width="18" height="18" viewBox="0 0 24 24"
                                 fill="{{ in_array($mobil->id, $favoritIds) ? '#fca5a5' : 'none' }}"
                                 stroke="{{ in_array($mobil->id, $favoritIds) ? '#dc2626' : 'var(--gray-700)' }}"
                                 stroke-width="2.2" stroke-linecap="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="car-body">
                        <div class="car-name">{{ $mobil->nama }}</div>
                        <div class="car-sub">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                            </svg>
                            {{ $mobil->merek }} · {{ $mobil->tahun }} · {{ $mobil->plat_nomor }}
                        </div>
                        <div class="car-footer">
                            <div class="car-price-wrap">
                                <div class="car-price">Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}</div>
                                <div class="car-price-unit">per hari</div>
                            </div>
                            <a href="{{ route('pemesanan.create', ['mobil_id' => $mobil->id]) }}"
                               class="btn-book" onclick="handlePesanClick(event)">
                                Pesan
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:40px 20px;color:var(--gray-500);">
                    <div style="font-size:48px;margin-bottom:12px;">🚗</div>
                    <div style="font-weight:600;">Belum ada mobil tersedia</div>
                </div>
            @endforelse
        </div>

        <div id="no-result">
            <div style="font-size:40px;margin-bottom:12px;">🔍</div>
            <div style="font-size:15px;font-weight:700;color:var(--gray-900);margin-bottom:6px;">Tidak ditemukan</div>
            <div style="font-size:13px;color:var(--gray-500);margin-bottom:20px;">Coba ubah filter atau kata kunci pencarian</div>
            <button onclick="resetFilters()"
                style="padding:10px 24px;background:var(--brand-400);color:#fff;border:none;
                       border-radius:var(--radius-md);font-family:var(--font);font-size:14px;
                       font-weight:700;cursor:pointer;">
                Reset Filter
            </button>
        </div>
    </div>

</div>

{{-- ══ GUEST SWITCH PAGE (HALAMAN PENAHAN) ══════════════════════════════════════════ --}}
<div id="guest-switch-page" class="guest-switch-page">
    <div class="guest-icon">🔒</div>
    <h2 class="guest-title">Halaman Tidak Tersedia</h2>
    <p class="guest-desc">Masuk untuk melanjutkan atau kembali ke beranda</p>
    
    <a href="{{ route('login') }}" class="btn-guest-login">Login</a>
    <button class="btn-guest-home" onclick="closeGuestPage()">Kembali ke halaman utama</button>
</div>

{{-- Filter Overlays ... --}}
@include('users.partials.bottom-nav')

<div id="toast" style="position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#333;color:#fff;padding:12px 24px;border-radius:50px;font-size:13px;opacity:0;pointer-events:none;transition:opacity 0.3s;z-index:9999;"></div>

<script>
var toastTimer;
var isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

/* ── SWITCH PAGE LOGIC ──────────────────────────────── */
function showGuestPage(e) {
    if (e) e.preventDefault();
    document.getElementById('guest-switch-page').classList.add('active');
}

function closeGuestPage() {
    document.getElementById('guest-switch-page').classList.remove('active');
}

// Fungsi khusus untuk tombol pesan di dalam card
function handlePesanClick(event) {
    // Cegah klik menembus ke card (agar tidak membuka halaman show)
    event.stopPropagation(); 
    
    // Jika belum login, cegah pindah halaman dan tampilkan switch page
    if (!isLoggedIn) {
        event.preventDefault();
        showGuestPage();
    }
}

// Global Click Interceptor untuk Guest
if (!isLoggedIn) {
    document.addEventListener('click', function(e) {
        // Cek apakah yang diklik adalah link <a> atau tombol di dalam <a>
        var link = e.target.closest('a');
        if (link) {
            var href = link.getAttribute('href');
            // Jika href mengarah ke halaman yang dilindungi (selain profil)
            if (href && (href.includes('/pemesanan') || href.includes('/favorit') || href.includes('/chat'))) {
                showGuestPage(e);
            }
        }
    });
}

/* ── FAVORIT ─────────────────── */
function getFavKey(id) { return 'fav_' + id; }

function applyFavState(btn, isFav) {
    var svg = btn.querySelector('svg');
    if (!svg) return;
    svg.setAttribute('fill',   isFav ? '#fca5a5' : 'none');
    svg.setAttribute('stroke', isFav ? '#dc2626' : 'var(--gray-700)');
    btn.dataset.fav = isFav ? 'true' : 'false';
    btn.classList.toggle('wishlisted', isFav);
}

document.addEventListener('DOMContentLoaded', function() {
    if (isLoggedIn) {
        document.querySelectorAll('.car-wishlist[data-id]').forEach(function(btn) {
            var cached = sessionStorage.getItem(getFavKey(btn.dataset.id));
            if (cached !== null) applyFavState(btn, cached === 'true');
        });
    }
});

function toggleFavorit(btn, mobilId) {
    // Tampilkan Switch Page jika Guest mencoba klik Favorit
    if (!isLoggedIn) {
        showGuestPage();
        return;
    }

    var isFav   = btn.dataset.fav === 'true';
    var willFav = !isFav;

    applyFavState(btn, willFav);
    sessionStorage.setItem(getFavKey(mobilId), willFav ? 'true' : 'false');
    btn.disabled = true;

    fetch('/favorit/' + mobilId + '/toggle', {
        method : 'POST',
        headers: {
            'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content,
            'Accept'          : 'application/json',
            'Content-Type'    : 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(function(r) { return r.ok ? r.json() : Promise.reject(); })
    .then(function(d) {
        applyFavState(btn, d.favorited);
        sessionStorage.setItem(getFavKey(mobilId), d.favorited ? 'true' : 'false');
        showToast(d.favorited ? '❤️ Ditambahkan ke favorit' : '🤍 Dihapus dari favorit');
    })
    .catch(function() {
        applyFavState(btn, isFav);
        sessionStorage.setItem(getFavKey(mobilId), isFav ? 'true' : 'false');
        showToast('⚠️ Gagal update favorit', 'error');
    })
    .finally(function() { btn.disabled = false; });
}

/* ── HELPERS & FILTERS (Tetap Sama) ────────────────────────────────────────── */
var state = { /* logic state untuk filter dan fungsi applyFilters() yang tidak berubah disembunyikan agar script fokus pada perbaikan */ };

function showToast(msg, type) {
    var t = document.getElementById('toast');
    if(!t) return;
    t.textContent = msg;
    t.style.opacity = '1';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function() { t.style.opacity = '0'; }, 3000);
}
</script>

</body>
</html>
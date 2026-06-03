<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda — Rental Mobil</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/dashboard.css'])
    <style>
        /* ── Search input ── */
        .search-input-wrap { position: relative; margin-bottom: 12px; }
        .search-input-wrap svg {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%); pointer-events: none; color: var(--gray-400);
        }
        .search-input-wrap input {
            width: 100%; padding: 11px 13px 11px 38px;
            border: 1.5px solid var(--gray-200); border-radius: var(--radius-sm);
            font-family: var(--font); font-size: 14px; color: var(--gray-900);
            background: #fff; transition: border-color .15s; box-sizing: border-box;
        }
        .search-input-wrap input:focus {
            outline: none; border-color: var(--brand-400);
            box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        }

        /* ── Filter bar ── */
        .filter-bar {
            display: flex; align-items: center; gap: 8px;
            padding: 0 20px; margin-bottom: 16px;
            overflow-x: auto; scrollbar-width: none;
        }
        .filter-bar::-webkit-scrollbar { display: none; }
        .filter-pill {
            flex-shrink: 0; display: flex; align-items: center; gap: 5px;
            padding: 8px 14px; background: #fff;
            border: 1.5px solid var(--gray-200); border-radius: 100px;
            font-family: var(--font); font-size: 12px; font-weight: 600;
            color: var(--gray-700); cursor: pointer; transition: all .15s; white-space: nowrap;
        }
        .filter-pill:hover, .filter-pill.active {
            border-color: var(--brand-400); background: var(--brand-50); color: var(--brand-400);
        }
        .filter-pill .dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--brand-400); display: none;
        }
        .filter-pill.active .dot { display: block; }

        /* ── Filter sheet (bottom sheet) ── */
        .filter-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.4); z-index: 200;
        }
        .filter-overlay.open { display: block; }
        .filter-sheet {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: #fff; border-radius: 24px 24px 0 0;
            padding: 20px 20px 40px; z-index: 201;
            transform: translateY(100%);
            transition: transform .3s cubic-bezier(.32,1,.58,1);
            max-height: 85vh; overflow-y: auto;
        }
        .filter-sheet.open { transform: translateY(0); }
        .filter-sheet-handle {
            width: 40px; height: 4px; background: var(--gray-200);
            border-radius: 2px; margin: 0 auto 20px;
        }
        .filter-section-title {
            font-size: 13px; font-weight: 700; color: var(--gray-700);
            margin-bottom: 10px; text-transform: uppercase; letter-spacing: .5px;
        }

        /* ── Price slider ── */
        .price-slider-wrap { padding: 4px 0 16px; }
        .price-range-labels {
            display: flex; justify-content: space-between;
            font-size: 13px; font-weight: 700; color: var(--brand-400); margin-bottom: 10px;
        }
        .range-track {
            position: relative; height: 4px;
            background: var(--gray-200); border-radius: 2px; margin: 16px 0;
        }
        .range-fill {
            position: absolute; height: 4px;
            background: var(--brand-400); border-radius: 2px;
        }
        input[type=range] {
            position: absolute; width: 100%; height: 4px;
            -webkit-appearance: none; appearance: none;
            background: transparent; pointer-events: none; top: 0; left: 0;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none; appearance: none;
            width: 20px; height: 20px; border-radius: 50%;
            background: var(--brand-400); border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(37,99,235,.4);
            pointer-events: all; cursor: pointer;
        }

        /* ── Merek grid ── */
        .merek-grid {
            display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px;
        }
        .merek-chip {
            padding: 8px 16px; border-radius: 100px;
            border: 1.5px solid var(--gray-200); background: #fff;
            font-family: var(--font); font-size: 12px; font-weight: 600;
            color: var(--gray-700); cursor: pointer; transition: all .15s;
        }
        .merek-chip:hover, .merek-chip.active {
            border-color: var(--brand-400); background: var(--brand-50); color: var(--brand-400);
        }

        /* ── Lokasi card ── */
        .lokasi-card {
            display: flex; align-items: center; gap: 14px;
            background: var(--brand-50); border: 1.5px solid var(--brand-100);
            border-radius: var(--radius-md); padding: 16px; margin-bottom: 16px;
        }
        .lokasi-card-icon {
            width: 44px; height: 44px; border-radius: 50%;
            background: var(--brand-400);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .lokasi-card-text { flex: 1; min-width: 0; }
        .lokasi-card-label { font-size: 11px; font-weight: 600; color: var(--brand-400);
            text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .lokasi-card-value { font-size: 14px; font-weight: 700; color: var(--gray-900);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .lokasi-card-sub { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
        .btn-maps {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 13px 16px;
            background: #fff; border: 1.5px solid var(--gray-200);
            border-radius: var(--radius-md); font-family: var(--font);
            font-size: 13px; font-weight: 700; color: var(--gray-700);
            cursor: pointer; transition: all .15s; text-decoration: none;
            margin-bottom: 8px;
        }
        .btn-maps:hover { border-color: var(--brand-400); color: var(--brand-400); background: var(--brand-50); }
        .btn-maps-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .lokasi-detecting {
            text-align: center; padding: 20px;
            font-size: 13px; color: var(--gray-500);
        }
        .lokasi-error {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: var(--radius-sm); padding: 12px 14px;
            font-size: 13px; color: #dc2626; margin-bottom: 12px; display: none;
        }

        /* ── Apply / Reset ── */
        .btn-apply-filter {
            width: 100%; padding: 14px; background: var(--brand-400);
            color: #fff; border: none; border-radius: var(--radius-md);
            font-family: var(--font); font-size: 15px; font-weight: 700;
            cursor: pointer; transition: background .15s; margin-top: 8px;
        }
        .btn-apply-filter:active { background: var(--brand-600); }
        .btn-reset-filter {
            width: 100%; padding: 12px; background: var(--gray-100);
            color: var(--gray-700); border: none; border-radius: var(--radius-md);
            font-family: var(--font); font-size: 14px; font-weight: 600;
            cursor: pointer; margin-top: 8px;
        }

        /* ── Result meta ── */
        .result-meta {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 20px; margin-bottom: 12px;
        }
        .result-count { font-size: 13px; color: var(--gray-500); }
        .result-count strong { color: var(--gray-900); }

        /* ── No result ── */
        #no-result {
            display: none; text-align: center; padding: 48px 20px; color: var(--gray-500);
        }

        /* ── Terlaris badge ── */
        .badge-terlaris {
            position: absolute; top: 12px; left: 12px;
            background: #f97316; color: #fff;
            font-size: 10px; font-weight: 700; padding: 3px 8px;
            border-radius: 20px; display: flex; align-items: center; gap: 3px;
        }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav-brand">Rental<span>Mobil</span></div>
    <a href="{{ route('user.notifikasi') }}" class="nav-icon" style="position:relative;text-decoration:none;color:inherit;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <span id="notif-badge" style="display:none;position:absolute;top:-3px;right:-3px;width:10px;height:10px;background:#ef4444;border-radius:50%;border:2px solid #fff;"></span>
    </a>
</nav>

<div class="content" style="padding-bottom:100px;">

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-greeting">Selamat datang, {{ Auth::user()->name }} 👋</div>
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

    {{-- Pemesanan Aktif --}}
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

    {{-- Data mobil --}}
    @php
        $semuaMobil = \App\Models\Mobil::where('status', 'tersedia')
            ->withCount(['pemesanans as total_selesai' => fn($q) => $q->where('status', 'selesai')])
            ->latest()->get();

        $favoritIds  = Auth::user()->favorits()->pluck('mobil_id')->toArray();
        $maxHarga    = (int) ($semuaMobil->max('harga_per_hari') ?: 1000000);

        $merekList   = \App\Models\Mobil::where('status', 'tersedia')
            ->distinct()->orderBy('merek')->pluck('merek');

        $terlarisIds = $semuaMobil->sortByDesc('total_selesai')
            ->filter(fn($m) => $m->total_selesai > 0)
            ->take(3)->pluck('id')->toArray();
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

                        {{-- Badge terlaris ATAU merek --}}
                        @if (in_array($mobil->id, $terlarisIds))
                            <span class="badge-terlaris">🔥 Terlaris</span>
                        @else
                            <span class="car-badge">{{ $mobil->merek }}</span>
                        @endif

                        {{-- Wishlist: SVG heart, state dari DB via $favoritIds --}}
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
                               class="btn-book" onclick="event.stopPropagation()">
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

{{-- ══ FILTER OVERLAY ══════════════════════════════════════════ --}}
<div class="filter-overlay" id="filter-overlay" onclick="closeSheet()"></div>

{{-- ── Sheet: Lokasi ── --}}
<div class="filter-sheet" id="sheet-lokasi">
    <div class="filter-sheet-handle"></div>
    <div style="font-size:17px;font-weight:800;margin-bottom:4px;">📍 Lokasi Saya</div>
    <div style="font-size:13px;color:var(--gray-500);margin-bottom:20px;">
        Deteksi posisi kamu dan buka lokasi rental terdekat di Google Maps
    </div>
    <div id="lokasi-detecting" class="lokasi-detecting" style="display:none;">
        <div style="font-size:28px;margin-bottom:8px;">🔍</div>
        <div>Mendeteksi lokasi kamu...</div>
    </div>
    <div id="lokasi-error" class="lokasi-error">
        ⚠️ Tidak dapat mendeteksi lokasi. Pastikan izin lokasi diaktifkan di browser kamu.
    </div>
    <div id="lokasi-result" style="display:none;">
        <div class="lokasi-card">
            <div class="lokasi-card-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                    <circle cx="12" cy="9" r="2.5" fill="white" stroke="none"/>
                </svg>
            </div>
            <div class="lokasi-card-text">
                <div class="lokasi-card-label">Lokasi Saya</div>
                <div class="lokasi-card-value" id="lokasi-alamat">Mendeteksi...</div>
                <div class="lokasi-card-sub" id="lokasi-koordinat"></div>
            </div>
        </div>
        <div style="margin-bottom:16px;">
            <div class="filter-section-title" style="margin-bottom:10px;">Rental Terdekat</div>
            <a id="maps-btn-2" href="#" target="_blank" rel="noopener" class="btn-maps">
                <div class="btn-maps-icon" style="background:#e3f2fd;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2.5">
                        <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                    </svg>
                </div>
                <div style="flex:1;">
                    <div style="font-size:13px;font-weight:700;color:var(--gray-900);">Petunjuk Arah ke Rental</div>
                    <div style="font-size:11px;color:var(--gray-500);margin-top:1px;">Navigasi dari posisi kamu sekarang</div>
                </div>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
            </a>
        </div>
    </div>
    <div id="lokasi-default">
        <div style="text-align:center;padding:16px 0 24px;">
            <div style="font-size:48px;margin-bottom:12px;">📍</div>
            <div style="font-size:14px;font-weight:600;color:var(--gray-700);margin-bottom:6px;">Izinkan akses lokasi</div>
            <div style="font-size:13px;color:var(--gray-500);">Tap tombol di bawah untuk mendeteksi posisi kamu dan menemukan rental terdekat</div>
        </div>
    </div>
    <button class="btn-apply-filter" id="btn-detect-lokasi" onclick="detectLokasi()">📍 Deteksi Lokasi Saya</button>
    <button class="btn-reset-filter" onclick="closeSheet()">Tutup</button>
</div>

{{-- ── Sheet: Harga ── --}}
<div class="filter-sheet" id="sheet-harga">
    <div class="filter-sheet-handle"></div>
    <div style="font-size:17px;font-weight:800;margin-bottom:20px;">💰 Rentang Harga</div>
    <div class="filter-section-title">Harga per Hari</div>
    <div class="price-slider-wrap">
        <div class="price-range-labels">
            <span id="label-min">Rp 0</span>
            <span id="label-max">Rp {{ number_format($maxHarga, 0, ',', '.') }}</span>
        </div>
        <div class="range-track" id="range-track">
            <div class="range-fill" id="range-fill"></div>
            <input type="range" id="range-min" min="0" max="{{ $maxHarga }}"
                   step="50000" value="0" oninput="updateSlider()">
            <input type="range" id="range-max" min="0" max="{{ $maxHarga }}"
                   step="50000" value="{{ $maxHarga }}" oninput="updateSlider()">
        </div>
    </div>
    <button class="btn-apply-filter" onclick="applySheet('harga')">Terapkan</button>
    <button class="btn-reset-filter" onclick="resetSheet('harga')">Reset</button>
</div>

{{-- ── Sheet: Merek ── --}}
<div class="filter-sheet" id="sheet-merek">
    <div class="filter-sheet-handle"></div>
    <div style="font-size:17px;font-weight:800;margin-bottom:20px;">🚗 Pilih Merek</div>
    <div class="merek-grid" id="merek-chips">
        <button class="merek-chip active" data-merek="" onclick="selectMerek(this)">Semua</button>
        @foreach ($merekList as $merek)
            <button class="merek-chip" data-merek="{{ strtolower($merek) }}"
                onclick="selectMerek(this)">{{ $merek }}</button>
        @endforeach
    </div>
    <button class="btn-apply-filter" onclick="applySheet('merek')">Terapkan</button>
    <button class="btn-reset-filter" onclick="resetSheet('merek')">Reset</button>
</div>

{{-- ── Sheet: Sort ── --}}
<div class="filter-sheet" id="sheet-sort">
    <div class="filter-sheet-handle"></div>
    <div style="font-size:17px;font-weight:800;margin-bottom:20px;">↕️ Urutkan</div>
    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;" id="sort-options">
        <button class="filter-pill active"
            style="width:100%;justify-content:flex-start;border-radius:var(--radius-sm);padding:13px 16px;"
            data-sort="default" onclick="selectSort(this)">✨ Terbaru</button>
        <button class="filter-pill"
            style="width:100%;justify-content:flex-start;border-radius:var(--radius-sm);padding:13px 16px;"
            data-sort="terlaris" onclick="selectSort(this)">🔥 Terlaris</button>
        <button class="filter-pill"
            style="width:100%;justify-content:flex-start;border-radius:var(--radius-sm);padding:13px 16px;"
            data-sort="harga-asc" onclick="selectSort(this)">💸 Harga Termurah</button>
        <button class="filter-pill"
            style="width:100%;justify-content:flex-start;border-radius:var(--radius-sm);padding:13px 16px;"
            data-sort="harga-desc" onclick="selectSort(this)">💎 Harga Tertinggi</button>
    </div>
    <button class="btn-apply-filter" onclick="applySheet('sort')">Terapkan</button>
</div>

@include('users.partials.bottom-nav')

<script>
/* ── STATE ──────────────────────────────────────────── */
var state = {
    search   : '',
    hargaMin : 0,
    hargaMax : {{ $maxHarga }},
    merek    : '',
    sort     : 'default',
    _hargaMin: 0,
    _hargaMax: {{ $maxHarga }},
    _merek   : '',
    _sort    : 'default',
};

/* ── SHEET OPEN / CLOSE ─────────────────────────────── */
var currentSheet = null;

function openSheet(name) {
    state._hargaMin = state.hargaMin;
    state._hargaMax = state.hargaMax;
    state._merek    = state.merek;
    state._sort     = state.sort;
    if (currentSheet) { closeSheet(true); }
    currentSheet = name;
    if (name === 'harga') syncHargaUI();
    if (name === 'merek') syncMerekUI();
    if (name === 'sort')  syncSortUI();
    document.getElementById('filter-overlay').classList.add('open');
    document.getElementById('sheet-' + name).classList.add('open');
}

function closeSheet(silent) {
    if (!currentSheet) return;
    document.getElementById('sheet-' + currentSheet).classList.remove('open');
    document.getElementById('filter-overlay').classList.remove('open');
    currentSheet = null;
}

function applySheet(name) {
    if (name === 'harga') {
        state.hargaMin = state._hargaMin;
        state.hargaMax = state._hargaMax;
        var changed = state.hargaMin > 0 || state.hargaMax < {{ $maxHarga }};
        document.getElementById('pill-harga').classList.toggle('active', changed);
        document.getElementById('pill-harga-label').textContent = changed
            ? fmtRibuan(state.hargaMin) + '–' + fmtRibuan(state.hargaMax) : 'Harga';
    }
    if (name === 'merek') {
        state.merek = state._merek;
        document.getElementById('pill-merek').classList.toggle('active', !!state.merek);
        document.getElementById('pill-merek-label').textContent =
            state.merek ? capitalise(state.merek) : 'Merek';
    }
    if (name === 'sort') {
        state.sort = state._sort;
        var changed = state.sort !== 'default';
        document.getElementById('pill-sort').classList.toggle('active', changed);
        var labels = { default:'Urutkan', terlaris:'Terlaris', 'harga-asc':'Termurah', 'harga-desc':'Tertinggi' };
        document.getElementById('pill-sort-label').textContent = labels[state.sort] || 'Urutkan';
    }
    closeSheet();
    applyFilters();
}

function resetSheet(name) {
    if (name === 'harga') { state._hargaMin = 0; state._hargaMax = {{ $maxHarga }}; syncHargaUI(); }
    if (name === 'merek') { state._merek = ''; syncMerekUI(); }
}

/* ── HARGA SLIDER ───────────────────────────────────── */
function syncHargaUI() {
    document.getElementById('range-min').value = state._hargaMin;
    document.getElementById('range-max').value = state._hargaMax;
    updateSlider();
}
function updateSlider() {
    var minEl  = document.getElementById('range-min');
    var maxEl  = document.getElementById('range-max');
    var minVal = parseInt(minEl.value);
    var maxVal = parseInt(maxEl.value);
    if (minVal >= maxVal) {
        if (document.activeElement === minEl) minVal = maxVal - 50000;
        else maxVal = minVal + 50000;
        minEl.value = minVal; maxEl.value = maxVal;
    }
    state._hargaMin = minVal; state._hargaMax = maxVal;
    var maxH  = {{ $maxHarga }};
    var left  = (minVal / maxH) * 100;
    var right = 100 - (maxVal / maxH) * 100;
    document.getElementById('range-fill').style.left  = left + '%';
    document.getElementById('range-fill').style.width = (100 - left - right) + '%';
    document.getElementById('label-min').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(minVal);
    document.getElementById('label-max').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(maxVal);
}

/* ── MEREK ──────────────────────────────────────────── */
function syncMerekUI() {
    document.querySelectorAll('.merek-chip').forEach(function(c) {
        c.classList.toggle('active', c.dataset.merek === state._merek);
    });
}
function selectMerek(el) { state._merek = el.dataset.merek; syncMerekUI(); }

/* ── SORT ───────────────────────────────────────────── */
function syncSortUI() {
    document.querySelectorAll('#sort-options button').forEach(function(b) {
        b.classList.toggle('active', b.dataset.sort === state._sort);
    });
}
function selectSort(el) { state._sort = el.dataset.sort; syncSortUI(); }

/* ── LOKASI ─────────────────────────────────────────── */
var RENTAL_LAT  = -6.2088;
var RENTAL_LNG  = 106.8456;

function detectLokasi() {
    if (!navigator.geolocation) {
        document.getElementById('lokasi-error').style.display = 'block'; return;
    }
    document.getElementById('lokasi-default').style.display   = 'none';
    document.getElementById('lokasi-detecting').style.display = 'block';
    document.getElementById('lokasi-error').style.display     = 'none';
    document.getElementById('btn-detect-lokasi').disabled     = true;
    document.getElementById('btn-detect-lokasi').textContent  = 'Mendeteksi...';

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            var lat = pos.coords.latitude.toFixed(6);
            var lng = pos.coords.longitude.toFixed(6);
            document.getElementById('lokasi-detecting').style.display = 'none';
            document.getElementById('lokasi-result').style.display    = 'block';
            document.getElementById('lokasi-koordinat').textContent   = lat + ', ' + lng;
            fetch('https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json')
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    var a = d.address || {};
                    var label = a.neighbourhood || a.suburb || a.city_district || a.city || a.county || 'Lokasi ditemukan';
                    document.getElementById('lokasi-alamat').textContent = label + ', ' + (a.city || a.county || '');
                })
                .catch(function() {
                    document.getElementById('lokasi-alamat').textContent = 'Koordinat: ' + lat + ', ' + lng;
                });
            document.getElementById('maps-btn-2').href =
                'https://www.google.com/maps/dir/' + lat + ',' + lng + '/' + RENTAL_LAT + ',' + RENTAL_LNG;
            document.getElementById('btn-detect-lokasi').textContent = '📍 Deteksi Ulang';
            document.getElementById('btn-detect-lokasi').disabled    = false;
            document.getElementById('pill-lokasi').classList.add('active');
            document.getElementById('pill-lokasi-label').textContent = 'Lokasi Saya';
        },
        function() {
            document.getElementById('lokasi-detecting').style.display = 'none';
            document.getElementById('lokasi-default').style.display   = 'block';
            document.getElementById('lokasi-error').style.display     = 'block';
            document.getElementById('btn-detect-lokasi').disabled     = false;
            document.getElementById('btn-detect-lokasi').textContent  = '📍 Coba Lagi';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

/* ── APPLY FILTERS ──────────────────────────────────── */
function applyFilters() {
    state.search = document.getElementById('search-input').value.toLowerCase().trim();
    var cards   = Array.from(document.querySelectorAll('#cars-grid .car-card'));
    var visible = [];
    cards.forEach(function(card) {
        var matchSearch = !state.search || card.dataset.nama.includes(state.search) || card.dataset.merek.includes(state.search);
        var matchHarga  = parseInt(card.dataset.harga) >= state.hargaMin && parseInt(card.dataset.harga) <= state.hargaMax;
        var matchMerek  = !state.merek || card.dataset.merek === state.merek;
        var show = matchSearch && matchHarga && matchMerek;
        card.style.display = show ? '' : 'none';
        if (show) visible.push(card);
    });
    if (state.sort !== 'default' && visible.length > 1) {
        var parent = document.getElementById('cars-grid');
        visible.sort(function(a, b) {
            if (state.sort === 'terlaris')   return parseInt(b.dataset.terlaris) - parseInt(a.dataset.terlaris);
            if (state.sort === 'harga-asc')  return parseInt(a.dataset.harga)    - parseInt(b.dataset.harga);
            if (state.sort === 'harga-desc') return parseInt(b.dataset.harga)    - parseInt(a.dataset.harga);
            return 0;
        });
        visible.forEach(function(c) { parent.appendChild(c); });
    }
    document.getElementById('result-count').textContent = visible.length;
    document.getElementById('no-result').style.display  = visible.length === 0 ? 'block' : 'none';
}

function resetFilters() {
    state.search = ''; state.hargaMin = 0; state.hargaMax = {{ $maxHarga }};
    state.merek  = ''; state.sort = 'default';
    document.getElementById('search-input').value = '';
    ['harga','merek','sort'].forEach(function(n) {
        document.getElementById('pill-' + n).classList.remove('active');
    });
    document.getElementById('pill-harga-label').textContent = 'Harga';
    document.getElementById('pill-merek-label').textContent = 'Merek';
    document.getElementById('pill-sort-label').textContent  = 'Urutkan';
    applyFilters();
}

/* ── FAVORIT (SVG, sessionStorage) ─────────────────── */
function getFavKey(id) { return 'fav_' + id; }

function applyFavState(btn, isFav) {
    var svg = btn.querySelector('svg');
    if (!svg) return;
    svg.setAttribute('fill',   isFav ? '#fca5a5' : 'none');
    svg.setAttribute('stroke', isFav ? '#dc2626' : 'var(--gray-700)');
    btn.dataset.fav = isFav ? 'true' : 'false';
    btn.classList.toggle('wishlisted', isFav);
}

// Sinkron icon dari sessionStorage saat halaman dimuat
// (mencegah delay setelah back dari halaman detail)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.car-wishlist[data-id]').forEach(function(btn) {
        var cached = sessionStorage.getItem(getFavKey(btn.dataset.id));
        if (cached !== null) applyFavState(btn, cached === 'true');
    });
});

function toggleFavorit(btn, mobilId) {
    var isFav   = btn.dataset.fav === 'true';
    var willFav = !isFav;

    // Optimistic UI
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
        // favorit updated
    })
    .catch(function() {
        applyFavState(btn, isFav);
        sessionStorage.setItem(getFavKey(mobilId), isFav ? 'true' : 'false');
        // gagal favorit
    })
    .finally(function() { btn.disabled = false; });
}

/* ── HELPERS ────────────────────────────────────────── */
function capitalise(s) { return s.charAt(0).toUpperCase() + s.slice(1); }
function fmtRibuan(n)  { return 'Rp' + Math.round(n / 1000) + 'rb'; }


// Init
updateSlider();
</script>


<script>
(function pollNotifBadge() {
    fetch('{{ route("notifikasi.unread") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        var badge = document.getElementById('notif-badge');
        if (badge) badge.style.display = data.unread > 0 ? 'block' : 'none';
    })
    .catch(() => {});
    setTimeout(pollNotifBadge, 30000);
})();
</script>
</body>
</html>
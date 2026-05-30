<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>{{ $mobil->nama }} — Rental Mobil</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/dashboard.css'])
    <style>
        /* ── Hero Gallery ─────────────────────────────────── */
        .gallery-wrap {
            position: relative;
            height: 280px;
            background: var(--gray-100);
            overflow: hidden;
            margin-top: var(--nav-h);
        }
        .gallery-slides {
            display: flex;
            height: 100%;
            transition: transform .38s cubic-bezier(.4,0,.2,1);
            will-change: transform;
        }
        .gallery-slide {
            flex-shrink: 0;
            width: 100%;
            height: 100%;
            position: relative;
        }
        .gallery-slide img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .gallery-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            font-size: 80px;
            background: linear-gradient(135deg, var(--brand-50), var(--brand-100));
        }
        /* Nav arrows */
        .gallery-arrow {
            position: absolute; top: 50%; transform: translateY(-50%);
            width: 36px; height: 36px;
            background: rgba(255,255,255,.82); backdrop-filter: blur(6px);
            border: none; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 10;
            box-shadow: var(--shadow-sm);
            transition: opacity .2s, transform .2s;
        }
        .gallery-arrow:active { transform: translateY(-50%) scale(.9); }
        .gallery-arrow.prev { left: 12px; }
        .gallery-arrow.next { right: 12px; }
        .gallery-arrow.hidden { opacity: 0; pointer-events: none; }
        /* Dots */
        .gallery-dots {
            position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 6px; z-index: 10;
        }
        .gallery-dot {
            width: 6px; height: 6px; border-radius: 3px;
            background: rgba(255,255,255,.5);
            transition: width .25s, background .25s;
            cursor: pointer;
        }
        .gallery-dot.active {
            width: 18px;
            background: white;
        }
        /* Counter badge */
        .gallery-count {
            position: absolute; bottom: 12px; right: 14px;
            background: rgba(0,0,0,.45); backdrop-filter: blur(4px);
            color: white; font-size: 11px; font-weight: 600;
            padding: 3px 9px; border-radius: 20px; z-index: 10;
        }
        /* Wishlist & share buttons overlay */
        .gallery-actions {
            position: absolute; top: 12px; right: 12px;
            display: flex; gap: 8px; z-index: 10;
        }
        .gallery-btn {
            width: 38px; height: 38px;
            background: rgba(255,255,255,.85); backdrop-filter: blur(4px);
            border-radius: 50%; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            box-shadow: var(--shadow-sm);
            transition: transform .2s, background .15s;
        }
        .gallery-btn:active { transform: scale(.88); }
        .gallery-btn.wishlisted { background: #fee2e2; }
        .gallery-btn.wishlisted svg { stroke: #dc2626; fill: #fca5a5; }

        /* Status badge overlay */
        .gallery-status {
            position: absolute; top: 12px; left: 12px; z-index: 10;
        }
        .status-pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 700;
        }
        .status-pill.tersedia  { background: #dcfce7; color: #16a34a; }
        .status-pill.disewa    { background: #fee2e2; color: #dc2626; }
        .status-pill .dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: currentColor;
        }

        /* ── Content ─────────────────────────────────────── */
        .detail-wrap {
            padding: 0 0 140px;
        }

        /* Card utama info */
        .detail-card {
            background: var(--white);
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
            margin-top: -20px;
            position: relative; z-index: 5;
            padding: 24px 20px 0;
            box-shadow: 0 -4px 20px rgba(0,0,0,.06);
        }

        /* Header harga + nama */
        .detail-header {
            margin-bottom: 16px;
        }
        .detail-name {
            font-size: 22px; font-weight: 800;
            color: var(--gray-900); letter-spacing: -.5px;
            margin-bottom: 3px;
            line-height: 1.25;
        }
        .detail-meta {
            font-size: 13px; color: var(--gray-500);
            display: flex; align-items: center; gap: 6px;
            flex-wrap: wrap;
        }
        .detail-meta-dot { color: var(--gray-300); }
        .detail-price-row {
            display: flex; align-items: flex-end;
            justify-content: space-between;
            margin-top: 14px;
        }
        .detail-price {
            font-size: 28px; font-weight: 800;
            color: var(--brand-400); letter-spacing: -1px;
        }
        .detail-price-unit {
            font-size: 13px; color: var(--gray-500);
            font-weight: 500; margin-left: 3px;
            letter-spacing: 0;
        }
        .detail-rating-badge {
            display: flex; align-items: center; gap: 5px;
            background: var(--gray-50); border: 1px solid var(--gray-100);
            border-radius: var(--radius-sm); padding: 6px 12px;
        }
        .detail-rating-badge .star { color: #f59e0b; font-size: 14px; }
        .detail-rating-badge .val { font-size: 15px; font-weight: 800; color: var(--gray-900); }
        .detail-rating-badge .cnt { font-size: 12px; color: var(--gray-500); }

        /* Specs row */
        .specs-row {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 20px 0;
            padding: 16px;
            background: var(--gray-50);
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-100);
        }
        .spec-box {
            display: flex; flex-direction: column; align-items: center; gap: 5px;
        }
        .spec-box-icon {
            font-size: 20px; line-height: 1;
        }
        .spec-box-label {
            font-size: 10px; color: var(--gray-500); font-weight: 600;
            text-transform: uppercase; letter-spacing: .5px;
        }
        .spec-box-val {
            font-size: 13px; font-weight: 700; color: var(--gray-900);
        }

        /* ── Section divider ─────────────────────────────── */
        .section-sep {
            height: 8px; background: var(--gray-50);
            margin: 0 -20px;
            border-top: 1px solid var(--gray-100);
            border-bottom: 1px solid var(--gray-100);
        }
        .detail-section { padding: 20px 20px 0; }
        .detail-section-title {
            font-size: 15px; font-weight: 800; color: var(--gray-900);
            margin-bottom: 14px;
            display: flex; align-items: center; gap: 8px;
        }
        .detail-section-title span { font-size: 18px; }

        /* Deskripsi */
        .deskripsi-text {
            font-size: 14px; line-height: 1.7; color: var(--gray-700);
        }
        .deskripsi-text.clamped {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .btn-expand {
            background: none; border: none; cursor: pointer;
            font-size: 13px; font-weight: 700; color: var(--brand-400);
            padding: 8px 0 0;
            display: flex; align-items: center; gap: 4px;
        }
        .btn-expand svg { transition: transform .2s; }
        .btn-expand.expanded svg { transform: rotate(180deg); }

        /* Fasilitas chips */
        .fasilitas-chips {
            display: flex; flex-wrap: wrap; gap: 8px;
        }
        .fasilitas-chip {
            display: flex; align-items: center; gap: 6px;
            background: var(--brand-50); border: 1.5px solid var(--brand-100);
            color: var(--brand-600);
            padding: 7px 13px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }
        .fasilitas-chip span { font-size: 15px; }

        /* Plat nomor chip */
        .plat-chip {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--gray-900); color: white;
            border: 2px solid var(--gray-700);
            padding: 8px 16px; border-radius: 6px;
            font-size: 16px; font-weight: 800;
            letter-spacing: 2px;
            font-family: monospace;
        }

        /* ── Reviews ─────────────────────────────────────── */
        .review-summary {
            display: flex; gap: 20px; align-items: center;
            padding: 16px; background: var(--gray-50);
            border-radius: var(--radius-md); border: 1px solid var(--gray-100);
            margin-bottom: 18px;
        }
        .review-score {
            text-align: center; flex-shrink: 0;
        }
        .review-score .big {
            font-size: 44px; font-weight: 800;
            color: var(--gray-900); letter-spacing: -2px;
            line-height: 1;
        }
        .review-score .stars { font-size: 18px; margin: 4px 0 2px; }
        .review-score .cnt { font-size: 11px; color: var(--gray-500); }
        .review-bars { flex: 1; display: flex; flex-direction: column; gap: 5px; }
        .bar-row {
            display: flex; align-items: center; gap: 8px;
            font-size: 11px; font-weight: 600;
        }
        .bar-row .lbl { color: var(--gray-500); width: 9px; text-align: right; }
        .bar-track {
            flex: 1; height: 5px;
            background: var(--gray-200); border-radius: 3px; overflow: hidden;
        }
        .bar-fill {
            height: 100%; background: #f59e0b; border-radius: 3px;
            transition: width .6s ease;
        }
        .bar-row .pct { color: var(--gray-500); width: 24px; }

        .review-list { display: flex; flex-direction: column; gap: 14px; }
        .review-item {
            padding: 14px 16px;
            background: var(--white); border-radius: var(--radius-md);
            border: 1px solid var(--gray-100);
        }
        .review-item-header {
            display: flex; align-items: center; gap: 10px; margin-bottom: 8px;
        }
        .reviewer-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--brand-100);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: var(--brand-600);
            flex-shrink: 0;
        }
        .reviewer-info { flex: 1; }
        .reviewer-name { font-size: 13px; font-weight: 700; color: var(--gray-900); }
        .reviewer-date { font-size: 11px; color: var(--gray-400); }
        .reviewer-stars { font-size: 13px; }
        .review-text {
            font-size: 13px; line-height: 1.65; color: var(--gray-700);
        }
        .review-empty {
            text-align: center; padding: 32px 20px;
            color: var(--gray-400); font-size: 14px;
        }
        .review-empty .icon { font-size: 36px; margin-bottom: 8px; }

        /* Wishlist form */
        #wishlist-form { display: inline; }

        /* ── Sticky Book Bar ─────────────────────────────── */
        .sticky-bar {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: var(--white);
            border-top: 1px solid var(--gray-100);
            padding: 12px 20px calc(12px + env(safe-area-inset-bottom));
            display: flex; align-items: center; gap: 14px;
            z-index: 200;
            box-shadow: 0 -4px 20px rgba(0,0,0,.08);
        }
        .sticky-bar-price { flex: 1; }
        .sticky-bar-price .lbl { font-size: 11px; color: var(--gray-500); font-weight: 500; }
        .sticky-bar-price .val {
            font-size: 20px; font-weight: 800; color: var(--brand-400);
            letter-spacing: -.5px;
        }
        .sticky-bar-price .unit { font-size: 12px; color: var(--gray-500); font-weight: 500; }
        .btn-book-main {
            flex: 1.4;
            background: var(--brand-400); color: white;
            border: none; border-radius: var(--radius-md);
            padding: 15px 20px;
            font-family: var(--font); font-size: 15px; font-weight: 700;
            cursor: pointer; text-decoration: none;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: background .15s, transform .1s;
            box-shadow: 0 4px 14px rgba(37,99,235,.35);
        }
        .btn-book-main:active { transform: scale(.98); background: var(--brand-600); }
        .btn-book-main.disabled {
            background: var(--gray-300); color: var(--gray-500);
            cursor: not-allowed; box-shadow: none;
            pointer-events: none;
        }

        /* Touch swipe hint overlay */
        .swipe-hint {
            position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%);
            background: rgba(0,0,0,.5); color: white;
            font-size: 11px; padding: 5px 12px; border-radius: 20px;
            white-space: nowrap; pointer-events: none;
            animation: hintFade 2.5s ease forwards;
            z-index: 10;
        }
        @keyframes hintFade {
            0%   { opacity: 0; transform: translateX(-50%) translateY(6px); }
            20%  { opacity: 1; transform: translateX(-50%) translateY(0); }
            75%  { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
</head>
<body>

{{-- ─── Top Nav ─────────────────────────────────────────── --}}
<nav class="nav">
    <button onclick="history.back()"
        style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Detail Mobil</div>
    <div style="width:36px;"></div>
</nav>

{{-- ─── Gallery ──────────────────────────────────────────── --}}
@php
    /*
     * Saat ini DB hanya menyimpan 1 foto (kolom `foto`).
     * Jika kelak multi-foto diimplementasi via relasi fotos(),
     * ganti $fotos di bawah dengan $mobil->fotos->pluck('path')->toArray()
     * dan hapus fallback berikut ini.
     */
    $fotos = $mobil->foto ? [$mobil->foto] : [];
    $jumlahFoto = count($fotos);
@endphp

<div class="gallery-wrap" id="gallery">

    {{-- Status pill --}}
    <div class="gallery-status">
        <div class="status-pill {{ $mobil->status }}">
            <span class="dot"></span>
            {{ $mobil->status === 'tersedia' ? 'Tersedia' : 'Sedang Disewa' }}
        </div>
    </div>

    {{-- Wishlist + share --}}
    <div class="gallery-actions">
        {{-- Share --}}
        <button class="gallery-btn" onclick="shareHalaman()" title="Bagikan">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="var(--gray-700)" stroke-width="2.2" stroke-linecap="round">
                <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
            </svg>
        </button>

        {{-- Wishlist toggle --}}
        <button type="button"
                class="gallery-btn {{ $isFav ? 'wishlisted' : '' }}"
                id="btn-wishlist"
                data-url="{{ route('user.favorit.toggle', $mobil) }}"
                data-fav="{{ $isFav ? 'true' : 'false' }}"
                title="{{ $isFav ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}"
                onclick="toggleWishlist(this)">
            <svg width="18" height="18" viewBox="0 0 24 24"
                 fill="{{ $isFav ? '#fca5a5' : 'none' }}"
                 stroke="{{ $isFav ? '#dc2626' : 'var(--gray-700)' }}"
                 stroke-width="2.2" stroke-linecap="round" id="heart-icon">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                         a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23
                         l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </button>
    </div>

    {{-- Slides --}}
    <div class="gallery-slides" id="slides">
        @if($jumlahFoto > 0)
            @foreach($fotos as $foto)
                <div class="gallery-slide">
                    <img src="{{ asset('storage/' . $foto) }}"
                         alt="{{ $mobil->nama }} foto {{ $loop->iteration }}"
                         loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                </div>
            @endforeach
        @else
            <div class="gallery-slide">
                <div class="gallery-placeholder">🚗</div>
            </div>
        @endif
    </div>

    {{-- Arrows (hanya tampil jika lebih dari 1 foto) --}}
    @if($jumlahFoto > 1)
        <button class="gallery-arrow prev" id="arrow-prev" onclick="galeri(-1)" aria-label="Foto sebelumnya">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gray-700)" stroke-width="2.5" stroke-linecap="round">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        <button class="gallery-arrow next" id="arrow-next" onclick="galeri(1)" aria-label="Foto berikutnya">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gray-700)" stroke-width="2.5" stroke-linecap="round">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>

        {{-- Dots --}}
        <div class="gallery-dots" id="dots">
            @foreach($fotos as $i => $foto)
                <div class="gallery-dot {{ $i === 0 ? 'active' : '' }}"
                     onclick="goSlide({{ $i }})" aria-label="Foto {{ $i + 1 }}"></div>
            @endforeach
        </div>

        {{-- Counter --}}
        <div class="gallery-count" id="counter">1 / {{ $jumlahFoto }}</div>

        {{-- Swipe hint --}}
        <div class="swipe-hint">Geser untuk melihat foto lainnya</div>
    @endif
</div>

{{-- ─── Detail Content ───────────────────────────────────── --}}
<div class="detail-wrap">
    <div class="detail-card">

        {{-- Nama + harga --}}
        <div class="detail-header">
            <h1 class="detail-name">{{ $mobil->nama }}</h1>
            <div class="detail-meta">
                <span>{{ $mobil->merek }}</span>
                <span class="detail-meta-dot">·</span>
                <span>{{ $mobil->tahun }}</span>
                <span class="detail-meta-dot">·</span>
                <span>🚗 Kendaraan Sewa</span>
            </div>
            <div class="detail-price-row">
                <div>
                    <span class="detail-price">
                        Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}
                    </span>
                    <span class="detail-price-unit">/ hari</span>
                </div>
                {{-- Rating placeholder (koneksi ke sistem ulasan saat sudah diimplementasi) --}}
                <div class="detail-rating-badge">
                    <span class="star">★</span>
                    <span class="val">—</span>
                    <span class="cnt">Belum ada ulasan</span>
                </div>
            </div>
        </div>

        {{-- Specs grid --}}
        <div class="specs-row">
            <div class="spec-box">
                <span class="spec-box-icon">📅</span>
                <span class="spec-box-label">Tahun</span>
                <span class="spec-box-val">{{ $mobil->tahun }}</span>
            </div>
            <div class="spec-box">
                <span class="spec-box-icon">🏷️</span>
                <span class="spec-box-label">Merek</span>
                <span class="spec-box-val">{{ $mobil->merek }}</span>
            </div>
            <div class="spec-box">
                <span class="spec-box-icon">
                    {{ $mobil->status === 'tersedia' ? '✅' : '🔴' }}
                </span>
                <span class="spec-box-label">Status</span>
                <span class="spec-box-val">
                    {{ $mobil->status === 'tersedia' ? 'Tersedia' : 'Disewa' }}
                </span>
            </div>
        </div>

    </div>{{-- /detail-card --}}

    {{-- ── Deskripsi ────────────────────────────────────── --}}
    <div class="detail-section" style="background:var(--white);padding-bottom:20px;">
        <div class="detail-section-title">
            <span>📝</span> Deskripsi
        </div>
        @if($mobil->deskripsi)
            <p class="deskripsi-text clamped" id="deskripsi-text">
                {{ $mobil->deskripsi }}
            </p>
            <button class="btn-expand" id="btn-expand" onclick="toggleDeskripsi()">
                Selengkapnya
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M6 9l6 6 6-6"/>
                </svg>
            </button>
        @else
            <p class="deskripsi-text" style="color:var(--gray-400);font-style:italic;">
                Deskripsi belum tersedia.
            </p>
        @endif
    </div>

    <div class="section-sep"></div>

    {{-- ── Fasilitas ────────────────────────────────────── --}}
    <div class="detail-section" style="background:var(--white);padding-bottom:20px;">
        <div class="detail-section-title">
            <span>✨</span> Fasilitas & Ketentuan
        </div>
        <div class="fasilitas-chips">
            <div class="fasilitas-chip"><span>❄️</span> AC</div>
            <div class="fasilitas-chip"><span>🎵</span> Audio</div>
            <div class="fasilitas-chip"><span>🔌</span> Charger USB</div>
            <div class="fasilitas-chip"><span>📸</span> Kamera Mundur</div>
            <div class="fasilitas-chip"><span>🛡️</span> Asuransi Dasar</div>
            <div class="fasilitas-chip"><span>⛽</span> BBM Sendiri</div>
        </div>
        <p style="font-size:12px;color:var(--gray-400);margin-top:12px;line-height:1.6;">
            * Fasilitas dapat berbeda per kendaraan. Konfirmasi ke admin untuk detail lebih lanjut.
        </p>
    </div>

    <div class="section-sep"></div>

    {{-- ── Nomor Plat ───────────────────────────────────── --}}
    <div class="detail-section" style="background:var(--white);padding-bottom:20px;">
        <div class="detail-section-title">
            <span>🪪</span> Identitas Kendaraan
        </div>
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div class="plat-chip">
                🇮🇩 &nbsp;{{ strtoupper($mobil->plat_nomor) }}
            </div>
            <div style="font-size:13px;color:var(--gray-500);">
                {{ $mobil->merek }} {{ $mobil->nama }} · {{ $mobil->tahun }}
            </div>
        </div>
    </div>

    <div class="section-sep"></div>

    {{-- ── Kebijakan Sewa ───────────────────────────────── --}}
    <div class="detail-section" style="background:var(--white);padding-bottom:20px;">
        <div class="detail-section-title">
            <span>📋</span> Kebijakan Sewa
        </div>
        @php
            $kebijakan = [
                ['icon' => '🕐', 'judul' => 'Pembatalan',
                 'isi'  => 'Pembatalan gratis selama pemesanan masih berstatus Menunggu. Setelah dikonfirmasi, tidak dapat dibatalkan.'],
                ['icon' => '🔑', 'judul' => 'Pengambilan Kendaraan',
                 'isi'  => 'Kendaraan diambil di lokasi yang disepakati pada tanggal mulai sewa. Bawa KTP/SIM yang masih berlaku.'],
                ['icon' => '⛽', 'judul' => 'Bahan Bakar',
                 'isi'  => 'Penyewa bertanggung jawab atas bahan bakar selama masa sewa. Kembalikan dengan kondisi bahan bakar yang sama.'],
                ['icon' => '🛡️', 'judul' => 'Kerusakan',
                 'isi'  => 'Kerusakan dan kehilangan sepenuhnya menjadi tanggung jawab penyewa. Disarankan untuk memeriksa kondisi kendaraan sebelum membawa pergi.'],
            ];
        @endphp
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach($kebijakan as $item)
                <div style="display:flex;gap:12px;align-items:flex-start;">
                    <div style="font-size:20px;flex-shrink:0;margin-top:1px;">{{ $item['icon'] }}</div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:var(--gray-900);margin-bottom:3px;">
                            {{ $item['judul'] }}
                        </div>
                        <div style="font-size:13px;color:var(--gray-600);line-height:1.6;">
                            {{ $item['isi'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="section-sep"></div>

    {{-- ── Ulasan ───────────────────────────────────────── --}}
    <div class="detail-section" style="background:var(--white);padding-bottom:24px;">
        <div class="detail-section-title">
            <span>⭐</span> Ulasan Pelanggan
        </div>

        {{--
            TODO (F-03, F-17): Ganti placeholder di bawah dengan data nyata setelah
            tabel reviews, model Review, dan relasi Mobil::reviews() diimplementasi.

            Contoh setelah implementasi:
            @php
                $ulasans    = $mobil->reviews()->with('user')->latest()->take(10)->get();
                $rataRata   = $mobil->reviews()->avg('rating') ?? 0;
                $totalUlasan = $mobil->reviews()->count();
                $distribusi = $mobil->reviews()->selectRaw('rating, count(*) as jml')
                                    ->groupBy('rating')->pluck('jml', 'rating');
            @endphp
        --}}
        @php
            $ulasans     = collect(); // kosong sampai sistem ulasan diimplementasi
            $rataRata    = 0;
            $totalUlasan = 0;
            $distribusi  = collect();
        @endphp

        @if($totalUlasan > 0)
            {{-- Summary dengan bar distribusi --}}
            <div class="review-summary">
                <div class="review-score">
                    <div class="big">{{ number_format($rataRata, 1) }}</div>
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= round($rataRata) ? '★' : '☆' }}
                        @endfor
                    </div>
                    <div class="cnt">{{ $totalUlasan }} ulasan</div>
                </div>
                <div class="review-bars">
                    @foreach([5,4,3,2,1] as $bintang)
                        @php $jml = $distribusi->get($bintang, 0); @endphp
                        <div class="bar-row">
                            <span class="lbl">{{ $bintang }}</span>
                            <div class="bar-track">
                                <div class="bar-fill"
                                     style="width:{{ $totalUlasan > 0 ? ($jml/$totalUlasan*100) : 0 }}%"></div>
                            </div>
                            <span class="pct">{{ $jml }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Daftar ulasan --}}
            <div class="review-list">
                @foreach($ulasans as $ulasan)
                    <div class="review-item">
                        <div class="review-item-header">
                            <div class="reviewer-avatar">
                                {{ strtoupper(substr($ulasan->user->name, 0, 1)) }}
                            </div>
                            <div class="reviewer-info">
                                <div class="reviewer-name">{{ $ulasan->user->name }}</div>
                                <div class="reviewer-date">
                                    {{ $ulasan->created_at->translatedFormat('d M Y') }}
                                </div>
                            </div>
                            <div class="reviewer-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <span style="color:{{ $i <= $ulasan->rating ? '#f59e0b' : '#d1d5db' }}">★</span>
                                @endfor
                            </div>
                        </div>
                        @if($ulasan->komentar)
                            <p class="review-text">{{ $ulasan->komentar }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="review-empty">
                <div class="icon">💬</div>
                <div style="font-weight:600;color:var(--gray-600);margin-bottom:4px;">
                    Belum ada ulasan
                </div>
                <div style="font-size:12px;">
                    Jadilah yang pertama mengulas kendaraan ini setelah menyewa.
                </div>
            </div>
        @endif
    </div>

</div>{{-- /detail-wrap --}}

{{-- ─── Sticky Book Bar ──────────────────────────────────── --}}
<div class="sticky-bar">
    <div class="sticky-bar-price">
        <div class="lbl">Mulai dari</div>
        <div>
            <span class="val">Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}</span>
            <span class="unit">/ hari</span>
        </div>
    </div>

    @if($mobil->tersedia())
        <a href="{{ route('pemesanan.create', ['mobil_id' => $mobil->id]) }}"
           class="btn-book-main">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                <rect width="18" height="18" x="3" y="4" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8"  y1="2" x2="8"  y2="6"/>
                <line x1="3"  y1="10" x2="21" y2="10"/>
                <line x1="8"  y1="14" x2="8"  y2="14" stroke-width="3"/>
                <line x1="12" y1="14" x2="12" y2="14" stroke-width="3"/>
                <line x1="16" y1="14" x2="16" y2="14" stroke-width="3"/>
            </svg>
            Pesan Sekarang
        </a>
    @else
        <button class="btn-book-main disabled" disabled>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
            </svg>
            Sedang Disewa
        </button>
    @endif
</div>

@include('users.partials.bottom-nav')

<script>
/* ─── Gallery slider ─────────────────────────────────────── */
const TOTAL   = {{ $jumlahFoto }};
let   current = 0;

function goSlide(idx) {
    current = Math.max(0, Math.min(idx, TOTAL - 1));
    document.getElementById('slides').style.transform =
        `translateX(-${current * 100}%)`;

    // Dots
    document.querySelectorAll('.gallery-dot').forEach((d, i) => {
        d.classList.toggle('active', i === current);
    });

    // Counter
    const counter = document.getElementById('counter');
    if (counter) counter.textContent = `${current + 1} / ${TOTAL}`;

    // Arrows
    const prev = document.getElementById('arrow-prev');
    const next = document.getElementById('arrow-next');
    if (prev) prev.classList.toggle('hidden', current === 0);
    if (next) next.classList.toggle('hidden', current === TOTAL - 1);
}

function galeri(dir) { goSlide(current + dir); }

/* Touch swipe */
@if($jumlahFoto > 1)
(function () {
    const el    = document.getElementById('gallery');
    let   startX = 0, startY = 0, dragging = false;

    el.addEventListener('touchstart', e => {
        startX   = e.touches[0].clientX;
        startY   = e.touches[0].clientY;
        dragging = true;
    }, { passive: true });

    el.addEventListener('touchend', e => {
        if (!dragging) return;
        dragging = false;
        const dx = e.changedTouches[0].clientX - startX;
        const dy = e.changedTouches[0].clientY - startY;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 40) {
            galeri(dx < 0 ? 1 : -1);
        }
    }, { passive: true });
})();
// init arrows
goSlide(0);
@endif

/* ─── Deskripsi toggle ───────────────────────────────────── */
function toggleDeskripsi() {
    const txt = document.getElementById('deskripsi-text');
    const btn = document.getElementById('btn-expand');
    if (!txt) return;
    const expanded = !txt.classList.contains('clamped');
    txt.classList.toggle('clamped', expanded);
    btn.classList.toggle('expanded', !expanded);
    btn.childNodes[0].textContent = expanded ? 'Selengkapnya' : 'Sembunyikan ';
}

/* ─── Wishlist toggle via fetch (AJAX) ──────────────────────
   Tidak reload halaman, langsung update UI + toast.          */
function toggleWishlist(btn) {
    const url    = btn.dataset.url;
    const icon   = document.getElementById('heart-icon');
    const isFav  = btn.dataset.fav === 'true';   // state SEKARANG sebelum toggle
    const willFav = !isFav;

    // Optimistic UI — update tampilan langsung
    btn.classList.toggle('wishlisted', willFav);
    icon.setAttribute('fill',   willFav ? '#fca5a5' : 'none');
    icon.setAttribute('stroke', willFav ? '#dc2626' : 'var(--gray-700)');
    btn.dataset.fav   = willFav ? 'true' : 'false';
    btn.title         = willFav ? 'Hapus dari Favorit' : 'Tambah ke Favorit';
    btn.disabled      = true;   // cegah double-tap

    fetch(url, {
        method : 'POST',
        headers: {
            'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]')?.content
                             ?? '{{ csrf_token() }}',
            'Accept'       : 'application/json',
            'Content-Type' : 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        // Sinkronkan state dengan respons server (jaga-jaga jika konflik)
        const serverFav = data.favorited;
        btn.classList.toggle('wishlisted', serverFav);
        icon.setAttribute('fill',   serverFav ? '#fca5a5' : 'none');
        icon.setAttribute('stroke', serverFav ? '#dc2626' : 'var(--gray-700)');
        btn.dataset.fav = serverFav ? 'true' : 'false';
        btn.title       = serverFav ? 'Hapus dari Favorit' : 'Tambah ke Favorit';
        showToast(serverFav ? '❤️ Ditambahkan ke favorit' : '🤍 Dihapus dari favorit');
    })
    .catch(() => {
        // Rollback jika request gagal
        btn.classList.toggle('wishlisted', isFav);
        icon.setAttribute('fill',   isFav ? '#fca5a5' : 'none');
        icon.setAttribute('stroke', isFav ? '#dc2626' : 'var(--gray-700)');
        btn.dataset.fav = isFav ? 'true' : 'false';
        showToast('⚠️ Gagal, coba lagi', 'error');
    })
    .finally(() => { btn.disabled = false; });
}

/* ─── Share ──────────────────────────────────────────────── */
function shareHalaman() {
    const data = {
        title: '{{ $mobil->nama }} — Rental Mobil',
        text:  'Cek kendaraan ini: {{ $mobil->nama }} ({{ $mobil->merek }}, {{ $mobil->tahun }})',
        url:   window.location.href,
    };
    if (navigator.share) {
        navigator.share(data).catch(() => {});
    } else {
        navigator.clipboard.writeText(window.location.href)
            .then(() => showToast('🔗 Link disalin!'))
            .catch(() => showToast('Salin URL dari address bar'));
    }
}
</script>

</body>
</html>
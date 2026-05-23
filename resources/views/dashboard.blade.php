<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DriveEase Dashboard</title>

    @vite([
        'resources/css/dashboard.css',
        'resources/js/dashboard.js'
    ])
</head>
<body>
<!-- ═══ TOP NAV ═══════════════════════════════════════════ -->
<nav class="nav">
  <div class="nav-brand">Drive<span>Ease</span></div>
  <button class="nav-icon" onclick="showToast('Tidak ada notifikasi baru')">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    <span class="badge"></span>
  </button>
</nav>

<!-- ═══ CUSTOMER HOME PAGE ════════════════════════════════ -->
<div class="page active content" id="page-home">

  <!-- Hero -->
  <div class="hero">
    <div class="hero-greeting">Selamat datang, Budi 👋</div>
    <div class="hero-title">Mau ke mana <em>hari ini?</em></div>
  </div>

  <!-- Search Card -->
  <div style="padding: 0 20px; margin-top: -48px; position: relative; z-index: 10;">
    <div class="search-card">
      <div class="search-row">
        <div class="search-field full" onclick="showToast('Pilih kota tujuan')">
          <div class="sf-label">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
            Kota
          </div>
          <div class="sf-value">Jakarta Selatan</div>
        </div>
        <div class="search-field" onclick="showToast('Pilih tanggal mulai')">
          <div class="sf-label">📅 Mulai</div>
          <div class="sf-value">20 Jan 2025</div>
        </div>
        <div class="search-field" onclick="showToast('Pilih tanggal selesai')">
          <div class="sf-label">📅 Selesai</div>
          <div class="sf-value">23 Jan 2025</div>
        </div>
      </div>
      <button class="search-btn" onclick="showToast('Mencari kendaraan tersedia...')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        Cari Kendaraan
      </button>
    </div>
  </div>

  <div style="height: 24px;"></div>

  <!-- Active Booking Banner -->
  <div class="section" style="padding-top: 0;">
    <div class="booking-banner" onclick="switchPage('page-bookings')">
      <div class="booking-icon">🚗</div>
      <div class="booking-info">
        <p>Pemesanan aktif</p>
        <strong>Toyota Avanza 2022 · DE20XA1B2</strong>
        <div style="font-size: 12px; color: var(--gray-500); margin-top: 2px;">19 Jan – 22 Jan 2025</div>
      </div>
      <span class="booking-status status-progress">Berjalan</span>
    </div>
  </div>

  <!-- Categories -->
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
      <div class="cat-chip" onclick="filterCategory(this)"><div class="cat-icon">⚡</div><div class="cat-label">Elektrik</div></div>
    </div>
  </div>

  <!-- Promo -->
  <div class="section">
    <div class="promo-banner">
      <div>
        <div class="promo-label">🔥 Penawaran Terbatas</div>
        <div class="promo-title">Diskon 25%</div>
        <div class="promo-desc">Sewa 3 hari ke atas · Berlaku s/d 31 Jan</div>
      </div>
      <div class="promo-code" onclick="copyPromo()">HEMAT25</div>
    </div>
  </div>

  <!-- Car Listings -->
  <div class="section">
    <div class="section-header">
      <span class="section-title">Tersedia di Jakarta</span>
      <button class="see-all" onclick="showToast('Lihat semua kendaraan')">Lihat semua</button>
    </div>

    <div class="cars-grid">

      <!-- Car 1 -->
      <div class="car-card" onclick="showCarDetail()">
        <div class="car-image-wrap">
          <div class="car-image-placeholder">🚙</div>
          <span class="car-badge">⭐ Populer</span>
          <button class="car-wishlist" onclick="toggleWishlist(event, this)">🤍</button>
        </div>
        <div class="car-body">
          <div class="car-name">Toyota Innova Reborn</div>
          <div class="car-sub">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
            Jakarta Selatan · Budi Santoso
          </div>
          <div class="car-specs">
            <span class="spec-item"><span class="spec-icon">👥</span> 7 Kursi</span>
            <span class="spec-item"><span class="spec-icon">⚙️</span> Otomatis</span>
            <span class="spec-item"><span class="spec-icon">⛽</span> Bensin</span>
            <span class="spec-item"><span class="spec-icon">❄️</span> AC</span>
          </div>
          <div class="car-footer">
            <div class="car-price-wrap">
              <div class="car-price">Rp 450.000</div>
              <div class="car-price-unit">per hari</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
              <div class="car-rating">
                <svg viewBox="0 0 24 24" fill="#f59e0b" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                4.8
              </div>
              <button class="btn-book" onclick="bookCar(event)">Pesan</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Car 2 -->
      <div class="car-card" onclick="showCarDetail()">
        <div class="car-image-wrap">
          <div class="car-image-placeholder">🚗</div>
          <span class="car-badge driver">+ Supir</span>
          <button class="car-wishlist" onclick="toggleWishlist(event, this)">❤️</button>
        </div>
        <div class="car-body">
          <div class="car-name">Honda CR-V Turbo</div>
          <div class="car-sub">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
            Jakarta Barat · Mitra Pro
          </div>
          <div class="car-specs">
            <span class="spec-item"><span class="spec-icon">👥</span> 5 Kursi</span>
            <span class="spec-item"><span class="spec-icon">⚙️</span> Otomatis</span>
            <span class="spec-item"><span class="spec-icon">⛽</span> Turbo</span>
            <span class="spec-item"><span class="spec-icon">📡</span> GPS</span>
          </div>
          <div class="car-footer">
            <div class="car-price-wrap">
              <div class="car-price">Rp 650.000</div>
              <div class="car-price-unit">per hari</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
              <div class="car-rating">
                <svg viewBox="0 0 24 24" fill="#f59e0b" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                4.9
              </div>
              <button class="btn-book" onclick="bookCar(event)">Pesan</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Car 3 -->
      <div class="car-card" onclick="showCarDetail()">
        <div class="car-image-wrap">
          <div class="car-image-placeholder">🏎️</div>
          <span class="car-badge promo">💸 Promo</span>
          <button class="car-wishlist" onclick="toggleWishlist(event, this)">🤍</button>
        </div>
        <div class="car-body">
          <div class="car-name">Toyota Avanza G</div>
          <div class="car-sub">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
            Jakarta Timur · Andi Wijaya
          </div>
          <div class="car-specs">
            <span class="spec-item"><span class="spec-icon">👥</span> 7 Kursi</span>
            <span class="spec-item"><span class="spec-icon">⚙️</span> Manual</span>
            <span class="spec-item"><span class="spec-icon">⛽</span> Bensin</span>
            <span class="spec-item"><span class="spec-icon">🎵</span> Audio</span>
          </div>
          <div class="car-footer">
            <div class="car-price-wrap">
              <div style="text-decoration:line-through;color:var(--gray-400);font-size:12px;">Rp 320.000</div>
              <div class="car-price">Rp 240.000</div>
              <div class="car-price-unit">per hari</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
              <div class="car-rating">
                <svg viewBox="0 0 24 24" fill="#f59e0b" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                4.6
              </div>
              <button class="btn-book" onclick="bookCar(event)">Pesan</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <div style="height: 20px;"></div>
</div><!-- /page-home -->


<!-- ═══ BOOKINGS PAGE ══════════════════════════════════════ -->
<div class="page content" id="page-bookings">
  <div class="section">
    <div class="section-title" style="margin-bottom:16px;">Pemesanan Saya</div>

    <!-- Filter tabs -->
    <div style="display:flex;gap:8px;margin-bottom:16px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none;">
      <button class="cat-chip active" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;">Semua</button>
      <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;">Berjalan</button>
      <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;">Menunggu</button>
      <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;">Selesai</button>
    </div>

    <div style="display:flex;flex-direction:column;gap:12px;">
      <!-- Booking 1 -->
      <div class="booking-item" onclick="showToast('Detail pemesanan')">
        <div class="booking-item-header">
          <div><div class="booking-item-name">Toyota Innova Reborn</div><div class="booking-item-code">DE20XA1B2 · 3 hari</div></div>
          <span class="booking-status status-progress">Berjalan</span>
        </div>
        <div class="booking-item-body">
          <span>19 Jan – 22 Jan 2025</span>
          <strong style="color:var(--brand-400)">Rp 1.350.000</strong>
        </div>
      </div>
      <!-- Booking 2 -->
      <div class="booking-item" onclick="showToast('Detail pemesanan')">
        <div class="booking-item-header">
          <div><div class="booking-item-name">Honda CR-V Turbo</div><div class="booking-item-code">DE15BC3D4 · 2 hari</div></div>
          <span class="booking-status status-pending">Menunggu</span>
        </div>
        <div class="booking-item-body">
          <span>25 Jan – 27 Jan 2025</span>
          <strong style="color:var(--brand-400)">Rp 1.300.000</strong>
        </div>
      </div>
      <!-- Booking 3 -->
      <div class="booking-item" onclick="showToast('Detail pemesanan')">
        <div class="booking-item-header">
          <div><div class="booking-item-name">Toyota Avanza G</div><div class="booking-item-code">DE08EF5G6 · 5 hari</div></div>
          <span class="booking-status status-confirmed" style="background:#dcfce7;color:var(--success)">Selesai</span>
        </div>
        <div class="booking-item-body">
          <span>01 Jan – 06 Jan 2025</span>
          <strong style="color:var(--gray-700)">Rp 1.200.000</strong>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ═══ CHAT PAGE ══════════════════════════════════════════ -->
<div class="page content" id="page-chat">
  <div class="section-title" style="padding:20px 20px 12px;">Pesan</div>
  <div class="chat-list">
    <div class="chat-item" onclick="showToast('Buka percakapan')">
      <div class="chat-avatar">BS</div>
      <div class="chat-meta">
        <div class="chat-name">Budi Santoso <span style="font-size:11px;color:var(--gray-400);font-weight:400;">(Mitra)</span></div>
        <div class="chat-preview">Baik pak, kendaraan sudah siap pukul 08.00</div>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
        <div class="chat-time">10:23</div>
      </div>
    </div>
    <div class="chat-item" onclick="showToast('Buka percakapan')">
      <div class="chat-avatar" style="background:var(--accent-100);color:var(--accent-500);">MP</div>
      <div class="chat-meta">
        <div class="chat-name">Mitra Pro <span style="font-size:11px;color:var(--gray-400);font-weight:400;">(Mitra)</span></div>
        <div class="chat-preview">Silakan konfirmasi pickup point-nya pak 🙏</div>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
        <div class="chat-time">Kemarin</div>
        <div class="chat-unread">2</div>
      </div>
    </div>
    <div class="chat-item" onclick="showToast('Buka percakapan')">
      <div class="chat-avatar" style="background:#dcfce7;color:var(--success);">AW</div>
      <div class="chat-meta">
        <div class="chat-name">Andi Wijaya <span style="font-size:11px;color:var(--gray-400);font-weight:400;">(Mitra)</span></div>
        <div class="chat-preview">Terima kasih sudah sewa, semoga puas 😊</div>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
        <div class="chat-time">5 Jan</div>
      </div>
    </div>
  </div>
</div>


<!-- ═══ PROFILE PAGE ═══════════════════════════════════════ -->
<div class="page content" id="page-profile">
  <div style="padding:24px 20px;">
    <!-- Profile header -->
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
      <div style="width:64px;height:64px;border-radius:50%;background:var(--brand-100);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:var(--brand-600);">BP</div>
      <div>
        <div style="font-size:18px;font-weight:700;">Budi Pratama</div>
        <div style="font-size:13px;color:var(--gray-500);">budi.pratama@email.com</div>
        <div style="font-size:12px;color:var(--brand-400);font-weight:600;margin-top:2px;">Pelanggan · Bergabung Jan 2024</div>
      </div>
    </div>

    <!-- Menu -->
    <div style="background:var(--white);border-radius:var(--radius-md);border:1px solid var(--gray-100);overflow:hidden;">
      <div onclick="showToast('Edit profil')" style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);cursor:pointer;">
        <span style="font-size:14px;font-weight:600;">👤 Edit Profil</span>
        <span style="color:var(--gray-400);">›</span>
      </div>
      <div onclick="showToast('Alamat tersimpan')" style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);cursor:pointer;">
        <span style="font-size:14px;font-weight:600;">📍 Alamat Tersimpan</span>
        <span style="color:var(--gray-400);">›</span>
      </div>
      <div onclick="showToast('Metode pembayaran')" style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);cursor:pointer;">
        <span style="font-size:14px;font-weight:600;">💳 Metode Pembayaran</span>
        <span style="color:var(--gray-400);">›</span>
      </div>
      <div onclick="showToast('Bantuan & Dukungan')" style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);cursor:pointer;">
        <span style="font-size:14px;font-weight:600;">❓ Bantuan</span>
        <span style="color:var(--gray-400);">›</span>
      </div>
      <div onclick="logout()" style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;cursor:pointer;">
    <span style="font-size:14px;font-weight:600;color:var(--danger);">🚪 Keluar</span>
    <span style="color:var(--gray-400);">›</span>
</div>
    </div>
  </div>
</div>


<!-- ═══ BOTTOM NAV ══════════════════════════════════════════ -->
<nav class="bottom-nav" id="bottom-nav">
  <!-- Customer Nav -->
  <button class="nav-item active" id="nav-home" onclick="switchPage('page-home', 'nav-home')">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
    Beranda
  </button>
  <button class="nav-item" id="nav-bookings" onclick="switchPage('page-bookings', 'nav-bookings')">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
    Booking
  </button>
  <div class="nav-center">
    <button class="nav-center-btn" onclick="showToast('🔍 Cari kendaraan sekarang')">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    </button>
  </div>
  <button class="nav-item" id="nav-chat" onclick="switchPage('page-chat', 'nav-chat')">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    Pesan
  </button>
  <button class="nav-item" id="nav-profile" onclick="switchPage('page-profile', 'nav-profile')">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
    Profil
  </button>
</nav>

<!-- ═══ TOAST ═══════════════════════════════════════════════ -->
<div class="toast" id="toast"></div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>
</body>
</html>
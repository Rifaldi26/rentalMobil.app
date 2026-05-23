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

<!-- ═══ PARTNER HOME PAGE ══════════════════════════════════ -->
<div class="page active content" id="page-partner">
  <div class="section">
    <div class="section-title" style="margin-bottom:4px;">Dashboard Mitra</div>
    <div style="font-size:13px;color:var(--gray-500);margin-bottom:20px;">Januari 2025</div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card highlight">
        <div class="stat-label">Pendapatan Bulan Ini</div>
        <div class="stat-value">Rp 8,4jt</div>
        <div class="stat-sub">↑ 12% dari bulan lalu</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Saldo</div>
        <div class="stat-value">Rp 3,2jt</div>
        <div class="stat-sub"><a href="#" onclick="showToast('Cairkan saldo')" style="color:var(--brand-400);text-decoration:none;font-weight:600;">Cairkan →</a></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Pemesanan</div>
        <div class="stat-value">24</div>
        <div class="stat-sub">3 menunggu konfirmasi</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Rating</div>
        <div class="stat-value">4.8 ⭐</div>
        <div class="stat-sub">dari 18 ulasan</div>
      </div>
    </div>

    <!-- Pending Bookings -->
    <div class="section-header">
      <span class="section-title">Konfirmasi Pemesanan</span>
      <span style="background:var(--accent-100);color:var(--accent-500);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">3 baru</span>
    </div>

    <div class="booking-list">
      <div class="booking-item">
        <div class="booking-item-header">
          <div>
            <div class="booking-item-name">Siti Rahma</div>
            <div class="booking-item-code">Toyota Innova · 25–28 Jan · 3 hari</div>
          </div>
          <span class="booking-status status-pending">Pending</span>
        </div>
        <div class="booking-item-body">
          <span>📍 Jl. Sudirman, Jaksel</span>
          <strong style="color:var(--brand-400)">Rp 1.350.000</strong>
        </div>
        <div class="booking-item-footer">
          <button class="btn-confirm" onclick="showToast('✅ Pemesanan dikonfirmasi!')">Konfirmasi</button>
          <button class="btn-reject"  onclick="showToast('❌ Pemesanan ditolak')">Tolak</button>
        </div>
      </div>
      <div class="booking-item">
        <div class="booking-item-header">
          <div>
            <div class="booking-item-name">Ahmad Fauzi</div>
            <div class="booking-item-code">Toyota Innova · 30 Jan – 1 Feb · 2 hari</div>
          </div>
          <span class="booking-status status-pending">Pending</span>
        </div>
        <div class="booking-item-body">
          <span>📍 Bandara Soetta, Tangerang</span>
          <strong style="color:var(--brand-400)">Rp 900.000</strong>
        </div>
        <div class="booking-item-footer">
          <button class="btn-confirm" onclick="showToast('✅ Pemesanan dikonfirmasi!')">Konfirmasi</button>
          <button class="btn-reject"  onclick="showToast('❌ Pemesanan ditolak')">Tolak</button>
        </div>
      </div>
    </div>

    <!-- Quick actions -->
    <div style="margin-top:20px;">
      <div class="section-title" style="margin-bottom:14px;">Kelola Armada</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <button onclick="showToast('Tambah kendaraan baru')" style="padding:16px;background:var(--white);border:1.5px solid var(--gray-200);border-radius:var(--radius-md);font-family:var(--font);font-size:13px;font-weight:600;color:var(--gray-700);cursor:pointer;text-align:left;">
          ➕ Tambah Kendaraan
        </button>
        <button onclick="showToast('Lihat semua kendaraan')" style="padding:16px;background:var(--white);border:1.5px solid var(--gray-200);border-radius:var(--radius-md);font-family:var(--font);font-size:13px;font-weight:600;color:var(--gray-700);cursor:pointer;text-align:left;">
          🚗 Armada Saya (3)
        </button>
        <button onclick="showToast('Kelola jadwal kendaraan')" style="padding:16px;background:var(--white);border:1.5px solid var(--gray-200);border-radius:var(--radius-md);font-family:var(--font);font-size:13px;font-weight:600;color:var(--gray-700);cursor:pointer;text-align:left;">
          📅 Jadwal Blokir
        </button>
        <button onclick="showToast('Laporan keuangan')" style="padding:16px;background:var(--white);border:1.5px solid var(--gray-200);border-radius:var(--radius-md);font-family:var(--font);font-size:13px;font-weight:600;color:var(--gray-700);cursor:pointer;text-align:left;">
          📊 Laporan
        </button>
      </div>
    </div>
  </div>
  <div style="height:20px;"></div>
</div>

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
      <div onclick="switchRole('partner')" style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);cursor:pointer;background:var(--brand-50);">
        <span style="font-size:14px;font-weight:600;color:var(--brand-400);">🤝 Daftar sebagai Mitra</span>
        <span style="color:var(--brand-300);">›</span>
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
  <button class="nav-item active" id="nav-home" onclick="switchPage('page-partner', 'nav-home')">
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
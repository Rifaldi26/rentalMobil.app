// ─── State ─────────────────────────────────────────────────
let currentNav  = 'nav-home';

// ─── Page Navigation ───────────────────────────────────────
window.switchPage = function(pageId, navId) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById(pageId).classList.add('active');
  if (navId) document.getElementById(navId).classList.add('active');
  currentNav = navId;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

window.updateBottomNavForPartner = function() {
  const nav = document.getElementById('bottom-nav');
  nav.innerHTML = `
    <button class="nav-item active" onclick="switchPage('page-partner', null);setActiveNav(this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect width="7" height="9" x="3" y="11" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="11" rx="1"/><rect width="7" height="5" x="3" y="3" rx="1"/></svg>
      Dashboard
    </button>
    <button class="nav-item" onclick="switchPage('page-bookings', null);setActiveNav(this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
      Booking
    </button>
    <div class="nav-center">
      <button class="nav-center-btn" onclick="showToast('➕ Tambah kendaraan baru')">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
    </div>
    <button class="nav-item" onclick="switchPage('page-chat', null);setActiveNav(this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Pesan
    </button>
    <button class="nav-item" onclick="switchPage('page-profile', null);setActiveNav(this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
      Profil
    </button>`;
}

window.updateBottomNavForCustomer = function() {
  const nav = document.getElementById('bottom-nav');
  nav.innerHTML = `
    <button class="nav-item active" id="nav-home" onclick="switchPage('page-home', 'nav-home')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Beranda
    </button>
    <button class="nav-item" id="nav-bookings" onclick="switchPage('page-bookings', 'nav-bookings')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
      Booking
    </button>
    <div class="nav-center">
      <button class="nav-center-btn" onclick="showToast('🔍 Cari kendaraan sekarang')">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </button>
    </div>
    <button class="nav-item" id="nav-chat" onclick="switchPage('page-chat', 'nav-chat')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Pesan
    </button>
    <button class="nav-item" id="nav-profile" onclick="switchPage('page-profile', 'nav-profile')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
      Profil
    </button>`;
}

window.setActiveNav = function(el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  el.classList.add('active');
}

// ─── UI Interactions ───────────────────────────────────────
window.filterCategory = function(el) {
  document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
  showToast('Filter: ' + el.querySelector('.cat-label').textContent);
}

window.toggleWishlist = function(e, btn) {
  e.stopPropagation();
  const isSaved = btn.textContent === '❤️';
  btn.textContent = isSaved ? '🤍' : '❤️';
  showToast(isSaved ? 'Dihapus dari wishlist' : '❤️ Ditambahkan ke wishlist');
}

window.bookCar = function(e) {
  e.stopPropagation();
  showToast('🚗 Melanjutkan pemesanan...');
  setTimeout(() => switchPage('page-bookings', 'nav-bookings'), 800);
}

window.showCarDetail = function() {
  showToast('📋 Membuka detail kendaraan...');
}

window.copyPromo = function() {
  showToast('✅ Kode promo HEMAT25 disalin!');
}

// ─── Toast ─────────────────────────────────────────────────
let toastTimer;
window.showToast = function(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => t.classList.remove('show'), 2200);
}

window.logout = function () {
  showToast('🚪 Keluar dari akun...');
  document.getElementById('logout-form').submit();
};
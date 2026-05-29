<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda — Rental Mobil</title>
    @vite(['resources/css/dashboard.css'])
</head>
<body>

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

<div class="content" style="padding-bottom:100px;">

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-greeting">Selamat datang, {{ Auth::user()->name }} 👋</div>
        <div class="hero-title">Mau ke mana <em>hari ini?</em></div>
    </div>

    {{-- Search Card --}}
    <div style="padding:0 20px;margin-top:-48px;position:relative;z-index:10;">
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

    <div style="height:24px;"></div>

    {{-- Alert --}}
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

    {{-- Pemesanan Aktif --}}
    @php
        $pemesananAktif = Auth::user()->pemesanans()
            ->with('mobil')
            ->whereIn('status', ['pending', 'dikonfirmasi'])
            ->latest()
            ->first();
    @endphp

    @if ($pemesananAktif)
        <div class="section" style="padding-top:0;">
            <a href="{{ route('user.pemesanan.index') }}"
               style="text-decoration:none;display:flex;align-items:center;gap:12px;background:#fff;border-radius:var(--radius-md);padding:14px 16px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                <div class="booking-icon">🚗</div>
                <div class="booking-info" style="flex:1;">
                    <p style="font-size:12px;color:var(--gray-500);margin:0;">Pemesanan aktif</p>
                    <strong style="font-size:14px;color:var(--gray-900);">{{ $pemesananAktif->mobil->nama }} {{ $pemesananAktif->mobil->tahun }}</strong>
                    <div style="font-size:12px;color:var(--gray-500);margin-top:2px;">
                        {{ $pemesananAktif->tanggal_mulai->format('d M') }} –
                        {{ $pemesananAktif->tanggal_selesai->format('d M Y') }}
                    </div>
                </div>
                <span class="booking-status status-{{ $pemesananAktif->status === 'dikonfirmasi' ? 'progress' : 'pending' }}">
                    {{ $pemesananAktif->status === 'dikonfirmasi' ? 'Berjalan' : 'Menunggu' }}
                </span>
            </a>
        </div>
    @endif

    {{-- Kategori --}}
    <div class="section">
        <div class="section-header">
            <span class="section-title">Jenis Kendaraan</span>
        </div>
        <div class="categories">
            <div class="cat-chip active" onclick="filterCategory(this, '')"><div class="cat-icon">🚙</div><div class="cat-label">Semua</div></div>
            <div class="cat-chip" onclick="filterCategory(this, 'Toyota')"><div class="cat-icon">🚐</div><div class="cat-label">MPV</div></div>
            <div class="cat-chip" onclick="filterCategory(this, 'Honda')"><div class="cat-icon">🛻</div><div class="cat-label">SUV</div></div>
            <div class="cat-chip" onclick="filterCategory(this, 'Suzuki')"><div class="cat-icon">🚗</div><div class="cat-label">Sedan</div></div>
            <div class="cat-chip" onclick="filterCategory(this, 'Daihatsu')"><div class="cat-icon">🏎️</div><div class="cat-label">City Car</div></div>
            <div class="cat-chip" onclick="filterCategory(this, 'Mitsubishi')"><div class="cat-icon">🚚</div><div class="cat-label">Pickup</div></div>
        </div>
    </div>

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
            <div class="cars-grid" id="cars-grid">
                @foreach ($mobils as $mobil)
                    <div class="car-card" data-merek="{{ $mobil->merek }}"
                         onclick="window.location.href='{{ route('user.mobil.show', $mobil) }}'">
                        <div class="car-image-wrap">
                            @if ($mobil->foto)
                                <img src="{{ asset('storage/'.$mobil->foto) }}"
                                     alt="{{ $mobil->nama }}"
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <div class="car-image-placeholder">🚗</div>
                            @endif
                            <span class="car-badge">{{ $mobil->merek }}</span>
                            <button class="car-wishlist" onclick="event.stopPropagation();toggleFavorit(event, this, {{ $mobil->id }})">🤍</button>
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
                                <a href="{{ route('pemesanan.create', ['mobil_id' => $mobil->id]) }}"
                                   class="btn-book"
                                   onclick="event.stopPropagation()">
                                    Pesan
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div style="height:20px;"></div>
</div>

@include('users.partials.bottom-nav')

<script>
var toastTimer;

function filterCategory(el, merek) {
    document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('#cars-grid .car-card').forEach(card => {
        card.style.display = (!merek || card.dataset.merek === merek) ? '' : 'none';
    });
}

function toggleFavorit(e, btn, mobilId) {
    e.stopPropagation();
    const isFav = btn.textContent.trim() === '❤️';
    btn.textContent = isFav ? '🤍' : '❤️';

    fetch(`/favorit/${mobilId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                ?? '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => showToast(data.message))
    .catch(() => {
        btn.textContent = isFav ? '❤️' : '🤍';
        showToast('Gagal update favorit');
    });
}

function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = `toast ${type} show`;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
}

function confirmLogout() {
    document.getElementById('modal-logout').style.display = 'flex';
}
</script>

</body>
</html>
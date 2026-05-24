<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Rental Mobil</title>
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
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
        {{-- Badge merah jika ada pending --}}
        @php $pendingCount = \App\Models\Pemesanan::where('status','pending')->count(); @endphp
        @if ($pendingCount > 0)
            <span class="badge">{{ $pendingCount }}</span>
        @endif
    </button>
</nav>

{{-- ═══ HALAMAN BERANDA ADMIN ══════════════════════════════ --}}
<div class="page active content" id="page-home">

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-greeting">Halo, {{ Auth::user()->name }} 👋</div>
        <div class="hero-title">Selamat datang, <em>Admin!</em></div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="section" style="padding-bottom:0;margin-top:-16px;">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#16a34a;">
                ✅ {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- ─── Stats ─── --}}
    @php
        $totalMobil         = \App\Models\Mobil::count();
        $mobilTersedia      = \App\Models\Mobil::where('status','tersedia')->count();
        $mobilDisewa        = \App\Models\Mobil::where('status','disewa')->count();
        $totalPemesanan     = \App\Models\Pemesanan::count();
        $pemesananPending   = \App\Models\Pemesanan::where('status','pending')->count();
        $pemesananBerjalan  = \App\Models\Pemesanan::where('status','dikonfirmasi')->count();
        $pendapatanBulanIni = \App\Models\Pemesanan::where('status','selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_harga');
        $pendapatanTotal    = \App\Models\Pemesanan::where('status','selesai')->sum('total_harga');
        $totalPelanggan     = \App\Models\User::where('role','pelanggan')->count();
    @endphp

    {{-- Stats Banner (mirip booking-banner pelanggan) --}}
    <div class="section" style="padding-top:0;margin-top:-28px;position:relative;z-index:10;">
        <div style="background:#fff;border-radius:var(--radius-lg);box-shadow:0 4px 20px rgba(0,0,0,.08);padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">

            <div style="background:linear-gradient(135deg,#1d4ed8,#2563eb);border-radius:var(--radius-md);padding:14px;">
                <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Pendapatan Bulan Ini</div>
                <div style="font-size:20px;font-weight:800;color:#fff;">
                    Rp {{ number_format($pendapatanBulanIni/1000000, 1, ',', '.') }}jt
                </div>
                <div style="font-size:11px;color:rgba(255,255,255,.6);margin-top:2px;">
                    Total: Rp {{ number_format($pendapatanTotal/1000000, 1, ',', '.') }}jt
                </div>
            </div>

            <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                <div style="font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Pemesanan</div>
                <div style="font-size:20px;font-weight:800;color:var(--gray-900);">{{ $totalPemesanan }}</div>
                <div style="font-size:11px;margin-top:2px;">
                    @if ($pemesananPending > 0)
                        <span style="color:var(--accent-500);font-weight:700;">{{ $pemesananPending }} menunggu ⚡</span>
                    @else
                        <span style="color:var(--success);">Semua ditangani ✅</span>
                    @endif
                </div>
            </div>

            <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                <div style="font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Armada</div>
                <div style="font-size:20px;font-weight:800;color:var(--gray-900);">{{ $totalMobil }} unit</div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">
                    {{ $mobilTersedia }} tersedia · <span style="color:var(--danger);">{{ $mobilDisewa }} disewa</span>
                </div>
            </div>

            <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                <div style="font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Pelanggan</div>
                <div style="font-size:20px;font-weight:800;color:var(--gray-900);">{{ $totalPelanggan }}</div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Pengguna terdaftar</div>
            </div>

        </div>
    </div>

    {{-- ─── Konfirmasi Pemesanan ─── --}}
    @php
        $pemesananMenunggu = \App\Models\Pemesanan::with(['user','mobil'])
            ->where('status','pending')->latest()->take(5)->get();
    @endphp

    <div class="section">
        <div class="section-header">
            <span class="section-title">Konfirmasi Pemesanan</span>
            @if ($pemesananPending > 0)
                <span style="background:#fff7ed;color:var(--accent-500);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">
                    {{ $pemesananPending }} baru
                </span>
            @endif
        </div>

        <div style="display:flex;flex-direction:column;gap:10px;margin-top:12px;">
            @forelse ($pemesananMenunggu as $p)
                @php $durasi = $p->tanggal_mulai->diffInDays($p->tanggal_selesai); @endphp
                <div class="booking-item">
                    <div class="booking-item-header">
                        <div>
                            <div class="booking-item-name">{{ $p->user->name }}</div>
                            <div class="booking-item-code">
                                {{ $p->mobil->nama }} ·
                                {{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M') }} ·
                                {{ $durasi }} hari
                            </div>
                        </div>
                        <span class="booking-status status-pending">Menunggu</span>
                    </div>
                    <div class="booking-item-body">
                        <span>📞 {{ $p->user->no_hp ?? '-' }}</span>
                        <strong style="color:var(--brand-400);">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                    </div>
                    <div class="booking-item-footer">
                        <form action="{{ route('admin.pemesanan.konfirmasi', $p) }}" method="POST" style="flex:1;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-confirm"
                                onclick="return confirm('Konfirmasi pemesanan {{ addslashes($p->user->name) }}?')">
                                ✅ Konfirmasi
                            </button>
                        </form>
                        <form action="{{ route('admin.pemesanan.tolak', $p) }}" method="POST" style="flex:1;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-reject"
                                onclick="return confirm('Tolak pemesanan ini?')">
                                ❌ Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="booking-item" style="text-align:center;padding:28px;color:var(--gray-500);">
                    <div style="font-size:32px;margin-bottom:8px;">✅</div>
                    <div style="font-weight:600;font-size:13px;">Tidak ada pemesanan pending</div>
                </div>
            @endforelse

            @if ($pemesananPending > 5)
                <a href="{{ route('admin.pemesanan.index', ['status' => 'pending']) }}"
                   style="text-align:center;font-size:13px;color:var(--brand-400);font-weight:600;text-decoration:none;padding:8px;display:block;">
                    Lihat semua {{ $pemesananPending }} pemesanan →
                </a>
            @endif
        </div>
    </div>

    {{-- ─── Sedang Berjalan ─── --}}
    @php
        $sedangBerjalan = \App\Models\Pemesanan::with(['user','mobil'])
            ->where('status','dikonfirmasi')->latest()->take(3)->get();
    @endphp

    <div class="section">
        <div class="section-header">
            <span class="section-title">Sedang Berjalan</span>
            <span style="font-size:12px;color:var(--gray-500);">{{ $pemesananBerjalan }} aktif</span>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px;margin-top:12px;">
            @forelse ($sedangBerjalan as $p)
                <div class="booking-item">
                    <div class="booking-item-header">
                        <div>
                            <div class="booking-item-name">{{ $p->user->name }}</div>
                            <div class="booking-item-code">
                                {{ $p->mobil->nama }} · s/d {{ $p->tanggal_selesai->format('d M Y') }}
                            </div>
                        </div>
                        <span class="booking-status status-progress">Berjalan</span>
                    </div>
                    <div class="booking-item-body">
                        <span>🚗 {{ $p->mobil->plat_nomor }}</span>
                        <strong style="color:var(--brand-400);">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                    </div>
                    <div class="booking-item-footer">
                        <form action="{{ route('admin.pemesanan.selesai', $p) }}" method="POST" style="flex:1;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-confirm"
                                onclick="return confirm('Tandai pemesanan ini selesai?')">
                                🏁 Tandai Selesai
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="booking-item" style="text-align:center;padding:20px;color:var(--gray-500);font-size:13px;">
                    Tidak ada pemesanan berjalan
                </div>
            @endforelse
        </div>
    </div>

    {{-- ─── Ringkasan Bulan Ini ─── --}}
    <div class="section">
        <div class="section-title" style="margin-bottom:14px;">Ringkasan Bulan Ini</div>
        <div style="background:#fff;border-radius:var(--radius-md);border:1px solid var(--gray-100);padding:16px;display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:var(--gray-500);">Pemesanan Selesai</span>
                <strong>{{ \App\Models\Pemesanan::where('status','selesai')->whereMonth('updated_at',now()->month)->count() }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:var(--gray-500);">Pemesanan Dibatalkan</span>
                <strong>{{ \App\Models\Pemesanan::where('status','dibatalkan')->whereMonth('updated_at',now()->month)->count() }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:var(--gray-500);">Pelanggan Baru</span>
                <strong>{{ \App\Models\User::where('role','pelanggan')->whereMonth('created_at',now()->month)->count() }}</strong>
            </div>
            <hr style="border:none;border-top:1px solid var(--gray-100);">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;font-weight:700;">Total Pendapatan</span>
                <strong style="color:var(--success);font-size:15px;">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <div style="height:20px;"></div>
</div>{{-- /page-home --}}


{{-- ═══ HALAMAN PEMESANAN ADMIN ════════════════════════════ --}}
<div class="page content" id="page-bookings">
    <div class="section">
        <div class="section-title" style="margin-bottom:16px;">Semua Pemesanan</div>

        {{-- Filter tabs --}}
        <div style="display:flex;gap:8px;margin-bottom:16px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none;">
            <button class="cat-chip active" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBookingAdmin('semua', this)">Semua</button>
            <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBookingAdmin('pending', this)">⏳ Menunggu</button>
            <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBookingAdmin('dikonfirmasi', this)">🔵 Berjalan</button>
            <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBookingAdmin('selesai', this)">✅ Selesai</button>
            <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
                onclick="filterBookingAdmin('dibatalkan', this)">❌ Dibatalkan</button>
        </div>

        @php
            $semuaPemesanan = \App\Models\Pemesanan::with(['user','mobil'])->latest()->get();
        @endphp

        @if ($semuaPemesanan->isEmpty())
            <div style="text-align:center;padding:60px 20px;color:var(--gray-500);">
                <div style="font-size:48px;margin-bottom:12px;">📋</div>
                <div style="font-weight:600;">Belum ada pemesanan</div>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:10px;" id="admin-booking-list">
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
                                <div class="booking-item-name">{{ $p->user->name }}</div>
                                <div class="booking-item-code">
                                    {{ $p->mobil->nama }} · {{ $p->mobil->plat_nomor }} · {{ $durasi }} hari
                                </div>
                            </div>
                            <span class="booking-status {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="booking-item-body">
                            <span>{{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M Y') }}</span>
                            <strong style="color:var(--brand-400);">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                        </div>
                        {{-- Aksi --}}
                        @if ($p->status === 'pending')
                            <div class="booking-item-footer" style="margin-top:10px;">
                                <form action="{{ route('admin.pemesanan.konfirmasi', $p) }}" method="POST" style="flex:1;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-confirm"
                                        onclick="return confirm('Konfirmasi pemesanan {{ addslashes($p->user->name) }}?')">
                                        ✅ Konfirmasi
                                    </button>
                                </form>
                                <form action="{{ route('admin.pemesanan.tolak', $p) }}" method="POST" style="flex:1;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-reject"
                                        onclick="return confirm('Tolak pemesanan ini?')">
                                        ❌ Tolak
                                    </button>
                                </form>
                            </div>
                        @elseif ($p->status === 'dikonfirmasi')
                            <div class="booking-item-footer" style="margin-top:10px;">
                                <form action="{{ route('admin.pemesanan.selesai', $p) }}" method="POST" style="flex:1;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-confirm"
                                        onclick="return confirm('Tandai selesai?')">
                                        🏁 Tandai Selesai
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


{{-- ═══ HALAMAN ARMADA ════════════════════════════════════ --}}
<div class="page content" id="page-armada">
    <div class="section">
        <div class="section-header" style="margin-bottom:16px;">
            <span class="section-title">Kelola Armada</span>
            <a href="{{ route('admin.mobil.create') }}"
               style="font-size:13px;font-weight:700;color:#fff;background:var(--brand-400);padding:8px 14px;border-radius:var(--radius-sm);text-decoration:none;">
                + Tambah
            </a>
        </div>

        @php $mobils = \App\Models\Mobil::latest()->get(); @endphp

        @if ($mobils->isEmpty())
            <div style="text-align:center;padding:60px 20px;color:var(--gray-500);">
                <div style="font-size:48px;margin-bottom:12px;">🚗</div>
                <div style="font-weight:600;">Belum ada data mobil</div>
                <a href="{{ route('admin.mobil.create') }}"
                   style="display:inline-block;margin-top:12px;color:var(--brand-400);font-weight:700;text-decoration:none;">
                    + Tambah Mobil Pertama
                </a>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach ($mobils as $mobil)
                    <div class="booking-item">
                        <div style="display:flex;gap:12px;align-items:center;">
                            {{-- Foto --}}
                            <div style="width:64px;height:52px;border-radius:var(--radius-sm);overflow:hidden;flex-shrink:0;background:var(--gray-100);display:flex;align-items:center;justify-content:center;">
                                @if ($mobil->foto)
                                    <img src="{{ asset('storage/'.$mobil->foto) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <span style="font-size:24px;">🚗</span>
                                @endif
                            </div>
                            {{-- Info --}}
                            <div style="flex:1;">
                                <div class="booking-item-name">{{ $mobil->nama }}</div>
                                <div class="booking-item-code">{{ $mobil->merek }} · {{ $mobil->tahun }} · {{ $mobil->plat_nomor }}</div>
                                <div style="font-size:13px;font-weight:700;color:var(--brand-400);margin-top:2px;">
                                    Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}/hari
                                </div>
                            </div>
                            {{-- Status --}}
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
                                <span class="booking-status {{ $mobil->status === 'tersedia' ? 'status-confirmed' : 'status-cancelled' }}">
                                    {{ $mobil->status === 'tersedia' ? '✅ Tersedia' : '🔴 Disewa' }}
                                </span>
                            </div>
                        </div>
                        {{-- Aksi --}}
                        <div style="display:flex;gap:8px;margin-top:10px;">
                            <a href="{{ route('admin.mobil.edit', $mobil) }}"
                               style="flex:1;padding:8px;background:var(--brand-50);color:var(--brand-400);border:none;border-radius:var(--radius-sm);font-size:12px;font-weight:700;text-align:center;text-decoration:none;">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('admin.mobil.toggle', $mobil) }}" method="POST" style="flex:1;">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    style="width:100%;padding:8px;background:#fff7ed;color:var(--accent-500);border:none;border-radius:var(--radius-sm);font-size:12px;font-weight:700;cursor:pointer;">
                                    🔄 Toggle Status
                                </button>
                            </form>
                            <form action="{{ route('admin.mobil.destroy', $mobil) }}" method="POST" style="flex:1;"
                                onsubmit="return confirm('Hapus {{ addslashes($mobil->nama) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    style="width:100%;padding:8px;background:#fef2f2;color:var(--danger);border:none;border-radius:var(--radius-sm);font-size:12px;font-weight:700;cursor:pointer;">
                                    🗑 Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>


{{-- ═══ HALAMAN PROFIL ADMIN ══════════════════════════════ --}}
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
                <div style="font-size:12px;color:var(--brand-400);font-weight:600;margin-top:2px;">
                    Admin · Bergabung {{ Auth::user()->created_at->translatedFormat('M Y') }}
                </div>
            </div>
        </div>

        {{-- Statistik --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:24px;">
            <div style="background:var(--brand-50);border-radius:var(--radius-md);padding:12px;text-align:center;">
                <div style="font-size:20px;font-weight:800;color:var(--brand-400);">{{ \App\Models\Mobil::count() }}</div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Total Mobil</div>
            </div>
            <div style="background:#f0fdf4;border-radius:var(--radius-md);padding:12px;text-align:center;">
                <div style="font-size:20px;font-weight:800;color:var(--success);">{{ \App\Models\Pemesanan::where('status','selesai')->count() }}</div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Selesai</div>
            </div>
            <div style="background:#fff7ed;border-radius:var(--radius-md);padding:12px;text-align:center;">
                <div style="font-size:20px;font-weight:800;color:var(--accent-500);">{{ \App\Models\User::where('role','pelanggan')->count() }}</div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Pelanggan</div>
            </div>
        </div>

        {{-- Menu --}}
        <div style="background:#fff;border-radius:var(--radius-md);border:1px solid var(--gray-100);overflow:hidden;">
            <a href="{{ route('admin.mobil.index') }}"
               style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);text-decoration:none;">
                <span style="font-size:14px;font-weight:600;color:var(--gray-900);">🚗 Kelola Mobil</span>
                <span style="color:var(--gray-400);">›</span>
            </a>
            <a href="{{ route('admin.pemesanan.index') }}"
               style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);text-decoration:none;">
                <span style="font-size:14px;font-weight:600;color:var(--gray-900);">📋 Semua Pemesanan</span>
                <span style="color:var(--gray-400);">›</span>
            </a>
            <a href="{{ route('admin.user.index') }}"
               style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);text-decoration:none;">
                <span style="font-size:14px;font-weight:600;color:var(--gray-900);">👥 Data Pelanggan</span>
                <span style="color:var(--gray-400);">›</span>
            </a>
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
        Pemesanan
        @if ($pemesananPending > 0)
            <span style="position:absolute;top:6px;right:calc(50% - 24px);background:var(--danger);color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px;">
                {{ $pemesananPending }}
            </span>
        @endif
    </button>
    <div class="nav-center">
        <button class="nav-center-btn" onclick="switchPage('page-armada', 'nav-armada')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/>
                <rect width="13" height="8" x="8" y="13" rx="2"/>
                <path d="M19 17v-4"/>
                <path d="M21 13h-6"/>
            </svg>
        </button>
    </div>
    <button class="nav-item" id="nav-armada" onclick="switchPage('page-armada', 'nav-armada')" style="display:none;"></button>
    <button class="nav-item" id="nav-chat" onclick="showToast('Fitur pesan akan segera hadir 💬')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        Pesan
    </button>
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

function switchPage(pageId, navId) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById(pageId).classList.add('active');
    if (navId) document.getElementById(navId)?.classList.add('active');
}

function filterBookingAdmin(status, el) {
    document.querySelectorAll('#page-bookings .cat-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('#admin-booking-list .booking-item').forEach(item => {
        item.style.display = (status === 'semua' || item.dataset.status === status) ? '' : 'none';
    });
}

function confirmLogout() {
    document.getElementById('modal-logout').style.display = 'flex';
}

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
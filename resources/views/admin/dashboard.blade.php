<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Rental Mobil</title>
    @vite(['resources/css/admin.css'])
</head>
<body>

@include('admin.partials.sidebar')

<div class="admin-main">

    {{-- ─── Header ─── --}}
    <div class="admin-header">
        <div>
            <div class="admin-title">Dashboard</div>
            <div class="admin-subtitle">{{ now()->translatedFormat('F Y') }}</div>
        </div>
        <div style="font-size:13px;color:var(--gray-500);">
            Halo, <strong>{{ Auth::user()->name }}</strong> 👋
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif

    {{-- ─── Stats ─── --}}
    @php
        $totalMobil        = \App\Models\Mobil::count();
        $mobilTersedia     = \App\Models\Mobil::where('status','tersedia')->count();
        $mobilDisewa       = \App\Models\Mobil::where('status','disewa')->count();
        $totalPemesanan    = \App\Models\Pemesanan::count();
        $pemesananPending  = \App\Models\Pemesanan::where('status','pending')->count();
        $pemesananBerjalan = \App\Models\Pemesanan::where('status','dikonfirmasi')->count();
        $pemesananSelesai  = \App\Models\Pemesanan::where('status','selesai')->count();
        $pendapatanBulanIni = \App\Models\Pemesanan::where('status','selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_harga');
        $pendapatanTotal   = \App\Models\Pemesanan::where('status','selesai')->sum('total_harga');
        $totalPelanggan    = \App\Models\User::where('role','pelanggan')->count();
    @endphp

    <div class="stats-grid">

        <div class="stat-card highlight">
            <div class="stat-label">Pendapatan Bulan Ini</div>
            <div class="stat-value">
                Rp {{ number_format($pendapatanBulanIni/1000000, 1, ',', '.') }}jt
            </div>
            <div class="stat-sub">
                Total: Rp {{ number_format($pendapatanTotal/1000000, 1, ',', '.') }}jt
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Pemesanan</div>
            <div class="stat-value">{{ $totalPemesanan }}</div>
            <div class="stat-sub">
                @if ($pemesananPending > 0)
                    <span style="color:var(--accent-500);font-weight:700;">
                        {{ $pemesananPending }} menunggu konfirmasi
                    </span>
                @else
                    Semua sudah ditangani ✅
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Armada Mobil</div>
            <div class="stat-value">{{ $totalMobil }} unit</div>
            <div class="stat-sub">
                {{ $mobilTersedia }} tersedia ·
                <span style="color:var(--danger);">{{ $mobilDisewa }} disewa</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Pelanggan</div>
            <div class="stat-value">{{ $totalPelanggan }}</div>
            <div class="stat-sub">Pengguna terdaftar</div>
        </div>

    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">

        {{-- ─── Konfirmasi Pemesanan ─── --}}
        <div>
            <div class="section-header" style="margin-bottom:14px;">
                <span class="section-title">Konfirmasi Pemesanan</span>
                @if ($pemesananPending > 0)
                    <span class="badge-new">{{ $pemesananPending }} baru</span>
                @endif
            </div>

            @php
                $pemesananMenunggu = \App\Models\Pemesanan::with(['user','mobil'])
                    ->where('status','pending')
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp

            @if ($pemesananMenunggu->isEmpty())
                <div class="card">
                    <div class="card-body" style="text-align:center;padding:40px;color:var(--gray-500);">
                        <div style="font-size:40px;margin-bottom:10px;">✅</div>
                        <div style="font-weight:600;">Tidak ada pemesanan pending</div>
                    </div>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach ($pemesananMenunggu as $p)
                        @php
                            $durasi = $p->tanggal_mulai->diffInDays($p->tanggal_selesai);
                        @endphp
                        <div class="booking-item">
                            <div class="booking-item-header">
                                <div>
                                    <div class="booking-item-name">{{ $p->user->name }}</div>
                                    <div class="booking-item-code">
                                        {{ $p->mobil->nama }} ·
                                        {{ $p->tanggal_mulai->format('d M') }} –
                                        {{ $p->tanggal_selesai->format('d M') }} ·
                                        {{ $durasi }} hari
                                    </div>
                                </div>
                                <span class="booking-status status-pending">Pending</span>
                            </div>
                            <div class="booking-item-body">
                                <span>📞 {{ $p->user->no_hp ?? '-' }}</span>
                                <strong style="color:var(--brand-400)">
                                    Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                                </strong>
                            </div>
                            <div class="booking-item-footer">
                                {{-- Konfirmasi --}}
                                <form action="{{ route('admin.pemesanan.konfirmasi', $p) }}" method="POST" style="flex:1;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-confirm"
                                        onclick="return confirm('Konfirmasi pemesanan {{ $p->user->name }}?')">
                                        ✅ Konfirmasi
                                    </button>
                                </form>
                                {{-- Tolak --}}
                                <form action="{{ route('admin.pemesanan.tolak', $p) }}" method="POST" style="flex:1;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-reject"
                                        onclick="return confirm('Tolak pemesanan ini?')">
                                        ❌ Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach

                    @if ($pemesananPending > 5)
                        <a href="{{ route('admin.pemesanan.index') }}"
                           style="text-align:center;font-size:13px;color:var(--brand-400);font-weight:600;text-decoration:none;padding:8px;">
                            Lihat semua {{ $pemesananPending }} pemesanan →
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- ─── Kolom Kanan ─── --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Pemesanan Berjalan --}}
            <div>
                <div class="section-header" style="margin-bottom:14px;">
                    <span class="section-title">Sedang Berjalan</span>
                    <span style="font-size:12px;color:var(--gray-500);">{{ $pemesananBerjalan }} aktif</span>
                </div>

                @php
                    $sedangBerjalan = \App\Models\Pemesanan::with(['user','mobil'])
                        ->where('status','dikonfirmasi')
                        ->latest()
                        ->take(3)
                        ->get();
                @endphp

                @if ($sedangBerjalan->isEmpty())
                    <div class="card">
                        <div class="card-body" style="text-align:center;padding:24px;color:var(--gray-500);font-size:13px;">
                            Tidak ada pemesanan berjalan
                        </div>
                    </div>
                @else
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach ($sedangBerjalan as $p)
                            <div class="booking-item">
                                <div class="booking-item-header">
                                    <div>
                                        <div class="booking-item-name">{{ $p->user->name }}</div>
                                        <div class="booking-item-code">
                                            {{ $p->mobil->nama }} ·
                                            s/d {{ $p->tanggal_selesai->format('d M Y') }}
                                        </div>
                                    </div>
                                    <span class="booking-status status-progress">Berjalan</span>
                                </div>
                                <div class="booking-item-body">
                                    <span>🚗 {{ $p->mobil->plat_nomor }}</span>
                                    <strong style="color:var(--brand-400)">
                                        Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                                    </strong>
                                </div>
                                {{-- Tandai Selesai --}}
                                <div class="booking-item-footer">
                                    <form action="{{ route('admin.pemesanan.selesai', $p) }}" method="POST" style="flex:1;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-confirm"
                                            onclick="return confirm('Tandai pemesanan ini selesai?')">
                                            🏁 Tandai Selesai
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quick Actions --}}
            <div>
                <div class="section-title" style="margin-bottom:14px;">Kelola Armada</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <a href="{{ route('admin.mobil.create') }}" class="quick-action">
                        <span class="qa-icon">➕</span>
                        <span>Tambah Mobil</span>
                    </a>
                    <a href="{{ route('admin.mobil.index') }}" class="quick-action">
                        <span class="qa-icon">🚗</span>
                        <span>Armada ({{ $totalMobil }})</span>
                    </a>
                    <a href="{{ route('admin.pemesanan.index') }}" class="quick-action">
                        <span class="qa-icon">📋</span>
                        <span>Semua Pemesanan</span>
                    </a>
                    <a href="{{ route('admin.user.index') }}" class="quick-action">
                        <span class="qa-icon">👥</span>
                        <span>Pelanggan</span>
                    </a>
                </div>
            </div>

            {{-- Pemesanan Selesai Bulan Ini --}}
            <div class="card">
                <div class="card-body">
                    <div class="section-title" style="margin-bottom:14px;">Ringkasan Bulan Ini</div>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;color:var(--gray-500);">Pemesanan Selesai</span>
                            <strong>
                                {{ \App\Models\Pemesanan::where('status','selesai')
                                    ->whereMonth('updated_at', now()->month)->count() }}
                            </strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;color:var(--gray-500);">Pemesanan Dibatalkan</span>
                            <strong>
                                {{ \App\Models\Pemesanan::where('status','dibatalkan')
                                    ->whereMonth('updated_at', now()->month)->count() }}
                            </strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;color:var(--gray-500);">Pelanggan Baru</span>
                            <strong>
                                {{ \App\Models\User::where('role','pelanggan')
                                    ->whereMonth('created_at', now()->month)->count() }}
                            </strong>
                        </div>
                        <hr style="border:none;border-top:1px solid var(--gray-100);margin:4px 0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;font-weight:700;">Pendapatan</span>
                            <strong style="color:var(--success);">
                                Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div style="height:32px;"></div>

</div>{{-- /admin-main --}}

<div class="toast" id="toast"></div>

<script>
let toastTimer;
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
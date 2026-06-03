<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Rental Mobil</title>
    @vite(['resources/css/dashboard.css'])
</head>
<body>

<nav class="nav">
    <div class="nav-brand">Rental<span>Mobil</span></div>
    <a href="{{ route('admin.notifikasi') }}" class="nav-icon" style="position:relative;text-decoration:none;color:inherit;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <span id="notif-badge" style="display:none;position:absolute;top:-3px;right:-3px;width:10px;height:10px;background:#ef4444;border-radius:50%;border:2px solid #fff;"></span>
    </a>
</nav>

<div class="content" style="padding-bottom:100px;">

    <div class="hero">
        <div class="hero-greeting">Halo, {{ Auth::user()->name }} 👋</div>
        <div class="hero-title">Selamat datang, <em>Admin!</em></div>
    </div>
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

    {{-- Stats --}}
    <div class="section" style="padding-top:0;margin-top:-28px;position:relative;z-index:10;">
        <div style="background:#fff;border-radius:var(--radius-lg);box-shadow:0 4px 20px rgba(0,0,0,.08);padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <a href="{{ route('admin.pemesanan.index') }}" style="text-decoration:none;background:linear-gradient(135deg,#1d4ed8,#2563eb);border-radius:var(--radius-md);padding:14px;">
                <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Pendapatan Bulan Ini</div>
                <div style="font-size:20px;font-weight:800;color:#fff;">Rp {{ number_format($pendapatanBulanIni/1000000, 1, ',', '.') }}jt</div>
                <div style="font-size:11px;color:rgba(255,255,255,.6);margin-top:2px;">Total: Rp {{ number_format($pendapatanTotal/1000000, 1, ',', '.') }}jt</div>
            </a>
            <a href="{{ route('admin.pemesanan.index') }}" style="text-decoration:none;background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                <div style="font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Pemesanan</div>
                <div style="font-size:20px;font-weight:800;color:var(--gray-900);">{{ $totalPemesanan }}</div>
                <div style="font-size:11px;margin-top:2px;">
                    @if ($pemesananPending > 0)
                        <span style="color:var(--accent-500);font-weight:700;">{{ $pemesananPending }} menunggu ⚡</span>
                    @else
                        <span style="color:var(--success);">Semua ditangani ✅</span>
                    @endif
                </div>
            </a>
            <a href="{{ route('admin.mobil.index') }}" style="text-decoration:none;background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                <div style="font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Armada</div>
                <div style="font-size:20px;font-weight:800;color:var(--gray-900);">{{ $totalMobil }} unit</div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">{{ $mobilTersedia }} tersedia · <span style="color:var(--danger);">{{ $mobilDisewa }} disewa</span></div>
            </a>
            <a href="{{ route('admin.user.index') }}" style="text-decoration:none;background:var(--gray-50);border-radius:var(--radius-md);padding:14px;">
                <div style="font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Pelanggan</div>
                <div style="font-size:20px;font-weight:800;color:var(--gray-900);">{{ $totalPelanggan }}</div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Pengguna terdaftar</div>
            </a>
        </div>
    </div>

    {{-- Konfirmasi Pemesanan --}}
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
                            <div class="booking-item-code">{{ $p->mobil->nama }} · {{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M') }} · {{ $durasi }} hari</div>
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
                            <button type="submit" class="btn-confirm" onclick="return confirm('Konfirmasi pemesanan {{ addslashes($p->user->name) }}?')">✅ Konfirmasi</button>
                        </form>
                        <form action="{{ route('admin.pemesanan.tolak', $p) }}" method="POST" style="flex:1;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-reject" onclick="return confirm('Tolak pemesanan ini?')">❌ Tolak</button>
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
                <a href="{{ route('admin.pemesanan.index') }}"
                   style="text-align:center;font-size:13px;color:var(--brand-400);font-weight:600;text-decoration:none;padding:8px;display:block;">
                    Lihat semua {{ $pemesananPending }} pemesanan →
                </a>
            @endif
        </div>
    </div>

    {{-- Sedang Berjalan --}}
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
                            <div class="booking-item-code">{{ $p->mobil->nama }} · s/d {{ $p->tanggal_selesai->format('d M Y') }}</div>
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
                            <button type="submit" class="btn-confirm" onclick="return confirm('Tandai pemesanan ini selesai?')">🏁 Tandai Selesai</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="booking-item" style="text-align:center;padding:20px;color:var(--gray-500);font-size:13px;">Tidak ada pemesanan berjalan</div>
            @endforelse
        </div>
    </div>

    {{-- Ringkasan Bulan Ini --}}
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

</div>

@include('admin.partials.bottom-nav')

<script>
function confirmLogout() {
    document.getElementById('modal-logout').style.display = 'flex';
}
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Saya — Rental Mobil</title>
    @vite(['resources/css/dashboard.css'])
</head>
<body class="user-page">
@include('users.partials.desktop-sidebar')

<nav class="nav">
    <button onclick="window.location.href='{{ route('dashboard') }}'"
        style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Pemesanan Saya</div>
    <div style="width:36px;"></div>
</nav>

<div class="content" style="padding:16px 20px 100px;">

    @if (session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#16a34a;margin-bottom:16px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Ringkasan --}}
    @php
        $semua       = Auth::user()->pemesanans()->count();
        $berjalan    = Auth::user()->pemesanans()->where('status','dikonfirmasi')->count();
        $menunggu    = Auth::user()->pemesanans()->where('status','pending')->count();
        $selesai     = Auth::user()->pemesanans()->where('status','selesai')->count();
    @endphp
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:16px;">
        <div style="background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:10px;text-align:center;">
            <div style="font-size:18px;font-weight:800;color:var(--gray-900);">{{ $semua }}</div>
            <div style="font-size:10px;color:var(--gray-500);margin-top:1px;">Semua</div>
        </div>
        <div style="background:var(--brand-50);border:1px solid #bfdbfe;border-radius:var(--radius-md);padding:10px;text-align:center;">
            <div style="font-size:18px;font-weight:800;color:var(--brand-400);">{{ $berjalan }}</div>
            <div style="font-size:10px;color:var(--gray-500);margin-top:1px;">Berjalan</div>
        </div>
        <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:var(--radius-md);padding:10px;text-align:center;">
            <div style="font-size:18px;font-weight:800;color:#854d0e;">{{ $menunggu }}</div>
            <div style="font-size:10px;color:var(--gray-500);margin-top:1px;">Menunggu</div>
        </div>
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:10px;text-align:center;">
            <div style="font-size:18px;font-weight:800;color:var(--success);">{{ $selesai }}</div>
            <div style="font-size:10px;color:var(--gray-500);margin-top:1px;">Selesai</div>
        </div>
    </div>

    {{-- Filter --}}
    <div style="display:flex;gap:8px;margin-bottom:16px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none;">
        <button class="cat-chip active" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('semua', this)">Semua</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('dikonfirmasi', this)">🔵 Berjalan</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('pending', this)">⏳ Menunggu</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('selesai', this)">✅ Selesai</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('dibatalkan', this)">❌ Dibatalkan</button>
    </div>

    {{-- List --}}
    @php
        $pemesanans = Auth::user()->pemesanans()->with('mobil')->latest()->get();
    @endphp

    @if ($pemesanans->isEmpty())
        <div style="text-align:center;padding:60px 20px;color:var(--gray-500);">
            <div style="font-size:48px;margin-bottom:12px;">📋</div>
            <div style="font-weight:600;">Belum ada pemesanan</div>
            <div style="font-size:13px;margin-top:4px;">Yuk mulai sewa mobil pertama kamu!</div>
            <a href="{{ route('dashboard') }}"
               style="display:inline-block;margin-top:16px;padding:10px 24px;background:var(--brand-400);color:#fff;border-radius:var(--radius-md);font-weight:700;text-decoration:none;">
                Cari Mobil
            </a>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:12px;" id="booking-list">
            @foreach ($pemesanans as $p)
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
                            <div class="booking-item-name">{{ $p->mobil->nama }}</div>
                            <div class="booking-item-code">
                                {{ $p->mobil->plat_nomor }} · {{ $durasi }} hari
                            </div>
                        </div>
                        <span class="booking-status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="booking-item-body">
                        <span>{{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M Y') }}</span>
                        <strong style="color:var(--brand-400);">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                    </div>
                    @if ($p->catatan)
                        <div style="font-size:12px;color:var(--gray-500);margin-top:6px;padding-top:6px;border-top:1px solid var(--gray-100);">
                            📝 {{ $p->catatan }}
                        </div>
                    @endif
                    @if ($p->status === 'pending')
                        <div style="margin-top:10px;">
                            <form action="{{ route('pemesanan.cancel', $p) }}" method="POST"
                                onsubmit="return confirm('Batalkan pemesanan {{ addslashes($p->mobil->nama) }}?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    style="width:100%;padding:9px;background:#fef2f2;color:#dc2626;border:none;border-radius:var(--radius-sm);font-family:var(--font);font-size:13px;font-weight:700;cursor:pointer;">
                                    ❌ Batalkan Pemesanan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>

@if(Auth::user()->role === 'admin')
    @include('admin.partials.bottom-nav')
@else
    @include('users.partials.bottom-nav')
@endif
<script>
function filterBooking(status, el) {
    document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('#booking-list .booking-item').forEach(item => {
        item.style.display = (status === 'semua' || item.dataset.status === status) ? '' : 'none';
    });
}
</script>

</body>
</html>
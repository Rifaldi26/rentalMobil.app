<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan — Admin</title>
    @vite(['resources/css/dashboard.css'])
</head>
<body>

<nav class="nav">
    <button onclick="window.location.href='{{ route('admin.dashboard') }}'"
        style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Pemesanan</div>
    <div style="width:36px;"></div>
</nav>

<div class="content" style="padding:16px 20px 100px;">

    @if (session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#16a34a;margin-bottom:16px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Filter tabs --}}
    <div style="display:flex;gap:8px;margin-bottom:16px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none;">
        <button class="cat-chip active" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('semua', this)">Semua</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('pending', this)">⏳ Menunggu</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('dikonfirmasi', this)">🔵 Berjalan</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('selesai', this)">✅ Selesai</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterBooking('dibatalkan', this)">❌ Dibatalkan</button>
    </div>

    @php $semuaPemesanan = \App\Models\Pemesanan::with(['user','mobil'])->latest()->get(); @endphp

    @if ($semuaPemesanan->isEmpty())
        <div style="text-align:center;padding:60px 20px;color:var(--gray-500);">
            <div style="font-size:48px;margin-bottom:12px;">📋</div>
            <div style="font-weight:600;">Belum ada pemesanan</div>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:10px;" id="booking-list">
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
                    @if ($p->status === 'pending')
                        <div class="booking-item-footer" style="margin-top:10px;">
                            <button type="button" class="btn-confirm"
                                onclick="bukaModal('konfirmasi', {{ $p->id }}, '{{ addslashes($p->user->name) }}', '{{ $p->mobil->nama }}', '{{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M Y') }}')">
                                ✅ Konfirmasi
                            </button>
                            <button type="button" class="btn-reject"
                                onclick="bukaModal('tolak', {{ $p->id }}, '{{ addslashes($p->user->name) }}', '{{ $p->mobil->nama }}', '{{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M Y') }}')">
                                ❌ Tolak
                            </button>
                        </div>
                    @elseif ($p->status === 'dikonfirmasi')
                            <div class="booking-item-footer" style="margin-top:10px;">
                                <button type="button" class="btn-confirm"
                                    onclick="bukaModal('selesai', {{ $p->id }}, '{{ addslashes($p->user->name) }}', '{{ $p->mobil->nama }}', '{{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M Y') }}')">
                                    🏁 Tandai Selesai
                                </button>
                            </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>

<div id="modal-aksi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:flex-end;justify-content:center;">
    <div style="background:#fff;border-radius:20px 20px 0 0;padding:20px 20px 32px;width:100%;max-width:480px;">
        <div style="width:36px;height:4px;background:#e5e7eb;border-radius:2px;margin:0 auto 16px;"></div>
        <div id="modal-icon" style="width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;margin:0 auto 12px;"></div>
        <div id="modal-title" style="font-size:15px;font-weight:700;text-align:center;margin-bottom:4px;"></div>
        <div id="modal-desc" style="font-size:13px;color:#6b7280;text-align:center;margin-bottom:20px;line-height:1.5;"></div>
        <button id="modal-submit" style="width:100%;padding:13px;border:none;border-radius:10px;font-size:14px;font-weight:700;color:#fff;cursor:pointer;margin-bottom:8px;"></button>
        <button onclick="tutupModal()" style="width:100%;padding:13px;background:#f3f4f6;color:#6b7280;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">Batal</button>
    </div>
</div>

@foreach($pemesanans as $p)
    <form id="form-konfirmasi-{{ $p->id }}" action="{{ route('admin.pemesanan.konfirmasi', $p) }}" method="POST" style="display:none;">
        @csrf @method('PATCH')
    </form>
    <form id="form-tolak-{{ $p->id }}" action="{{ route('admin.pemesanan.tolak', $p) }}" method="POST" style="display:none;">
        @csrf @method('PATCH')
    </form>
    <form id="form-selesai-{{ $p->id }}" action="{{ route('admin.pemesanan.selesai', $p) }}" method="POST" style="display:none;">
        @csrf @method('PATCH')
    </form>

@endforeach


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

function bukaModal(aksi, id, nama, mobil, tanggal) {
    var isKonfirmasi = aksi === 'konfirmasi';
    var isSelesai    = aksi === 'selesai';

    var icon  = isKonfirmasi ? '✅' : isSelesai ? '🏁' : '❌';
    var iconBg = isKonfirmasi ? '#dcfce7' : isSelesai ? '#dbeafe' : '#fee2e2';
    var title = isKonfirmasi ? 'Konfirmasi pemesanan?' : isSelesai ? 'Tandai selesai?' : 'Tolak pemesanan ini?';
    var btnBg  = isKonfirmasi ? '#16a34a' : isSelesai ? '#2563eb' : '#dc2626';
    var btnTxt = isKonfirmasi ? 'Ya, konfirmasi' : isSelesai ? 'Ya, selesai' : 'Ya, tolak';

    document.getElementById('modal-icon').style.background = iconBg;
    document.getElementById('modal-icon').textContent      = icon;
    document.getElementById('modal-title').textContent     = title;
    document.getElementById('modal-desc').innerHTML        = nama + ' &mdash; ' + mobil + '<br>' + tanggal;

    var btn = document.getElementById('modal-submit');
    btn.textContent     = btnTxt;
    btn.style.background = btnBg;
    btn.onclick         = function() { document.getElementById('form-' + aksi + '-' + id).submit(); };

    document.getElementById('modal-aksi').style.display = 'flex';
}


function tutupModal() {
    document.getElementById('modal-aksi').style.display = 'none';
}

// Klik backdrop untuk tutup
document.getElementById('modal-aksi').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});
</script>
</body>
</html>
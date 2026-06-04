<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Pesan Mobil — Rental Mobil</title>
    @vite(['resources/css/dashboard.css', 'resources/css/pemesanan.css'])
</head>
<body class="user-page">
@include('users.partials.desktop-sidebar')
{{-- ─── Top Nav ─── --}}
<nav class="nav">
    <button class="nav-back" onclick="history.back()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Form Pemesanan</div>
    <div style="width:36px;"></div>
</nav>

<div class="pesan-content">

    {{-- ─── Info Mobil ─── --}}
    <div class="mobil-summary">
        <div class="mobil-summary-foto">
            @if ($mobil->foto)
                <img src="{{ asset('storage/' . $mobil->foto) }}" alt="{{ $mobil->nama }}">
            @else
                <div class="mobil-foto-placeholder">🚗</div>
            @endif
        </div>
        <div class="mobil-summary-info">
            <div class="mobil-summary-nama">{{ $mobil->nama }}</div>
            <div class="mobil-summary-meta">{{ $mobil->merek }} · {{ $mobil->tahun }} · {{ $mobil->plat_nomor }}</div>
            <div class="mobil-summary-harga">
                Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}
                <span>/hari</span>
            </div>
        </div>
    </div>

    {{-- Error --}}
    @if ($errors->any())
        <div class="alert-error">
            ⚠️ {{ $errors->first() }}
        </div>
    @endif

    {{-- ─── Form ─── --}}
    <form method="POST" action="{{ route('pemesanan.store') }}" id="form-pesan">
        @csrf
        <input type="hidden" name="mobil_id" value="{{ $mobil->id }}">

        {{-- Tanggal --}}
        <div class="form-section">
            <div class="form-section-title">📅 Tanggal Sewa</div>

            <div class="tanggal-grid">
                <div class="form-group">
                    <label class="form-label">Mulai <span class="req">*</span></label>
                    <input
                        type="date"
                        name="tanggal_mulai"
                        id="tanggal_mulai"
                        class="form-input @error('tanggal_mulai') error @enderror"
                        value="{{ old('tanggal_mulai', now()->format('Y-m-d')) }}"
                        min="{{ now()->format('Y-m-d') }}"
                        required
                        onchange="hitungTotal()"
                    >
                    @error('tanggal_mulai')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Selesai <span class="req">*</span></label>
                    <input
                        type="date"
                        name="tanggal_selesai"
                        id="tanggal_selesai"
                        class="form-input @error('tanggal_selesai') error @enderror"
                        value="{{ old('tanggal_selesai', now()->addDay()->format('Y-m-d')) }}"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        required
                        onchange="hitungTotal()"
                    >
                    @error('tanggal_selesai')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Durasi & Total --}}
            <div class="durasi-card" id="durasi-card">
                <div class="durasi-row">
                    <span>Durasi Sewa</span>
                    <strong id="durasi-text">1 hari</strong>
                </div>
                <div class="durasi-row">
                    <span>Harga per Hari</span>
                    <span>Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}</span>
                </div>
                <div class="durasi-divider"></div>
                <div class="durasi-row total">
                    <span>Total Harga</span>
                    <strong id="total-text">Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        {{-- Data Pemesan --}}
        <div class="form-section">
            <div class="form-section-title">👤 Data Pemesan</div>

            <div class="form-group">
                <label class="form-label">Nama</label>
                <input
                    type="text"
                    class="form-input"
                    value="{{ Auth::user()->name }}"
                    disabled
                >
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input
                    type="email"
                    class="form-input"
                    value="{{ Auth::user()->email }}"
                    disabled
                >
            </div>

            <div class="form-group">
                <label class="form-label">Nomor HP</label>
                <input
                    type="text"
                    class="form-input"
                    value="{{ Auth::user()->no_hp ?? '-' }}"
                    disabled
                >
            </div>
        </div>

        {{-- Catatan --}}
        <div class="form-section">
            <div class="form-section-title">📝 Catatan (Opsional)</div>
            <div class="form-group">
                <textarea
                    name="catatan"
                    class="form-input"
                    rows="3"
                    placeholder="Permintaan khusus, lokasi pengantaran, dll..."
                >{{ old('catatan') }}</textarea>
            </div>
        </div>

        {{-- Syarat & Ketentuan --}}
        <div class="syarat-box">
            <div class="syarat-title">📋 Syarat & Ketentuan</div>
            <ul class="syarat-list">
                <li>Pembatalan hanya bisa dilakukan selama status masih <strong>Menunggu</strong></li>
                <li>Pemesanan aktif setelah dikonfirmasi oleh admin</li>
                <li>Keterlambatan pengembalian dikenakan biaya tambahan</li>
                <li>Kerusakan menjadi tanggung jawab penyewa</li>
            </ul>
            <label class="syarat-check">
                <input type="checkbox" id="setuju" required>
                Saya menyetujui syarat dan ketentuan di atas
            </label>
        </div>

    </form>

</div>{{-- /pesan-content --}}

{{-- ─── Sticky Bottom ─── --}}
<div class="sticky-bottom">
    <div class="sticky-total">
        <div style="font-size:12px;color:var(--gray-500);">Total Pembayaran</div>
        <div class="sticky-harga" id="sticky-total">
            Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}
        </div>
    </div>
    <button type="submit" form="form-pesan" class="btn-pesan" id="btn-pesan">
        Pesan Sekarang
    </button>
</div>

<script>
const hargaPerHari = {{ $mobil->harga_per_hari }};

function hitungTotal() {
    const mulai   = document.getElementById('tanggal_mulai').value;
    const selesai = document.getElementById('tanggal_selesai').value;

    if (!mulai || !selesai) return;

    const tMulai   = new Date(mulai);
    const tSelesai = new Date(selesai);
    const durasi   = Math.round((tSelesai - tMulai) / (1000 * 60 * 60 * 24));

    if (durasi <= 0) {
        document.getElementById('tanggal_selesai').setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
        return;
    }

    document.getElementById('tanggal_selesai').setCustomValidity('');

    const total = durasi * hargaPerHari;
    const fmt   = new Intl.NumberFormat('id-ID').format(total);

    document.getElementById('durasi-text').textContent = durasi + ' hari';
    document.getElementById('total-text').textContent  = 'Rp ' + fmt;
    document.getElementById('sticky-total').textContent = 'Rp ' + fmt;

    // Update min tanggal selesai
    const minSelesai = new Date(tMulai);
    minSelesai.setDate(minSelesai.getDate() + 1);
    document.getElementById('tanggal_selesai').min =
        minSelesai.toISOString().split('T')[0];
}

// Jalankan saat load
hitungTotal();

// Validasi form sebelum submit
document.getElementById('form-pesan').addEventListener('submit', function(e) {
    const setuju = document.getElementById('setuju');
    if (!setuju.checked) {
        e.preventDefault();
        alert('Harap setujui syarat dan ketentuan terlebih dahulu.');
        return;
    }
    document.getElementById('btn-pesan').disabled = true;
    document.getElementById('btn-pesan').textContent = 'Memproses...';
});
</script>

</body>
</html>
{{-- resources/views/admin/laporan/invoice-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #111827;
            background: #fff;
        }

        /* ── Header ── */
        .header {
            background: #1a3a6b;
            color: #fff;
            padding: 28px 36px;
            margin-bottom: 0;
        }
        .header-top {
            display: table;
            width: 100%;
        }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .company-name { font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
        .company-sub  { font-size: 11px; color: rgba(255,255,255,.65); margin-top: 3px; }
        .invoice-label { font-size: 11px; color: rgba(255,255,255,.65); text-transform: uppercase; letter-spacing: 1px; }
        .invoice-number { font-size: 20px; font-weight: 700; margin-top: 2px; }

        /* ── Status badge ── */
        .status-bar {
            padding: 10px 36px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #fff;
        }
        .status-selesai    { background: #16a34a; }
        .status-dikonfirmasi { background: #2563eb; }
        .status-pending    { background: #d97706; }
        .status-dibatalkan { background: #dc2626; }

        /* ── Body ── */
        .body { padding: 28px 36px; }

        /* ── Info section ── */
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }
        .info-title {
            font-size: 10px; font-weight: 700; color: #6b7280;
            text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: 6px; border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }
        .info-val { font-size: 12px; color: #111827; line-height: 1.7; }
        .info-val strong { font-weight: 700; }

        /* ── Tabel detail ── */
        .table-detail {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-detail thead tr {
            background: #f3f4f6;
        }
        .table-detail th {
            padding: 9px 12px;
            font-size: 10px; font-weight: 700; color: #374151;
            text-transform: uppercase; letter-spacing: .4px;
            text-align: left; border-bottom: 1px solid #e5e7eb;
        }
        .table-detail td {
            padding: 10px 12px;
            font-size: 12px; color: #374151;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        .table-detail tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }

        /* ── Total box ── */
        .total-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 24px;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }
        .total-row:last-child { margin-bottom: 0; }
        .total-label { display: table-cell; font-size: 12px; color: #374151; }
        .total-value { display: table-cell; text-align: right; font-size: 12px; color: #374151; }
        .total-row.grand .total-label { font-size: 14px; font-weight: 700; color: #1a3a6b; }
        .total-row.grand .total-value { font-size: 14px; font-weight: 700; color: #1a3a6b; }

        /* ── Catatan ── */
        .catatan-box {
            background: #fef9c3;
            border-left: 3px solid #d97706;
            padding: 10px 14px;
            margin-bottom: 24px;
            border-radius: 0 6px 6px 0;
            font-size: 11px;
            color: #374151;
        }
        .catatan-box strong { color: #92400e; }

        /* ── Footer ── */
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            display: table;
            width: 100%;
        }
        .footer-left  { display: table-cell; vertical-align: bottom; }
        .footer-right { display: table-cell; text-align: center; vertical-align: bottom; }
        .footer-text { font-size: 10px; color: #9ca3af; line-height: 1.6; }
        .ttd-box {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 20px;
            display: inline-block;
        }
        .ttd-label { font-size: 10px; color: #6b7280; margin-bottom: 40px; }
        .ttd-name  { font-size: 11px; font-weight: 700; color: #111827; border-top: 1px solid #d1d5db; padding-top: 4px; }

        /* ── Watermark BATAL ── */
        .watermark {
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translateX(-50%) rotate(-30deg);
            font-size: 72px;
            font-weight: 900;
            color: rgba(220,38,38,.12);
            letter-spacing: 8px;
            text-transform: uppercase;
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>

@if($pemesanan->status === 'dibatalkan')
    <div class="watermark">DIBATALKAN</div>
@endif

{{-- ── Header ──────────────────────────────────────────────── --}}
<div class="header">
    <div class="header-top">
        <div class="header-left">
            <div class="company-name">🚗 DriveEase</div>
            <div class="company-sub">Layanan Rental Mobil Terpercaya</div>
        </div>
        <div class="header-right">
            <div class="invoice-label">Invoice</div>
            <div class="invoice-number">#{{ str_pad($pemesanan->id, 5, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>
</div>

{{-- ── Status bar ──────────────────────────────────────────── --}}
<div class="status-bar status-{{ $pemesanan->status }}">
    Status: {{ ucfirst($pemesanan->status) }}
    &nbsp;|&nbsp;
    Dibuat: {{ $pemesanan->created_at->format('d F Y, H:i') }} WIB
</div>

<div class="body">

    {{-- ── Info Pelanggan & Mobil ──────────────────────────── --}}
    <div class="info-row">
        <div class="info-col">
            <div class="info-title">Data Pelanggan</div>
            <div class="info-val">
                <strong>{{ $pemesanan->user->name }}</strong><br>
                {{ $pemesanan->user->email }}<br>
                {{ $pemesanan->user->no_hp ?? '-' }}
            </div>
        </div>
        <div class="info-col">
            <div class="info-title">Detail Kendaraan</div>
            <div class="info-val">
                <strong>{{ $pemesanan->mobil->nama }}</strong><br>
                {{ $pemesanan->mobil->merek ?? '' }}
                @if($pemesanan->mobil->tahun) · {{ $pemesanan->mobil->tahun }} @endif<br>
                Plat: {{ $pemesanan->mobil->plat_nomor ?? '-' }}
            </div>
        </div>
    </div>

    {{-- ── Tabel Rincian ───────────────────────────────────── --}}
    <table class="table-detail">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Durasi</th>
                <th>Harga/Hari</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Sewa {{ $pemesanan->mobil->nama }}</strong><br>
                    <span style="font-size:10px;color:#6b7280;">{{ $pemesanan->mobil->merek ?? '' }}</span>
                </td>
                <td>{{ $pemesanan->tanggal_mulai->format('d M Y') }}</td>
                <td>{{ $pemesanan->tanggal_selesai->format('d M Y') }}</td>
                <td>{{ $pemesanan->durasiHari() }} hari</td>
                <td>Rp {{ number_format($pemesanan->mobil->harga_per_hari, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ── Total ────────────────────────────────────────────── --}}
    <div class="total-box">
        <div class="total-row">
            <div class="total-label">Subtotal</div>
            <div class="total-value">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</div>
        </div>
        <div class="total-row">
            <div class="total-label">Biaya Layanan</div>
            <div class="total-value">Rp 0</div>
        </div>
        <div class="total-row grand">
            <div class="total-label">Total Pembayaran</div>
            <div class="total-value">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- ── Catatan ──────────────────────────────────────────── --}}
    @if($pemesanan->catatan)
        <div class="catatan-box">
            <strong>Catatan:</strong> {{ $pemesanan->catatan }}
        </div>
    @endif

    {{-- ── Footer TTD ──────────────────────────────────────── --}}
    <div class="footer">
        <div class="footer-left">
            <div class="footer-text">
                Terima kasih telah menggunakan layanan DriveEase.<br>
                Dokumen ini dicetak secara otomatis dan sah tanpa tanda tangan basah.<br>
                Hubungi kami jika ada pertanyaan mengenai invoice ini.
            </div>
        </div>
        <div class="footer-right">
            <div class="ttd-box">
                <div class="ttd-label">Hormat kami,</div>
                <div class="ttd-name">Admin DriveEase</div>
            </div>
        </div>
    </div>

</div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Receipt {{ $booking->booking_code }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body { background:#f8fafc; }
        .receipt { max-width:560px;margin:40px auto;background:#fff;border-radius:var(--radius-xl);box-shadow:var(--shadow-lg);overflow:hidden; }
        .receipt-header { background:var(--navy-900);padding:28px;text-align:center;color:#fff; }
        .receipt-body { padding:28px; }
        .receipt-row { display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--gray-100); }
        .receipt-row:last-child { border-bottom:none; }
        .receipt-total { display:flex;justify-content:space-between;padding-top:14px;font-size:1.1rem; }
        @media print { body { background:#fff; } .no-print { display:none; } }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:1.5rem;font-weight:900;margin-bottom:4px;">Rent<span style="color:var(--brand-400);">Wheels</span></div>
            <div style="font-size:.875rem;opacity:.7;margin-bottom:12px;">E-Receipt Pemesanan</div>
            <div style="font-size:1.25rem;font-weight:700;letter-spacing:1px;">{{ $booking->booking_code }}</div>
        </div>

        <div class="receipt-body">
            {{-- Status --}}
            <div style="text-align:center;margin-bottom:20px;">
                <div style="display:inline-flex;align-items:center;gap:6px;padding:8px 20px;background:var(--success-bg);border-radius:20px;color:var(--success);font-weight:700;">
                    ✅ Pemesanan Selesai
                </div>
            </div>

            {{-- Kendaraan --}}
            <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:14px;margin-bottom:20px;">
                <div class="fw-700" style="margin-bottom:2px;">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} {{ $booking->vehicle->year }}</div>
                <div class="text-sm text-muted">{{ $booking->vehicle->plate_number }} · {{ $booking->vehicle->city }}</div>
            </div>

            {{-- Detail --}}
            @foreach([
                ['Pelanggan',     $booking->user->name],
                ['Tanggal Mulai', $booking->start_date->isoFormat('D MMMM Y')],
                ['Tanggal Selesai',$booking->end_date->isoFormat('D MMMM Y')],
                ['Durasi',        $booking->duration_days . ' hari'],
                ['Metode Bayar',  Str::upper(str_replace('_',' ', $booking->payment?->method ?? 'N/A'))],
                ['Dibayar',       $booking->payment?->paid_at?->isoFormat('D MMM Y HH:mm') ?? 'N/A'],
            ] as [$label, $value])
                <div class="receipt-row">
                    <span class="text-sm text-muted">{{ $label }}</span>
                    <span class="fw-600 text-sm">{{ $value }}</span>
                </div>
            @endforeach

            {{-- Breakdown --}}
            <div style="margin-top:16px;padding-top:16px;border-top:1.5px solid var(--gray-200);">
                <div class="receipt-row">
                    <span class="text-sm text-muted">Sewa Kendaraan</span>
                    <span class="fw-600 text-sm">{{ $booking->total_price_formatted }}</span>
                </div>
                <div class="receipt-row">
                    <span class="text-sm text-muted">Biaya Layanan</span>
                    <span class="fw-600 text-sm">Rp {{ number_format($booking->service_fee,0,',','.') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="text-sm text-muted">Deposit</span>
                    <span class="fw-600 text-sm">Rp {{ number_format($booking->deposit,0,',','.') }}</span>
                </div>
                <div class="receipt-total">
                    <span class="fw-700">Total Dibayar</span>
                    <span class="fw-700" style="color:var(--brand-500);">Rp {{ number_format($booking->grand_total,0,',','.') }}</span>
                </div>
            </div>

            {{-- Footer --}}
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--gray-100);text-align:center;font-size:.75rem;color:var(--gray-400);">
                <div>Terima kasih telah menggunakan layanan RentWheels</div>
                <div style="margin-top:4px;">Dicetak {{ now()->isoFormat('D MMMM Y, HH:mm') }}</div>
            </div>
        </div>
    </div>

    <div class="no-print" style="text-align:center;margin-top:20px;padding-bottom:40px;display:flex;gap:10px;justify-content:center;">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak / Simpan PDF</button>
        <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-secondary">← Kembali</a>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>

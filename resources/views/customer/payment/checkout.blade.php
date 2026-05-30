<x-guest-layout>
    <x-slot:title>Pembayaran {{ $booking->booking_code }}</x-slot:title>

    @push('styles')
    <style>
        .payment-method-label { display:flex;align-items:center;gap:12px;padding:14px 16px;border:1.5px solid var(--gray-200);border-radius:var(--radius-md);cursor:pointer;transition:all .15s;margin-bottom:8px; }
        .payment-method-label:has(input:checked) { border-color:var(--brand-400);background:var(--brand-50); }
        .payment-method-label:hover { border-color:var(--gray-300); }
        .payment-method-label input[type=radio] { accent-color:var(--brand-400); }
        .payment-logo { font-size:1.2rem; }
    </style>
    @endpush

    <div class="container" style="padding-top:32px;padding-bottom:60px;max-width:860px;">
        
        <div style="text-align:center;margin-bottom:32px;">
            <h2>Selesaikan Pembayaran</h2>
            <p class="text-muted text-sm" style="margin-top:4px;">
                Kode pemesanan: <strong>{{ $booking->booking_code }}</strong>
            </p>
        </div>

        {{-- Countdown warning --}}
        <div class="alert alert-warning" x-data="countdown({{ $booking->created_at->addHours(2)->timestamp }})">
            ⏰ Selesaikan pembayaran dalam
            <strong x-text="timeLeft"></strong>
            sebelum pemesanan otomatis dibatalkan.
        </div>

        <div style="display:grid;grid-template-columns:1fr 320px;gap:28px;align-items:start;">

            {{-- Pilih Metode Pembayaran --}}
           {{-- Selesaikan Pembayaran --}}
    <div class="card">
        <div class="card-header"><div class="card-title">💳 Selesaikan Pembayaran</div></div>
        <div class="card-body">

            <div style="padding:14px;background:var(--info-bg);border-radius:var(--radius-md);font-size:.8rem;color:var(--info);margin-bottom:20px;">
                🔒 Pembayaran diproses melalui Midtrans yang telah tersertifikasi PCI DSS. Data kartu Anda aman.
            </div>

            <p style="font-size:.875rem;color:var(--gray-500);margin-bottom:20px;">
                Klik tombol di bawah untuk memilih metode pembayaran dan menyelesaikan transaksi Anda.
            </p>

            <button id="pay-button"
                    onclick="openMidtrans()"
                    class="btn btn-primary btn-block btn-lg" style="width:100%;justify-content:center;">
                💳 Bayar Sekarang — Rp {{ number_format($booking->grand_total, 0, ',', '.') }}
            </button>
        </div>
    </div>

            {{-- Order Summary --}}
            <div>
                <div class="card">
                    <div class="card-header"><div class="card-title">Ringkasan Pesanan</div></div>
                    <div class="card-body">
                        <div style="display:flex;gap:10px;margin-bottom:14px;">
                            <img src="{{ $booking->vehicle->primary_photo_url }}"
                                 style="width:70px;height:54px;border-radius:var(--radius-sm);object-fit:cover;background:var(--gray-100);flex-shrink:0;">
                            <div>
                                <div class="fw-700 text-sm">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</div>
                                <div class="text-xs text-muted">{{ $booking->vehicle->city }}</div>
                            </div>
                        </div>

                        <div style="font-size:.875rem;display:flex;flex-direction:column;gap:8px;padding-bottom:12px;border-bottom:1px solid var(--gray-100);">
                            <div style="display:flex;justify-content:space-between;">
                                <span class="text-muted">{{ $booking->start_date->isoFormat('D MMM') }} – {{ $booking->end_date->isoFormat('D MMM Y') }}</span>
                                <span class="fw-600">{{ $booking->duration_days }} hari</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;">
                                <span class="text-muted">Sewa</span>
                                <span class="fw-600">{{ $booking->total_price_formatted }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;">
                                <span class="text-muted">Biaya Layanan</span>
                                <span class="fw-600">Rp {{ number_format($booking->service_fee,0,',','.') }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;">
                                <span class="text-muted">Deposit</span>
                                <span class="fw-600">Rp {{ number_format($booking->deposit,0,',','.') }}</span>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:space-between;padding-top:12px;">
                            <span class="fw-700">Grand Total</span>
                            <span class="fw-700" style="color:var(--brand-500);font-size:1.05rem;">
                                Rp {{ number_format($booking->grand_total,0,',','.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div style="margin-top:12px;font-size:.78rem;color:var(--gray-500);text-align:center;line-height:1.6;">
                    Dengan membayar, Anda menyetujui Syarat & Ketentuan RentWheels.<br>
                    Deposit akan dikembalikan setelah kendaraan kembali dalam kondisi baik.
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Midtrans Snap JS --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
    function openMidtrans() {
        const token = '{{ $booking->payment?->snap_token ?? 'dev-snap-token-'.$booking->id }}';
        if (token.startsWith('dev-')) {
            // Development mode — simulate success
            window.location = '{{ route('payment.finish', $booking) }}';
            return;
        }
        snap.pay(token, {
            onSuccess: (result) => { window.location = '{{ route('payment.finish', $booking) }}'; },
            onPending: (result) => { window.location = '{{ route('customer.bookings.show', $booking) }}'; },
            onError:   (result) => { alert('Pembayaran gagal. Silakan coba lagi.'); },
            onClose:   ()       => { console.log('Snap ditutup.'); },
        });
    }

    function countdown(expiresAt) {
        return {
            timeLeft: '',
            init() {
                this.tick();
                setInterval(() => this.tick(), 1000);
            },
            tick() {
                const diff = expiresAt * 1000 - Date.now();
                if (diff <= 0) { this.timeLeft = '00:00'; return; }
                const m = Math.floor(diff / 60000).toString().padStart(2,'0');
                const s = Math.floor((diff % 60000) / 1000).toString().padStart(2,'0');
                this.timeLeft = `${m}:${s}`;
            }
        }
    }
    </script>
    @endpush
</x-guest-layout>

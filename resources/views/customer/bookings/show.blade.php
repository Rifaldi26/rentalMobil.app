<x-guest-layout>
    <x-slot:title>Detail Pemesanan — {{ $booking->booking_code }}</x-slot:title>

    <div class="container" style="padding-top:32px;padding-bottom:64px;">

        {{-- Breadcrumb --}}
        <nav style="display:flex;align-items:center;gap:6px;font-size:.8rem;color:var(--gray-400);margin-bottom:20px;">
            <a href="{{ route('customer.bookings.index') }}" style="color:var(--gray-400);">Pemesanan Saya</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:var(--gray-700);">{{ $booking->booking_code }}</span>
        </nav>

        {{-- Status Banner --}}
        @php
        $bannerConfig = [
            'pending'       => ['bg'=>'var(--warning-bg)','border'=>'var(--warning-border)','color'=>'var(--warning)','icon'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>','msg'=>'Menunggu konfirmasi admin. Segera lakukan pembayaran.'],
            'dikonfirmasi'  => ['bg'=>'var(--info-bg)','border'=>'var(--info-border)','color'=>'var(--info)','icon'=>'<polyline points="20 6 9 17 4 12"/>','msg'=>'Pemesanan dikonfirmasi! Harap siapkan pembayaran jika belum.'],
            'aktif'         => ['bg'=>'var(--brand-50)','border'=>'var(--brand-200)','color'=>'var(--brand-600)','icon'=>'<rect x="1" y="3" width="15" height="13" rx="2"/>','msg'=>'Kendaraan sedang aktif disewa. Selamat menikmati perjalanan!'],
            'selesai'       => ['bg'=>'var(--success-bg)','border'=>'var(--success-border)','color'=>'var(--success)','icon'=>'<circle cx="12" cy="12" r="10"/><polyline points="20 6 9 17 4 12"/>','msg'=>'Pemesanan selesai. Terima kasih telah menggunakan RentWheels!'],
            'dibatalkan'    => ['bg'=>'var(--danger-bg)','border'=>'var(--danger-border)','color'=>'var(--danger)','icon'=>'<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>','msg'=>'Pemesanan ini telah dibatalkan.'],
        ];
        $banner = $bannerConfig[$booking->status->value] ?? $bannerConfig['pending'];
        @endphp
        <div style="background:{{ $banner['bg'] }};border:1px solid {{ $banner['border'] }};border-radius:var(--radius-lg);padding:14px 18px;display:flex;align-items:center;gap:12px;margin-bottom:24px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $banner['color'] }}" stroke-width="2" style="flex-shrink:0;">{!! $banner['icon'] !!}</svg>
            <span style="font-size:.875rem;font-weight:600;color:{{ $banner['color'] }};">{{ $banner['msg'] }}</span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;" class="booking-show-layout">

            {{-- ── Left ──────────────────────────────── --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Vehicle --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
                        <img src="{{ $booking->vehicle->primary_photo_url }}"
                             style="width:120px;height:86px;object-fit:cover;border-radius:var(--radius-md);flex-shrink:0;"
                             alt="{{ $booking->vehicle->brand }}">
                        <div style="flex:1;">
                            <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1rem;margin-bottom:4px;">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</div>
                            <div style="font-size:.85rem;color:var(--gray-400);margin-bottom:8px;">{{ $booking->vehicle->year }} · {{ $booking->vehicle->license_plate }}</div>
                            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                                <span style="font-size:.8rem;background:var(--gray-100);color:var(--gray-600);padding:3px 10px;border-radius:var(--radius-full);">{{ $booking->vehicle->category->label() }}</span>
                                <span style="font-size:.8rem;background:var(--gray-100);color:var(--gray-600);padding:3px 10px;border-radius:var(--radius-full);">{{ ucfirst($booking->vehicle->transmission) }}</span>
                                <span style="font-size:.8rem;background:var(--gray-100);color:var(--gray-600);padding:3px 10px;border-radius:var(--radius-full);">{{ $booking->vehicle->capacity }} orang</span>
                            </div>
                        </div>
                        <a href="{{ route('cars.show', $booking->vehicle) }}" class="btn btn-ghost btn-sm">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Lihat
                        </a>
                    </div>
                </div>

                {{-- Booking Info --}}
                <div class="card" style="padding:20px;">
                    <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Detail Pemesanan</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        @foreach([
                            ['label'=>'Kode Booking','value'=>$booking->booking_code],
                            ['label'=>'Status','value'=>$booking->status->label()],
                            ['label'=>'Tanggal Mulai','value'=>$booking->start_date->format('d M Y')],
                            ['label'=>'Tanggal Selesai','value'=>$booking->end_date->format('d M Y')],
                            ['label'=>'Durasi','value'=>$booking->duration_days.' hari'],
                            ['label'=>'Dengan Sopir','value'=>$booking->with_driver ? 'Ya' : 'Tidak'],
                        ] as $d)
                        <div style="padding:10px 12px;background:var(--gray-50);border-radius:var(--radius-md);">
                            <div style="font-size:.7rem;color:var(--gray-400);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px;">{{ $d['label'] }}</div>
                            <div style="font-weight:700;font-size:.875rem;">{{ $d['value'] }}</div>
                        </div>
                        @endforeach
                    </div>
                    @if($booking->notes)
                    <div style="margin-top:12px;padding:12px 14px;background:var(--gray-50);border-radius:var(--radius-md);">
                        <div style="font-size:.72rem;color:var(--gray-400);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Catatan Anda</div>
                        <p style="font-size:.875rem;margin:0;color:var(--gray-600);">{{ $booking->notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- Payment Status --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                        <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;">Pembayaran</div>
                        @if($booking->status->value === 'pending' || $booking->status->value === 'dikonfirmasi')
                        @php $paid = $booking->payment_status?->isPaid() ?? false; @endphp
                        @if(!$paid)
                        <a href="{{ route('customer.bookings.pay', $booking) }}" class="btn btn-primary btn-sm">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            Bayar Sekarang
                        </a>
                        @endif
                        @endif
                    </div>
                    @forelse($booking->payment ? [$booking->payment] : [] as $payment)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:var(--gray-50);border-radius:var(--radius-md);margin-bottom:8px;flex-wrap:wrap;gap:8px;">
                        <div>
                            <div style="font-weight:700;font-size:.875rem;text-transform:uppercase;">{{ $payment->payment_method }}</div>
                            <div style="font-size:.75rem;color:var(--gray-400);">{{ $payment->created_at->format('d M Y H:i') }}</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-family:'Sora',sans-serif;font-weight:800;color:var(--brand-700);">Rp {{ number_format($payment->amount,0,',','.') }}</span>
                            @php
                                $scMap = [
                                    'pending'    => ['bg'=>'var(--warning-bg)','color'=>'var(--warning)','label'=>'Menunggu'],
                                    'processing' => ['bg'=>'var(--info-bg)','color'=>'var(--info)','label'=>'Diproses'],
                                    'paid'       => ['bg'=>'var(--success-bg)','color'=>'var(--success)','label'=>'✓ Terverifikasi'],
                                    'failed'     => ['bg'=>'var(--danger-bg)','color'=>'var(--danger)','label'=>'Ditolak'],
                                    'expired'    => ['bg'=>'var(--danger-bg)','color'=>'var(--danger)','label'=>'Kedaluwarsa'],
                                ];
                                $sc = $scMap[$payment->status?->value] ?? ['bg'=>'var(--gray-100)','color'=>'var(--gray-500)','label'=>'Unknown'];
                            @endphp
                            <span style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:.72rem;font-weight:700;padding:2px 8px;border-radius:var(--radius-full);">{{ $sc['label'] }}</span>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:20px;color:var(--gray-400);font-size:.875rem;">Belum ada pembayaran dilakukan</div>
                    @endforelse
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="{{ route('customer.chat.show', $booking) }}" class="btn btn-secondary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Chat Admin
                    </a>
                    @if($booking->status->value === 'selesai' && !$booking->review)
                    <a href="{{ route('customer.reviews.create', $booking) }}" class="btn btn-amber">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Beri Ulasan
                    </a>
                    @endif
                    @if(in_array($booking->status->value, ['pending']))
                    <form method="POST" action="{{ route('customer.bookings.cancel', $booking) }}"
                          onsubmit="return confirm('Yakin batalkan pemesanan ini?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-ghost" style="color:var(--danger);">Batalkan Pesanan</button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- ── Right: Price Summary ─────────────── --}}
            <div>
                <div class="card" style="padding:20px;position:sticky;top:calc(var(--navbar-height) + 16px);">
                    <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Rincian Harga</div>
                    <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;font-size:.875rem;">
                            <span style="color:var(--gray-500);">Rp {{ number_format($booking->vehicle->price_per_day,0,',','.') }} × {{ $booking->duration_days }} hari</span>
                            <span class="fw-600">Rp {{ number_format($booking->vehicle->price_per_day * $booking->duration_days,0,',','.') }}</span>
                        </div>
                        @if($booking->with_driver)
                        <div style="display:flex;justify-content:space-between;font-size:.875rem;">
                            <span style="color:var(--gray-500);">Sopir × {{ $booking->duration_days }} hari</span>
                            <span class="fw-600">Rp {{ number_format(($booking->vehicle->driver_price_per_day ?? 0) * $booking->duration_days,0,',','.') }}</span>
                        </div>
                        @endif
                        <div style="height:1px;background:var(--gray-100);"></div>
                        <div style="display:flex;justify-content:space-between;">
                            <span class="fw-700">Total</span>
                            <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem;color:var(--brand-600);">Rp {{ number_format($booking->total_price,0,',','.') }}</span>
                        </div>
                    </div>
                    @php $totalPaid = $booking->payment?->amount ?? 0; @endphp
                    @if($totalPaid > 0)
                    <div style="background:var(--success-bg);border-radius:var(--radius-md);padding:10px 12px;display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span style="font-size:.85rem;font-weight:700;color:var(--success);">Lunas · Rp {{ number_format($totalPaid,0,',','.') }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <style>
    @media(max-width:768px) { .booking-show-layout { grid-template-columns: 1fr !important; } }
    </style>

</x-guest-layout>

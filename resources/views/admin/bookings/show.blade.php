<x-app-layout>
    <x-slot:title>Detail Pemesanan — {{ $booking->booking_code }}</x-slot:title>

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                <h1 style="font-size:1.3rem;margin:0;">{{ $booking->booking_code }}</h1>
                <span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
            </div>
            <p class="text-sm text-muted">Dibuat {{ $booking->created_at->diffForHumans() }} · {{ $booking->created_at->format('d M Y H:i') }}</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            {{-- Status Actions --}}
            @if($booking->status->value === 'pending')
            <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Konfirmasi
                </button>
            </form>
            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" style="display:inline;"
                  onsubmit="return confirm('Batalkan pemesanan ini?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Batalkan</button>
            </form>
            @elseif($booking->status->value === 'dikonfirmasi')
            <form method="POST" action="{{ route('admin.bookings.activate', $booking) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/></svg>
                    Mulai Sewa
                </button>
            </form>
            @elseif($booking->status->value === 'aktif')
            <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}" style="display:inline;"
                  onsubmit="return confirm('Tandai pemesanan ini selesai?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Selesaikan
                </button>
            </form>
            @endif
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;" class="booking-detail-layout">

        {{-- ── Left Column ──────────────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Vehicle Info --}}
            <div class="card" style="padding:20px;">
                <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Kendaraan</div>
                <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
                    <img src="{{ $booking->vehicle->primary_photo_url }}"
                         style="width:120px;height:86px;object-fit:cover;border-radius:var(--radius-md);flex-shrink:0;"
                         alt="{{ $booking->vehicle->brand }}">
                    <div style="flex:1;min-width:0;">
                        <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1rem;margin-bottom:4px;">
                            {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}
                        </div>
                        <div style="font-size:.85rem;color:var(--gray-400);margin-bottom:8px;">
                            {{ $booking->vehicle->year }} · {{ $booking->vehicle->license_plate }} · {{ $booking->vehicle->category->label() }}
                        </div>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <span style="font-size:.8rem;color:var(--gray-500);">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                {{ $booking->vehicle->capacity }} orang
                            </span>
                            <span style="font-size:.8rem;color:var(--gray-500);">{{ ucfirst($booking->vehicle->transmission) }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.vehicles.edit', $booking->vehicle) }}"
                       class="btn btn-ghost btn-sm" style="flex-shrink:0;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                    </a>
                </div>
            </div>

            {{-- Booking Details --}}
            <div class="card" style="padding:20px;">
                <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Detail Pemesanan</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    @foreach([
                        ['label'=>'Tanggal Mulai','value'=>$booking->start_date->format('d M Y').' ('.$booking->start_date->translatedFormat('l').')'],
                        ['label'=>'Tanggal Selesai','value'=>$booking->end_date->format('d M Y').' ('.$booking->end_date->translatedFormat('l').')'],
                        ['label'=>'Durasi','value'=>$booking->duration_days.' hari'],
                        ['label'=>'Dengan Sopir','value'=>$booking->with_driver ? 'Ya' : 'Tidak'],
                    ] as $d)
                    <div style="padding:12px 14px;background:var(--gray-50);border-radius:var(--radius-md);">
                        <div style="font-size:.72rem;color:var(--gray-400);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">{{ $d['label'] }}</div>
                        <div style="font-weight:700;font-size:.9rem;">{{ $d['value'] }}</div>
                    </div>
                    @endforeach
                </div>
                @if($booking->notes)
                <div style="margin-top:14px;padding:14px;background:var(--gray-50);border-radius:var(--radius-md);">
                    <div style="font-size:.75rem;color:var(--gray-400);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Catatan dari Pelanggan</div>
                    <p style="font-size:.875rem;color:var(--gray-600);margin:0;">{{ $booking->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Payments --}}
            <div class="card" style="padding:20px;">
                <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Pembayaran</div>
                @forelse($booking->payments as $payment)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 14px;background:var(--gray-50);border-radius:var(--radius-md);margin-bottom:8px;flex-wrap:wrap;gap:10px;">
                    <div>
                        <div style="font-weight:700;font-size:.875rem;text-transform:uppercase;">{{ $payment->payment_method }}</div>
                        <div style="font-size:.78rem;color:var(--gray-400);">{{ $payment->created_at->format('d M Y H:i') }}</div>
                        @if($payment->notes)
                        <div style="font-size:.78rem;color:var(--gray-500);margin-top:2px;">{{ $payment->notes }}</div>
                        @endif
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-family:'Sora',sans-serif;font-weight:800;color:var(--brand-700);">
                            Rp {{ number_format($payment->amount,0,',','.') }}
                        </span>
                        @php $s = ['pending'=>['bg'=>'var(--warning-bg)','color'=>'var(--warning)','label'=>'Menunggu'],'sukses'=>['bg'=>'var(--success-bg)','color'=>'var(--success)','label'=>'Terverifikasi'],'gagal'=>['bg'=>'var(--danger-bg)','color'=>'var(--danger)','label'=>'Ditolak']][$payment->status] ?? ['bg'=>'var(--gray-100)','color'=>'var(--gray-500)','label'=>$payment->status]; @endphp
                        <span style="background:{{ $s['bg'] }};color:{{ $s['color'] }};font-size:.72rem;font-weight:700;padding:2px 8px;border-radius:var(--radius-full);">{{ $s['label'] }}</span>
                        @if($payment->proof_url)
                        <a href="{{ $payment->proof_url }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:.78rem;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Bukti
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:20px;color:var(--gray-400);font-size:.875rem;">Belum ada pembayaran</div>
                @endforelse
            </div>

            {{-- Chat shortcut --}}
            <a href="{{ route('admin.chat.show', $booking) }}" class="card" style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;transition:box-shadow .15s;" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:38px;height:38px;background:var(--brand-50);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand-600)" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:.9rem;">Chat dengan Pelanggan</div>
                        <div style="font-size:.78rem;color:var(--gray-400);">{{ $booking->messages->count() }} pesan</div>
                    </div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>

        </div>

        {{-- ── Right Column ─────────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Customer --}}
            <div class="card" style="padding:20px;">
                <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Pelanggan</div>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                    <img src="{{ $booking->user->avatar_url }}" alt="{{ $booking->user->name }}"
                         style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
                    <div>
                        <div style="font-weight:700;font-size:.9rem;">{{ $booking->user->name }}</div>
                        <div style="font-size:.78rem;color:var(--gray-400);">{{ $booking->user->email }}</div>
                        @if($booking->user->phone)
                        <div style="font-size:.78rem;color:var(--gray-400);">{{ $booking->user->phone }}</div>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $booking->user) }}" class="btn btn-secondary" style="width:100%;justify-content:center;font-size:.85rem;">
                    Lihat Profil Pelanggan
                </a>
            </div>

            {{-- Price Summary --}}
            <div class="card" style="padding:20px;">
                <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Rincian Harga</div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div style="display:flex;justify-content:space-between;font-size:.875rem;">
                        <span style="color:var(--gray-500);">Sewa kendaraan</span>
                        <span class="fw-600">Rp {{ number_format($booking->vehicle->price_per_day * $booking->duration_days,0,',','.') }}</span>
                    </div>
                    @if($booking->with_driver)
                    <div style="display:flex;justify-content:space-between;font-size:.875rem;">
                        <span style="color:var(--gray-500);">Biaya sopir</span>
                        <span class="fw-600">Rp {{ number_format(($booking->vehicle->driver_price_per_day ?? 0) * $booking->duration_days,0,',','.') }}</span>
                    </div>
                    @endif
                    <div style="height:1px;background:var(--gray-100);margin:4px 0;"></div>
                    <div style="display:flex;justify-content:space-between;">
                        <span class="fw-700">Total</span>
                        <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:1rem;color:var(--brand-600);">Rp {{ number_format($booking->total_price,0,',','.') }}</span>
                    </div>
                    @php $paid = $booking->payment?->amount ?? 0; @endphp
                    @if($paid > 0)
                    <div style="display:flex;justify-content:space-between;font-size:.875rem;">
                        <span style="color:var(--success);">Sudah Dibayar</span>
                        <span style="color:var(--success);font-weight:700;">Rp {{ number_format($paid,0,',','.') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="card" style="padding:20px;">
                <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Timeline</div>
                <div style="display:flex;flex-direction:column;gap:0;">
                    @foreach($timeline as $event)
                    <div style="display:flex;gap:12px;position:relative;">
                        <div style="display:flex;flex-direction:column;align-items:center;">
                            <div style="width:10px;height:10px;border-radius:50%;background:{{ $event['color'] }};flex-shrink:0;margin-top:4px;"></div>
                            @if(!$loop->last)
                            <div style="width:1px;flex:1;background:var(--gray-200);margin:2px 0;min-height:24px;"></div>
                            @endif
                        </div>
                        <div style="padding-bottom:14px;flex:1;">
                            <div style="font-weight:700;font-size:.85rem;color:var(--gray-800);">{{ $event['action'] }}</div>
                            <div style="font-size:.75rem;color:var(--gray-400);">{{ $event['time'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <style>
    @media(max-width:900px) { .booking-detail-layout { grid-template-columns: 1fr !important; } }
    </style>

</x-app-layout>

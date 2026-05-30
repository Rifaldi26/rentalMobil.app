<x-guest-layout>
    <x-slot:title>Pemesanan Saya</x-slot:title>

    <div class="container" style="padding-top:36px;padding-bottom:60px;">

        {{-- Header --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
            <div>
                <h1 style="font-size:1.5rem;margin-bottom:4px;">Pemesanan Saya</h1>
                <p class="text-sm text-muted">Kelola semua perjalanan Anda bersama RentWheels</p>
            </div>
            <a href="{{ route('cars.index') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Pesan Kendaraan
            </a>
        </div>

        {{-- Stats Summary --}}
        @php
            $myStats = [
                'total'     => auth()->user()->bookings()->count(),
                'active'    => auth()->user()->bookings()->whereIn('status', ['dikonfirmasi','aktif'])->count(),
                'done'      => auth()->user()->bookings()->where('status','selesai')->count(),
                'pending'   => auth()->user()->bookings()->where('status','pending')->count(),
            ];
        @endphp
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;">
            @foreach([
                ['label'=>'Total Pemesanan','value'=>$myStats['total'],'color'=>'var(--brand-600)','bg'=>'var(--brand-50)'],
                ['label'=>'Menunggu Konfirmasi','value'=>$myStats['pending'],'color'=>'var(--warning)','bg'=>'#fffbeb'],
                ['label'=>'Sedang Aktif','value'=>$myStats['active'],'color'=>'#3b82f6','bg'=>'#eff6ff'],
                ['label'=>'Selesai','value'=>$myStats['done'],'color'=>'var(--success)','bg'=>'#ecfdf5'],
            ] as $s)
            <div style="background:{{ $s['bg'] }};border-radius:var(--radius-lg);padding:16px 18px;text-align:center;">
                <div style="font-family:'Sora',sans-serif;font-size:1.6rem;font-weight:800;color:{{ $s['color'] }};line-height:1;margin-bottom:5px;">{{ $s['value'] }}</div>
                <div style="font-size:.78rem;color:var(--gray-600);font-weight:600;">{{ $s['label'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Status Filter Tabs --}}
        <div class="filter-chips" style="margin-bottom:20px;">
            <a href="{{ route('customer.bookings.index') }}" class="filter-chip {{ !request('status') ? 'active' : '' }}">
                Semua
            </a>
            @foreach(\App\Enums\BookingStatus::cases() as $s)
            <a href="{{ route('customer.bookings.index', ['status' => $s->value]) }}"
               class="filter-chip {{ request('status') === $s->value ? 'active' : '' }}">
                {{ $s->label() }}
            </a>
            @endforeach
        </div>

        {{-- Booking List --}}
        @forelse($bookings as $booking)
        <div class="card" style="margin-bottom:16px;transition:box-shadow .15s;"
             onmouseover="this.style.boxShadow='var(--shadow-md)'"
             onmouseout="this.style.boxShadow='var(--shadow-sm)'">
            <div style="display:grid;grid-template-columns:auto 1fr auto;gap:20px;padding:20px 22px;align-items:center;">

                {{-- Vehicle Image --}}
                <img src="{{ $booking->vehicle->primary_photo_url }}"
                     style="width:100px;height:72px;object-fit:cover;border-radius:var(--radius-md);flex-shrink:0;"
                     alt="{{ $booking->vehicle->brand }}">

                {{-- Info --}}
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;flex-wrap:wrap;">
                        <span style="font-family:'Sora',sans-serif;font-weight:700;font-size:.95rem;">
                            {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}
                        </span>
                        <span class="badge badge-{{ ['pending'=>'pending','dikonfirmasi'=>'confirmed','aktif'=>'active','selesai'=>'done','dibatalkan'=>'cancelled'][$booking->status->value] ?? 'pending' }}">
                            {{ $booking->status->label() }}
                        </span>
                    </div>

                    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:8px;">
                        <span style="font-size:.8rem;color:var(--gray-500);display:flex;align-items:center;gap:5px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $booking->start_date->format('d M Y') }} – {{ $booking->end_date->format('d M Y') }}
                        </span>
                        <span style="font-size:.8rem;color:var(--gray-500);display:flex;align-items:center;gap:5px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $booking->duration_days }} hari
                        </span>
                        <span style="font-size:.8rem;color:var(--gray-500);display:flex;align-items:center;gap:5px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            {{ $booking->booking_code }}
                        </span>
                    </div>

                    {{-- Payment status --}}
                    @php $paid = $booking->payment_status?->isPaid() ?? false; @endphp
                    <span class="badge {{ $paid ? 'badge-paid' : 'badge-unpaid' }}" style="font-size:.72rem;">
                        {{ $paid ? '✓ Lunas' : 'Belum Bayar' }}
                    </span>
                </div>

                {{-- Price + Actions --}}
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem;color:var(--brand-700);margin-bottom:12px;">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </div>
                    <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
                        <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-sm btn-secondary">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Detail
                        </a>
                        @if(in_array($booking->status->value, ['dikonfirmasi','aktif']))
                        <a href="{{ route('customer.chat.show', $booking) }}" class="btn btn-sm btn-primary">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            Chat
                        </a>
                        @endif
                        @if($booking->status->value === 'pending' && !$paid)
                        <a href="{{ route('customer.bookings.pay', $booking) }}" class="btn btn-sm btn-amber">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            Bayar
                        </a>
                        @endif
                        @if($booking->status->value === 'selesai' && !$booking->hasReview())
                        <a href="{{ route('customer.reviews.create', $booking) }}" class="btn btn-sm btn-outline">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            Beri Ulasan
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="empty-state" style="padding:72px 24px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <h3>Belum ada pemesanan</h3>
                <p>Anda belum pernah memesan kendaraan. Mulai perjalanan Anda sekarang!</p>
            </div>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($bookings->hasPages())
        <div style="display:flex;justify-content:center;margin-top:20px;">
            {{ $bookings->withQueryString()->links('vendor.pagination.simple-rentwheels') }}
        </div>
        @endif
    </div>

    @push('styles')
    <style>
    @media(max-width:768px){
        .booking-card-grid { grid-template-columns: 1fr !important; gap: 14px !important; }
        .booking-item-inner { grid-template-columns: 1fr !important; }
        .booking-item-inner > img { width: 100% !important; height: 160px !important; }
        .booking-item-inner > div:last-child { text-align: left !important; }
        .booking-item-inner > div:last-child .btn { align-self: flex-start !important; }
        .stats-grid { grid-template-columns: 1fr 1fr !important; }
    }
    </style>
    @endpush
</x-guest-layout>

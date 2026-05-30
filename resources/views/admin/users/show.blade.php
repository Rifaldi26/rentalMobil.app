<x-app-layout>
    <x-slot:title>Detail Pelanggan — {{ $user->name }}</x-slot:title>

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                 style="width:52px;height:52px;border-radius:50%;object-fit:cover;">
            <div>
                <h1 style="font-size:1.3rem;margin-bottom:2px;">{{ $user->name }}</h1>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span style="font-size:.85rem;color:var(--gray-400);">{{ $user->email }}</span>
                    @if($user->banned_at)
                    <span class="badge" style="background:var(--danger-bg);color:var(--danger);">Diblokir</span>
                    @elseif($user->bookings_count > 0)
                    <span class="badge" style="background:var(--success-bg);color:var(--success);">Aktif</span>
                    @else
                    <span class="badge" style="background:var(--gray-100);color:var(--gray-500);">Baru</span>
                    @endif
                </div>
            </div>
        </div>
        <div style="display:flex;gap:10px;">
            @if(!$user->banned_at)
            <form method="POST" action="{{ route('admin.users.ban', $user) }}"
                  onsubmit="return confirm('Blokir akun {{ $user->name }}?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    Blokir Akun
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-secondary btn-sm">Buka Blokir</button>
            </form>
            @endif
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:300px 1fr;gap:24px;" class="user-detail-layout">

        {{-- ── Left: Profile Info ─────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card" style="padding:20px;">
                <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;">Info Akun</div>
                @foreach([
                    ['label'=>'ID Pelanggan','value'=>'#'.$user->id],
                    ['label'=>'Bergabung','value'=>$user->created_at->format('d M Y')],
                    ['label'=>'Login Terakhir','value'=>$user->last_login_at?->diffForHumans() ?? '—'],
                    ['label'=>'Nomor HP','value'=>$user->phone ?? '—'],
                    ['label'=>'Alamat','value'=>$user->address ?? '—'],
                ] as $info)
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--gray-100);font-size:.875rem;">
                    <span style="color:var(--gray-400);font-weight:600;">{{ $info['label'] }}</span>
                    <span style="font-weight:700;text-align:right;max-width:160px;word-break:break-word;">{{ $info['value'] }}</span>
                </div>
                @endforeach
            </div>

            {{-- Stats --}}
            <div class="card" style="padding:20px;">
                <div style="font-weight:800;font-size:.85rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Statistik</div>
                @foreach([
                    ['label'=>'Total Pemesanan','value'=>$user->bookings_count,'color'=>'var(--brand-600)'],
                    ['label'=>'Selesai','value'=>$completedBookings,'color'=>'var(--success)'],
                    ['label'=>'Dibatalkan','value'=>$cancelledBookings,'color'=>'var(--danger)'],
                    ['label'=>'Total Pengeluaran','value'=>'Rp '.number_format($user->total_spend ?? 0,0,',','.'),'color'=>'var(--gray-800)'],
                    ['label'=>'Rata-rata Rating','value'=>$user->reviews_avg_rating ? number_format($user->reviews_avg_rating,1).' ⭐' : '—','color'=>'var(--amber-500)'],
                ] as $s)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-100);font-size:.875rem;">
                    <span style="color:var(--gray-500);">{{ $s['label'] }}</span>
                    <span style="font-family:'Sora',sans-serif;font-weight:800;color:{{ $s['color'] }};">{{ $s['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Right: Booking History ──────────────── --}}
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">Riwayat Pemesanan</div>
                    <span style="font-size:.8rem;color:var(--gray-400);">{{ $bookings->total() }} total</span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Kendaraan</th>
                                <th>Periode</th>
                                <th style="text-align:right;">Total</th>
                                <th>Pembayaran</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td><span style="font-family:'Sora',sans-serif;font-weight:700;font-size:.8rem;">{{ $booking->booking_code }}</span></td>
                                <td>
                                    <div style="font-size:.875rem;font-weight:600;">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</div>
                                    <div style="font-size:.75rem;color:var(--gray-400);">{{ $booking->vehicle->license_plate }}</div>
                                </td>
                                <td style="font-size:.8rem;color:var(--gray-500);">
                                    {{ $booking->start_date->format('d M Y') }}<br>
                                    {{ $booking->end_date->format('d M Y') }}
                                </td>
                                <td style="text-align:right;font-weight:700;font-size:.875rem;color:var(--brand-700);">
                                    Rp {{ number_format($booking->total_price,0,',','.') }}
                                </td>
                                <td>
                                    @php $paid = $booking->payment_status?->isPaid() ?? false; @endphp
                                    <span class="badge {{ $paid ? 'badge-paid' : 'badge-unpaid' }}">
                                        {{ $paid ? '✓ Lunas' : 'Belum Bayar' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-ghost btn-sm">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:28px;color:var(--gray-400);">Belum ada pemesanan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($bookings->hasPages())
                <div style="padding:14px 20px;border-top:1px solid var(--gray-100);">
                    {{ $bookings->links('vendor.pagination.simple-rentwheels') }}
                </div>
                @endif
            </div>
        </div>

    </div>

    <style>
    @media(max-width:900px) { .user-detail-layout { grid-template-columns: 1fr !important; } }
    </style>

</x-app-layout>

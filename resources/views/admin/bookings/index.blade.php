<x-app-layout>
    <x-slot:title>Kelola Pemesanan</x-slot:title>

    {{-- ── Page Header ─────────────────────────────────────── --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;margin-bottom:4px;">Kelola Pemesanan</h1>
            <p class="text-sm text-muted">{{ $bookings->total() }} total pemesanan ditemukan</p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Export
            </a>
        </div>
    </div>

    {{-- ── Filters ──────────────────────────────────────────── --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('admin.bookings.index') }}">
                <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                    {{-- Search --}}
                    <div style="flex:1;min-width:200px;">
                        <div class="form-label-sm">Cari</div>
                        <div class="input-icon">
                            <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="q" class="form-input" placeholder="Kode booking, nama, plat..." value="{{ request('q') }}" style="padding-left:36px;">
                        </div>
                    </div>
                    {{-- Status --}}
                    <div style="min-width:150px;">
                        <div class="form-label-sm">Status</div>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach(\App\Enums\BookingStatus::cases() as $s)
                            <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Date Range --}}
                    <div>
                        <div class="form-label-sm">Dari Tanggal</div>
                        <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
                    </div>
                    <div>
                        <div class="form-label-sm">Sampai</div>
                        <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
                    </div>
                    {{-- Buttons --}}
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Filter
                        </button>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.73"/></svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Status Tabs ──────────────────────────────────────── --}}
    <div class="filter-chips" style="margin-bottom:20px;">
        @php
            $statusCounts = \App\Models\Booking::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count','status');
        @endphp
        <a href="{{ route('admin.bookings.index') }}" class="filter-chip {{ !request('status') ? 'active' : '' }}">
            Semua
            <span style="background:rgba(0,0,0,.1);padding:1px 7px;border-radius:var(--radius-full);font-size:.7rem;">{{ $bookings->total() }}</span>
        </a>
        @foreach(\App\Enums\BookingStatus::cases() as $s)
        <a href="{{ route('admin.bookings.index', ['status' => $s->value]) }}"
           class="filter-chip {{ request('status') === $s->value ? 'active' : '' }}">
            {{ $s->label() }}
            @if(($statusCounts[$s->value] ?? 0) > 0)
            <span style="background:rgba(0,0,0,.1);padding:1px 7px;border-radius:var(--radius-full);font-size:.7rem;">{{ $statusCounts[$s->value] }}</span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- ── Bookings Table ───────────────────────────────────── --}}
    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all" style="width:15px;height:15px;cursor:pointer;">
                        </th>
                        <th>Kode Booking</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Periode Sewa</th>
                        <th>Durasi</th>
                        <th>Total Bayar</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <input type="checkbox" class="booking-checkbox" value="{{ $booking->id }}"
                                   style="width:15px;height:15px;cursor:pointer;">
                        </td>
                        <td>
                            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:.82rem;color:var(--brand-700);">{{ $booking->booking_code }}</div>
                            <div style="font-size:.7rem;color:var(--gray-400);">{{ $booking->created_at->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <img src="{{ $booking->user->avatar_url }}" class="avatar avatar-sm" alt="">
                                <div>
                                    <div style="font-weight:600;font-size:.85rem;">{{ $booking->user->name }}</div>
                                    <div style="font-size:.72rem;color:var(--gray-500);">{{ $booking->user->phone ?? $booking->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:.85rem;font-weight:600;">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</div>
                            <div style="font-size:.72rem;color:var(--gray-500);">{{ $booking->vehicle->plate_number }}</div>
                        </td>
                        <td style="font-size:.82rem;white-space:nowrap;">
                            <div>{{ $booking->start_date->format('d M Y') }}</div>
                            <div style="color:var(--gray-500);">s/d {{ $booking->end_date->format('d M Y') }}</div>
                        </td>
                        <td style="font-size:.85rem;text-align:center;font-weight:700;">
                            {{ $booking->duration_days }} hari
                        </td>
                        <td style="font-weight:700;font-size:.875rem;color:var(--brand-700);white-space:nowrap;">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </td>
                        <td>
                            @php $paid = $booking->payment_status?->isPaid() ?? false; @endphp
                            <span class="badge {{ $paid ? 'badge-paid' : 'badge-unpaid' }}">
                                {{ $paid ? 'Lunas' : 'Belum Bayar' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ ['pending'=>'pending','dikonfirmasi'=>'confirmed','aktif'=>'active','selesai'=>'done','dibatalkan'=>'cancelled'][$booking->status->value] ?? 'pending' }}">
                                {{ $booking->status->label() }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="{{ route('admin.bookings.show', $booking) }}"
                                   class="btn btn-sm btn-ghost btn-icon" title="Detail">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                @if($booking->status->value === 'pending')
                                <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success btn-icon" title="Konfirmasi">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    </button>
                                </form>
                                @endif
                                @if(!$booking->status->isFinal())
                                <a href="{{ route('admin.chat.show', $booking) }}"
                                   class="btn btn-sm btn-ghost btn-icon" title="Chat">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <h3>Tidak ada pemesanan</h3>
                            <p>Tidak ada pemesanan yang cocok dengan filter.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
        <div class="card-footer" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <span style="font-size:.8rem;color:var(--gray-500);">
                Menampilkan {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} dari {{ $bookings->total() }}
            </span>
            <div class="pagination">
                {{ $bookings->withQueryString()->links('vendor.pagination.simple-rentwheels') }}
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
    document.getElementById('select-all')?.addEventListener('change', function() {
        document.querySelectorAll('.booking-checkbox').forEach(cb => cb.checked = this.checked);
    });
    </script>
    @endpush
</x-app-layout>

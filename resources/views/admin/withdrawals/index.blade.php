<x-app-layout>
    <x-slot:title>Keuangan & Saldo</x-slot:title>

    @push('styles')
    <style>
    .balance-card {
        background:linear-gradient(135deg,var(--navy-900) 0%,var(--brand-900) 100%);
        border-radius:var(--radius-xl);padding:28px 32px;color:#fff;position:relative;overflow:hidden;
    }
    .balance-card::before {
        content:'';position:absolute;top:-60px;right:-60px;
        width:200px;height:200px;border-radius:50%;
        border:1px solid rgba(255,255,255,.06);
    }
    .balance-card::after {
        content:'';position:absolute;bottom:-40px;left:-40px;
        width:140px;height:140px;border-radius:50%;
        border:1px solid rgba(255,255,255,.06);
    }
    .withdraw-status-badge {
        display:inline-flex;align-items:center;gap:4px;padding:3px 10px;
        border-radius:var(--radius-full);font-size:.72rem;font-weight:700;letter-spacing:.2px;
    }
    </style>
    @endpush

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;margin-bottom:4px;">Keuangan & Saldo</h1>
            <p class="text-sm text-muted">Rekap pendapatan dan pencatatan keuangan</p>
        </div>
        <button onclick="document.getElementById('withdraw-modal').style.display='flex'"
                class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Catat Penarikan
        </button>
    </div>

    {{-- Balance Overview --}}
    <div class="balance-card" style="margin-bottom:24px;">
        <div style="position:relative;z-index:1;">
            <div style="font-size:.8rem;color:rgba(255,255,255,.5);font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin-bottom:8px;">Saldo Tersedia</div>
            <div style="font-family:'Sora',sans-serif;font-size:2.4rem;font-weight:800;letter-spacing:-1px;margin-bottom:20px;">
                Rp {{ number_format($balance['available'], 0, ',', '.') }}
            </div>
            <div style="display:flex;gap:32px;flex-wrap:wrap;">
                @foreach([
                    ['label'=>'Total Pendapatan','value'=>'Rp '.number_format($balance['total_income'],0,',','.'),'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                    ['label'=>'Total Ditarik','value'=>'Rp '.number_format($balance['total_withdrawn'],0,',','.'),'icon'=>'<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>'],
                    ['label'=>'Pending Konfirmasi','value'=>'Rp '.number_format($balance['pending_payment'],0,',','.'),'icon'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
                ] as $b)
                <div>
                    <div style="font-size:.75rem;color:rgba(255,255,255,.45);font-weight:600;margin-bottom:2px;">{{ $b['label'] }}</div>
                    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:.95rem;color:rgba(255,255,255,.9);">{{ $b['value'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
        @foreach([
            ['label'=>'Pendapatan Bulan Ini','value'=>'Rp '.number_format($monthlyIncome,0,',','.'),'trend'=>$incomeTrend,'icon'=>'<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>','color'=>'var(--success)','bg'=>'var(--success-bg)'],
            ['label'=>'Pembayaran Terproses','value'=>$processedPayments.' transaksi','trend'=>null,'icon'=>'<polyline points="20 6 9 17 4 12"/>','color'=>'var(--brand-600)','bg'=>'var(--brand-50)'],
            ['label'=>'Menunggu Verifikasi','value'=>$pendingPayments.' pembayaran','trend'=>null,'icon'=>'<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>','color'=>'var(--warning)','bg'=>'var(--warning-bg)'],
        ] as $m)
        <div class="card" style="padding:18px 20px;display:flex;align-items:center;gap:14px;">
            <div style="width:42px;height:42px;background:{{ $m['bg'] }};border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $m['color'] }}" stroke-width="2">{!! $m['icon'] !!}</svg>
            </div>
            <div>
                <div style="font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;color:{{ $m['color'] }};">{{ $m['value'] }}</div>
                <div style="font-size:.78rem;color:var(--gray-500);font-weight:600;">{{ $m['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: '{{ request('tab','payments') }}' }">
        <div class="filter-chips" style="margin-bottom:20px;">
            <button @click="tab='payments'" :class="{'active': tab==='payments'}" class="filter-chip">Pembayaran Masuk</button>
            <button @click="tab='withdrawals'" :class="{'active': tab==='withdrawals'}" class="filter-chip">Riwayat Penarikan</button>
        </div>

        {{-- Payments Tab --}}
        <div x-show="tab==='payments'">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">Pembayaran Masuk</div>
                    <a href="{{ route('admin.payments.export') }}" class="btn btn-ghost btn-sm">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/></svg>
                        Ekspor
                    </a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Pelanggan</th>
                                <th>Metode</th>
                                <th>Bukti</th>
                                <th style="text-align:right;">Jumlah</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $payment->booking) }}"
                                       style="font-family:'Sora',sans-serif;font-weight:700;font-size:.8rem;color:var(--brand-600);">
                                        {{ $payment->booking->booking_code }}
                                    </a>
                                </td>
                                <td style="font-size:.875rem;">{{ $payment->booking->user->name }}</td>
                                <td>
                                    <span class="badge" style="background:var(--gray-100);color:var(--gray-700);text-transform:uppercase;">
                                        {{ $payment->payment_method }}
                                    </span>
                                </td>
                                <td>
                                    @if($payment->proof_url)
                                    <a href="{{ $payment->proof_url }}" target="_blank" class="btn btn-ghost btn-sm">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Lihat
                                    </a>
                                    @else
                                    <span style="font-size:.78rem;color:var(--gray-300);">—</span>
                                    @endif
                                </td>
                                <td style="text-align:right;font-weight:700;font-size:.875rem;color:var(--brand-700);">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td style="font-size:.8rem;color:var(--gray-500);">{{ $payment->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'pending' => ['bg'=>'var(--warning-bg)','color'=>'var(--warning)','label'=>'Menunggu'],
                                            'sukses'  => ['bg'=>'var(--success-bg)','color'=>'var(--success)','label'=>'Terverifikasi'],
                                            'gagal'   => ['bg'=>'var(--danger-bg)','color'=>'var(--danger)','label'=>'Ditolak'],
                                        ];
                                        $s = $statusMap[$payment->status] ?? $statusMap['pending'];
                                    @endphp
                                    <span class="withdraw-status-badge" style="background:{{ $s['bg'] }};color:{{ $s['color'] }};">{{ $s['label'] }}</span>
                                </td>
                                <td style="text-align:right;">
                                    @if($payment->status === 'pending')
                                    <div style="display:flex;justify-content:flex-end;gap:6px;">
                                        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-primary btn-sm" style="font-size:.75rem;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                                Verifikasi
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" style="display:inline;"
                                              onsubmit="return confirm('Tolak pembayaran ini?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);font-size:.75rem;">Tolak</button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:32px;color:var(--gray-400);">
                                    Belum ada pembayaran
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($payments->hasPages())
                <div style="padding:16px 20px;border-top:1px solid var(--gray-100);">
                    {{ $payments->links('vendor.pagination.simple-rentwheels') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Withdrawals Tab --}}
        <div x-show="tab==='withdrawals'">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">Riwayat Penarikan Dana</div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Rekening Tujuan</th>
                                <th style="text-align:right;">Jumlah</th>
                                <th>Status</th>
                                <th>Dicatat oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawals as $w)
                            <tr>
                                <td style="font-size:.85rem;">{{ $w->created_at->format('d M Y') }}</td>
                                <td style="font-size:.875rem;">{{ $w->description ?? '—' }}</td>
                                <td style="font-size:.8rem;color:var(--gray-500);">{{ $w->bank_name }} · {{ $w->account_number }}</td>
                                <td style="text-align:right;font-weight:700;color:var(--danger);font-size:.875rem;">
                                    — Rp {{ number_format($w->amount, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="withdraw-status-badge"
                                          style="background:{{ $w->status === 'selesai' ? 'var(--success-bg)' : 'var(--warning-bg)' }};
                                                 color:{{ $w->status === 'selesai' ? 'var(--success)' : 'var(--warning)' }};">
                                        {{ $w->status === 'selesai' ? 'Selesai' : 'Proses' }}
                                    </span>
                                </td>
                                <td style="font-size:.8rem;color:var(--gray-400);">{{ $w->user->name ?? 'Sistem' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center;padding:32px;color:var(--gray-400);">
                                    Belum ada riwayat penarikan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Withdraw Modal --}}
    <div id="withdraw-modal"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;padding:20px;"
         onclick="if(event.target===this)this.style.display='none'">
        <div style="background:#fff;border-radius:var(--radius-xl);padding:28px;width:100%;max-width:480px;box-shadow:var(--shadow-xl);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h3 style="font-size:1.05rem;margin:0;">Catat Penarikan Dana</h3>
                <button onclick="document.getElementById('withdraw-modal').style.display='none'"
                        style="background:none;border:none;cursor:pointer;color:var(--gray-400);padding:4px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.withdrawals.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Jumlah (Rp)</label>
                    <input type="number" name="amount" class="form-input" placeholder="0" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Bank / Rekening</label>
                    <input type="text" name="bank_name" class="form-input" placeholder="BCA, BNI, dll." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Rekening</label>
                    <input type="text" name="account_number" class="form-input" placeholder="1234-5678-9012" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
                    <input type="text" name="description" class="form-input" placeholder="Keperluan penarikan...">
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
                    <button type="button" onclick="document.getElementById('withdraw-modal').style.display='none'" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>

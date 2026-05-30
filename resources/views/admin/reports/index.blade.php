<x-app-layout>
    <x-slot:title>Laporan & Analitik</x-slot:title>

    @push('styles')
    <style>
    .metric-card { padding:20px 22px;transition:box-shadow .15s; }
    .metric-card:hover { box-shadow:var(--shadow-md); }
    .metric-value { font-family:'Sora',sans-serif;font-size:1.6rem;font-weight:800;line-height:1;margin-bottom:6px; }
    .metric-trend {
        display:inline-flex;align-items:center;gap:4px;
        padding:2px 8px;border-radius:var(--radius-full);
        font-size:.72rem;font-weight:700;
    }
    .trend-up { background:var(--success-bg);color:var(--success); }
    .trend-down { background:var(--danger-bg);color:var(--danger); }
    .chart-bar-wrap { display:flex;flex-direction:column;gap:12px; }
    .chart-bar-row { display:flex;align-items:center;gap:12px; }
    .chart-bar-label { width:90px;font-size:.8rem;color:var(--gray-600);font-weight:600;text-align:right;flex-shrink:0; }
    .chart-bar-track { flex:1;height:28px;background:var(--gray-100);border-radius:var(--radius-full);overflow:hidden; }
    .chart-bar-fill {
        height:100%;border-radius:var(--radius-full);
        display:flex;align-items:center;padding-right:10px;justify-content:flex-end;
        transition:width .6s cubic-bezier(.34,1.56,.64,1);
        font-size:.75rem;font-weight:700;color:#fff;
    }
    .chart-bar-val { width:80px;font-size:.8rem;color:var(--gray-600);font-weight:600;text-align:left;flex-shrink:0; }
    .period-btn { padding:6px 14px;border-radius:var(--radius-full);font-size:.8rem;font-weight:700;border:1.5px solid var(--gray-200);background:#fff;cursor:pointer;transition:all .15s; }
    .period-btn.active { background:var(--brand-600);color:#fff;border-color:var(--brand-600); }
    .period-btn:hover:not(.active) { border-color:var(--brand-400);color:var(--brand-600); }
    </style>
    @endpush

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;margin-bottom:4px;">Laporan & Analitik</h1>
            <p class="text-sm text-muted">Performa bisnis RentWheels</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.reports.export', ['period' => request('period','month')]) }}" class="btn btn-secondary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Ekspor PDF
            </a>
        </div>
    </div>

    {{-- Period Selector --}}
    <div style="display:flex;gap:6px;margin-bottom:24px;flex-wrap:wrap;">
        @foreach(['week'=>'7 Hari','month'=>'30 Hari','quarter'=>'3 Bulan','year'=>'1 Tahun'] as $val => $label)
        <a href="{{ route('admin.reports.index', ['period' => $val]) }}"
           class="period-btn {{ request('period','month') === $val ? 'active' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Key Metrics --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
        @foreach([
            ['label'=>'Pendapatan','value'=>'Rp '.number_format($metrics['revenue'],0,',','.'),'trend'=>$metrics['revenue_trend'],'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>','color'=>'var(--brand-600)','bg'=>'var(--brand-50)'],
            ['label'=>'Pemesanan','value'=>$metrics['bookings'],'trend'=>$metrics['bookings_trend'],'icon'=>'<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>','color'=>'var(--info)','bg'=>'var(--info-bg)'],
            ['label'=>'Pelanggan Baru','value'=>$metrics['new_customers'],'trend'=>$metrics['customers_trend'],'icon'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>','color'=>'var(--success)','bg'=>'var(--success-bg)'],
            ['label'=>'Rata-rata Booking','value'=>'Rp '.number_format($metrics['avg_booking'],0,',','.'),'trend'=>$metrics['avg_trend'],'icon'=>'<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>','color'=>'var(--warning)','bg'=>'var(--warning-bg)'],
        ] as $m)
        <div class="card metric-card">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
                <div style="width:38px;height:38px;background:{{ $m['bg'] }};border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $m['color'] }}" stroke-width="2">{!! $m['icon'] !!}</svg>
                </div>
                @if($m['trend'] !== null)
                <span class="metric-trend {{ $m['trend'] >= 0 ? 'trend-up' : 'trend-down' }}">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        @if($m['trend'] >= 0)
                        <polyline points="18 15 12 9 6 15"/>
                        @else
                        <polyline points="6 9 12 15 18 9"/>
                        @endif
                    </svg>
                    {{ abs($m['trend']) }}%
                </span>
                @endif
            </div>
            <div class="metric-value" style="color:{{ $m['color'] }};">{{ $m['value'] }}</div>
            <div style="font-size:.8rem;color:var(--gray-400);font-weight:600;">{{ $m['label'] }}</div>
        </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

        {{-- Pendapatan per Bulan --}}
        <div class="card" style="padding:22px;">
            <div class="card-header-title" style="font-size:.95rem;font-weight:800;margin-bottom:18px;">Pendapatan Bulanan</div>
            <div class="chart-bar-wrap">
                @php $maxRev = max(array_column($monthlyRevenue, 'revenue') ?: [1]); @endphp
                @foreach($monthlyRevenue as $row)
                <div class="chart-bar-row">
                    <div class="chart-bar-label">{{ $row['month'] }}</div>
                    <div class="chart-bar-track">
                        <div class="chart-bar-fill"
                             style="width:{{ max(5, ($row['revenue']/$maxRev)*100) }}%;background:linear-gradient(90deg,var(--brand-600),var(--brand-400));">
                            @if($row['revenue'] > 0)
                            <span>{{ number_format($row['revenue']/1000000,1) }}jt</span>
                            @endif
                        </div>
                    </div>
                    <div class="chart-bar-val">Rp {{ number_format($row['revenue']/1000,0) }}K</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Status Distribusi --}}
        <div class="card" style="padding:22px;">
            <div class="card-header-title" style="font-size:.95rem;font-weight:800;margin-bottom:18px;">Distribusi Status Pemesanan</div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @php $totalBookings = array_sum(array_column($statusDistribution, 'count')) ?: 1; @endphp
                @foreach($statusDistribution as $row)
                @php $pct = round(($row['count']/$totalBookings)*100,1); @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:.85rem;font-weight:600;">{{ $row['label'] }}</span>
                        <span style="font-size:.85rem;color:var(--gray-400);">{{ $row['count'] }} ({{ $pct }}%)</span>
                    </div>
                    <div style="height:8px;background:var(--gray-100);border-radius:var(--radius-full);overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $row['color'] }};border-radius:var(--radius-full);transition:width .5s;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

        {{-- Top Vehicles --}}
        <div class="card" style="padding:22px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <div class="card-header-title" style="font-size:.95rem;font-weight:800;">Kendaraan Terpopuler</div>
            </div>
            @foreach($topVehicles as $i => $v)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--gray-100);">
                <div style="width:24px;height:24px;background:{{ $i===0?'var(--amber-500)':($i===1?'var(--gray-400)':($i===2?'#cd7f32':'var(--gray-200))')) }};border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;color:{{ $i<3?'#fff':'var(--gray-500)' }};flex-shrink:0;">
                    {{ $i+1 }}
                </div>
                <img src="{{ $v->primary_photo_url }}" style="width:44px;height:32px;object-fit:cover;border-radius:var(--radius-sm);" alt="{{ $v->brand }}">
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;font-size:.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $v->brand }} {{ $v->model }}</div>
                    <div style="font-size:.75rem;color:var(--gray-400);">{{ $v->bookings_count }} pemesanan</div>
                </div>
                <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:.85rem;color:var(--brand-600);white-space:nowrap;">
                    Rp {{ number_format($v->bookings_revenue/1000,0) }}K
                </div>
            </div>
            @endforeach
        </div>

        {{-- Booking Harian (7 hari terakhir) --}}
        <div class="card" style="padding:22px;">
            <div class="card-header-title" style="font-size:.95rem;font-weight:800;margin-bottom:18px;">Booking Harian (7 Hari)</div>
            <div class="chart-bar-wrap">
                @php $maxDaily = max(array_column($dailyBookings, 'count') ?: [1]); @endphp
                @foreach($dailyBookings as $row)
                <div class="chart-bar-row">
                    <div class="chart-bar-label">{{ \Carbon\Carbon::parse($row['date'])->format('D d/m') }}</div>
                    <div class="chart-bar-track">
                        <div class="chart-bar-fill"
                             style="width:{{ max(5, ($row['count']/($maxDaily ?: 1))*100) }}%;background:linear-gradient(90deg,var(--info),#38bdf8);">
                        </div>
                    </div>
                    <div class="chart-bar-val">{{ $row['count'] }} booking</div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Recent Transactions --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-title">Transaksi Terbaru</div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Periode</th>
                        <th style="text-align:right;">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $booking)
                    <tr>
                        <td><span style="font-family:'Sora',sans-serif;font-weight:700;font-size:.8rem;">{{ $booking->booking_code }}</span></td>
                        <td style="font-size:.875rem;">{{ $booking->user->name }}</td>
                        <td style="font-size:.875rem;">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</td>
                        <td style="font-size:.8rem;color:var(--gray-500);">
                            {{ $booking->start_date->format('d M') }} – {{ $booking->end_date->format('d M Y') }}
                        </td>
                        <td style="text-align:right;font-weight:700;font-size:.875rem;color:var(--brand-700);">
                            Rp {{ number_format($booking->total_price,0,',','.') }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>

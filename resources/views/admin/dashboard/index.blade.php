<x-app-layout>
    <x-slot:title>Dashboard</x-slot:title>

    @push('styles')
    <style>
    .quick-action { display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:20px 12px;background:#fff;border:1.5px solid var(--gray-200);border-radius:var(--radius-lg);text-decoration:none;transition:all .2s;text-align:center;cursor:pointer; }
    .quick-action:hover { border-color:var(--brand-400);background:var(--brand-50);transform:translateY(-2px);box-shadow:var(--shadow-sm); }
    .quick-action svg { width:22px;height:22px; }
    .quick-action span { font-size:.78rem;font-weight:700;color:var(--gray-700);font-family:'Sora',sans-serif; }
    .activity-item { display:flex;align-items:flex-start;gap:12px;padding:12px 0;border-bottom:1px solid var(--gray-50); }
    .activity-item:last-child { border-bottom:none; }
    .activity-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:6px; }
    @media(max-width:1280px){ .dashboard-grid { grid-template-columns:1fr !important; } }
    @media(max-width:768px){
        .stats-grid { grid-template-columns:1fr 1fr; }
        .quick-actions-grid { grid-template-columns:repeat(3,1fr) !important; }
    }
    @media(max-width:480px){ .stats-grid { grid-template-columns:1fr; } .quick-actions-grid { grid-template-columns:repeat(2,1fr) !important; } }
    </style>
    @endpush

    {{-- ── Page Header ─────────────────────────────────────── --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
        <div>
            <h1 style="font-size:1.5rem;margin-bottom:4px;">Selamat datang, {{ Str::before(auth()->user()->name, ' ') }} 👋</h1>
            <p class="text-sm text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }} · Pantau performa armada Anda hari ini</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Laporan
            </a>
            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Kendaraan
            </a>
        </div>
    </div>

    {{-- ── Stats Cards ─────────────────────────────────────── --}}
    <div class="stats-grid" style="margin-bottom:24px;">

        <div class="stat-card teal">
            <div class="stat-icon teal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="stat-value">{{ 'Rp '.number_format($stats['revenue_month'] ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Pendapatan Bulan Ini</div>
            <div class="stat-change {{ ($stats['revenue_change'] ?? 0) >= 0 ? 'up' : 'down' }}">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    @if(($stats['revenue_change'] ?? 0) >= 0)
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                    @else
                        <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/>
                    @endif
                </svg>
                {{ abs($stats['revenue_change'] ?? 0) }}% vs bulan lalu
            </div>
        </div>

        <div class="stat-card blue">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="stat-value">{{ $stats['bookings_month'] ?? 0 }}</div>
            <div class="stat-label">Pemesanan Bulan Ini</div>
            <div class="stat-change {{ ($stats['bookings_change'] ?? 0) >= 0 ? 'up' : 'down' }}">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    @if(($stats['bookings_change'] ?? 0) >= 0)
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                    @else
                        <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/>
                    @endif
                </svg>
                {{ abs($stats['bookings_change'] ?? 0) }}% vs bulan lalu
            </div>
        </div>

        <div class="stat-card amber">
            <div class="stat-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect width="13" height="8" x="8" y="13" rx="2"/></svg>
            </div>
            <div class="stat-value">{{ $stats['vehicles_active'] ?? 0 }}</div>
            <div class="stat-label">Kendaraan Aktif</div>
            <div class="stat-change" style="color:var(--gray-500);">
                dari {{ $stats['vehicles_total'] ?? 0 }} total armada
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-value">{{ $stats['customers_total'] ?? 0 }}</div>
            <div class="stat-label">Total Pelanggan</div>
            <div class="stat-change up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                +{{ $stats['customers_new_month'] ?? 0 }} bulan ini
            </div>
        </div>

    </div>

    {{-- ── Secondary Stats Row ─────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;">
        @foreach([
            ['label'=>'Menunggu Konfirmasi','value'=>$stats['pending'] ?? 0,'color'=>'var(--warning)','route'=>route('admin.bookings.index',['status'=>'pending'])],
            ['label'=>'Sedang Berjalan','value'=>$stats['active'] ?? 0,'color'=>'#3b82f6','route'=>route('admin.bookings.index',['status'=>'aktif'])],
            ['label'=>'Selesai Bulan Ini','value'=>$stats['done_month'] ?? 0,'color'=>'var(--success)','route'=>route('admin.bookings.index',['status'=>'selesai'])],
            ['label'=>'Dibatalkan Bulan Ini','value'=>$stats['cancelled_month'] ?? 0,'color'=>'var(--danger)','route'=>route('admin.bookings.index',['status'=>'dibatalkan'])],
        ] as $s)
        <a href="{{ $s['route'] }}" style="background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:14px 16px;text-decoration:none;display:flex;align-items:center;justify-content:space-between;transition:box-shadow .15s;"
           onmouseover="this.style.boxShadow='var(--shadow-sm)'" onmouseout="this.style.boxShadow='none'">
            <span style="font-size:.8rem;color:var(--gray-600);font-weight:600;">{{ $s['label'] }}</span>
            <span style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;color:{{ $s['color'] }};">{{ $s['value'] }}</span>
        </a>
        @endforeach
    </div>

    {{-- ── Quick Actions ────────────────────────────────────── --}}
    <div style="margin-bottom:28px;">
        <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--gray-500);margin-bottom:14px;">Aksi Cepat</div>
        <div class="quick-actions-grid" style="display:grid;grid-template-columns:repeat(6,1fr);gap:10px;">
            @foreach([
                ['href'=>route('admin.bookings.index',['status'=>'pending']),'color'=>'var(--warning)','bg'=>'#fffbeb','title'=>'Konfirmasi Booking',
                 'icon'=>'<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
                 'badge'=>$stats['pending'] ?? 0],
                ['href'=>route('admin.vehicles.create'),'color'=>'var(--brand-600)','bg'=>'var(--brand-50)','title'=>'Tambah Kendaraan',
                 'icon'=>'<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>'],
                ['href'=>route('admin.schedule.index'),'color'=>'#3b82f6','bg'=>'#eff6ff','title'=>'Lihat Jadwal',
                 'icon'=>'<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="8" y2="14"/><line x1="12" y1="14" x2="12" y2="14"/>'],
                ['href'=>route('admin.bookings.index'),'color'=>'#8b5cf6','bg'=>'#f5f3ff','title'=>'Pesan Masuk',
                 'icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
                ['href'=>route('admin.withdrawals.index'),'color'=>'var(--success)','bg'=>'#ecfdf5','title'=>'Keuangan',
                 'icon'=>'<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>'],
                ['href'=>route('admin.reports.index'),'color'=>'var(--amber-600)','bg'=>'#fffbeb','title'=>'Laporan',
                 'icon'=>'<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>'],
            ] as $action)
            <a href="{{ $action['href'] }}" class="quick-action" style="position:relative;">
                @if(!empty($action['badge']) && $action['badge'] > 0)
                <span class="badge-dot" style="position:absolute;top:8px;right:8px;">{{ $action['badge'] }}</span>
                @endif
                <div style="width:42px;height:42px;border-radius:var(--radius-md);background:{{ $action['bg'] }};display:flex;align-items:center;justify-content:center;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="{{ $action['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                        {!! $action['icon'] !!}
                    </svg>
                </div>
                <span>{{ $action['title'] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ── Main Grid ────────────────────────────────────────── --}}
    <div class="dashboard-grid" style="display:grid;grid-template-columns:1fr 380px;gap:24px;">

        {{-- LEFT COLUMN --}}
        <div style="display:flex;flex-direction:column;gap:24px;">

            {{-- Revenue Chart --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        Grafik Pendapatan
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <select class="form-select" style="width:auto;padding:6px 28px 6px 10px;font-size:.8rem;" id="chart-period">
                            <option value="7">7 Hari</option>
                            <option value="30" selected>30 Hari</option>
                            <option value="90">3 Bulan</option>
                        </select>
                    </div>
                </div>
                <div class="card-body" style="padding:20px;">
                    <canvas id="revenueChart" height="260"></canvas>
                </div>
            </div>

            {{-- Recent Bookings --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Pemesanan Terbaru
                    </div>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-ghost" style="font-size:.78rem;">
                        Lihat Semua
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Kendaraan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings ?? [] as $booking)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <img src="{{ $booking->user->avatar_url }}" class="avatar avatar-sm" alt="">
                                        <div>
                                            <div style="font-weight:600;font-size:.875rem;">{{ $booking->user->name }}</div>
                                            <div style="font-size:.75rem;color:var(--gray-500);">{{ $booking->booking_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:.85rem;">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</td>
                                <td style="font-size:.82rem;white-space:nowrap;">
                                    {{ $booking->start_date->format('d M') }} – {{ $booking->end_date->format('d M Y') }}
                                </td>
                                <td style="font-weight:700;font-size:.875rem;color:var(--brand-700);">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ ['pending'=>'pending','dikonfirmasi'=>'confirmed','aktif'=>'active','selesai'=>'done','dibatalkan'=>'cancelled'][$booking->status->value] ?? 'pending' }}">
                                        {{ $booking->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-ghost btn-icon">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6">
                                <div class="empty-state" style="padding:32px;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/></svg>
                                    <p>Belum ada pemesanan</p>
                                </div>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div style="display:flex;flex-direction:column;gap:24px;">

            {{-- Vehicle Occupancy --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect width="13" height="8" x="8" y="13" rx="2"/></svg>
                        Status Armada
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $vehicleStats = [
                            ['label'=>'Tersedia','key'=>'available','color'=>'var(--success)','bg'=>'var(--success-bg)'],
                            ['label'=>'Disewa','key'=>'rented','color'=>'#3b82f6','bg'=>'#eff6ff'],
                            ['label'=>'Perawatan','key'=>'maintenance','color'=>'var(--warning)','bg'=>'var(--warning-bg)'],
                        ];
                        $total = max(1, ($stats['vehicles_available'] ?? 0) + ($stats['vehicles_rented'] ?? 0) + ($stats['vehicles_maintenance'] ?? 0));
                    @endphp
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        @foreach($vehicleStats as $vs)
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                <span style="font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:7px;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:{{ $vs['color'] }};display:inline-block;"></span>
                                    {{ $vs['label'] }}
                                </span>
                                <span style="font-size:.82rem;font-weight:700;color:{{ $vs['color'] }};">{{ $stats['vehicles_'.$vs['key']] ?? 0 }} unit</span>
                            </div>
                            <div style="height:6px;background:var(--gray-100);border-radius:var(--radius-full);overflow:hidden;">
                                <div style="height:100%;width:{{ round(($stats['vehicles_'.$vs['key']] ?? 0) / $total * 100) }}%;background:{{ $vs['color'] }};border-radius:var(--radius-full);transition:width 1s ease;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--gray-100);text-align:center;">
                        <div style="font-family:'Sora',sans-serif;font-size:2rem;font-weight:800;color:var(--brand-600);">
                            {{ $total > 0 ? round(($stats['vehicles_rented'] ?? 0) / $total * 100) : 0 }}%
                        </div>
                        <div style="font-size:.8rem;color:var(--gray-500);margin-top:4px;">Tingkat Utilisasi Armada</div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Aktivitas Terkini
                    </div>
                </div>
                <div class="card-body" style="padding:16px 20px;">
                    @php
                        $activities = \App\Models\AuditLog::with('user')
                            ->latest()->take(8)->get();
                    @endphp
                    @forelse($activities as $act)
                    <div class="activity-item">
                        <div class="activity-dot" style="background:{{ ['booking_created'=>'var(--brand-500)','booking_confirmed'=>'var(--success)','booking_cancelled'=>'var(--danger)','vehicle_added'=>'#8b5cf6','payment_received'=>'var(--amber-500)'][$act->event] ?? 'var(--gray-400)' }};"></div>
                        <div>
                            <div style="font-size:.82rem;font-weight:600;color:var(--gray-800);line-height:1.4;">{{ $act->description }}</div>
                            <div style="font-size:.72rem;color:var(--gray-400);margin-top:2px;">
                                {{ $act->user?->name ?? 'Sistem' }} · {{ $act->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state" style="padding:24px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <p style="font-size:.875rem;">Belum ada aktivitas</p>
                    </div>
                    @endforelse
                    <a href="{{ route('admin.audit.index') }}" class="btn btn-ghost btn-sm btn-block" style="margin-top:12px;font-size:.8rem;">
                        Lihat semua aktivitas
                    </a>
                </div>
            </div>

            {{-- Top Vehicles --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Kendaraan Terpopuler
                    </div>
                </div>
                <div class="card-body" style="padding:12px 16px;">
                    @php
                        $topVehicles = \App\Models\Vehicle::withCount('bookings')
                            ->orderByDesc('bookings_count')->take(5)->get();
                    @endphp
                    @foreach($topVehicles as $i => $v)
                    <div style="display:flex;align-items:center;gap:12px;padding:10px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--gray-50);' : '' }}">
                        <span style="width:24px;text-align:center;font-family:'Sora',sans-serif;font-size:.8rem;font-weight:800;color:{{ $i === 0 ? 'var(--amber-500)' : 'var(--gray-400)' }};">{{ $i + 1 }}</span>
                        <img src="{{ $v->primary_photo_url }}" style="width:40px;height:30px;object-fit:cover;border-radius:var(--radius-sm);" alt="">
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.82rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $v->brand }} {{ $v->model }}</div>
                            <div style="font-size:.72rem;color:var(--gray-500);">{{ $v->bookings_count }} pemesanan</div>
                        </div>
                        <div style="font-family:'Sora',sans-serif;font-size:.85rem;font-weight:800;color:var(--brand-600);">
                            Rp {{ number_format($v->price_per_day, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const chartData = @json($chartData ?? []);
        const labels  = chartData.map(d => d.label) || [];
        const revenue = chartData.map(d => d.revenue) || [];
        const bookings= chartData.map(d => d.bookings) || [];

        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Pendapatan (Rp)',
                        data: revenue,
                        backgroundColor: 'rgba(20,184,166,.15)',
                        borderColor: '#14b8a6',
                        borderWidth: 2,
                        borderRadius: 6,
                        yAxisID: 'y',
                        type: 'bar',
                    },
                    {
                        label: 'Pemesanan',
                        data: bookings,
                        borderColor: '#f59e0b',
                        backgroundColor: 'transparent',
                        borderWidth: 2.5,
                        tension: .4,
                        pointBackgroundColor: '#f59e0b',
                        pointRadius: 3,
                        yAxisID: 'y1',
                        type: 'line',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Nunito', size: 12 }, usePointStyle: true, padding: 16 } },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,.9)',
                        padding: 12,
                        titleFont: { family: 'Sora', size: 13, weight: '700' },
                        bodyFont: { family: 'Nunito', size: 12 },
                        callbacks: {
                            label: ctx => ctx.datasetIndex === 0
                                ? `Pendapatan: Rp ${ctx.parsed.y.toLocaleString('id-ID')}`
                                : `Pemesanan: ${ctx.parsed.y}`
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Nunito', size: 11 }, color: '#94a3b8' } },
                    y:  { position: 'left',  grid: { color: 'rgba(0,0,0,.04)' }, ticks: { font: { family: 'Nunito', size: 11 }, color: '#94a3b8', callback: v => 'Rp ' + (v/1000).toFixed(0) + 'rb' } },
                    y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { font: { family: 'Nunito', size: 11 }, color: '#94a3b8' } }
                }
            }
        });
    });
    </script>
    @endpush
</x-app-layout>

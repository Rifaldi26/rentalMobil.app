<x-app-layout>
    <x-slot:title>Jadwal & Ketersediaan</x-slot:title>

    @push('styles')
    <style>
    .calendar-grid {
        display:grid;grid-template-columns:repeat(7,1fr);
        gap:1px;background:var(--gray-200);border-radius:var(--radius-md);overflow:hidden;
    }
    .cal-day-header {
        background:var(--gray-50);padding:8px 4px;text-align:center;
        font-size:.75rem;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.4px;
    }
    .cal-day {
        background:#fff;min-height:80px;padding:6px 8px;
        position:relative;transition:background .15s;
    }
    .cal-day:hover { background:var(--gray-50); }
    .cal-day.today { background:var(--brand-50); }
    .cal-day.other-month .day-num { color:var(--gray-300); }
    .day-num {
        font-size:.8rem;font-weight:700;color:var(--gray-700);
        width:22px;height:22px;display:flex;align-items:center;justify-content:center;
        border-radius:50%;
    }
    .today .day-num { background:var(--brand-600);color:#fff; }
    .booking-block {
        margin-top:2px;padding:2px 6px;border-radius:3px;
        font-size:.68rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
        cursor:pointer;
    }
    .vehicle-row {
        display:grid;grid-template-columns:200px 1fr;
        border-bottom:1px solid var(--gray-100);min-height:52px;
    }
    .vehicle-row:last-child { border-bottom:none; }
    .vehicle-label {
        padding:12px 14px;display:flex;align-items:center;gap:10px;
        border-right:1px solid var(--gray-100);background:var(--gray-50);position:sticky;left:0;z-index:1;
    }
    .timeline-cell {
        position:relative;display:flex;align-items:center;
        overflow:visible;
    }
    .timeline-block {
        position:absolute;height:32px;border-radius:var(--radius-sm);
        display:flex;align-items:center;padding:0 8px;
        font-size:.72rem;font-weight:700;white-space:nowrap;overflow:hidden;
        cursor:pointer;transition:opacity .15s;
    }
    .timeline-block:hover { opacity:.85; }
    .view-tabs { display:flex;gap:4px;margin-bottom:20px; }
    .view-tab {
        padding:7px 16px;border-radius:var(--radius-full);font-size:.8rem;font-weight:700;
        border:1.5px solid var(--gray-200);background:#fff;cursor:pointer;transition:all .15s;
    }
    .view-tab.active { background:var(--brand-600);color:#fff;border-color:var(--brand-600); }
    </style>
    @endpush

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;margin-bottom:4px;">Jadwal & Ketersediaan</h1>
            <p class="text-sm text-muted">Pantau ketersediaan seluruh armada kendaraan</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            {{-- Month Navigator --}}
            <a href="{{ route('admin.schedule.index', ['month' => $prevMonth, 'year' => $prevYear]) }}" class="btn btn-ghost btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <span style="font-family:'Sora',sans-serif;font-weight:800;min-width:120px;text-align:center;">
                {{ $monthName }} {{ $year }}
            </span>
            <a href="{{ route('admin.schedule.index', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="btn btn-ghost btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="{{ route('admin.schedule.index') }}" class="btn btn-secondary btn-sm">Hari Ini</a>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
        @foreach([
            ['label'=>'Kendaraan Tersedia','value'=>$stats['available'],'color'=>'var(--success)','bg'=>'var(--success-bg)'],
            ['label'=>'Sedang Disewa','value'=>$stats['rented'],'color'=>'var(--info)','bg'=>'var(--info-bg)'],
            ['label'=>'Dalam Servis','value'=>$stats['maintenance'],'color'=>'var(--warning)','bg'=>'var(--warning-bg)'],
            ['label'=>'Booking Bulan Ini','value'=>$stats['month_bookings'],'color'=>'var(--brand-600)','bg'=>'var(--brand-50)'],
        ] as $s)
        <div class="card" style="padding:14px 16px;display:flex;align-items:center;gap:10px;">
            <div style="width:10px;height:10px;background:{{ $s['color'] }};border-radius:50%;flex-shrink:0;"></div>
            <div>
                <div style="font-family:'Sora',sans-serif;font-size:1.3rem;font-weight:800;color:{{ $s['color'] }};line-height:1;">{{ $s['value'] }}</div>
                <div style="font-size:.75rem;color:var(--gray-500);font-weight:600;">{{ $s['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- View Toggle --}}
    <div class="view-tabs">
        <button class="view-tab {{ request('view','timeline') === 'timeline' ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.schedule.index', array_merge(request()->query(), ['view'=>'timeline'])) }}'">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            Timeline
        </button>
        <button class="view-tab {{ request('view') === 'calendar' ? 'active' : '' }}"
                onclick="window.location='{{ route('admin.schedule.index', array_merge(request()->query(), ['view'=>'calendar'])) }}'">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Kalender
        </button>
    </div>

    @if(request('view') === 'calendar')
    {{-- Calendar View --}}
    <div class="card" style="padding:20px;">
        <div class="calendar-grid">
            @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
            <div class="cal-day-header">{{ $d }}</div>
            @endforeach

            @foreach($calendarDays as $day)
            <div class="cal-day {{ $day['is_today'] ? 'today' : '' }} {{ !$day['current_month'] ? 'other-month' : '' }}">
                <div class="day-num">{{ $day['day'] }}</div>
                @foreach($day['bookings'] as $booking)
                <div class="booking-block"
                     style="background:{{ ['dikonfirmasi'=>'var(--info-bg)','aktif'=>'var(--brand-50)','selesai'=>'var(--success-bg)'][$booking->status->value] ?? 'var(--gray-100)' }};
                            color:{{ ['dikonfirmasi'=>'var(--info)','aktif'=>'var(--brand-700)','selesai'=>'var(--success)'][$booking->status->value] ?? 'var(--gray-600)' }};"
                     title="{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} — {{ $booking->user->name }}">
                    {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>

    @else
    {{-- Timeline View --}}
    <div class="card" style="overflow-x:auto;">
        <div style="min-width:800px;">

            {{-- Day Headers --}}
            <div style="display:grid;grid-template-columns:200px 1fr;border-bottom:2px solid var(--gray-200);">
                <div style="padding:10px 14px;background:var(--gray-50);border-right:1px solid var(--gray-100);">
                    <span style="font-size:.75rem;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.4px;">Kendaraan</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat({{ $daysInMonth }},1fr);background:var(--gray-50);">
                    @for($d = 1; $d <= $daysInMonth; $d++)
                    <div style="padding:8px 2px;text-align:center;border-right:1px solid var(--gray-100);">
                        <div style="font-size:.65rem;font-weight:700;color:{{ $d == now()->day && $month == now()->month && $year == now()->year ? 'var(--brand-600)' : 'var(--gray-400)' }};">
                            {{ $d }}
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Vehicle Rows --}}
            @foreach($vehicles as $vehicle)
            <div class="vehicle-row">
                <div class="vehicle-label">
                    <img src="{{ $vehicle->primary_photo_url }}"
                         style="width:36px;height:26px;object-fit:cover;border-radius:4px;flex-shrink:0;"
                         alt="{{ $vehicle->brand }}">
                    <div style="min-width:0;">
                        <div style="font-weight:700;font-size:.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                        <div style="font-size:.7rem;color:var(--gray-400);">{{ $vehicle->license_plate }}</div>
                    </div>
                </div>
                <div class="timeline-cell" style="position:relative;">
                    {{-- Day Grid Lines --}}
                    <div style="display:grid;grid-template-columns:repeat({{ $daysInMonth }},1fr);width:100%;height:100%;position:absolute;top:0;left:0;">
                        @for($d = 1; $d <= $daysInMonth; $d++)
                        <div style="border-right:1px solid var(--gray-100);
                                    {{ $d == now()->day && $month == now()->month && $year == now()->year ? 'background:rgba(20,184,166,.04);' : '' }}">
                        </div>
                        @endfor
                    </div>

                    {{-- Booking Blocks --}}
                    @foreach($vehicle->monthBookings as $booking)
                    @php
                        $startDay = max(1, $booking->start_date->day);
                        $endDay   = min($daysInMonth, $booking->end_date->day);
                        $left     = (($startDay - 1) / $daysInMonth) * 100;
                        $width    = (($endDay - $startDay + 1) / $daysInMonth) * 100;
                        $colors   = [
                            'pending'      => ['bg'=>'#fef3c7','text'=>'#92400e'],
                            'dikonfirmasi' => ['bg'=>'#dbeafe','text'=>'#1e40af'],
                            'aktif'        => ['bg'=>'var(--brand-100)','text'=>'var(--brand-800)'],
                            'selesai'      => ['bg'=>'#d1fae5','text'=>'#065f46'],
                            'dibatalkan'   => ['bg'=>'#fee2e2','text'=>'#991b1b'],
                        ];
                        $c = $colors[$booking->status->value] ?? $colors['pending'];
                    @endphp
                    <div class="timeline-block"
                         style="left:{{ $left }}%;width:{{ $width }}%;background:{{ $c['bg'] }};color:{{ $c['text'] }};"
                         title="{{ $booking->user->name }} · {{ $booking->start_date->format('d M') }} – {{ $booking->end_date->format('d M') }}"
                         onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
                        {{ Str::limit($booking->user->name, 14) }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Legend --}}
    <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:14px;">
        @foreach([
            ['label'=>'Pending','color'=>'#fef3c7','text'=>'#92400e'],
            ['label'=>'Dikonfirmasi','color'=>'#dbeafe','text'=>'#1e40af'],
            ['label'=>'Aktif','color'=>'var(--brand-100)','text'=>'var(--brand-800)'],
            ['label'=>'Selesai','color'=>'#d1fae5','text'=>'#065f46'],
            ['label'=>'Dibatalkan','color'=>'#fee2e2','text'=>'#991b1b'],
        ] as $l)
        <div style="display:flex;align-items:center;gap:6px;">
            <div style="width:12px;height:12px;border-radius:2px;background:{{ $l['color'] }};border:1px solid {{ $l['text'] }}22;"></div>
            <span style="font-size:.78rem;color:var(--gray-500);">{{ $l['label'] }}</span>
        </div>
        @endforeach
    </div>
    @endif

</x-app-layout>

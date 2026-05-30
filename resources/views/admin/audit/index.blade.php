<x-app-layout>
    <x-slot:title>Log Aktivitas</x-slot:title>

    @push('styles')
    <style>
    .log-row {
        display:grid;grid-template-columns:140px 36px 1fr 160px;
        align-items:flex-start;gap:14px;padding:14px 20px;
        border-bottom:1px solid var(--gray-100);transition:background .12s;
    }
    .log-row:hover { background:var(--gray-50); }
    .log-row:last-child { border-bottom:none; }
    .log-icon-wrap {
        width:34px;height:34px;border-radius:var(--radius-md);
        display:flex;align-items:center;justify-content:center;flex-shrink:0;
    }
    .log-action { font-size:.85rem;font-weight:700;color:var(--gray-800); }
    .log-desc   { font-size:.82rem;color:var(--gray-500);margin-top:2px;line-height:1.5; }
    .log-meta   { font-size:.75rem;color:var(--gray-400); }
    .event-badge {
        display:inline-flex;align-items:center;padding:2px 8px;
        border-radius:var(--radius-full);font-size:.68rem;font-weight:700;
        letter-spacing:.3px;text-transform:uppercase;
    }
    </style>
    @endpush

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;margin-bottom:4px;">Log Aktivitas</h1>
            <p class="text-sm text-muted">Rekam jejak semua aktivitas sistem dan pengguna</p>
        </div>
        <div style="display:flex;gap:10px;">
            <form method="POST" action="{{ route('admin.audit.clear') }}"
                  onsubmit="return confirm('Hapus log lebih dari 90 hari yang lalu?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-secondary" style="color:var(--danger);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    Bersihkan Log Lama
                </button>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
        @foreach([
            ['label'=>'Log Hari Ini','value'=>$stats['today'],'color'=>'var(--brand-600)','bg'=>'var(--brand-50)'],
            ['label'=>'Aksi Admin','value'=>$stats['admin_actions'],'color'=>'var(--info)','bg'=>'var(--info-bg)'],
            ['label'=>'Aksi Pelanggan','value'=>$stats['user_actions'],'color'=>'var(--success)','bg'=>'var(--success-bg)'],
            ['label'=>'Error / Gagal','value'=>$stats['errors'],'color'=>'var(--danger)','bg'=>'var(--danger-bg)'],
        ] as $s)
        <div class="card" style="padding:14px 16px;">
            <div style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;color:{{ $s['color'] }};margin-bottom:4px;">{{ $s['value'] }}</div>
            <div style="font-size:.78rem;color:var(--gray-500);font-weight:600;">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body" style="padding:14px 18px;">
            <form method="GET">
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                    <div style="flex:1;min-width:180px;">
                        <div class="form-label-sm">Cari</div>
                        <div class="input-icon">
                            <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="q" class="form-input" placeholder="Nama, aksi, IP..." value="{{ request('q') }}" style="padding-left:36px;">
                        </div>
                    </div>
                    <div style="min-width:150px;">
                        <div class="form-label-sm">Jenis Aksi</div>
                        <select name="event" class="form-select">
                            <option value="">Semua Aksi</option>
                            @foreach(\App\Enums\AuditEvent::cases() as $e)
                            <option value="{{ $e->value }}" {{ request('event') === $e->value ? 'selected' : '' }}>{{ $e->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width:130px;">
                        <div class="form-label-sm">Aktor</div>
                        <select name="actor" class="form-select">
                            <option value="">Semua</option>
                            <option value="admin" {{ request('actor') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="customer" {{ request('actor') === 'customer' ? 'selected' : '' }}>Pelanggan</option>
                            <option value="system" {{ request('actor') === 'system' ? 'selected' : '' }}>Sistem</option>
                        </select>
                    </div>
                    <div style="min-width:130px;">
                        <div class="form-label-sm">Rentang Waktu</div>
                        <select name="range" class="form-select">
                            <option value="today" {{ request('range','today') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="week"  {{ request('range') === 'week'  ? 'selected' : '' }}>7 Hari</option>
                            <option value="month" {{ request('range') === 'month' ? 'selected' : '' }}>30 Hari</option>
                            <option value="all"   {{ request('range') === 'all'   ? 'selected' : '' }}>Semua</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Filter
                        </button>
                        <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.73"/></svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Log List --}}
    <div class="card">
        @php
        $eventConfig = [
            'login'            => ['bg'=>'var(--brand-50)','color'=>'var(--brand-600)','icon'=>'<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>'],
            'logout'           => ['bg'=>'var(--gray-100)','color'=>'var(--gray-500)','icon'=>'<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>'],
            'booking_created'  => ['bg'=>'var(--info-bg)','color'=>'var(--info)','icon'=>'<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>'],
            'booking_confirmed'=> ['bg'=>'var(--success-bg)','color'=>'var(--success)','icon'=>'<polyline points="20 6 9 17 4 12"/>'],
            'booking_cancelled'=> ['bg'=>'var(--danger-bg)','color'=>'var(--danger)','icon'=>'<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'],
            'payment_verified' => ['bg'=>'var(--success-bg)','color'=>'var(--success)','icon'=>'<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>'],
            'vehicle_created'  => ['bg'=>'var(--brand-50)','color'=>'var(--brand-600)','icon'=>'<rect width="13" height="8" x="8" y="13" rx="2"/>'],
            'vehicle_updated'  => ['bg'=>'var(--warning-bg)','color'=>'var(--warning)','icon'=>'<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>'],
            'user_banned'      => ['bg'=>'var(--danger-bg)','color'=>'var(--danger)','icon'=>'<circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>'],
            'review_created'   => ['bg'=>'#fef3c7','color'=>'#92400e','icon'=>'<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>'],
        ];
        @endphp

        @forelse($logs as $log)
        @php $cfg = $eventConfig[$log->event] ?? ['bg'=>'var(--gray-100)','color'=>'var(--gray-500)','icon'=>'<circle cx="12" cy="12" r="10"/>']; @endphp
        <div class="log-row">
            {{-- Timestamp --}}
            <div>
                <div style="font-size:.8rem;font-weight:700;color:var(--gray-700);">{{ $log->created_at->format('d M Y') }}</div>
                <div class="log-meta">{{ $log->created_at->format('H:i:s') }}</div>
                <div class="log-meta" style="margin-top:2px;">{{ $log->created_at->diffForHumans() }}</div>
            </div>

            {{-- Icon --}}
            <div class="log-icon-wrap" style="background:{{ $cfg['bg'] }};">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="{{ $cfg['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    {!! $cfg['icon'] !!}
                </svg>
            </div>

            {{-- Content --}}
            <div>
                <div class="log-action">
                    {{ $log->action }}
                    @if($log->subject_type && $log->subject_id)
                    <span style="font-weight:400;color:var(--gray-400);">&nbsp;·&nbsp;{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</span>
                    @endif
                </div>
                @if($log->description)
                <div class="log-desc">{{ $log->description }}</div>
                @endif
                @if($log->properties && count($log->properties) > 0)
                <div style="margin-top:6px;display:flex;flex-wrap:wrap;gap:4px;">
                    @foreach(array_slice($log->properties, 0, 4) as $key => $val)
                    <span style="font-size:.7rem;background:var(--gray-100);color:var(--gray-600);padding:2px 7px;border-radius:3px;font-family:monospace;">
                        {{ $key }}: {{ is_string($val) ? Str::limit($val, 20) : json_encode($val) }}
                    </span>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Actor --}}
            <div style="text-align:right;">
                @if($log->causer)
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;margin-bottom:3px;">
                    <img src="{{ $log->causer->avatar_url }}" alt="{{ $log->causer->name }}"
                         style="width:22px;height:22px;border-radius:50%;object-fit:cover;">
                    <span style="font-size:.8rem;font-weight:600;color:var(--gray-700);">{{ Str::before($log->causer->name, ' ') }}</span>
                </div>
                @else
                <span style="font-size:.78rem;color:var(--gray-400);">Sistem</span>
                @endif
                @if($log->ip_address)
                <div class="log-meta">{{ $log->ip_address }}</div>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:48px;text-align:center;color:var(--gray-400);">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;opacity:.3;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <div style="font-weight:600;margin-bottom:4px;">Tidak ada log ditemukan</div>
            <div style="font-size:.85rem;">Coba ubah filter pencarian</div>
        </div>
        @endforelse

        @if($logs->hasPages())
        <div style="padding:16px 20px;border-top:1px solid var(--gray-100);">
            {{ $logs->appends(request()->query())->links('vendor.pagination.simple-rentwheels') }}
        </div>
        @endif
    </div>

</x-app-layout>

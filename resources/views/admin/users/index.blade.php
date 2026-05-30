<x-app-layout>
    <x-slot:title>Data Pelanggan</x-slot:title>

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;margin-bottom:4px;">Data Pelanggan</h1>
            <p class="text-sm text-muted">{{ $users->total() }} pelanggan terdaftar</p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.users.export') }}" class="btn btn-secondary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Ekspor CSV
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
        @foreach([
            ['label'=>'Total Pelanggan','value'=>$stats['total'],'icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>','color'=>'var(--brand-600)','bg'=>'var(--brand-50)'],
            ['label'=>'Pelanggan Aktif','value'=>$stats['active'],'icon'=>'<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>','color'=>'var(--success)','bg'=>'var(--success-bg)'],
            ['label'=>'Baru Bulan Ini','value'=>$stats['new_this_month'],'icon'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>','color'=>'var(--info)','bg'=>'var(--info-bg)'],
            ['label'=>'Tidak Aktif','value'=>$stats['inactive'],'icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/>','color'=>'var(--gray-400)','bg'=>'var(--gray-100)'],
        ] as $s)
        <div class="card" style="padding:16px 18px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;background:{{ $s['bg'] }};border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $s['color'] }}" stroke-width="2">{!! $s['icon'] !!}</svg>
            </div>
            <div>
                <div style="font-family:'Sora',sans-serif;font-size:1.3rem;font-weight:800;color:{{ $s['color'] }};line-height:1;">{{ $s['value'] }}</div>
                <div style="font-size:.78rem;color:var(--gray-500);font-weight:600;">{{ $s['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET">
                <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                    <div style="flex:1;min-width:200px;">
                        <div class="form-label-sm">Cari Pelanggan</div>
                        <div class="input-icon">
                            <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="q" class="form-input" placeholder="Nama, email, nomor HP..." value="{{ request('q') }}" style="padding-left:36px;">
                        </div>
                    </div>
                    <div style="min-width:150px;">
                        <div class="form-label-sm">Status</div>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Diblokir</option>
                        </select>
                    </div>
                    <div style="min-width:150px;">
                        <div class="form-label-sm">Urutkan</div>
                        <select name="sort" class="form-select">
                            <option value="newest" {{ request('sort','newest') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="most_bookings" {{ request('sort') === 'most_bookings' ? 'selected' : '' }}>Booking Terbanyak</option>
                            <option value="highest_spend" {{ request('sort') === 'highest_spend' ? 'selected' : '' }}>Pengeluaran Tertinggi</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Cari
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.73"/></svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Kontak</th>
                        <th style="text-align:center;">Pemesanan</th>
                        <th style="text-align:right;">Total Pengeluaran</th>
                        <th>Bergabung</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                <div>
                                    <div style="font-weight:700;font-size:.875rem;">{{ $user->name }}</div>
                                    <div style="font-size:.75rem;color:var(--gray-400);">ID #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:.85rem;">{{ $user->email }}</div>
                            @if($user->phone)
                            <div style="font-size:.78rem;color:var(--gray-400);">{{ $user->phone }}</div>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span style="font-family:'Sora',sans-serif;font-weight:700;font-size:.95rem;">{{ $user->bookings_count }}</span>
                            <div style="font-size:.72rem;color:var(--gray-400);">pemesanan</div>
                        </td>
                        <td style="text-align:right;">
                            <span style="font-family:'Sora',sans-serif;font-weight:700;font-size:.875rem;color:var(--brand-700);">
                                Rp {{ number_format($user->total_spend ?? 0, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:.85rem;">{{ $user->created_at->format('d M Y') }}</div>
                            <div style="font-size:.75rem;color:var(--gray-400);">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            @if($user->banned_at)
                            <span class="badge" style="background:var(--danger-bg);color:var(--danger);">Diblokir</span>
                            @elseif($user->bookings_count > 0)
                            <span class="badge" style="background:var(--success-bg);color:var(--success);">Aktif</span>
                            @else
                            <span class="badge" style="background:var(--gray-100);color:var(--gray-500);">Baru</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;justify-content:flex-end;gap:6px;" x-data="{ open: false }">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost btn-sm">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Detail
                                </a>
                                <div style="position:relative;">
                                    <button @click="open = !open" class="btn btn-ghost btn-sm">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                         class="dropdown-menu" style="right:0;top:calc(100% + 4px);min-width:160px;">
                                        <a href="{{ route('admin.bookings.index', ['user' => $user->id]) }}" class="dropdown-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                            Riwayat Booking
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        @if(!$user->banned_at)
                                        <form method="POST" action="{{ route('admin.users.ban', $user) }}"
                                              onsubmit="return confirm('Blokir pelanggan {{ $user->name }}?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item danger">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                Blokir Akun
                                            </button>
                                        </form>
                                        @else
                                        <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                                Buka Blokir
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--gray-400);">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 10px;opacity:.4;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <div style="font-weight:600;">Tidak ada pelanggan ditemukan</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div style="padding:16px 20px;border-top:1px solid var(--gray-100);">
            {{ $users->appends(request()->query())->links('vendor.pagination.simple-rentwheels') }}
        </div>
        @endif
    </div>

</x-app-layout>

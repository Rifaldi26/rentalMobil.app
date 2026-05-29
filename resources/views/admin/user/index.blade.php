<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelanggan — Admin</title>
    @vite(['resources/css/dashboard.css'])
</head>
<body>

<nav class="nav">
    <button onclick="window.location.href='{{ route('admin.dashboard') }}'"
        style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Pelanggan</div>
    <div style="width:36px;"></div>
</nav>

<div class="content" style="padding:16px 20px 100px;">

    @if (session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#16a34a;margin-bottom:16px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @php
        $users = \App\Models\User::where('role', 'pelanggan')
            ->withCount(['pemesanans as total_pemesanan'])
            ->withSum(['pemesanans as total_pengeluaran' => fn($q) => $q->where('status', 'selesai')], 'total_harga')
            ->latest()
            ->get();

        $totalUser      = $users->count();
        $userAktif      = $users->filter(fn($u) => $u->total_pemesanan > 0)->count();
        $userBulanIni   = $users->filter(fn($u) => $u->created_at->isCurrentMonth())->count();
    @endphp

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:20px;">
        <div style="background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:12px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:var(--gray-900);">{{ $totalUser }}</div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Total</div>
        </div>
        <div style="background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:12px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:var(--brand-400);">{{ $userAktif }}</div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Pernah Pesan</div>
        </div>
        <div style="background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:12px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:var(--success);">{{ $userBulanIni }}</div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Baru Bulan Ini</div>
        </div>
    </div>

    {{-- Search --}}
    <div style="position:relative;margin-bottom:16px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--gray-400);pointer-events:none;">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input
            type="text"
            id="search-input"
            placeholder="Cari nama atau email..."
            oninput="filterUsers()"
            style="width:100%;padding:11px 13px 11px 38px;border:1.5px solid var(--gray-200);border-radius:var(--radius-sm);
                   font-size:14px;color:var(--gray-900);background:#fff;font-family:var(--font);box-sizing:border-box;
                   transition:border-color .15s;"
            onfocus="this.style.borderColor='var(--brand-400)'"
            onblur="this.style.borderColor='var(--gray-200)'"
        >
    </div>

    {{-- Filter tabs --}}
    <div style="display:flex;gap:8px;margin-bottom:16px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none;">
        <button class="cat-chip active" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterTab('semua', this)">Semua</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterTab('aktif', this)">🔵 Pernah Pesan</button>
        <button class="cat-chip" style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;"
            onclick="filterTab('baru', this)">🆕 Bulan Ini</button>
    </div>

    {{-- User list --}}
    @if ($users->isEmpty())
        <div style="text-align:center;padding:60px 20px;color:var(--gray-500);">
            <div style="font-size:48px;margin-bottom:12px;">👥</div>
            <div style="font-weight:600;">Belum ada pelanggan terdaftar</div>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:10px;" id="user-list">
            @foreach ($users as $user)
                @php
                    $isAktif = $user->total_pemesanan > 0;
                    $isBaru  = $user->created_at->isCurrentMonth();
                    $inisial = strtoupper(substr($user->name, 0, 1));
                    $warnaBg = collect(['#dbeafe','#dcfce7','#fef9c3','#fce7f3','#ede9fe'])
                        ->get($user->id % 5, '#f3f4f6');
                    $warnaText = collect(['#1d4ed8','#15803d','#a16207','#be185d','#6d28d9'])
                        ->get($user->id % 5, '#374151');
                @endphp
                <div class="booking-item user-card"
                     data-aktif="{{ $isAktif ? 'ya' : 'tidak' }}"
                     data-baru="{{ $isBaru ? 'ya' : 'tidak' }}"
                     data-nama="{{ strtolower($user->name) }}"
                     data-email="{{ strtolower($user->email) }}">

                    <div style="display:flex;align-items:center;gap:12px;">
                        {{-- Avatar --}}
                        <div style="width:44px;height:44px;border-radius:50%;background:{{ $warnaBg }};
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:17px;font-weight:800;color:{{ $warnaText }};flex-shrink:0;">
                            {{ $inisial }}
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:14px;font-weight:700;color:var(--gray-900);
                                        white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $user->name }}
                            </div>
                            <div style="font-size:12px;color:var(--gray-500);margin-top:1px;
                                        white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $user->email }}
                            </div>
                            <div style="font-size:11px;color:var(--gray-400);margin-top:2px;">
                                📅 Daftar {{ $user->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>

                        {{-- Status badge --}}
                        <div style="flex-shrink:0;text-align:right;">
                            @if ($isAktif)
                                <span style="background:#eff6ff;color:#1d4ed8;font-size:10px;font-weight:700;
                                             padding:3px 8px;border-radius:20px;display:block;white-space:nowrap;">
                                    Aktif
                                </span>
                            @else
                                <span style="background:var(--gray-100);color:var(--gray-400);font-size:10px;font-weight:600;
                                             padding:3px 8px;border-radius:20px;display:block;white-space:nowrap;">
                                    Belum pesan
                                </span>
                            @endif
                            @if ($isBaru)
                                <span style="background:#f0fdf4;color:#16a34a;font-size:10px;font-weight:700;
                                             padding:2px 7px;border-radius:20px;display:block;white-space:nowrap;margin-top:4px;">
                                    Baru
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Stats row --}}
                    <div style="display:flex;gap:0;margin-top:12px;border-top:1px solid var(--gray-100);padding-top:10px;">
                        <div style="flex:1;text-align:center;">
                            <div style="font-size:15px;font-weight:800;color:var(--gray-900);">
                                {{ $user->total_pemesanan }}
                            </div>
                            <div style="font-size:10px;color:var(--gray-400);margin-top:1px;">Pemesanan</div>
                        </div>
                        <div style="width:1px;background:var(--gray-100);"></div>
                        <div style="flex:1;text-align:center;">
                            <div style="font-size:15px;font-weight:800;color:var(--brand-400);">
                                {{ $user->total_pengeluaran > 0
                                    ? 'Rp ' . number_format($user->total_pengeluaran / 1000000, 1, ',', '.') . 'jt'
                                    : 'Rp 0' }}
                            </div>
                            <div style="font-size:10px;color:var(--gray-400);margin-top:1px;">Total Sewa</div>
                        </div>
                        <div style="width:1px;background:var(--gray-100);"></div>
                        <div style="flex:1;text-align:center;">
                            <div style="font-size:15px;font-weight:800;color:var(--gray-700);">
                                {{ $user->no_hp ?? '—' }}
                            </div>
                            <div style="font-size:10px;color:var(--gray-400);margin-top:1px;">No. HP</div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div style="display:flex;gap:8px;margin-top:10px;">
                        <a href="mailto:{{ $user->email }}"
                           style="flex:1;display:block;text-align:center;padding:9px;
                                  background:var(--gray-50);border:1px solid var(--gray-200);
                                  border-radius:var(--radius-sm);font-size:12px;font-weight:600;
                                  color:var(--gray-700);text-decoration:none;">
                            ✉️ Email
                        </a>
                        <a href="{{ route('admin.pemesanan.index') }}?search={{ urlencode($user->name) }}"
                           style="flex:1;display:block;text-align:center;padding:9px;
                                  background:#eff6ff;border:1px solid #bfdbfe;
                                  border-radius:var(--radius-sm);font-size:12px;font-weight:600;
                                  color:#1d4ed8;text-decoration:none;">
                            📋 Riwayat
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Empty state (search) --}}
        <div id="empty-search" style="display:none;text-align:center;padding:48px 20px;color:var(--gray-500);">
            <div style="font-size:40px;margin-bottom:10px;">🔍</div>
            <div style="font-weight:600;font-size:14px;">Pelanggan tidak ditemukan</div>
            <div style="font-size:12px;margin-top:4px;">Coba kata kunci lain</div>
        </div>
    @endif

</div>

@include('admin.partials.bottom-nav')

<script>
let activeTab = 'semua';

function filterTab(tab, el) {
    activeTab = tab;
    document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    applyFilters();
}

function filterUsers() {
    applyFilters();
}

function applyFilters() {
    const keyword = document.getElementById('search-input').value.toLowerCase().trim();
    const cards   = document.querySelectorAll('.user-card');
    let visible   = 0;

    cards.forEach(card => {
        const matchTab    = activeTab === 'semua'
            || (activeTab === 'aktif' && card.dataset.aktif === 'ya')
            || (activeTab === 'baru'  && card.dataset.baru  === 'ya');
        const matchSearch = !keyword
            || card.dataset.nama.includes(keyword)
            || card.dataset.email.includes(keyword);

        const show = matchTab && matchSearch;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('empty-search').style.display = visible === 0 ? 'block' : 'none';
}
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pemesanan — Admin</title>
    @vite(['resources/css/admin.css'])
</head>
<body>

@include('admin.partials.sidebar')

<div class="admin-main">

    {{-- ─── Header ─── --}}
    <div class="admin-header">
        <div>
            <div class="admin-title">Daftar Pemesanan</div>
            <div class="admin-subtitle">Total {{ $pemesanans->total() }} pemesanan</div>
        </div>
    </div>

    {{-- ─── Alert ─── --}}
    @if (session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ─── Filter & Search ─── --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('admin.pemesanan.index') }}"
                  style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">

                {{-- Search --}}
                <div style="flex:1;min-width:200px;">
                    <label class="form-label">Cari Pelanggan / Mobil</label>
                    <input
                        type="text"
                        name="search"
                        class="form-input"
                        placeholder="Nama pelanggan atau nama mobil..."
                        value="{{ request('search') }}"
                    >
                </div>

                {{-- Filter Status --}}
                <div style="min-width:160px;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="">Semua Status</option>
                        <option value="pending"      {{ request('status') === 'pending'       ? 'selected' : '' }}>⏳ Menunggu</option>
                        <option value="dikonfirmasi" {{ request('status') === 'dikonfirmasi'  ? 'selected' : '' }}>🔵 Berjalan</option>
                        <option value="selesai"      {{ request('status') === 'selesai'       ? 'selected' : '' }}>✅ Selesai</option>
                        <option value="dibatalkan"   {{ request('status') === 'dibatalkan'    ? 'selected' : '' }}>❌ Dibatalkan</option>
                    </select>
                </div>

                {{-- Filter Bulan --}}
                <div style="min-width:160px;">
                    <label class="form-label">Bulan</label>
                    <input
                        type="month"
                        name="bulan"
                        class="form-input"
                        value="{{ request('bulan') }}"
                    >
                </div>

                {{-- Tombol --}}
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn-primary">🔍 Filter</button>
                    <a href="{{ route('admin.pemesanan.index') }}" class="btn-secondary">Reset</a>
                </div>

            </form>
        </div>
    </div>

    {{-- ─── Tab Ringkasan ─── --}}
    @php
        $counts = [
            'semua'       => \App\Models\Pemesanan::count(),
            'pending'     => \App\Models\Pemesanan::where('status','pending')->count(),
            'dikonfirmasi'=> \App\Models\Pemesanan::where('status','dikonfirmasi')->count(),
            'selesai'     => \App\Models\Pemesanan::where('status','selesai')->count(),
            'dibatalkan'  => \App\Models\Pemesanan::where('status','dibatalkan')->count(),
        ];
    @endphp

    <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        @foreach ([
            ''             => ['label' => 'Semua',     'icon' => '📋', 'count' => $counts['semua']],
            'pending'      => ['label' => 'Menunggu',  'icon' => '⏳', 'count' => $counts['pending']],
            'dikonfirmasi' => ['label' => 'Berjalan',  'icon' => '🔵', 'count' => $counts['dikonfirmasi']],
            'selesai'      => ['label' => 'Selesai',   'icon' => '✅', 'count' => $counts['selesai']],
            'dibatalkan'   => ['label' => 'Dibatalkan','icon' => '❌', 'count' => $counts['dibatalkan']],
        ] as $val => $tab)
            <a href="{{ route('admin.pemesanan.index', array_merge(request()->except('status','page'), $val ? ['status' => $val] : [])) }}"
               style="padding:8px 16px;border-radius:var(--radius-sm);font-size:13px;font-weight:700;text-decoration:none;border:1.5px solid;transition:all .15s;
               {{ request('status', '') === $val
                   ? 'background:var(--brand-400);color:#fff;border-color:var(--brand-400);'
                   : 'background:#fff;color:var(--gray-700);border-color:var(--gray-200);' }}">
                {{ $tab['icon'] }} {{ $tab['label'] }}
                <span style="margin-left:4px;padding:1px 6px;border-radius:20px;font-size:11px;
                    {{ request('status', '') === $val ? 'background:rgba(255,255,255,.25);color:#fff;' : 'background:var(--gray-100);color:var(--gray-500);' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- ─── Tabel Pemesanan ─── --}}
    <div class="card">
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pelanggan</th>
                        <th>Mobil</th>
                        <th>Tanggal Sewa</th>
                        <th>Durasi</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pemesanans as $i => $p)
                        @php
                            $durasi = $p->tanggal_mulai->diffInDays($p->tanggal_selesai);
                        @endphp
                        <tr>
                            <td>{{ $pemesanans->firstItem() + $i }}</td>

                            {{-- Pelanggan --}}
                            <td>
                                <div class="fw-600">{{ $p->user->name }}</div>
                                <div class="text-sm text-gray">{{ $p->user->no_hp ?? $p->user->email }}</div>
                            </td>

                            {{-- Mobil --}}
                            <td>
                                <div class="fw-600">{{ $p->mobil->nama }}</div>
                                <div class="text-sm text-gray">{{ $p->mobil->plat_nomor }}</div>
                            </td>

                            {{-- Tanggal --}}
                            <td>
                                <div class="fw-600">{{ $p->tanggal_mulai->format('d M Y') }}</div>
                                <div class="text-sm text-gray">s/d {{ $p->tanggal_selesai->format('d M Y') }}</div>
                            </td>

                            {{-- Durasi --}}
                            <td>
                                <span class="badge-plat">{{ $durasi }} hari</span>
                            </td>

                            {{-- Total Harga --}}
                            <td>
                                <div class="fw-600" style="color:var(--brand-400);">
                                    Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                                </div>
                                @if ($p->catatan)
                                    <div class="text-sm text-gray" title="{{ $p->catatan }}">
                                        📝 {{ Str::limit($p->catatan, 25) }}
                                    </div>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @php
                                    $badge = match($p->status) {
                                        'pending'      => ['class' => 'status-pending',     'label' => '⏳ Menunggu'],
                                        'dikonfirmasi' => ['class' => 'status-dikonfirmasi','label' => '🔵 Berjalan'],
                                        'selesai'      => ['class' => 'status-selesai',     'label' => '✅ Selesai'],
                                        'dibatalkan'   => ['class' => 'status-dibatalkan',  'label' => '❌ Dibatalkan'],
                                        default        => ['class' => '',                   'label' => $p->status],
                                    };
                                @endphp
                                <span class="badge-status {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td>
                                <div class="action-group">
                                    @if ($p->status === 'pending')
                                        {{-- Konfirmasi --}}
                                        <form action="{{ route('admin.pemesanan.konfirmasi', $p) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-edit"
                                                onclick="return confirm('Konfirmasi pemesanan {{ addslashes($p->user->name) }}?')">
                                                ✅ Konfirmasi
                                            </button>
                                        </form>
                                        {{-- Tolak --}}
                                        <form action="{{ route('admin.pemesanan.tolak', $p) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-delete"
                                                onclick="return confirm('Tolak pemesanan ini?')">
                                                ❌ Tolak
                                            </button>
                                        </form>

                                    @elseif ($p->status === 'dikonfirmasi')
                                        {{-- Selesai --}}
                                        <form action="{{ route('admin.pemesanan.selesai', $p) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-edit"
                                                onclick="return confirm('Tandai pemesanan ini selesai?')">
                                                🏁 Selesai
                                            </button>
                                        </form>

                                    @else
                                        <span class="text-sm text-gray">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:60px;color:var(--gray-500);">
                                <div style="font-size:40px;margin-bottom:12px;">📋</div>
                                <div style="font-weight:600;">
                                    {{ request('search') || request('status') || request('bulan')
                                        ? 'Tidak ada pemesanan yang cocok dengan filter'
                                        : 'Belum ada pemesanan' }}
                                </div>
                                @if (request('search') || request('status') || request('bulan'))
                                    <a href="{{ route('admin.pemesanan.index') }}"
                                       style="display:inline-block;margin-top:12px;color:var(--brand-400);font-weight:600;text-decoration:none;">
                                        Reset filter →
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($pemesanans->hasPages())
            <div class="pagination-wrap">
                {{ $pemesanans->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>

</body>
</html>
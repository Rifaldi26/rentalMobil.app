<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mobil — Admin</title>
    @vite(['resources/css/dashboard.css'])
</head>
<body>

{{-- ═══ TOP NAV ═══════════════════════════════════════════ --}}
<nav class="nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-back" style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);text-decoration:none;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </a>
    <div class="nav-brand" style="font-size:16px;">Kelola Mobil</div>
    <a href="{{ route('admin.mobil.create') }}"
       style="background:var(--brand-400);color:#fff;border:none;border-radius:var(--radius-sm);padding:7px 14px;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap;">
        + Tambah
    </a>
</nav>

<div class="content" style="padding:16px 20px 100px;">

    {{-- Alert --}}
    @if (session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#16a34a;margin-bottom:16px;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);padding:12px 14px;font-size:13px;font-weight:600;color:#dc2626;margin-bottom:16px;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Ringkasan --}}
    @php
        $totalMobil    = $mobils->total();
        $tersediaCount = \App\Models\Mobil::where('status','tersedia')->count();
        $disewaCount   = \App\Models\Mobil::where('status','disewa')->count();
    @endphp
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:20px;">
        <div style="background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:var(--gray-900);">{{ $totalMobil }}</div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Total Unit</div>
        </div>
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#16a34a;">{{ $tersediaCount }}</div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Tersedia</div>
        </div>
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:800;color:#dc2626;">{{ $disewaCount }}</div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Disewa</div>
        </div>
    </div>

    {{-- Filter Status --}}
    <div style="display:flex;gap:8px;margin-bottom:16px;overflow-x:auto;scrollbar-width:none;padding-bottom:2px;">
        <button class="cat-chip {{ !request('status') ? 'active' : '' }}"
            onclick="window.location='{{ route('admin.mobil.index') }}'"
            style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;">
            Semua ({{ $totalMobil }})
        </button>
        <button class="cat-chip {{ request('status') === 'tersedia' ? 'active' : '' }}"
            onclick="window.location='{{ route('admin.mobil.index', ['status' => 'tersedia']) }}'"
            style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;">
            ✅ Tersedia ({{ $tersediaCount }})
        </button>
        <button class="cat-chip {{ request('status') === 'disewa' ? 'active' : '' }}"
            onclick="window.location='{{ route('admin.mobil.index', ['status' => 'disewa']) }}'"
            style="flex-direction:row;min-width:auto;padding:8px 16px;font-size:12px;">
            🔴 Disewa ({{ $disewaCount }})
        </button>
    </div>

    {{-- Daftar Mobil --}}
    @forelse ($mobils as $mobil)
        <div class="booking-item" style="margin-bottom:10px;">
            <div style="display:flex;gap:12px;align-items:flex-start;">
                {{-- Foto --}}
                <div style="width:80px;height:64px;border-radius:var(--radius-sm);overflow:hidden;flex-shrink:0;background:var(--gray-100);display:flex;align-items:center;justify-content:center;">
                    @if ($mobil->foto)
                        <img src="{{ asset('storage/'.$mobil->foto) }}"
                             alt="{{ $mobil->nama }}"
                             style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <span style="font-size:28px;">🚗</span>
                    @endif
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0;">
                    <div style="font-size:14px;font-weight:700;color:var(--gray-900);">{{ $mobil->nama }}</div>
                    <div style="font-size:12px;color:var(--gray-500);margin-top:1px;">
                        {{ $mobil->merek }} · {{ $mobil->tahun }} · {{ $mobil->plat_nomor }}
                    </div>
                    <div style="font-size:14px;font-weight:800;color:var(--brand-400);margin-top:4px;">
                        Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}<span style="font-size:11px;font-weight:500;color:var(--gray-500);">/hari</span>
                    </div>
                </div>

                {{-- Status Toggle --}}
                <form action="{{ route('admin.mobil.toggle', $mobil) }}" method="POST" style="flex-shrink:0;">
                    @csrf @method('PATCH')
                    <button type="submit"
                        style="padding:5px 10px;border:none;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;
                        {{ $mobil->status === 'tersedia'
                            ? 'background:#f0fdf4;color:#16a34a;'
                            : 'background:#fef2f2;color:#dc2626;' }}">
                        {{ $mobil->status === 'tersedia' ? '✅ Tersedia' : '🔴 Disewa' }}
                    </button>
                </form>
            </div>

            {{-- Deskripsi --}}
            @if ($mobil->deskripsi)
                <div style="font-size:12px;color:var(--gray-500);margin-top:8px;line-height:1.5;">
                    {{ Str::limit($mobil->deskripsi, 80) }}
                </div>
            @endif

            {{-- Aksi --}}
            <div style="display:flex;gap:8px;margin-top:10px;">
                <a href="{{ route('admin.mobil.edit', $mobil) }}"
                   style="flex:1;padding:9px;background:var(--brand-50);color:var(--brand-400);border:none;border-radius:var(--radius-sm);font-size:13px;font-weight:700;text-align:center;text-decoration:none;">
                    ✏️ Edit
                </a>
                <form action="{{ route('admin.mobil.destroy', $mobil) }}" method="POST" style="flex:1;"
                      onsubmit="return confirm('Hapus {{ addslashes($mobil->nama) }}? Data tidak bisa dikembalikan.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        style="width:100%;padding:9px;background:#fef2f2;color:#dc2626;border:none;border-radius:var(--radius-sm);font-size:13px;font-weight:700;cursor:pointer;">
                        🗑 Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:60px 20px;color:var(--gray-500);">
            <div style="font-size:48px;margin-bottom:12px;">🚗</div>
            <div style="font-weight:600;font-size:15px;">Belum ada data mobil</div>
            <a href="{{ route('admin.mobil.create') }}"
               style="display:inline-block;margin-top:16px;padding:12px 24px;background:var(--brand-400);color:#fff;border-radius:var(--radius-md);font-weight:700;text-decoration:none;">
                + Tambah Mobil Pertama
            </a>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if ($mobils->hasPages())
        <div style="margin-top:16px;">
            {{ $mobils->appends(request()->query())->links() }}
        </div>
    @endif

</div>

{{-- ═══ BOTTOM NAV ════════════════════════════════════════ --}}
@include('admin.partials.bottom-nav')

<div class="toast" id="toast"></div>
<script>
let toastTimer;
function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = `toast ${type} show`;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
}
</script>

</body>
</html>
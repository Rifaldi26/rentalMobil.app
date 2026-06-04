<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit — Rental Mobil</title>
    @vite(['resources/css/dashboard.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="user-page">
@include('users.partials.desktop-sidebar')
<nav class="nav">
    <button onclick="history.back()"
        style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Favorit</div>
    <div style="width:36px;"></div>
</nav>

<div class="content" style="padding:16px 20px 100px;">

    @if (session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:12px 14px;
                    font-size:13px;font-weight:600;color:#16a34a;margin-bottom:16px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Header count --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div style="font-size:13px;color:var(--gray-500);">
            <span id="count-label">{{ $favorits->count() }}</span> kendaraan disimpan
        </div>
    </div>

    {{-- Empty state --}}
    @if ($favorits->isEmpty())
        <div style="text-align:center;padding:72px 20px 40px;" id="empty-state">
            <div style="font-size:56px;margin-bottom:16px;">🤍</div>
            <div style="font-size:16px;font-weight:700;color:var(--gray-900);margin-bottom:8px;">Belum ada favorit</div>
            <div style="font-size:13px;color:var(--gray-500);margin-bottom:24px;">
                Tap ikon ❤️ di halaman beranda untuk menyimpan kendaraan favoritmu
            </div>
            <a href="{{ route('dashboard') }}"
               style="display:inline-block;padding:12px 28px;background:var(--brand-400);color:#fff;
                      border-radius:var(--radius-md);font-size:14px;font-weight:700;text-decoration:none;">
                Jelajahi Kendaraan
            </a>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:12px;" id="favorit-list">
            @foreach ($favorits as $fav)
                @php $mobil = $fav->mobil; @endphp
                <div class="favorit-card" id="card-{{ $mobil->id }}"
                     style="background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);overflow:hidden;
                            box-shadow:0 2px 8px rgba(0,0,0,.04);">

                    <div style="display:flex;gap:0;">
                        {{-- Foto --}}
                        <div style="width:100px;flex-shrink:0;background:var(--gray-100);position:relative;">
                            @if ($mobil->foto)
                                <img src="{{ asset('storage/' . $mobil->foto) }}"
                                     alt="{{ $mobil->nama }}"
                                     style="width:100%;height:100%;object-fit:cover;display:block;">
                            @else
                                <div style="width:100%;height:100%;min-height:90px;display:flex;align-items:center;
                                            justify-content:center;font-size:32px;">
                                    🚗
                                </div>
                            @endif

                            {{-- Status badge --}}
                            @if (!$mobil->tersedia())
                                <div style="position:absolute;inset:0;background:rgba(0,0,0,.45);
                                            display:flex;align-items:center;justify-content:center;">
                                    <span style="background:rgba(0,0,0,.6);color:#fff;font-size:10px;font-weight:700;
                                                 padding:3px 8px;border-radius:20px;">Tidak Tersedia</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;padding:12px 12px 10px;display:flex;flex-direction:column;gap:4px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                                <div>
                                    <div style="font-size:14px;font-weight:700;color:var(--gray-900);">
                                        {{ $mobil->nama }}
                                    </div>
                                    <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">
                                        {{ $mobil->merek }} · {{ $mobil->tahun }} · {{ $mobil->plat_nomor }}
                                    </div>
                                </div>
                                {{-- Hapus favorit --}}
                                <button
                                    onclick="hapusFavorit({{ $mobil->id }}, this)"
                                    style="background:none;border:none;cursor:pointer;padding:4px;font-size:20px;
                                           line-height:1;flex-shrink:0;color:#ef4444;"
                                    title="Hapus dari favorit">
                                    ❤️
                                </button>
                            </div>

                            <div style="font-size:15px;font-weight:800;color:var(--brand-400);margin-top:4px;">
                                Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}
                                <span style="font-size:11px;font-weight:500;color:var(--gray-500);">/hari</span>
                            </div>

                            {{-- Actions --}}
                            <div style="display:flex;gap:8px;margin-top:6px;">
                                <a href="{{ route('user.mobil.show', $mobil) }}"
                                   style="flex:1;text-align:center;padding:8px;background:var(--gray-50);
                                          border:1px solid var(--gray-200);border-radius:var(--radius-sm);
                                          font-size:12px;font-weight:600;color:var(--gray-700);text-decoration:none;">
                                    Detail
                                </a>
                                @if ($mobil->tersedia())
                                    <a href="{{ route('pemesanan.create', ['mobil_id' => $mobil->id]) }}"
                                       style="flex:1;text-align:center;padding:8px;background:var(--brand-400);
                                              border-radius:var(--radius-sm);font-size:12px;font-weight:700;
                                              color:#fff;text-decoration:none;">
                                        Pesan
                                    </a>
                                @else
                                    <div style="flex:1;text-align:center;padding:8px;background:var(--gray-100);
                                                border-radius:var(--radius-sm);font-size:12px;font-weight:600;
                                                color:var(--gray-400);">
                                        Tidak Tersedia
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Empty state setelah semua dihapus via JS --}}
        <div id="empty-after-delete"
             style="display:none;text-align:center;padding:72px 20px 40px;">
            <div style="font-size:56px;margin-bottom:16px;">🤍</div>
            <div style="font-size:16px;font-weight:700;color:var(--gray-900);margin-bottom:8px;">Daftar favorit kosong</div>
            <div style="font-size:13px;color:var(--gray-500);margin-bottom:24px;">
                Jelajahi kendaraan dan simpan yang kamu suka
            </div>
            <a href="{{ route('dashboard') }}"
               style="display:inline-block;padding:12px 28px;background:var(--brand-400);color:#fff;
                      border-radius:var(--radius-md);font-size:14px;font-weight:700;text-decoration:none;">
                Jelajahi Kendaraan
            </a>
        </div>
    @endif

</div>

@if(Auth::user()->role === 'admin')
    @include('admin.partials.bottom-nav')
@else
    @include('users.partials.bottom-nav')
@endif
<script>
function hapusFavorit(mobilId, btn) {
    // Optimistic UI — langsung sembunyikan kartu
    const card = document.getElementById('card-' + mobilId);
    if (card) {
        card.style.transition = 'opacity .25s, transform .25s';
        card.style.opacity    = '0';
        card.style.transform  = 'translateX(20px)';
        setTimeout(() => card.remove(), 260);
    }

    // Update counter
    const countEl = document.getElementById('count-label');
    if (countEl) {
        const current = parseInt(countEl.textContent) || 0;
        const next    = Math.max(0, current - 1);
        countEl.textContent = next;

        // Tampilkan empty state jika sudah kosong semua
        if (next === 0) {
            setTimeout(() => {
                const list = document.getElementById('favorit-list');
                if (list) list.style.display = 'none';
                const empty = document.getElementById('empty-after-delete');
                if (empty) empty.style.display = 'block';
            }, 300);
        }
    }

    // Kirim ke server
    fetch(`/favorit/${mobilId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    }).catch(() => {
        // Jika gagal, reload untuk sinkronisasi
        showToast('Terjadi kesalahan, memuat ulang...', 'error');
        setTimeout(() => location.reload(), 1500);
    });
}
</script>

</body>
</html>
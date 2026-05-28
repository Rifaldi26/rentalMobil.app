<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin — Rental Mobil</title>
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
    <div class="nav-brand" style="font-size:16px;">Profil</div>
    <div style="width:36px;"></div>
</nav>

<div class="content" style="padding:24px 20px 100px;">

    {{-- Profile Header --}}
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--brand-100);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:var(--brand-600);">
            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
        </div>
        <div>
            <div style="font-size:18px;font-weight:700;">{{ Auth::user()->name }}</div>
            <div style="font-size:13px;color:var(--gray-500);">{{ Auth::user()->email }}</div>
            <div style="font-size:12px;color:var(--gray-500);margin-top:1px;">{{ Auth::user()->no_hp ?? '-' }}</div>
            <div style="font-size:12px;color:var(--brand-400);font-weight:600;margin-top:2px;">
                Admin · Bergabung {{ Auth::user()->created_at->translatedFormat('M Y') }}
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:24px;">
        <div style="background:var(--brand-50);border-radius:var(--radius-md);padding:12px;text-align:center;">
            <div style="font-size:20px;font-weight:800;color:var(--brand-400);">{{ \App\Models\Mobil::count() }}</div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Total Mobil</div>
        </div>
        <div style="background:#f0fdf4;border-radius:var(--radius-md);padding:12px;text-align:center;">
            <div style="font-size:20px;font-weight:800;color:var(--success);">{{ \App\Models\Pemesanan::where('status','selesai')->count() }}</div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Selesai</div>
        </div>
        <div style="background:#fff7ed;border-radius:var(--radius-md);padding:12px;text-align:center;">
            <div style="font-size:20px;font-weight:800;color:var(--accent-500);">{{ \App\Models\User::where('role','pelanggan')->count() }}</div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;">Pelanggan</div>
        </div>
    </div>

    {{-- Menu --}}
    <div style="background:#fff;border-radius:var(--radius-md);border:1px solid var(--gray-100);overflow:hidden;">
        <a href="{{ route('profile.edit') }}"
           style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);text-decoration:none;">
            <span style="font-size:14px;font-weight:600;color:var(--gray-800);">👤 Edit Profil</span>
            <span style="color:var(--gray-400);">›</span>
        </a>
        <a href="{{ route('admin.pemesanan.index') }}"
           style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);text-decoration:none;">
            <span style="font-size:14px;font-weight:600;color:var(--gray-800);">📋 Semua Pemesanan</span>
            <span style="color:var(--gray-400);">›</span>
        </a>
        <a href="{{ route('admin.user.index') }}"
           style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);text-decoration:none;">
            <span style="font-size:14px;font-weight:600;color:var(--gray-800);">👥 Data Pelanggan</span>
            <span style="color:var(--gray-400);">›</span>
        </a>
        <a href="{{ route('admin.mobil.index') }}"
           style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--gray-100);text-decoration:none;">
            <span style="font-size:14px;font-weight:600;color:var(--gray-800);">🚗 Kelola Armada</span>
            <span style="color:var(--gray-400);">›</span>
        </a>
        <div onclick="confirmLogout()"
             style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;cursor:pointer;">
            <span style="font-size:14px;font-weight:600;color:var(--danger);">🚪 Keluar</span>
            <span style="color:var(--gray-400);">›</span>
        </div>
    </div>

</div>

@include('admin.partials.bottom-nav')

<script>
function confirmLogout() {
    document.getElementById('modal-logout').style.display = 'flex';
}
</script>

</body>
</html>
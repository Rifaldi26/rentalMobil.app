{{-- resources/views/admin/notifikasi/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Admin — Rental Mobil</title>
    @vite(['resources/css/dashboard.css'])
    <style>
        .notif-list { padding: 16px 20px 100px; }
        .notif-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .notif-header h2 { font-size: 16px; font-weight: 700; color: var(--gray-900); }
        .btn-hapus-semua {
            font-size: 12px; color: var(--danger, #ef4444);
            background: none; border: none; cursor: pointer;
            font-weight: 600; padding: 4px 8px;
        }
        .notif-empty { text-align: center; padding: 60px 20px; color: var(--gray-400); }
        .notif-empty svg { margin: 0 auto 12px; display: block; opacity: .4; }
        .notif-item {
            display: flex; gap: 12px; background: #fff;
            border-radius: var(--radius-md, 12px); padding: 14px;
            margin-bottom: 10px; border: 1px solid var(--gray-100);
            text-decoration: none; color: inherit; width: 100%;
            text-align: left; cursor: pointer;
        }
        .notif-item.belum-dibaca { background: #eff6ff; border-color: #bfdbfe; }
        .notif-dot { width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; flex-shrink: 0; margin-top: 6px; }
        .notif-icon { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px; }
        .icon-success { background: #dcfce7; }
        .icon-warning { background: #fef3c7; }
        .icon-info    { background: #dbeafe; }
        .notif-body { flex: 1; min-width: 0; }
        .notif-judul { font-size: 14px; font-weight: 700; color: var(--gray-900); margin-bottom: 3px; }
        .notif-pesan { font-size: 12px; color: var(--gray-500); line-height: 1.5; }
        .notif-waktu { font-size: 11px; color: var(--gray-400); margin-top: 5px; }
    </style>
</head>
<body>

<nav class="nav">
    <a href="{{ route('admin.dashboard') }}" style="background:none;border:none;cursor:pointer;padding:4px;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </a>
    <div class="nav-brand" style="position:absolute;left:50%;transform:translateX(-50%);">Notifikasi</div>
    <div style="width:30px;"></div>
</nav>

<div class="notif-list">
    <div class="notif-header">
        <h2>Semua Notifikasi</h2>
        @if($notifikasis->isNotEmpty())
            <form action="{{ route('notifikasi.hapus') }}" method="POST" onsubmit="return confirm('Hapus semua notifikasi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-hapus-semua">Hapus semua</button>
            </form>
        @endif
    </div>

    @if($notifikasis->isEmpty())
        <div class="notif-empty">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <p>Belum ada notifikasi</p>
        </div>
    @else
        @foreach($notifikasis as $notif)
            @php
                $icon = match($notif->tipe) { 'success' => '✅', 'warning' => '⚠️', default => '🔔' };
                $iconClass = match($notif->tipe) { 'success' => 'icon-success', 'warning' => 'icon-warning', default => 'icon-info' };
            @endphp
            <form action="{{ route('notifikasi.baca', $notif) }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="notif-item {{ !$notif->dibaca ? 'belum-dibaca' : '' }}">
                    @if(!$notif->dibaca)
                        <div class="notif-dot"></div>
                    @else
                        <div style="width:8px;flex-shrink:0;"></div>
                    @endif
                    <div class="notif-icon {{ $iconClass }}">{{ $icon }}</div>
                    <div class="notif-body">
                        <div class="notif-judul">{{ $notif->judul }}</div>
                        <div class="notif-pesan">{{ $notif->pesan }}</div>
                        <div class="notif-waktu">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                </button>
            </form>
        @endforeach
        <div style="margin-top:16px;">{{ $notifikasis->links() }}</div>
    @endif
</div>

@include('admin.partials.bottom-nav')
</body>
</html>

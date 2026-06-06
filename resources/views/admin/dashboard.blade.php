@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . Auth::user()->name . '! 👋')

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush

@section('content')
<div class="admin-content">

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <a href="{{ route('admin.pemesanan.index') }}"
           style="text-decoration:none;background:linear-gradient(135deg,#1d4ed8,#2563eb);border-radius:var(--radius-md);padding:20px;">
            <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Pendapatan Bulan Ini</div>
            <div style="font-size:24px;font-weight:800;color:#fff;">Rp {{ number_format($pendapatanBulanIni/1000000, 1, ',', '.') }}jt</div>
            <div style="font-size:12px;color:rgba(255,255,255,.6);margin-top:4px;">Total: Rp {{ number_format($pendapatanTotal/1000000, 1, ',', '.') }}jt</div>
        </a>
        <a href="{{ route('admin.pemesanan.index') }}"
           style="text-decoration:none;background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Pemesanan</div>
            <div style="font-size:24px;font-weight:800;color:var(--gray-900);">{{ $totalPemesanan }}</div>
            <div style="font-size:12px;margin-top:4px;">
                @if ($pemesananPending > 0)
                    <span style="color:var(--accent-500);font-weight:700;">{{ $pemesananPending }} menunggu ⚡</span>
                @else
                    <span style="color:var(--success);">Semua ditangani ✅</span>
                @endif
            </div>
        </a>
        <a href="{{ route('admin.mobil.index') }}"
           style="text-decoration:none;background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Armada</div>
            <div style="font-size:24px;font-weight:800;color:var(--gray-900);">{{ $totalMobil }} unit</div>
            <div style="font-size:12px;color:var(--gray-500);margin-top:4px;">{{ $mobilTersedia }} tersedia · <span style="color:var(--danger);">{{ $mobilDisewa }} disewa</span></div>
        </a>
        <a href="{{ route('admin.user.index') }}"
           style="text-decoration:none;background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:20px;">
            <div style="font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Pelanggan</div>
            <div style="font-size:24px;font-weight:800;color:var(--gray-900);">{{ $totalPelanggan }}</div>
            <div style="font-size:12px;color:var(--gray-500);margin-top:4px;">Pengguna terdaftar</div>
        </a>
    </div>

    {{-- Konfirmasi Pemesanan --}}
    @php
        $pemesananMenunggu = \App\Models\Pemesanan::with(['user','mobil'])
            ->where('status','pending')->latest()->take(5)->get();
    @endphp
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:15px;font-weight:700;">Konfirmasi Pemesanan</span>
            @if ($pemesananPending > 0)
                <span style="background:#fff7ed;color:var(--accent-500);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">{{ $pemesananPending }} baru</span>
            @endif
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
            @forelse ($pemesananMenunggu as $p)
                @php $durasi = $p->tanggal_mulai->diffInDays($p->tanggal_selesai); @endphp
                <div class="booking-item">
                    <div class="booking-item-header">
                        <div>
                            <div class="booking-item-name">{{ $p->user->name }}</div>
                            <div class="booking-item-code">{{ $p->mobil->nama }} · {{ $p->tanggal_mulai->format('d M') }} – {{ $p->tanggal_selesai->format('d M') }} · {{ $durasi }} hari</div>
                        </div>
                        <span class="booking-status status-pending">Menunggu</span>
                    </div>
                    <div class="booking-item-body">
                        <span>📞 {{ $p->user->no_hp ?? '-' }}</span>
                        <strong style="color:var(--brand-400);">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                    </div>
                    <div class="booking-item-footer">
                        <form action="{{ route('admin.pemesanan.konfirmasi', $p) }}" method="POST" style="flex:1;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-confirm" onclick="return confirm('Konfirmasi pemesanan {{ addslashes($p->user->name) }}?')">✅ Konfirmasi</button>
                        </form>
                        <form action="{{ route('admin.pemesanan.tolak', $p) }}" method="POST" style="flex:1;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-reject" onclick="return confirm('Tolak pemesanan ini?')">❌ Tolak</button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:28px;color:var(--gray-500);">
                    <div style="font-size:32px;margin-bottom:8px;">✅</div>
                    <div style="font-weight:600;font-size:13px;">Tidak ada pemesanan pending</div>
                </div>
            @endforelse
            @if ($pemesananPending > 5)
                <a href="{{ route('admin.pemesanan.index') }}"
                   style="text-align:center;font-size:13px;color:var(--brand-400);font-weight:600;text-decoration:none;padding:8px;display:block;">
                    Lihat semua {{ $pemesananPending }} pemesanan →
                </a>
            @endif
        </div>
    </div>

    {{-- Sedang Berjalan --}}
    @php
        $sedangBerjalan = \App\Models\Pemesanan::with(['user','mobil'])
            ->where('status','dikonfirmasi')->latest()->take(3)->get();
    @endphp
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:15px;font-weight:700;">Sedang Berjalan</span>
            <span style="font-size:12px;color:var(--gray-500);">{{ $pemesananBerjalan }} aktif</span>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
            @forelse ($sedangBerjalan as $p)
                <div class="booking-item">
                    <div class="booking-item-header">
                        <div>
                            <div class="booking-item-name">{{ $p->user->name }}</div>
                            <div class="booking-item-code">{{ $p->mobil->nama }} · s/d {{ $p->tanggal_selesai->format('d M Y') }}</div>
                        </div>
                        <span class="booking-status status-progress">Berjalan</span>
                    </div>
                    <div class="booking-item-body">
                        <span>🚗 {{ $p->mobil->plat_nomor }}</span>
                        <strong style="color:var(--brand-400);">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
                    </div>
                    <div class="booking-item-footer">
                        <form action="{{ route('admin.pemesanan.selesai', $p) }}" method="POST" style="flex:1;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-confirm" onclick="return confirm('Tandai pemesanan ini selesai?')">🏁 Tandai Selesai</button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:20px;color:var(--gray-500);font-size:13px;">Tidak ada pemesanan berjalan</div>
            @endforelse
        </div>
    </div>

    {{-- Ringkasan Bulan Ini --}}
    <div class="card">
        <div class="card-header">
            <span style="font-size:15px;font-weight:700;">Ringkasan Bulan Ini</span>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:var(--gray-500);">Pemesanan Selesai</span>
                <strong>{{ \App\Models\Pemesanan::where('status','selesai')->whereMonth('updated_at',now()->month)->count() }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:var(--gray-500);">Pemesanan Dibatalkan</span>
                <strong>{{ \App\Models\Pemesanan::where('status','dibatalkan')->whereMonth('updated_at',now()->month)->count() }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:var(--gray-500);">Pelanggan Baru</span>
                <strong>{{ \App\Models\User::where('role','pelanggan')->whereMonth('created_at',now()->month)->count() }}</strong>
            </div>
            <hr style="border:none;border-top:1px solid var(--gray-100);">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;font-weight:700;">Total Pendapatan</span>
                <strong style="color:var(--success);font-size:15px;">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

</div>
@endsection
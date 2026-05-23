<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mobil — Admin</title>
    @vite(['resources/css/admin.css'])
</head>
<body>

@include('admin.partials.sidebar')

<div class="admin-main">

    {{-- Header --}}
    <div class="admin-header">
        <div>
            <h1 class="admin-title">Kelola Mobil</h1>
            <p class="admin-subtitle">Total {{ $mobils->total() }} unit terdaftar</p>
        </div>
        <a href="{{ route('admin.mobil.create') }}" class="btn-primary">
            + Tambah Mobil
        </a>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Tabel --}}
    <div class="card">
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama & Merek</th>
                        <th>Plat Nomor</th>
                        <th>Harga/Hari</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mobils as $i => $mobil)
                        <tr>
                            <td>{{ $mobils->firstItem() + $i }}</td>
                            <td>
                                @if ($mobil->foto)
                                    <img src="{{ asset('storage/' . $mobil->foto) }}"
                                         alt="{{ $mobil->nama }}"
                                         class="table-foto">
                                @else
                                    <div class="table-foto-placeholder">🚗</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-600">{{ $mobil->nama }}</div>
                                <div class="text-sm text-gray">{{ $mobil->merek }} · {{ $mobil->tahun }}</div>
                            </td>
                            <td>
                                <span class="badge-plat">{{ $mobil->plat_nomor }}</span>
                            </td>
                            <td>
                                <div class="fw-600">Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <form action="{{ route('admin.mobil.toggle', $mobil) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="badge-status {{ $mobil->status === 'tersedia' ? 'status-tersedia' : 'status-disewa' }}">
                                        {{ $mobil->status === 'tersedia' ? '✅ Tersedia' : '🔴 Disewa' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('admin.mobil.edit', $mobil) }}" class="btn-edit">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('admin.mobil.destroy', $mobil) }}" method="POST"
                                          onsubmit="return confirm('Hapus {{ $mobil->nama }}? Data tidak bisa dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">🗑 Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:48px;color:#6b7280;">
                                <div style="font-size:48px;margin-bottom:12px;">🚗</div>
                                <div style="font-weight:600;">Belum ada data mobil</div>
                                <a href="{{ route('admin.mobil.create') }}" class="btn-primary" style="margin-top:16px;display:inline-block;">
                                    + Tambah Mobil Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($mobils->hasPages())
            <div class="pagination-wrap">
                {{ $mobils->links() }}
            </div>
        @endif
    </div>

</div>

</body>
</html>
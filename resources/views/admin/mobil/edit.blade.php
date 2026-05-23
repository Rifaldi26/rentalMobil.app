<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($mobil) ? 'Edit' : 'Tambah' }} Mobil — Admin</title>
    @vite(['resources/css/admin.css'])
</head>
<body>

@include('admin.partials.sidebar')

<div class="admin-main">

    {{-- Header --}}
    <div class="admin-header">
        <div>
            <h1 class="admin-title">{{ isset($mobil) ? 'Edit Mobil' : 'Tambah Mobil' }}</h1>
            <p class="admin-subtitle">
                {{ isset($mobil) ? 'Perbarui data ' . $mobil->nama : 'Tambahkan unit mobil baru' }}
            </p>
        </div>
        <a href="{{ route('admin.mobil.index') }}" class="btn-secondary">← Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form
                method="POST"
                action="{{ isset($mobil) ? route('admin.mobil.update', $mobil) : route('admin.mobil.store') }}"
                enctype="multipart/form-data"
            >
                @csrf
                @if (isset($mobil))
                    @method('PUT')
                @endif

                <div class="form-grid">

                    {{-- Nama --}}
                    <div class="form-group">
                        <label class="form-label">Nama Mobil <span class="req">*</span></label>
                        <input
                            type="text"
                            name="nama"
                            class="form-input @error('nama') error @enderror"
                            placeholder="cth: Toyota Innova Reborn"
                            value="{{ old('nama', $mobil->nama ?? '') }}"
                            required
                        >
                        @error('nama')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Merek --}}
                    <div class="form-group">
                        <label class="form-label">Merek <span class="req">*</span></label>
                        <select name="merek" class="form-input @error('merek') error @enderror" required>
                            <option value="">-- Pilih Merek --</option>
                            @foreach (['Toyota','Honda','Suzuki','Mitsubishi','Daihatsu','Nissan','Hyundai','Wuling','BYD','BMW','Mercedes-Benz','Lainnya'] as $m)
                                <option value="{{ $m }}"
                                    {{ old('merek', $mobil->merek ?? '') === $m ? 'selected' : '' }}>
                                    {{ $m }}
                                </option>
                            @endforeach
                        </select>
                        @error('merek')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tahun --}}
                    <div class="form-group">
                        <label class="form-label">Tahun <span class="req">*</span></label>
                        <input
                            type="number"
                            name="tahun"
                            class="form-input @error('tahun') error @enderror"
                            placeholder="{{ date('Y') }}"
                            value="{{ old('tahun', $mobil->tahun ?? '') }}"
                            min="2000"
                            max="{{ date('Y') }}"
                            required
                        >
                        @error('tahun')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Plat Nomor --}}
                    <div class="form-group">
                        <label class="form-label">Plat Nomor <span class="req">*</span></label>
                        <input
                            type="text"
                            name="plat_nomor"
                            class="form-input @error('plat_nomor') error @enderror"
                            placeholder="cth: B 1234 ABC"
                            value="{{ old('plat_nomor', $mobil->plat_nomor ?? '') }}"
                            style="text-transform:uppercase;"
                            required
                        >
                        @error('plat_nomor')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Harga per hari --}}
                    <div class="form-group">
                        <label class="form-label">Harga per Hari (Rp) <span class="req">*</span></label>
                        <input
                            type="number"
                            name="harga_per_hari"
                            class="form-input @error('harga_per_hari') error @enderror"
                            placeholder="cth: 350000"
                            value="{{ old('harga_per_hari', $mobil->harga_per_hari ?? '') }}"
                            min="50000"
                            step="1000"
                            required
                        >
                        @error('harga_per_hari')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label">Status <span class="req">*</span></label>
                        <select name="status" class="form-input @error('status') error @enderror" required>
                            <option value="tersedia" {{ old('status', $mobil->status ?? 'tersedia') === 'tersedia' ? 'selected' : '' }}>
                                ✅ Tersedia
                            </option>
                            <option value="disewa" {{ old('status', $mobil->status ?? '') === 'disewa' ? 'selected' : '' }}>
                                🔴 Sedang Disewa
                            </option>
                        </select>
                        @error('status')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                </div>{{-- /form-grid --}}

                {{-- Deskripsi --}}
                <div class="form-group" style="margin-top:4px;">
                    <label class="form-label">Deskripsi</label>
                    <textarea
                        name="deskripsi"
                        class="form-input @error('deskripsi') error @enderror"
                        rows="3"
                        placeholder="Fitur, kondisi, dan keterangan tambahan..."
                    >{{ old('deskripsi', $mobil->deskripsi ?? '') }}</textarea>
                    @error('deskripsi')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Upload Foto --}}
                <div class="form-group">
                    <label class="form-label">Foto Mobil</label>

                    {{-- Preview foto lama (edit mode) --}}
                    @if (isset($mobil) && $mobil->foto)
                        <div style="margin-bottom:10px;">
                            <img src="{{ asset('storage/' . $mobil->foto) }}"
                                 alt="Foto saat ini"
                                 style="height:120px;border-radius:8px;object-fit:cover;border:1px solid #e5e7eb;">
                            <div style="font-size:12px;color:#6b7280;margin-top:4px;">Foto saat ini — upload baru untuk mengganti</div>
                        </div>
                    @endif

                    <div class="upload-area" id="upload-area" onclick="document.getElementById('foto-input').click()">
                        <div class="upload-icon">📷</div>
                        <div class="upload-text">Klik untuk upload foto</div>
                        <div class="upload-hint">JPG, PNG, WEBP · Maks. 2MB</div>
                        <img id="foto-preview" style="display:none;max-height:160px;border-radius:8px;margin-top:12px;object-fit:cover;">
                    </div>
                    <input
                        type="file"
                        name="foto"
                        id="foto-input"
                        accept="image/*"
                        style="display:none;"
                        onchange="previewFoto(this)"
                    >
                    @error('foto')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn-primary">
                        {{ isset($mobil) ? '💾 Simpan Perubahan' : '➕ Tambah Mobil' }}
                    </button>
                    <a href="{{ route('admin.mobil.index') }}" class="btn-secondary">Batal</a>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('foto-preview');
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.querySelector('.upload-text').textContent = input.files[0].name;
            document.querySelector('.upload-hint').textContent =
                (input.files[0].size / 1024).toFixed(0) + ' KB';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>
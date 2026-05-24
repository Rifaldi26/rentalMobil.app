<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($mobil) ? 'Edit' : 'Tambah' }} Mobil — Admin</title>
    @vite(['resources/css/dashboard.css', 'resources/css/pemesanan.css'])
</head>
<body>

{{-- ═══ TOP NAV ═══════════════════════════════════════════ --}}
<nav class="nav">
    <a href="{{ route('admin.mobil.index') }}" style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);text-decoration:none;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </a>
    <div class="nav-brand" style="font-size:16px;">
        {{ isset($mobil) ? 'Edit Mobil' : 'Tambah Mobil' }}
    </div>
    <div style="width:36px;"></div>
</nav>

<div style="padding:16px 20px 120px;">

    {{-- Info mobil (edit mode) --}}
    @if (isset($mobil))
        <div style="display:flex;gap:12px;align-items:center;background:#fff;border:1px solid var(--gray-100);border-radius:var(--radius-md);padding:14px;margin-bottom:20px;">
            <div style="width:64px;height:52px;border-radius:var(--radius-sm);overflow:hidden;flex-shrink:0;background:var(--gray-100);display:flex;align-items:center;justify-content:center;">
                @if ($mobil->foto)
                    <img src="{{ asset('storage/'.$mobil->foto) }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:24px;">🚗</span>
                @endif
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;">{{ $mobil->nama }}</div>
                <div style="font-size:12px;color:var(--gray-500);">{{ $mobil->merek }} · {{ $mobil->tahun }} · {{ $mobil->plat_nomor }}</div>
                <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;
                    {{ $mobil->status === 'tersedia' ? 'background:#f0fdf4;color:#16a34a;' : 'background:#fef2f2;color:#dc2626;' }}">
                    {{ $mobil->status === 'tersedia' ? '✅ Tersedia' : '🔴 Disewa' }}
                </span>
            </div>
        </div>
    @endif

    <form
        method="POST"
        id="form-mobil"
        action="{{ isset($mobil) ? route('admin.mobil.update', $mobil) : route('admin.mobil.store') }}"
        enctype="multipart/form-data"
    >
        @csrf
        @if (isset($mobil)) @method('PUT') @endif

        {{-- ─── Data Mobil ─── --}}
        <div class="form-section">
            <div class="form-section-title">🚗 Data Mobil</div>

            {{-- Nama --}}
            <div class="form-group">
                <label class="form-label">Nama Mobil <span class="req">*</span></label>
                <input type="text" name="nama"
                    class="form-input @error('nama') error @enderror"
                    placeholder="cth: Toyota Innova Reborn"
                    value="{{ old('nama', $mobil->nama ?? '') }}"
                    required>
                @error('nama') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            {{-- Merek --}}
            <div class="form-group">
                <label class="form-label">Merek <span class="req">*</span></label>
                <select name="merek" class="form-input @error('merek') error @enderror" required>
                    <option value="">-- Pilih Merek --</option>
                    @foreach (['Toyota','Honda','Suzuki','Mitsubishi','Daihatsu','Nissan','Hyundai','Wuling','BYD','BMW','Mercedes-Benz','Lainnya'] as $m)
                        <option value="{{ $m }}" {{ old('merek', $mobil->merek ?? '') === $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
                @error('merek') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            {{-- Tahun & Plat Nomor --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Tahun <span class="req">*</span></label>
                    <input type="number" name="tahun"
                        class="form-input @error('tahun') error @enderror"
                        placeholder="{{ date('Y') }}"
                        value="{{ old('tahun', $mobil->tahun ?? '') }}"
                        min="2000" max="{{ date('Y') }}" required>
                    @error('tahun') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Plat Nomor <span class="req">*</span></label>
                    <input type="text" name="plat_nomor"
                        class="form-input @error('plat_nomor') error @enderror"
                        placeholder="B 1234 ABC"
                        value="{{ old('plat_nomor', $mobil->plat_nomor ?? '') }}"
                        style="text-transform:uppercase;" required>
                    @error('plat_nomor') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

        </div>

        {{-- ─── Harga & Status ─── --}}
        <div class="form-section">
            <div class="form-section-title">💰 Harga & Status</div>

            <div class="form-group">
                <label class="form-label">Harga per Hari (Rp) <span class="req">*</span></label>
                <input type="number" name="harga_per_hari"
                    class="form-input @error('harga_per_hari') error @enderror"
                    placeholder="cth: 350000"
                    value="{{ old('harga_per_hari', $mobil->harga_per_hari ?? '') }}"
                    min="50000" step="1000" required
                    oninput="previewHarga(this.value)">
                <div id="preview-harga" style="font-size:12px;color:var(--brand-400);font-weight:700;margin-top:4px;"></div>
                @error('harga_per_hari') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status <span class="req">*</span></label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <label style="border:1.5px solid;border-radius:var(--radius-md);padding:12px;cursor:pointer;text-align:center;transition:all .15s;
                        {{ old('status', $mobil->status ?? 'tersedia') === 'tersedia' ? 'border-color:#16a34a;background:#f0fdf4;' : 'border-color:var(--gray-200);background:#fff;' }}"
                        onclick="selectStatus('tersedia', this)">
                        <input type="radio" name="status" value="tersedia" style="display:none;"
                            {{ old('status', $mobil->status ?? 'tersedia') === 'tersedia' ? 'checked' : '' }}>
                        <div style="font-size:20px;">✅</div>
                        <div style="font-size:13px;font-weight:700;margin-top:4px;
                            {{ old('status', $mobil->status ?? 'tersedia') === 'tersedia' ? 'color:#16a34a;' : 'color:var(--gray-700);' }}">
                            Tersedia
                        </div>
                    </label>
                    <label style="border:1.5px solid;border-radius:var(--radius-md);padding:12px;cursor:pointer;text-align:center;transition:all .15s;
                        {{ old('status', $mobil->status ?? '') === 'disewa' ? 'border-color:#dc2626;background:#fef2f2;' : 'border-color:var(--gray-200);background:#fff;' }}"
                        onclick="selectStatus('disewa', this)">
                        <input type="radio" name="status" value="disewa" style="display:none;"
                            {{ old('status', $mobil->status ?? '') === 'disewa' ? 'checked' : '' }}>
                        <div style="font-size:20px;">🔴</div>
                        <div style="font-size:13px;font-weight:700;margin-top:4px;
                            {{ old('status', $mobil->status ?? '') === 'disewa' ? 'color:#dc2626;' : 'color:var(--gray-700);' }}">
                            Sedang Disewa
                        </div>
                    </label>
                </div>
                @error('status') <div class="field-error">{{ $message }}</div> @enderror
            </div>

        </div>

        {{-- ─── Deskripsi ─── --}}
        <div class="form-section">
            <div class="form-section-title">📝 Deskripsi</div>
            <div class="form-group" style="margin-bottom:0;">
                <textarea name="deskripsi"
                    class="form-input @error('deskripsi') error @enderror"
                    rows="3"
                    placeholder="Fitur, kondisi, kapasitas, dan keterangan tambahan...">{{ old('deskripsi', $mobil->deskripsi ?? '') }}</textarea>
                @error('deskripsi') <div class="field-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- ─── Foto ─── --}}
        <div class="form-section">
            <div class="form-section-title">📷 Foto Mobil</div>

            {{-- Preview foto lama --}}
            @if (isset($mobil) && $mobil->foto)
                <div style="margin-bottom:12px;">
                    <img src="{{ asset('storage/'.$mobil->foto) }}"
                         style="width:100%;max-height:180px;object-fit:cover;border-radius:var(--radius-md);border:1px solid var(--gray-100);">
                    <div style="font-size:12px;color:var(--gray-500);margin-top:4px;text-align:center;">
                        Foto saat ini · Upload baru untuk mengganti
                    </div>
                </div>
            @endif

            <div style="border:2px dashed var(--gray-300);border-radius:var(--radius-md);padding:28px;text-align:center;cursor:pointer;background:var(--gray-50);"
                 id="upload-area" onclick="document.getElementById('foto-input').click()">
                <div style="font-size:32px;margin-bottom:6px;">📷</div>
                <div style="font-size:14px;font-weight:600;color:var(--gray-700);" id="upload-text">
                    Klik untuk upload foto
                </div>
                <div style="font-size:12px;color:var(--gray-500);margin-top:2px;" id="upload-hint">
                    JPG, PNG, WEBP · Maks. 2MB
                </div>
                <img id="foto-preview" style="display:none;max-height:160px;border-radius:var(--radius-sm);margin-top:12px;object-fit:cover;width:100%;">
            </div>
            <input type="file" name="foto" id="foto-input" accept="image/*" style="display:none;" onchange="previewFoto(this)">
            @error('foto') <div class="field-error">{{ $message }}</div> @enderror
        </div>

    </form>

</div>

{{-- ═══ STICKY BOTTOM ══════════════════════════════════════ --}}
<div style="position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid var(--gray-100);padding:14px 20px;display:flex;gap:10px;z-index:50;box-shadow:0 -4px 16px rgba(0,0,0,.08);">
    <a href="{{ route('admin.mobil.index') }}"
       style="flex:1;padding:14px;background:var(--gray-100);color:var(--gray-700);border:none;border-radius:var(--radius-md);font-size:14px;font-weight:700;text-align:center;text-decoration:none;">
        Batal
    </a>
    <button type="submit" form="form-mobil"
        style="flex:2;padding:14px;background:var(--brand-400);color:#fff;border:none;border-radius:var(--radius-md);font-size:15px;font-weight:700;cursor:pointer;">
        {{ isset($mobil) ? '💾 Simpan Perubahan' : '➕ Tambah Mobil' }}
    </button>
</div>

<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('foto-preview');
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('upload-text').textContent = input.files[0].name;
            document.getElementById('upload-hint').textContent =
                (input.files[0].size / 1024).toFixed(0) + ' KB';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewHarga(val) {
    const el = document.getElementById('preview-harga');
    if (val && !isNaN(val)) {
        el.textContent = '→ Rp ' + new Intl.NumberFormat('id-ID').format(val) + ' per hari';
    } else {
        el.textContent = '';
    }
}

function selectStatus(val, el) {
    // Reset semua
    document.querySelectorAll('[name="status"]').forEach(r => {
        const lbl = r.closest('label');
        lbl.style.borderColor = 'var(--gray-200)';
        lbl.style.background  = '#fff';
        lbl.querySelector('div:last-child').style.color = 'var(--gray-700)';
        r.checked = false;
    });
    // Set yang dipilih
    const radio = el.querySelector('input[type="radio"]');
    radio.checked = true;
    if (val === 'tersedia') {
        el.style.borderColor = '#16a34a';
        el.style.background  = '#f0fdf4';
        el.querySelector('div:last-child').style.color = '#16a34a';
    } else {
        el.style.borderColor = '#dc2626';
        el.style.background  = '#fef2f2';
        el.querySelector('div:last-child').style.color = '#dc2626';
    }
}

// Jalankan preview harga saat load (edit mode)
@if (isset($mobil) && $mobil->harga_per_hari)
    previewHarga({{ $mobil->harga_per_hari }});
@endif
</script>

</body>
</html>
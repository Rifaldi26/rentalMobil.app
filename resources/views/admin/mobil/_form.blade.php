{{--
    Partial: _form.blade.php
    Dipakai oleh: create.blade.php & edit.blade.php
    Variabel: $mobil (opsional, hanya ada saat edit)
--}}

{{-- Info Mobil — hanya tampil saat mode edit --}}
@isset($mobil)
    <div class="mobil-preview-card mb-20">
        <div class="mobil-preview-card__thumb">
            @if ($mobil->foto)
                <img src="{{ asset('storage/'.$mobil->foto) }}"
                     class="mobil-preview-card__img" alt="{{ $mobil->nama }}">
            @else
                <div class="mobil-preview-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                        <path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1l2-3h10l2 3h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/>
                        <circle cx="7.5" cy="17" r="2.5"/><circle cx="16.5" cy="17" r="2.5"/>
                    </svg>
                </div>
            @endif
        </div>
        <div class="mobil-preview-card__info">
            <div class="mobil-preview-card__nama">{{ $mobil->nama }}</div>
            <div class="mobil-preview-card__meta">
                {{ $mobil->merek }} · {{ $mobil->tahun }} · {{ $mobil->plat_nomor }}
            </div>
            <span class="badge badge--{{ $mobil->status === 'tersedia' ? 'success' : 'danger' }}">
                {{ $mobil->status === 'tersedia' ? ' Tersedia' : ' Disewa' }}
            </span>
        </div>
    </div>
@endisset

<form method="POST"
      id="form-mobil"
      action="{{ isset($mobil) ? route('admin.mobil.update', $mobil) : route('admin.mobil.store') }}"
      enctype="multipart/form-data">
    @csrf
    @isset($mobil) @method('PUT') @endisset

    {{-- ─── Data Mobil ─── --}}
    <div class="form-section">
        <div class="form-section-title"> Data Mobil</div>

        <div class="form-group">
            <label class="form-label">Nama Mobil <span class="req">*</span></label>
            <input type="text" name="nama"
                   class="form-input @error('nama') error @enderror"
                   placeholder="cth: Toyota Innova Reborn"
                   value="{{ old('nama', $mobil->nama ?? '') }}"
                   required>
            @error('nama') <div class="field-error">{{ $message }}</div> @enderror
        </div>

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
            @error('merek') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
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
                       class="form-input form-input--uppercase @error('plat_nomor') error @enderror"
                       placeholder="B 1234 ABC"
                       value="{{ old('plat_nomor', $mobil->plat_nomor ?? '') }}"
                       required>
                @error('plat_nomor') <div class="field-error">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- ─── Harga & Status ─── --}}
    <div class="form-section">
        <div class="form-section-title"> Harga & Status</div>

        <div class="form-group">
            <label class="form-label">Harga per Hari (Rp) <span class="req">*</span></label>
            <input type="number" name="harga_per_hari"
                   id="input-harga"
                   class="form-input @error('harga_per_hari') error @enderror"
                   placeholder="cth: 350000"
                   value="{{ old('harga_per_hari', $mobil->harga_per_hari ?? '') }}"
                   min="50000" step="1000" required>
            <div id="preview-harga" class="form-hint form-hint--brand"></div>
            @error('harga_per_hari') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Status <span class="req">*</span></label>
            <div class="form-row">
                @php $currentStatus = old('status', $mobil->status ?? 'tersedia'); @endphp

                <label class="status-option {{ $currentStatus === 'tersedia' ? 'status-option--active-success' : '' }}"
                    data-status="tersedia">
                    <input type="radio" name="status" value="tersedia" class="sr-only"
                        {{ $currentStatus === 'tersedia' ? 'checked' : '' }}>
                    <div class="status-option__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div class="status-option__label">Tersedia</div>
                </label>

                <label class="status-option {{ $currentStatus === 'disewa' ? 'status-option--active-danger' : '' }}"
                    data-status="disewa">
                    <input type="radio" name="status" value="disewa" class="sr-only"
                        {{ $currentStatus === 'disewa' ? 'checked' : '' }}>
                    <div class="status-option__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                        </svg>
                    </div>
                    <div class="status-option__label">Sedang Disewa</div>
                </label>
            </div>
            @error('status') <div class="field-error">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- ─── Deskripsi ─── --}}
    <div class="form-section">
        <div class="form-section-title">Deskripsi</div>
        <div class="form-group mb-0">
            <textarea name="deskripsi"
                      class="form-input @error('deskripsi') error @enderror"
                      rows="3"
                      placeholder="Fitur, kondisi, kapasitas, dan keterangan tambahan...">{{ old('deskripsi', $mobil->deskripsi ?? '') }}</textarea>
            @error('deskripsi') <div class="field-error">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- ─── Foto ─── --}}
    <div class="form-section">
        <div class="form-section-title"> Foto Mobil</div>

        @isset($mobil)
            @if ($mobil->foto)
                <div class="foto-current mb-12">
                    <img src="{{ asset('storage/'.$mobil->foto) }}"
                         class="foto-current__img" alt="Foto saat ini">
                    <div class="foto-current__caption">
                        Foto saat ini · Upload baru untuk mengganti
                    </div>
                </div>
            @endif
        @endisset

        <div class="upload-area" id="upload-area">
            <div class="upload-area__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
            </div>
            <div class="upload-area__text" id="upload-text">Klik untuk upload foto</div>
            <div class="upload-area__hint" id="upload-hint">JPG, PNG, WEBP · Maks. 2MB</div>
            <img id="foto-preview" class="upload-area__preview is-hidden" alt="Preview">
        </div>

        <input type="file" name="foto" id="foto-input"
               accept="image/*" class="is-hidden">

        @error('foto') <div class="field-error">{{ $message }}</div> @enderror
    </div>

</form>

{{-- Action Bar --}}
<div class="form-action-bar">
    <a href="{{ route('admin.mobil.index') }}" class="btn btn-secondary btn-action-bar">
        Batal
    </a>
    <button type="submit" form="form-mobil" class="btn btn-primary btn-action-bar btn-action-bar--grow">
        {{ isset($mobil) ? ' Simpan Perubahan' : ' Tambah Mobil' }}
    </button>
</div>

@push('meta')
    <meta name="mobil-harga" content="{{ old('harga_per_hari', $mobil->harga_per_hari ?? '') }}">
@endpush
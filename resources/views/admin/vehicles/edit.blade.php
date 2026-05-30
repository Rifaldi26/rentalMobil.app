<x-app-layout>
    <x-slot:title>Edit Kendaraan — {{ $vehicle->brand }} {{ $vehicle->model }}</x-slot:title>

    @push('styles')
    <style>
    .form-section-title {
        font-family:'Sora',sans-serif;font-weight:800;font-size:.9rem;
        color:var(--gray-700);text-transform:uppercase;letter-spacing:.5px;
        padding-bottom:10px;border-bottom:2px solid var(--brand-100);
        margin-bottom:18px;display:flex;align-items:center;gap:8px;
    }
    .photo-preview-item {
        position:relative;border-radius:var(--radius-md);overflow:hidden;
        aspect-ratio:4/3;background:var(--gray-100);
    }
    .photo-preview-item img { width:100%;height:100%;object-fit:cover; }
    .photo-remove-btn {
        position:absolute;top:4px;right:4px;width:22px;height:22px;
        background:rgba(0,0,0,.6);color:#fff;border:none;border-radius:50%;
        cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;
    }
    .photo-primary-badge {
        position:absolute;bottom:4px;left:4px;background:var(--brand-600);color:#fff;
        font-size:.6rem;font-weight:700;padding:2px 6px;border-radius:3px;
    }
    .feature-tag-input {
        display:inline-flex;align-items:center;gap:6px;padding:4px 10px;
        background:var(--brand-50);color:var(--brand-700);border-radius:var(--radius-full);
        font-size:.8rem;font-weight:600;border:1px solid var(--brand-200);
    }
    .feature-tag-input button { background:none;border:none;cursor:pointer;color:var(--brand-400);padding:0;line-height:1; }
    </style>
    @endpush

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;margin-bottom:4px;">Edit Kendaraan</h1>
            <p class="text-sm text-muted">{{ $vehicle->brand }} {{ $vehicle->model }} · {{ $vehicle->license_plate }}</p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('public.cars.show', $vehicle) }}" target="_blank" class="btn btn-ghost btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Lihat Publik
            </a>
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:20px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
        <div>
            <strong>Mohon perbaiki kesalahan berikut:</strong>
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;" class="vehicle-form-layout">

            {{-- ── Left Column ──────────────────────────── --}}
            <div>

                {{-- Informasi Dasar --}}
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="13" height="8" x="8" y="13" rx="2"/></svg>
                        Informasi Dasar
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Merk <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="brand" class="form-input @error('brand') is-invalid @enderror"
                                   value="{{ old('brand', $vehicle->brand) }}" required>
                            @error('brand')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Model <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="model" class="form-input @error('model') is-invalid @enderror"
                                   value="{{ old('model', $vehicle->model) }}" required>
                            @error('model')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tahun <span style="color:var(--danger);">*</span></label>
                            <input type="number" name="year" class="form-input @error('year') is-invalid @enderror"
                                   value="{{ old('year', $vehicle->year) }}" min="2000" max="{{ now()->year + 1 }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor Polisi <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="license_plate" class="form-input @error('license_plate') is-invalid @enderror"
                                   value="{{ old('license_plate', $vehicle->license_plate) }}" style="text-transform:uppercase;" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kategori <span style="color:var(--danger);">*</span></label>
                            <select name="category" class="form-select" required>
                                @foreach(\App\Enums\VehicleCategory::cases() as $c)
                                <option value="{{ $c->value }}" {{ old('category', $vehicle->category->value) === $c->value ? 'selected' : '' }}>{{ $c->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Warna</label>
                            <input type="text" name="color" class="form-input"
                                   value="{{ old('color', $vehicle->color) }}">
                        </div>
                    </div>
                </div>

                {{-- Spesifikasi --}}
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/></svg>
                        Spesifikasi
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Transmisi <span style="color:var(--danger);">*</span></label>
                            <select name="transmission" class="form-select" required>
                                <option value="manual"   {{ old('transmission', $vehicle->transmission) === 'manual' ? 'selected' : '' }}>Manual</option>
                                <option value="otomatis" {{ old('transmission', $vehicle->transmission) === 'otomatis' ? 'selected' : '' }}>Otomatis (Matic)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kapasitas (orang) <span style="color:var(--danger);">*</span></label>
                            <select name="capacity" class="form-select" required>
                                @foreach([2,4,5,6,7,8,9,10,12,15] as $cap)
                                <option value="{{ $cap }}" {{ old('capacity', $vehicle->capacity) == $cap ? 'selected' : '' }}>{{ $cap }} orang</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bahan Bakar</label>
                            <select name="fuel_type" class="form-select">
                                @foreach(['bensin','solar','hybrid','listrik'] as $ft)
                                <option value="{{ $ft }}" {{ old('fuel_type', $vehicle->fuel_type) === $ft ? 'selected' : '' }}>{{ ucfirst($ft) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kapasitas Bagasi</label>
                            <input type="text" name="luggage_capacity" class="form-input"
                                   value="{{ old('luggage_capacity', $vehicle->luggage_capacity) }}">
                        </div>
                    </div>
                </div>

                {{-- Fitur --}}
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/></svg>
                        Fitur & Fasilitas
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;min-height:36px;" id="features-container">
                        @foreach(old('features', $vehicle->features ?? []) as $f)
                        <div class="feature-tag-input">
                            <span>{{ $f }}</span>
                            <button type="button" onclick="this.closest('.feature-tag-input').remove()">✕</button>
                            <input type="hidden" name="features[]" value="{{ $f }}">
                        </div>
                        @endforeach
                    </div>
                    <div style="display:flex;gap:8px;">
                        <input type="text" id="feature-input" class="form-input"
                               placeholder="Ketik fitur lalu Enter..."
                               style="flex:1;" onkeydown="if(event.key==='Enter'){event.preventDefault();addFeature();}">
                        <button type="button" onclick="addFeature()" class="btn btn-secondary">Tambah</button>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;">
                        @foreach(['AC','GPS','Bluetooth','USB Charger','WiFi','Kursi Bayi','Sunroof','Kamera Mundur'] as $s)
                        <button type="button" onclick="addFeatureValue('{{ $s }}')"
                                style="padding:3px 9px;border:1px solid var(--gray-200);border-radius:var(--radius-full);background:#fff;font-size:.75rem;cursor:pointer;">
                            + {{ $s }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/></svg>
                        Deskripsi
                    </div>
                    <textarea name="description" class="form-input" rows="4" style="resize:vertical;">{{ old('description', $vehicle->description) }}</textarea>
                </div>

                {{-- Foto Kendaraan --}}
                <div class="card" style="padding:24px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>
                        Foto Kendaraan
                    </div>

                    {{-- Existing Photos --}}
                    @if($vehicle->photos->count() > 0)
                    <div style="margin-bottom:16px;">
                        <div class="form-label-sm" style="margin-bottom:10px;">Foto Saat Ini</div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;">
                            @foreach($vehicle->photos as $photo)
                            <div class="photo-preview-item">
                                <img src="{{ $photo->url }}" alt="foto">
                                @if($photo->is_primary)
                                <div class="photo-primary-badge">UTAMA</div>
                                @endif
                                <form method="POST" action="{{ route('admin.vehicles.photos.destroy', [$vehicle, $photo]) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="photo-remove-btn"
                                            onclick="return confirm('Hapus foto ini?')">✕</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Add New Photos --}}
                    <div style="border:2px dashed var(--gray-300);border-radius:var(--radius-lg);padding:20px;text-align:center;cursor:pointer;transition:all .2s;"
                         onclick="document.getElementById('new-photos-input').click()"
                         onmouseover="this.style.borderColor='var(--brand-400)'" onmouseout="this.style.borderColor='var(--gray-300)'">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" style="margin:0 auto 8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <div style="font-size:.85rem;font-weight:600;color:var(--gray-600);">Tambah Foto Baru</div>
                        <div style="font-size:.75rem;color:var(--gray-400);margin-top:2px;">PNG, JPG · Maks 5MB</div>
                    </div>
                    <input type="file" id="new-photos-input" name="new_photos[]" multiple accept="image/*" style="display:none;"
                           onchange="previewNewPhotos(this)">
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-top:10px;" id="new-photos-preview"></div>
                </div>

            </div>

            {{-- ── Right Column ─────────────────────── --}}
            <div>
                <div style="position:sticky;top:calc(var(--topbar-height) + 16px);display:flex;flex-direction:column;gap:16px;">

                    {{-- Harga --}}
                    <div class="card" style="padding:20px;">
                        <div class="form-section-title" style="font-size:.85rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            Harga Sewa
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga / Hari (Rp) <span style="color:var(--danger);">*</span></label>
                            <input type="number" name="price_per_day" class="form-input"
                                   value="{{ old('price_per_day', $vehicle->price_per_day) }}" required>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-top:1px solid var(--gray-100);"
                             x-data="{ hasDriver: {{ $vehicle->has_driver ? 'true' : 'false' }} }">
                            <div>
                                <div style="font-weight:700;font-size:.875rem;">Tersedia Sopir</div>
                            </div>
                            <label style="position:relative;display:inline-block;width:42px;height:24px;cursor:pointer;">
                                <input type="checkbox" name="has_driver" value="1"
                                       {{ $vehicle->has_driver ? 'checked' : '' }}
                                       @change="hasDriver = $event.target.checked"
                                       style="opacity:0;width:0;height:0;">
                                <span style="position:absolute;inset:0;background:var(--gray-300);border-radius:24px;transition:.2s;"
                                      :style="hasDriver ? 'background:var(--brand-500)' : ''">
                                    <span style="position:absolute;top:3px;left:3px;width:18px;height:18px;background:#fff;border-radius:50%;transition:.2s;"
                                          :style="hasDriver ? 'transform:translateX(18px)' : ''"></span>
                                </span>
                            </label>
                        </div>
                        <div x-data="{ hasDriver: {{ $vehicle->has_driver ? 'true' : 'false' }} }" x-show="hasDriver" style="margin-top:8px;" x-cloak>
                            <label class="form-label">Harga Sopir / Hari (Rp)</label>
                            <input type="number" name="driver_price_per_day" class="form-input"
                                   value="{{ old('driver_price_per_day', $vehicle->driver_price_per_day) }}">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="card" style="padding:20px;">
                        <div class="form-section-title" style="font-size:.85rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                            Status & Publikasi
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status Kendaraan</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Enums\VehicleStatus::cases() as $s)
                                <option value="{{ $s->value }}" {{ old('status', $vehicle->status->value) === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-top:1px solid var(--gray-100);"
                             x-data="{ pub: {{ $vehicle->is_published ? 'true' : 'false' }} }">
                            <div>
                                <div style="font-weight:700;font-size:.875rem;">Dipublikasikan</div>
                            </div>
                            <label style="position:relative;display:inline-block;width:42px;height:24px;cursor:pointer;">
                                <input type="checkbox" name="is_published" value="1"
                                       {{ $vehicle->is_published ? 'checked' : '' }}
                                       @change="pub = $event.target.checked"
                                       style="opacity:0;width:0;height:0;">
                                <span style="position:absolute;inset:0;background:var(--gray-300);border-radius:24px;transition:.2s;"
                                      :style="pub ? 'background:var(--brand-500)' : ''">
                                    <span style="position:absolute;top:3px;left:3px;width:18px;height:18px;background:#fff;border-radius:50%;transition:.2s;"
                                          :style="pub ? 'transform:translateX(18px)' : ''"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Danger Zone --}}
                    <div class="card" style="padding:18px;border:1.5px solid var(--danger-border);">
                        <div style="font-weight:700;font-size:.85rem;color:var(--danger);margin-bottom:10px;">Zona Berbahaya</div>
                        <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}"
                              onsubmit="return confirm('Yakin hapus kendaraan ini? Tindakan tidak dapat dibatalkan!')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);width:100%;justify-content:center;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                Hapus Kendaraan
                            </button>
                        </form>
                    </div>

                    {{-- Save --}}
                    <button type="submit" class="btn btn-primary" style="justify-content:center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Simpan Perubahan
                    </button>

                </div>
            </div>

        </div>
    </form>

    @push('scripts')
    <script>
    function addFeature() {
        const input = document.getElementById('feature-input');
        const val = input.value.trim();
        if (!val) return;
        addFeatureValue(val);
        input.value = '';
    }
    function addFeatureValue(val) {
        const container = document.getElementById('features-container');
        const existing = Array.from(container.querySelectorAll('input[name="features[]"]')).map(i => i.value);
        if (existing.includes(val)) return;
        const tag = document.createElement('div');
        tag.className = 'feature-tag-input';
        tag.innerHTML = `<span>${val}</span><button type="button" onclick="this.closest('.feature-tag-input').remove()">✕</button><input type="hidden" name="features[]" value="${val}">`;
        container.appendChild(tag);
    }
    function previewNewPhotos(input) {
        const preview = document.getElementById('new-photos-preview');
        preview.innerHTML = '';
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const item = document.createElement('div');
                item.className = 'photo-preview-item';
                item.innerHTML = `<img src="${e.target.result}">`;
                preview.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }
    </script>
    @endpush

    <style>
    @media(max-width:900px) {
        .vehicle-form-layout { grid-template-columns: 1fr !important; }
        .vehicle-form-layout > div:last-child > div { position: static !important; }
    }
    </style>

</x-app-layout>

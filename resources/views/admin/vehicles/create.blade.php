<x-app-layout>
    <x-slot:title>Tambah Kendaraan</x-slot:title>

    @push('styles')
    <style>
    .form-section { margin-bottom:28px; }
    .form-section-title {
        font-family:'Sora',sans-serif;font-weight:800;font-size:.9rem;
        color:var(--gray-700);text-transform:uppercase;letter-spacing:.5px;
        padding-bottom:10px;border-bottom:2px solid var(--brand-100);
        margin-bottom:18px;display:flex;align-items:center;gap:8px;
    }
    .photo-upload-area {
        border:2px dashed var(--gray-300);border-radius:var(--radius-lg);
        padding:28px 20px;text-align:center;cursor:pointer;transition:all .2s;
    }
    .photo-upload-area:hover,.photo-upload-area.dragover {
        border-color:var(--brand-400);background:var(--brand-50);
    }
    .photo-preview-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-top:12px; }
    .photo-preview-item {
        position:relative;border-radius:var(--radius-md);overflow:hidden;
        aspect-ratio:4/3;background:var(--gray-100);
    }
    .photo-preview-item img { width:100%;height:100%;object-fit:cover; }
    .photo-remove-btn {
        position:absolute;top:4px;right:4px;width:22px;height:22px;
        background:rgba(0,0,0,.6);color:#fff;border:none;border-radius:50%;
        cursor:pointer;display:flex;align-items:center;justify-content:center;
        font-size:12px;line-height:1;
    }
    .photo-primary-badge {
        position:absolute;bottom:4px;left:4px;background:var(--brand-600);color:#fff;
        font-size:.6rem;font-weight:700;padding:2px 6px;border-radius:3px;letter-spacing:.3px;
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
            <h1 style="font-size:1.4rem;margin-bottom:4px;">Tambah Kendaraan Baru</h1>
            <p class="text-sm text-muted">Isi data lengkap kendaraan untuk dipublikasikan</p>
        </div>
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
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

    <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data" id="vehicle-form">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;" class="vehicle-form-layout">

            {{-- ── Left Column ──────────────────────────── --}}
            <div>

                {{-- Informasi Dasar --}}
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="13" height="8" x="8" y="13" rx="2"/><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Informasi Dasar
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Merk <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="brand" class="form-input @error('brand') is-invalid @enderror"
                                   value="{{ old('brand') }}" placeholder="Toyota, Honda, Suzuki..." required>
                            @error('brand')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Model <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="model" class="form-input @error('model') is-invalid @enderror"
                                   value="{{ old('model') }}" placeholder="Avanza, Brio, Ertiga..." required>
                            @error('model')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tahun <span style="color:var(--danger);">*</span></label>
                            <input type="number" name="year" class="form-input @error('year') is-invalid @enderror"
                                   value="{{ old('year', now()->year) }}" min="2000" max="{{ now()->year + 1 }}" required>
                            @error('year')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor Polisi <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="license_plate" class="form-input @error('license_plate') is-invalid @enderror"
                                   value="{{ old('license_plate') }}" placeholder="B 1234 ABC" style="text-transform:uppercase;" required>
                            @error('license_plate')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kategori <span style="color:var(--danger);">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                @foreach(\App\Enums\VehicleCategory::cases() as $c)
                                <option value="{{ $c->value }}" {{ old('category') === $c->value ? 'selected' : '' }}>{{ $c->label() }}</option>
                                @endforeach
                            </select>
                            @error('category')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Warna</label>
                            <input type="text" name="color" class="form-input @error('color') is-invalid @enderror"
                                   value="{{ old('color') }}" placeholder="Putih, Hitam, Silver...">
                        </div>
                    </div>
                </div>

                {{-- Spesifikasi --}}
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83"/></svg>
                        Spesifikasi
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Transmisi <span style="color:var(--danger);">*</span></label>
                            <select name="transmission" class="form-select @error('transmission') is-invalid @enderror" required>
                                <option value="">Pilih Transmisi</option>
                                <option value="manual"   {{ old('transmission') === 'manual' ? 'selected' : '' }}>Manual</option>
                                <option value="otomatis" {{ old('transmission') === 'otomatis' ? 'selected' : '' }}>Otomatis (Matic)</option>
                            </select>
                            @error('transmission')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kapasitas (orang) <span style="color:var(--danger);">*</span></label>
                            <select name="capacity" class="form-select @error('capacity') is-invalid @enderror" required>
                                @foreach([2,4,5,6,7,8,9,10,12,15] as $cap)
                                <option value="{{ $cap }}" {{ old('capacity') == $cap ? 'selected' : '' }}>{{ $cap }} orang</option>
                                @endforeach
                            </select>
                            @error('capacity')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bahan Bakar</label>
                            <select name="fuel_type" class="form-select">
                                <option value="bensin"  {{ old('fuel_type','bensin') === 'bensin'  ? 'selected' : '' }}>Bensin</option>
                                <option value="solar"   {{ old('fuel_type') === 'solar'   ? 'selected' : '' }}>Solar</option>
                                <option value="hybrid"  {{ old('fuel_type') === 'hybrid'  ? 'selected' : '' }}>Hybrid</option>
                                <option value="listrik" {{ old('fuel_type') === 'listrik' ? 'selected' : '' }}>Listrik</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kapasitas Bagasi</label>
                            <input type="text" name="luggage_capacity" class="form-input"
                                   value="{{ old('luggage_capacity') }}" placeholder="2 koper, 3 tas...">
                        </div>
                    </div>
                </div>

                {{-- Fitur & Fasilitas --}}
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Fitur & Fasilitas
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;min-height:36px;" id="features-container">
                        @foreach(old('features', []) as $f)
                        <div class="feature-tag-input">
                            <span>{{ $f }}</span>
                            <button type="button" onclick="this.closest('.feature-tag-input').remove();syncFeatures()">✕</button>
                            <input type="hidden" name="features[]" value="{{ $f }}">
                        </div>
                        @endforeach
                    </div>

                    <div style="display:flex;gap:8px;">
                        <input type="text" id="feature-input" class="form-input"
                               placeholder="Ketik fitur lalu Enter (AC, GPS, Bluetooth...)"
                               style="flex:1;" onkeydown="if(event.key==='Enter'){event.preventDefault();addFeature();}">
                        <button type="button" onclick="addFeature()" class="btn btn-secondary">Tambah</button>
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:12px;">
                        @foreach(['AC','GPS','Bluetooth','USB Charger','WiFi','Kursi Bayi','Sunroof','Kamera Mundur','Sensor Parkir','Entertainment System'] as $suggestion)
                        <button type="button" onclick="addFeatureValue('{{ $suggestion }}')"
                                style="padding:4px 10px;border:1px solid var(--gray-200);border-radius:var(--radius-full);background:#fff;font-size:.75rem;cursor:pointer;transition:all .15s;"
                                onmouseover="this.style.borderColor='var(--brand-400)';this.style.color='var(--brand-600)'"
                                onmouseout="this.style.borderColor='var(--gray-200)';this.style.color='inherit'">
                            + {{ $suggestion }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
                        Deskripsi
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <textarea name="description" class="form-input" rows="4"
                                  placeholder="Deskripsi kendaraan, kondisi, keunggulan, dll."
                                  style="resize:vertical;">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- Foto Kendaraan --}}
                <div class="card" style="padding:24px;">
                    <div class="form-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        Foto Kendaraan
                    </div>

                    <div class="photo-upload-area" id="photo-drop-zone"
                         onclick="document.getElementById('photos-input').click()"
                         ondragover="event.preventDefault();this.classList.add('dragover')"
                         ondragleave="this.classList.remove('dragover')"
                         ondrop="handlePhotoDrop(event)">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" style="margin:0 auto 10px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <div style="font-weight:600;color:var(--gray-600);margin-bottom:4px;">Klik atau seret foto ke sini</div>
                        <div style="font-size:.8rem;color:var(--gray-400);">PNG, JPG · Maks 5MB per foto · Foto pertama = foto utama</div>
                    </div>
                    <input type="file" id="photos-input" name="photos[]" multiple accept="image/*"
                           style="display:none;" onchange="previewPhotos(this)">

                    <div class="photo-preview-grid" id="photo-preview-grid"></div>
                </div>

            </div>

            {{-- ── Right Column (Sticky) ─────────────── --}}
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
                            <input type="number" name="price_per_day" class="form-input @error('price_per_day') is-invalid @enderror"
                                   value="{{ old('price_per_day') }}" placeholder="200000" min="0" step="10000" required>
                            @error('price_per_day')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        {{-- Sopir --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-top:1px solid var(--gray-100);margin-top:4px;" x-data="{ hasDriver: {{ old('has_driver') ? 'true' : 'false' }} }">
                            <div>
                                <div style="font-weight:700;font-size:.875rem;">Tersedia Sopir</div>
                                <div style="font-size:.75rem;color:var(--gray-400);">Pelanggan bisa pilih dengan/tanpa sopir</div>
                            </div>
                            <label style="position:relative;display:inline-block;width:42px;height:24px;cursor:pointer;">
                                <input type="checkbox" name="has_driver" value="1"
                                       {{ old('has_driver') ? 'checked' : '' }}
                                       @change="hasDriver = $event.target.checked"
                                       style="opacity:0;width:0;height:0;">
                                <span style="position:absolute;inset:0;background:var(--gray-300);border-radius:24px;transition:.2s;"
                                      :style="hasDriver ? 'background:var(--brand-500)' : ''">
                                    <span style="position:absolute;top:3px;left:3px;width:18px;height:18px;background:#fff;border-radius:50%;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.2);"
                                          :style="hasDriver ? 'transform:translateX(18px)' : ''"></span>
                                </span>
                            </label>
                        </div>

                        <div x-data="{ hasDriver: {{ old('has_driver') ? 'true' : 'false' }} }" x-show="hasDriver" style="margin-top:10px;" x-cloak>
                            <label class="form-label">Harga Sopir / Hari (Rp)</label>
                            <input type="number" name="driver_price_per_day" class="form-input"
                                   value="{{ old('driver_price_per_day', 150000) }}" placeholder="150000" min="0" step="10000">
                        </div>
                    </div>

                    {{-- Status & Publikasi --}}
                    <div class="card" style="padding:20px;">
                        <div class="form-section-title" style="font-size:.85rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Status & Publikasi
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status Kendaraan</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Enums\VehicleStatus::cases() as $s)
                                <option value="{{ $s->value }}" {{ old('status', 'tersedia') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-top:1px solid var(--gray-100);"
                             x-data="{ pub: {{ old('is_published', 1) ? 'true' : 'false' }} }">
                            <div>
                                <div style="font-weight:700;font-size:.875rem;">Publikasikan</div>
                                <div style="font-size:.75rem;color:var(--gray-400);">Tampilkan di halaman publik</div>
                            </div>
                            <label style="position:relative;display:inline-block;width:42px;height:24px;cursor:pointer;">
                                <input type="checkbox" name="is_published" value="1"
                                       {{ old('is_published', 1) ? 'checked' : '' }}
                                       @change="pub = $event.target.checked"
                                       style="opacity:0;width:0;height:0;">
                                <span style="position:absolute;inset:0;background:var(--gray-300);border-radius:24px;transition:.2s;"
                                      :style="pub ? 'background:var(--brand-500)' : ''">
                                    <span style="position:absolute;top:3px;left:3px;width:18px;height:18px;background:#fff;border-radius:50%;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.2);"
                                          :style="pub ? 'transform:translateX(18px)' : ''"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <button type="submit" name="action" value="publish" class="btn btn-primary" style="justify-content:center;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            Simpan & Publikasikan
                        </button>
                        <button type="submit" name="action" value="draft" class="btn btn-secondary" style="justify-content:center;">
                            Simpan sebagai Draft
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </form>

    @push('scripts')
    <script>
    // Feature tags
    function addFeature() {
        const input = document.getElementById('feature-input');
        const val = input.value.trim();
        if (!val) return;
        addFeatureValue(val);
        input.value = '';
    }

    function addFeatureValue(val) {
        const container = document.getElementById('features-container');
        // Prevent duplicates
        const existing = Array.from(container.querySelectorAll('input[name="features[]"]')).map(i => i.value);
        if (existing.includes(val)) return;

        const tag = document.createElement('div');
        tag.className = 'feature-tag-input';
        tag.innerHTML = `<span>${val}</span><button type="button" onclick="this.closest('.feature-tag-input').remove()">✕</button><input type="hidden" name="features[]" value="${val}">`;
        container.appendChild(tag);
    }

    // Photo preview
    let photoFiles = [];

    function previewPhotos(input) {
        Array.from(input.files).forEach(file => {
            if (photoFiles.length >= 8) return;
            photoFiles.push(file);
            addPhotoPreview(file, photoFiles.length - 1);
        });
    }

    function addPhotoPreview(file, idx) {
        const grid = document.getElementById('photo-preview-grid');
        const reader = new FileReader();
        reader.onload = e => {
            const item = document.createElement('div');
            item.className = 'photo-preview-item';
            item.id = 'photo-' + idx;
            item.innerHTML = `
                <img src="${e.target.result}" alt="foto ${idx+1}">
                ${idx === 0 ? '<div class="photo-primary-badge">UTAMA</div>' : ''}
                <button type="button" class="photo-remove-btn" onclick="removePhoto(${idx})">✕</button>
            `;
            grid.appendChild(item);
        };
        reader.readAsDataURL(file);
    }

    function removePhoto(idx) {
        const el = document.getElementById('photo-' + idx);
        if (el) el.remove();
        photoFiles[idx] = null;
        syncPhotoInput();
    }

    function syncPhotoInput() {
        const dt = new DataTransfer();
        photoFiles.filter(Boolean).forEach(f => dt.items.add(f));
        document.getElementById('photos-input').files = dt.files;
    }

    function handlePhotoDrop(e) {
        e.preventDefault();
        document.getElementById('photo-drop-zone').classList.remove('dragover');
        const dt = new DataTransfer();
        Array.from(e.dataTransfer.files).forEach(f => {
            if (f.type.startsWith('image/')) { dt.items.add(f); }
        });
        const input = document.getElementById('photos-input');
        input.files = dt.files;
        previewPhotos(input);
    }

    // Toggle driver price visibility
    document.querySelectorAll('[name="has_driver"]').forEach(cb => {
        cb.addEventListener('change', () => {
            const driverPriceGroup = document.querySelector('[name="driver_price_per_day"]')?.closest('.form-group');
            // Alpine handles this, but backup vanilla
        });
    });
    </script>
    @endpush

    <style>
    @media(max-width:900px) {
        .vehicle-form-layout { grid-template-columns: 1fr !important; }
        .vehicle-form-layout > div:last-child > div { position: static !important; }
    }
    </style>

</x-app-layout>

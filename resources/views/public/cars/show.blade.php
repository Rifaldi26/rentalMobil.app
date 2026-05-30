<x-guest-layout>
    <x-slot:title>{{ $vehicle->brand }} {{ $vehicle->model }} — RentWheels</x-slot:title>

    @push('styles')
    <style>
    .photo-thumbs { display:flex;gap:8px;overflow-x:auto;padding-bottom:4px;scrollbar-width:thin; }
    .photo-thumb {
        width:72px;height:52px;object-fit:cover;border-radius:var(--radius-sm);
        cursor:pointer;opacity:.6;transition:all .2s;flex-shrink:0;
        border:2px solid transparent;
    }
    .photo-thumb.active,.photo-thumb:hover { opacity:1;border-color:var(--brand-500); }
    .spec-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:12px; }
    .spec-item {
        display:flex;align-items:center;gap:10px;padding:12px 14px;
        background:var(--gray-50);border-radius:var(--radius-md);
    }
    .spec-icon {
        width:34px;height:34px;background:#fff;border-radius:var(--radius-sm);
        display:flex;align-items:center;justify-content:center;flex-shrink:0;
        box-shadow:var(--shadow-xs);
    }
    .feature-tag {
        display:inline-flex;align-items:center;gap:6px;padding:5px 12px;
        background:var(--brand-50);color:var(--brand-700);border-radius:var(--radius-full);
        font-size:.78rem;font-weight:600;border:1px solid var(--brand-100);
    }
    .review-card { padding:18px 20px;border-bottom:1px solid var(--gray-100); }
    .review-card:last-child { border-bottom:none; }
    .sticky-booking {
        position:sticky;top:calc(var(--navbar-height) + 16px);
    }
    @media(max-width:768px){
        .car-detail-grid{grid-template-columns:1fr!important;}
        .spec-grid{grid-template-columns:1fr 1fr;}
        .sticky-booking{position:static;}
    }
    </style>
    @endpush

    <div class="container" style="padding-top:32px;padding-bottom:64px;">

        {{-- Breadcrumb --}}
        <nav style="display:flex;align-items:center;gap:6px;font-size:.8rem;color:var(--gray-400);margin-bottom:20px;">
            <a href="{{ route('home') }}" style="color:var(--gray-400);">Beranda</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <a href="{{ route('cars.index') }}" style="color:var(--gray-400);">Kendaraan</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:var(--gray-700);font-weight:600;">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
        </nav>

        <div style="display:grid;grid-template-columns:1fr 360px;gap:32px;" class="car-detail-grid">

            {{-- ── Left Column ────────────────────────────────── --}}
            <div>

                {{-- Photo Gallery --}}
                <div class="card" style="overflow:hidden;margin-bottom:24px;">
                    <div style="position:relative;">
                        <img id="main-photo"
                             src="{{ $vehicle->primary_photo_url }}"
                             alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                             style="width:100%;height:340px;object-fit:cover;">

                        {{-- Badges --}}
                        <div style="position:absolute;top:14px;left:14px;display:flex;gap:6px;">
                            @if($vehicle->has_driver)
                            <span class="badge" style="background:rgba(20,184,166,.9);color:#fff;backdrop-filter:blur(4px);">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Tersedia Sopir
                            </span>
                            @endif
                            <span class="badge badge-{{ $vehicle->status }}" style="backdrop-filter:blur(4px);">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        </div>

                        @if($vehicle->photos->count() > 1)
                        <button onclick="cyclePhoto(-1)"
                                style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:36px;height:36px;background:rgba(255,255,255,.9);border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-md);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gray-700)" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <button onclick="cyclePhoto(1)"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:36px;height:36px;background:rgba(255,255,255,.9);border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-md);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gray-700)" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                        @endif
                    </div>

                    @if($vehicle->photos->count() > 1)
                    <div style="padding:12px 16px;">
                        <div class="photo-thumbs">
                            @foreach($vehicle->photos as $i => $photo)
                            <img src="{{ $photo->url }}"
                                 class="photo-thumb {{ $i === 0 ? 'active' : '' }}"
                                 onclick="setPhoto('{{ $photo->url }}', this)"
                                 alt="Foto {{ $i + 1 }}">
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Vehicle Info --}}
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
                        <div>
                            <h1 style="font-size:1.5rem;margin-bottom:6px;">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                <span style="font-size:.85rem;color:var(--gray-500);">{{ $vehicle->year }} · {{ $vehicle->license_plate }}</span>
                                @if($vehicle->reviews_avg_rating)
                                <div style="display:flex;align-items:center;gap:4px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="var(--amber-500)" stroke="var(--amber-500)" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span style="font-size:.85rem;font-weight:700;color:var(--gray-800);">{{ number_format($vehicle->reviews_avg_rating, 1) }}</span>
                                    <span style="font-size:.8rem;color:var(--gray-400);">({{ $vehicle->reviews_count }} ulasan)</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;color:var(--brand-600);">
                                Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}
                            </div>
                            <div style="font-size:.8rem;color:var(--gray-400);">per hari</div>
                        </div>
                    </div>

                    {{-- Specs --}}
                    <div class="spec-grid">
                        <div class="spec-item">
                            <div class="spec-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--brand-600)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div style="font-size:.72rem;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Kapasitas</div>
                                <div style="font-size:.9rem;font-weight:700;">{{ $vehicle->capacity }} orang</div>
                            </div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--brand-600)" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                            </div>
                            <div>
                                <div style="font-size:.72rem;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Transmisi</div>
                                <div style="font-size:.9rem;font-weight:700;">{{ ucfirst($vehicle->transmission) }}</div>
                            </div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--brand-600)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div>
                                <div style="font-size:.72rem;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Bahan Bakar</div>
                                <div style="font-size:.9rem;font-weight:700;">{{ ucfirst($vehicle->fuel_type ?? 'Bensin') }}</div>
                            </div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--brand-600)" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            </div>
                            <div>
                                <div style="font-size:.72rem;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Kategori</div>
                                <div style="font-size:.9rem;font-weight:700;">{{ $vehicle->category->label() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deskripsi --}}
                @if($vehicle->description)
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <h3 style="font-size:1rem;margin-bottom:12px;">Deskripsi Kendaraan</h3>
                    <p style="font-size:.9rem;color:var(--gray-600);line-height:1.75;">{{ $vehicle->description }}</p>
                </div>
                @endif

                {{-- Fitur & Fasilitas --}}
                @if($vehicle->features && count($vehicle->features) > 0)
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <h3 style="font-size:1rem;margin-bottom:14px;">Fitur & Fasilitas</h3>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach($vehicle->features as $feature)
                        <span class="feature-tag">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ $feature }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Ulasan --}}
                @if($vehicle->reviews->count() > 0)
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="font-size:1rem;margin:0;">Ulasan Pelanggan</h3>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="var(--amber-500)" stroke="var(--amber-500)" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem;">{{ number_format($vehicle->reviews_avg_rating, 1) }}</span>
                            <span style="font-size:.85rem;color:var(--gray-400);">dari {{ $vehicle->reviews_count }} ulasan</span>
                        </div>
                    </div>

                    @foreach($vehicle->reviews->take(5) as $review)
                    <div class="review-card">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}"
                                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                                <div>
                                    <div style="font-weight:700;font-size:.875rem;">{{ $review->user->name }}</div>
                                    <div style="font-size:.75rem;color:var(--gray-400);">{{ $review->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div style="display:flex;gap:2px;">
                                @for($i = 1; $i <= 5; $i++)
                                <svg width="12" height="12" viewBox="0 0 24 24"
                                     fill="{{ $i <= $review->rating ? 'var(--amber-500)' : 'var(--gray-200)' }}"
                                     stroke="{{ $i <= $review->rating ? 'var(--amber-500)' : 'var(--gray-200)' }}"
                                     stroke-width="1">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                        <p style="font-size:.875rem;color:var(--gray-600);margin:0;line-height:1.6;">{{ $review->comment }}</p>
                        @endif
                    </div>
                    @endforeach

                    @if($vehicle->reviews_count > 5)
                    <div style="text-align:center;padding-top:16px;">
                        <a href="#" style="font-size:.875rem;color:var(--brand-600);font-weight:600;">
                            Lihat semua {{ $vehicle->reviews_count }} ulasan
                        </a>
                    </div>
                    @endif
                </div>
                @endif

            </div>

            {{-- ── Right Column (Booking Form) ─────────────── --}}
            <div class="sticky-booking">
                <div class="card" style="padding:22px;">
                    <div style="margin-bottom:18px;">
                        <div style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;color:var(--brand-600);">
                            Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}
                        </div>
                        <div style="font-size:.8rem;color:var(--gray-400);">per hari · belum termasuk sopir</div>
                    </div>

                    @if($vehicle->status === 'tersedia')
                    <form action="{{ route('customer.bookings.store') }}" method="POST" id="booking-form">
                        @csrf
                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                        {{-- Tanggal Mulai --}}
                        <div class="form-group">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date"
                                   class="form-input"
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   value="{{ old('start_date') }}"
                                   required onchange="calcTotal()">
                        </div>

                        {{-- Tanggal Selesai --}}
                        <div class="form-group">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date"
                                   class="form-input"
                                   min="{{ now()->addDays(2)->format('Y-m-d') }}"
                                   value="{{ old('end_date') }}"
                                   required onchange="calcTotal()">
                        </div>

                        {{-- Sopir --}}
                        @if($vehicle->has_driver)
                        <div class="form-group">
                            <label class="form-label">Pilihan Sopir</label>
                            <select name="with_driver" class="form-select" onchange="calcTotal()">
                                <option value="0">Tanpa Sopir</option>
                                <option value="1">Dengan Sopir (+Rp {{ number_format($vehicle->driver_price_per_day ?? 150000, 0, ',', '.') }}/hari)</option>
                            </select>
                        </div>
                        @endif

                        {{-- Catatan --}}
                        <div class="form-group">
                            <label class="form-label">Catatan <span class="text-muted">(opsional)</span></label>
                            <textarea name="notes" class="form-input" rows="2"
                                      placeholder="Permintaan khusus, titik jemput, dll."
                                      style="resize:none;">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Summary --}}
                        <div id="booking-summary" style="display:none;background:var(--gray-50);border-radius:var(--radius-md);padding:14px;margin-bottom:16px;">
                            <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:6px;">
                                <span style="color:var(--gray-500);">Sewa kendaraan</span>
                                <span id="summary-base" class="fw-600">—</span>
                            </div>
                            <div id="summary-driver-row" style="display:none;flex;justify-content:space-between;font-size:.85rem;margin-bottom:6px;">
                                <span style="color:var(--gray-500);">Biaya sopir</span>
                                <span id="summary-driver" class="fw-600">—</span>
                            </div>
                            <div style="height:1px;background:var(--gray-200);margin:10px 0;"></div>
                            <div style="display:flex;justify-content:space-between;font-size:.95rem;">
                                <span class="fw-700">Total</span>
                                <span id="summary-total" style="font-family:'Sora',sans-serif;font-weight:800;color:var(--brand-600);font-size:1rem;">—</span>
                            </div>
                        </div>

                        @auth
                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Pesan Sekarang
                        </button>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary" style="width:100%;justify-content:center;display:flex;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            Masuk untuk Memesan
                        </a>
                        @endauth
                    </form>
                    @else
                    <div class="alert alert-warning">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                        Kendaraan ini sedang tidak tersedia untuk disewa
                    </div>
                    <a href="{{ route('cars.index') }}" class="btn btn-secondary" style="width:100%;justify-content:center;">
                        Lihat Kendaraan Lain
                    </a>
                    @endif
                </div>

                {{-- Info Card --}}
                <div class="card" style="padding:18px;margin-top:14px;">
                    <div style="font-weight:700;font-size:.875rem;margin-bottom:12px;">Info Penting</div>
                    @foreach([
                        ['icon'=>'<polyline points="20 6 9 17 4 12"/>','text'=>'Konfirmasi dalam 1–2 jam kerja'],
                        ['icon'=>'<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>','text'=>'Pembayaran via transfer bank / e-wallet'],
                        ['icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>','text'=>'Wajib KTP & SIM aktif saat pengambilan'],
                        ['icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>','text'=>'Chat langsung dengan admin tersedia 24/7'],
                    ] as $info)
                    <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--brand-500)" stroke-width="2" style="flex-shrink:0;margin-top:2px;">
                            {!! $info['icon'] !!}
                        </svg>
                        <span style="font-size:.8rem;color:var(--gray-500);">{{ $info['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    const pricePerDay = {{ $vehicle->price_per_day }};
    const driverPricePerDay = {{ $vehicle->driver_price_per_day ?? 150000 }};

    function calcTotal() {
        const start = document.getElementById('start_date')?.value;
        const end   = document.getElementById('end_date')?.value;
        if (!start || !end) return;

        const days = Math.ceil((new Date(end) - new Date(start)) / 86400000);
        if (days < 1) return;

        const withDriver = document.querySelector('[name="with_driver"]')?.value === '1';
        const base       = days * pricePerDay;
        const driver     = withDriver ? days * driverPricePerDay : 0;
        const total      = base + driver;

        const fmt = n => 'Rp ' + n.toLocaleString('id-ID');

        document.getElementById('booking-summary').style.display = 'block';
        document.getElementById('summary-base').textContent = fmt(base) + ' (' + days + ' hari)';
        document.getElementById('summary-total').textContent = fmt(total);

        const driverRow = document.getElementById('summary-driver-row');
        if (withDriver) {
            driverRow.style.display = 'flex';
            document.getElementById('summary-driver').textContent = fmt(driver);
        } else {
            driverRow.style.display = 'none';
        }

        // Update end date min
        const endInput = document.getElementById('end_date');
        const minEnd = new Date(start);
        minEnd.setDate(minEnd.getDate() + 1);
        endInput.min = minEnd.toISOString().split('T')[0];
    }

    function setPhoto(url, el) {
        document.getElementById('main-photo').src = url;
        document.querySelectorAll('.photo-thumb').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    const photos = {{ json_encode($vehicle->photos->pluck('url')->toArray()) }};
    let photoIdx = 0;

    function cyclePhoto(dir) {
        photoIdx = (photoIdx + dir + photos.length) % photos.length;
        const thumbs = document.querySelectorAll('.photo-thumb');
        document.getElementById('main-photo').src = photos[photoIdx];
        thumbs.forEach((t, i) => t.classList.toggle('active', i === photoIdx));
    }
    </script>
    @endpush

</x-guest-layout>

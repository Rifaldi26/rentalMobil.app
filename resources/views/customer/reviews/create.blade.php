<x-guest-layout>
    <x-slot:title>Beri Ulasan — {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</x-slot:title>

    @push('styles')
    <style>
    .star-btn {
        background:none;border:none;cursor:pointer;padding:4px;
        transition:transform .15s;
    }
    .star-btn:hover { transform:scale(1.15); }
    .star-btn svg { width:36px;height:36px;transition:fill .15s,stroke .15s; }
    .aspect-item {
        display:flex;align-items:center;justify-content:space-between;
        padding:14px 0;border-bottom:1px solid var(--gray-100);
    }
    .aspect-item:last-child { border-bottom:none; }
    .mini-stars { display:flex;gap:3px; }
    .mini-star-btn { background:none;border:none;cursor:pointer;padding:1px; }
    .mini-star-btn svg { width:22px;height:22px;transition:fill .1s; }
    </style>
    @endpush

    <div class="container container-sm" style="padding-top:36px;padding-bottom:64px;">

        {{-- Breadcrumb --}}
        <nav style="display:flex;align-items:center;gap:6px;font-size:.8rem;color:var(--gray-400);margin-bottom:24px;">
            <a href="{{ route('customer.bookings.index') }}" style="color:var(--gray-400);">Pemesanan Saya</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:var(--gray-700);">Beri Ulasan</span>
        </nav>

        {{-- Vehicle Card --}}
        <div class="card" style="padding:20px;margin-bottom:24px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <img src="{{ $booking->vehicle->primary_photo_url }}"
                 style="width:88px;height:64px;object-fit:cover;border-radius:var(--radius-md);flex-shrink:0;"
                 alt="{{ $booking->vehicle->brand }}">
            <div style="flex:1;min-width:0;">
                <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1rem;">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</div>
                <div style="font-size:.85rem;color:var(--gray-400);margin-bottom:2px;">{{ $booking->vehicle->year }} · {{ $booking->vehicle->license_plate }}</div>
                <div style="font-size:.8rem;color:var(--gray-500);">
                    Disewa: {{ $booking->start_date->format('d M') }} – {{ $booking->end_date->format('d M Y') }}
                    · {{ $booking->duration_days }} hari
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:.75rem;color:var(--gray-400);">Kode</div>
                <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:.9rem;">{{ $booking->booking_code }}</div>
            </div>
        </div>

        {{-- Review Form --}}
        <div class="card" style="padding:28px;">
            <h2 style="font-size:1.1rem;margin-bottom:4px;">Bagaimana Pengalaman Anda?</h2>
            <p class="text-sm text-muted" style="margin-bottom:24px;">Ulasan Anda membantu pelanggan lain membuat keputusan yang tepat</p>

            @if($errors->any())
            <div class="alert alert-danger">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            <form action="{{ route('customer.reviews.store', $booking) }}" method="POST">
                @csrf

                {{-- Overall Rating --}}
                <div style="text-align:center;margin-bottom:28px;">
                    <div style="font-weight:700;margin-bottom:12px;font-size:.95rem;">Rating Keseluruhan</div>
                    <div id="star-container" style="display:flex;justify-content:center;gap:4px;margin-bottom:10px;">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="star-btn" onclick="setRating({{ $i }})" onmouseover="hoverRating({{ $i }})" onmouseout="hoverRating(0)">
                            <svg viewBox="0 0 24 24" fill="var(--gray-200)" stroke="var(--gray-200)" stroke-width="1">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}" required>
                    <div id="rating-label" style="font-size:.875rem;color:var(--gray-400);font-weight:600;min-height:1.4em;"></div>
                </div>

                {{-- Aspect Ratings --}}
                <div style="margin-bottom:24px;">
                    <div class="form-label" style="margin-bottom:2px;">Penilaian per Aspek <span class="text-muted">(opsional)</span></div>
                    <p style="font-size:.8rem;color:var(--gray-400);margin-bottom:14px;">Lebih detail membantu calon penyewa lainnya</p>

                    @foreach([
                        ['name'=>'rating_cleanliness','label'=>'Kebersihan Kendaraan','icon'=>'<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>'],
                        ['name'=>'rating_comfort','label'=>'Kenyamanan','icon'=>'<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>'],
                        ['name'=>'rating_condition','label'=>'Kondisi Mesin & Performa','icon'=>'<circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>'],
                        ['name'=>'rating_service','label'=>'Pelayanan Admin','icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
                    ] as $aspect)
                    <div class="aspect-item">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;background:var(--brand-50);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--brand-600)" stroke-width="2">{!! $aspect['icon'] !!}</svg>
                            </div>
                            <span style="font-size:.875rem;font-weight:600;">{{ $aspect['label'] }}</span>
                        </div>
                        <div class="mini-stars" id="mini-{{ $aspect['name'] }}">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="mini-star-btn"
                                    onclick="setAspect('{{ $aspect['name'] }}', {{ $i }})"
                                    onmouseover="hoverAspect('{{ $aspect['name'] }}', {{ $i }})"
                                    onmouseout="hoverAspect('{{ $aspect['name'] }}', 0)">
                                <svg viewBox="0 0 24 24" fill="var(--gray-200)" stroke="var(--gray-200)" stroke-width="1">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </button>
                            @endfor
                            <input type="hidden" name="{{ $aspect['name'] }}" id="input-{{ $aspect['name'] }}" value="{{ old($aspect['name']) }}">
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Comment --}}
                <div class="form-group">
                    <label class="form-label" for="comment">Cerita Pengalaman Anda</label>
                    <textarea id="comment" name="comment" class="form-input @error('comment') is-invalid @enderror"
                              rows="4" placeholder="Bagaimana kondisi kendaraan? Pelayanannya? Pengalaman perjalanan Anda?..."
                              style="resize:vertical;">{{ old('comment') }}</textarea>
                    <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                        <span id="char-count" style="font-size:.75rem;color:var(--gray-400);">0 / 500</span>
                    </div>
                    @error('comment')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Photo Upload --}}
                <div class="form-group">
                    <label class="form-label">Foto Kendaraan <span class="text-muted">(opsional, maks 3)</span></label>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;" id="photo-preview-container">
                        <label style="width:90px;height:90px;border:2px dashed var(--gray-300);border-radius:var(--radius-md);display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;gap:6px;transition:all .2s;"
                               onmouseover="this.style.borderColor='var(--brand-400)'" onmouseout="this.style.borderColor='var(--gray-300)'"
                               for="review-photos">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span style="font-size:.7rem;color:var(--gray-400);">Tambah Foto</span>
                        </label>
                        <input type="file" id="review-photos" name="photos[]" multiple accept="image/*"
                               style="display:none;" onchange="previewPhotos(this)" max="3">
                    </div>
                </div>

                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary" style="flex:1;min-width:160px;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Kirim Ulasan
                    </button>
                    <a href="{{ route('customer.bookings.index') }}" class="btn btn-secondary">
                        Lewati
                    </a>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    const labels = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Bagus', 'Sangat Bagus!'];
    const colors = ['', 'var(--danger)', 'var(--warning)', 'var(--amber-500)', 'var(--brand-500)', 'var(--success)'];
    let currentRating = {{ old('rating', 0) }};

    function renderStars(container, val, color = 'var(--amber-500)') {
        container.querySelectorAll('svg').forEach((s, i) => {
            const c = i < val ? color : 'var(--gray-200)';
            s.setAttribute('fill', c); s.setAttribute('stroke', c);
        });
    }

    function setRating(val) {
        currentRating = val;
        document.getElementById('rating-input').value = val;
        renderStars(document.getElementById('star-container'), val, colors[val]);
        const lbl = document.getElementById('rating-label');
        lbl.textContent = labels[val];
        lbl.style.color = colors[val];
    }

    function hoverRating(val) {
        renderStars(document.getElementById('star-container'), val || currentRating, colors[val || currentRating]);
    }

    // Aspect ratings
    const aspectRatings = {};
    function setAspect(name, val) {
        aspectRatings[name] = val;
        document.getElementById('input-' + name).value = val;
        renderStars(document.getElementById('mini-' + name), val, 'var(--amber-500)');
    }
    function hoverAspect(name, val) {
        renderStars(document.getElementById('mini-' + name), val || aspectRatings[name] || 0, 'var(--amber-500)');
    }

    // Char counter
    document.getElementById('comment').addEventListener('input', function() {
        const len = this.value.length;
        const el = document.getElementById('char-count');
        el.textContent = len + ' / 500';
        el.style.color = len > 450 ? 'var(--danger)' : 'var(--gray-400)';
        if (len > 500) this.value = this.value.slice(0, 500);
    });

    // Photo preview
    function previewPhotos(input) {
        const container = document.getElementById('photo-preview-container');
        container.querySelectorAll('.preview-img').forEach(el => el.remove());
        Array.from(input.files).slice(0, 3).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'preview-img';
                div.style = 'position:relative;width:90px;height:90px;';
                div.innerHTML = `<img src="${e.target.result}" style="width:90px;height:90px;object-fit:cover;border-radius:var(--radius-md);">`;
                container.insertBefore(div, container.querySelector('label'));
            };
            reader.readAsDataURL(file);
        });
    }

    // Init old rating
    if (currentRating) setRating(currentRating);
    </script>
    @endpush

</x-guest-layout>

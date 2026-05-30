<x-guest-layout>
    <x-slot:title>Sewa Kendaraan Mudah & Terpercaya</x-slot:title>

    @push('styles')
    <style>
    .hero-grid { display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center; }
    .category-card {
        display:flex;flex-direction:column;align-items:center;gap:10px;
        padding:22px 16px;background:#fff;border:1.5px solid var(--gray-200);
        border-radius:var(--radius-lg);cursor:pointer;transition:all .2s;text-decoration:none;
        min-width:100px;flex-shrink:0;
    }
    .category-card:hover,.category-card.active {
        border-color:var(--brand-400);background:var(--brand-50);
        transform:translateY(-2px);box-shadow:var(--shadow-sm);
    }
    .category-card-icon {
        width:48px;height:48px;border-radius:var(--radius-md);
        background:var(--gray-100);display:flex;align-items:center;justify-content:center;
        transition:background .2s;
    }
    .category-card-icon svg { width:24px;height:24px;color:var(--gray-600); }
    .category-card:hover .category-card-icon,.category-card.active .category-card-icon {
        background:var(--brand-100);
    }
    .category-card:hover .category-card-icon svg,.category-card.active .category-card-icon svg {
        color:var(--brand-600);
    }
    .category-card-label { font-size:.75rem;font-weight:700;font-family:'Sora',sans-serif;color:var(--gray-700);text-align:center; }
    .vehicles-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px; }
    .how-step { display:flex;flex-direction:column;align-items:center;text-align:center;padding:28px 20px; }
    .how-step-num {
        width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;
        margin-bottom:16px;position:relative;font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem;
    }
    .trust-item { display:flex;align-items:center;gap:14px;padding:18px 20px;background:#fff;border-radius:var(--radius-lg);border:1px solid var(--gray-100);box-shadow:var(--shadow-xs); }
    .trust-icon { width:44px;height:44px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .trust-icon svg { width:22px;height:22px; }
    @media(max-width:768px){
        .hero-grid{grid-template-columns:1fr;gap:32px;}
        .hero-search-col{display:none;}
        .vehicles-grid{grid-template-columns:repeat(2,1fr);gap:14px;}
        .how-grid{grid-template-columns:1fr 1fr!important;}
        .trust-grid{grid-template-columns:1fr!important;}
    }
    @media(max-width:480px){
        .vehicles-grid{grid-template-columns:1fr;}
        .how-grid{grid-template-columns:1fr!important;}
    }
    </style>
    @endpush

    {{-- ── HERO ──────────────────────────────────────────────── --}}
    <section class="hero-section">
        <div class="container" style="position:relative;z-index:1;">
            <div class="hero-grid">
                {{-- Left Copy --}}
                <div>
                    <div class="hero-eyebrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Armada Terawat · Harga Transparan
                    </div>
                    <h1 class="hero-title">
                        Sewa Kendaraan<br>
                        Kapan Saja,<br>
                        Kemana <em>Saja</em>
                    </h1>
                    <p class="hero-subtitle">
                        Nikmati kemudahan sewa kendaraan berkualitas langsung dari RentWheels.
                        Armada terawat, harga jelas, booking online 24 jam tanpa ribet.
                    </p>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <a href="{{ route('cars.index') }}" class="btn btn-amber btn-lg">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Cari Kendaraan
                        </a>
                        <a href="#cara-pemesanan" class="btn btn-lg" style="background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.2);">
                            Cara Pemesanan
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                    {{-- Stats --}}
                    <div style="display:flex;gap:28px;margin-top:40px;flex-wrap:wrap;padding-top:32px;border-top:1px solid rgba(255,255,255,.1);">
                        @foreach([
                            ['value'=>'200+','label'=>'Armada Aktif'],
                            ['value'=>'5.000+','label'=>'Perjalanan Sukses'],
                            ['value'=>'4.9★','label'=>'Rating Rata-rata'],
                        ] as $stat)
                        <div>
                            <div style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;color:#fff;line-height:1;">{{ $stat['value'] }}</div>
                            <div style="font-size:.78rem;color:rgba(255,255,255,.5);margin-top:4px;font-weight:500;">{{ $stat['label'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Search Box --}}
                <div class="hero-search-col">
                    <div class="search-card">
                        <div class="search-card-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand-600)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Cari Kendaraan Sekarang
                        </div>
                        <form action="{{ route('cars.search') }}" method="GET">
                            <div class="form-group">
                                <div class="search-field-label">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    Kota / Lokasi
                                </div>
                                <div class="input-icon">
                                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <input type="text" name="city" class="form-input" placeholder="Jakarta, Bali, Surabaya...">
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
                                <div>
                                    <div class="search-field-label">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        Tanggal Mulai
                                    </div>
                                    <input type="date" name="start_date" class="form-input" min="{{ today()->format('Y-m-d') }}">
                                </div>
                                <div>
                                    <div class="search-field-label">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        Tanggal Selesai
                                    </div>
                                    <input type="date" name="end_date" class="form-input" min="{{ today()->addDay()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="search-field-label">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect width="13" height="8" x="8" y="13" rx="2"/></svg>
                                    Jenis Kendaraan
                                </div>
                                <select name="category" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    @foreach(\App\Enums\VehicleCategory::cases() as $cat)
                                        <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                Temukan Kendaraan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Mobile Search Bar --}}
    <div style="display:none;padding:16px;" class="mobile-search-bar">
        <a href="{{ route('cars.index') }}" class="btn btn-primary btn-block btn-lg" style="justify-content:center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Cari Kendaraan Sekarang
        </a>
    </div>

    {{-- ── CATEGORIES ────────────────────────────────────────── --}}
    <section style="padding:60px 0 40px;">
        <div class="container">
            <div style="text-align:center;margin-bottom:32px;">
                <h2>Pilih Jenis Kendaraan</h2>
                <p style="color:var(--gray-500);margin-top:8px;font-size:.95rem;">
                    Dari kendaraan harian hingga perjalanan jauh
                </p>
            </div>
            <div style="display:flex;gap:12px;overflow-x:auto;padding-bottom:8px;scrollbar-width:none;justify-content:center;flex-wrap:wrap;">
                @php
                $categoryIcons = [
                    'sedan'   => '<path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1l3-4h8l3 4h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/><circle cx="7.5" cy="17" r="2"/><circle cx="16.5" cy="17" r="2"/>',
                    'suv'     => '<path d="M3 17h2m14 0h2M5 17H3V9l3-5h12l3 5v8h-2m-14 0v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1m8 0v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1"/><circle cx="7.5" cy="17" r="2"/><circle cx="16.5" cy="17" r="2"/>',
                    'mpv'     => '<path d="M5 17H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-2"/><circle cx="7.5" cy="17" r="2"/><circle cx="16.5" cy="17" r="2"/>',
                    'minibus' => '<rect x="1" y="4" width="22" height="14" rx="2"/><path d="M1 11h22"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
                    'truk'    => '<path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18" r="2"/><circle cx="18.5" cy="18" r="2"/>',
                    'motor'   => '<circle cx="5" cy="17" r="3"/><circle cx="19" cy="17" r="3"/><path d="M9 7h5l3 4H9L7 7"/><path d="M5 17 9 7"/>',
                ];
                @endphp
                @foreach(\App\Enums\VehicleCategory::cases() as $cat)
                    <a href="{{ route('cars.search', ['category' => $cat->value]) }}"
                       class="category-card {{ request('category') === $cat->value ? 'active' : '' }}">
                        <div class="category-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                {!! $categoryIcons[$cat->value] ?? '<path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect width="13" height="8" x="8" y="13" rx="2"/>' !!}
                            </svg>
                        </div>
                        <span class="category-card-label">{{ $cat->label() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── FEATURED VEHICLES ──────────────────────────────────── --}}
    <section style="padding:20px 0 64px;background:var(--gray-50);">
        <div class="container">
            <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:28px;gap:16px;">
                <div>
                    <h2>Kendaraan Populer</h2>
                    <p style="color:var(--gray-500);margin-top:6px;font-size:.9rem;">
                        Dipilih berdasarkan rating & ulasan terbaik pelanggan
                    </p>
                </div>
                <a href="{{ route('cars.index') }}" class="btn btn-secondary btn-sm" style="flex-shrink:0;">
                    Lihat Semua
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
            @php
                $featured = \App\Models\Vehicle::published()
                    ->with(['primaryPhoto'])
                    ->orderByDesc('avg_rating')
                    ->take(6)->get();
            @endphp
            <div class="vehicles-grid">
                @forelse($featured as $vehicle)
                    <x-vehicle-card :vehicle="$vehicle" />
                @empty
                    <div class="empty-state" style="grid-column:1/-1;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect width="13" height="8" x="8" y="13" rx="2"/></svg>
                        <h3>Belum ada kendaraan tersedia</h3>
                        <p>Armada sedang disiapkan. Cek kembali nanti.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ── HOW IT WORKS ───────────────────────────────────────── --}}
    <section id="cara-pemesanan" style="padding:80px 0;">
        <div class="container">
            <div style="text-align:center;margin-bottom:52px;">
                <div class="hero-eyebrow" style="display:inline-flex;color:var(--brand-600);background:var(--brand-50);border-color:var(--brand-200);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Proses Mudah dalam 4 Langkah
                </div>
                <h2 style="margin-top:14px;">Cara Sewa Kendaraan</h2>
            </div>
            <div class="how-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;position:relative;">
                {{-- Connector line --}}
                <div style="position:absolute;top:42px;left:calc(12.5% + 16px);right:calc(12.5% + 16px);height:2px;background:var(--gray-200);z-index:0;"></div>

                @foreach([
                    ['num'=>'01','bg'=>'var(--brand-50)','color'=>'var(--brand-600)','title'=>'Pilih Kendaraan','desc'=>'Temukan kendaraan yang sesuai kebutuhan dari armada kami yang lengkap.',
                     'icon'=>'<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>'],
                    ['num'=>'02','bg'=>'#eff6ff','color'=>'#3b82f6','title'=>'Isi Formulir','desc'=>'Pilih tanggal sewa, lokasi pengambilan, dan unggah dokumen yang diperlukan.',
                     'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'],
                    ['num'=>'03','bg'=>'#fff7ed','color'=>'var(--amber-600)','title'=>'Bayar Online','desc'=>'Bayar via transfer bank, dompet digital, atau kartu kredit/debit dengan aman.',
                     'icon'=>'<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>'],
                    ['num'=>'04','bg'=>'#f0fdf4','color'=>'var(--success)','title'=>'Siap Meluncur','desc'=>'Kami konfirmasi dan kendaraan siap diambil sesuai jadwal yang Anda tentukan.',
                     'icon'=>'<path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect width="13" height="8" x="8" y="13" rx="2"/>'],
                ] as $step)
                <div class="how-step" style="position:relative;z-index:1;">
                    <div style="width:64px;height:64px;border-radius:50%;background:{{ $step['bg'] }};border:3px solid #fff;box-shadow:var(--shadow-md);display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="{{ $step['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            {!! $step['icon'] !!}
                        </svg>
                    </div>
                    <div style="font-size:.65rem;font-weight:800;color:{{ $step['color'] }};letter-spacing:1px;margin-bottom:6px;text-transform:uppercase;">{{ $step['num'] }}</div>
                    <h4 style="margin-bottom:8px;font-size:1rem;">{{ $step['title'] }}</h4>
                    <p style="font-size:.85rem;color:var(--gray-500);line-height:1.7;">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── TRUST INDICATORS ──────────────────────────────────── --}}
    <section style="padding:20px 0 80px;background:var(--gray-50);">
        <div class="container">
            <div style="text-align:center;margin-bottom:36px;">
                <h2>Mengapa Pilih RentWheels?</h2>
                <p style="color:var(--gray-500);margin-top:8px;">Kepercayaan Anda adalah prioritas utama kami</p>
            </div>
            <div class="trust-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;">
                @foreach([
                    ['icon'=>'<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>','bg'=>'var(--brand-50)','color'=>'var(--brand-600)','title'=>'Transaksi Dijamin Aman','desc'=>'Pembayaran diproses dengan enkripsi SSL & sistem keamanan berlapis.'],
                    ['icon'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>','bg'=>'#f0fdf4','color'=>'var(--success)','title'=>'Armada Terverifikasi','desc'=>'Setiap kendaraan melewati inspeksi rutin dan sertifikasi kelayakan.'],
                    ['icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 0 2-2h14a2 2 0 0 1 2 2z"/>','bg'=>'#eff6ff','color'=>'#3b82f6','title'=>'Layanan Pelanggan 24/7','desc'=>'Tim kami siap membantu Anda kapan saja melalui chat atau telepon.'],
                    ['icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>','bg'=>'#fff7ed','color'=>'var(--amber-600)','title'=>'Harga Transparan','desc'=>'Tidak ada biaya tersembunyi. Bayar sesuai yang tertera di halaman pemesanan.'],
                ] as $trust)
                <div class="trust-item">
                    <div class="trust-icon" style="background:{{ $trust['bg'] }};">
                        <svg viewBox="0 0 24 24" fill="none" stroke="{{ $trust['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            {!! $trust['icon'] !!}
                        </svg>
                    </div>
                    <div>
                        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:.9rem;margin-bottom:4px;">{{ $trust['title'] }}</div>
                        <div style="font-size:.8rem;color:var(--gray-500);line-height:1.55;">{{ $trust['desc'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── TENTANG KAMI ───────────────────────────────────────── --}}
    <section id="tentang-kami" style="padding:80px 0;">
        <div class="container">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center;">
                <div>
                    <div class="hero-eyebrow" style="display:inline-flex;color:var(--brand-600);background:var(--brand-50);border-color:var(--brand-200);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Tentang RentWheels
                    </div>
                    <h2 style="margin-top:14px;margin-bottom:18px;">
                        Perjalanan Nyaman<br>Dimulai dari Sini
                    </h2>
                    <p style="color:var(--gray-600);line-height:1.8;margin-bottom:16px;">
                        RentWheels adalah platform sewa kendaraan yang dikelola langsung oleh tim profesional kami. Berdiri sejak 2020, kami telah melayani ribuan pelanggan di seluruh Indonesia dengan armada yang selalu terawat dan terjamin kualitasnya.
                    </p>
                    <p style="color:var(--gray-600);line-height:1.8;margin-bottom:28px;">
                        Setiap kendaraan kami dipilih dengan ketat, dirawat secara berkala, dan dilengkapi asuransi untuk memberikan pengalaman berkendara yang aman dan nyaman.
                    </p>
                    <a href="{{ route('cars.index') }}" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect width="13" height="8" x="8" y="13" rx="2"/></svg>
                        Lihat Armada Kami
                    </a>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    @foreach([
                        ['num'=>'200+','label'=>'Unit Kendaraan','color'=>'var(--brand-600)','bg'=>'var(--brand-50)'],
                        ['num'=>'5.000+','label'=>'Pelanggan Puas','color'=>'#3b82f6','bg'=>'#eff6ff'],
                        ['num'=>'4+','label'=>'Tahun Pengalaman','color'=>'var(--amber-600)','bg'=>'#fffbeb'],
                        ['num'=>'24/7','label'=>'Layanan Pelanggan','color'=>'var(--success)','bg'=>'#ecfdf5'],
                    ] as $item)
                    <div style="background:{{ $item['bg'] }};border-radius:var(--radius-lg);padding:24px;text-align:center;">
                        <div style="font-family:'Sora',sans-serif;font-size:2rem;font-weight:800;color:{{ $item['color'] }};line-height:1;margin-bottom:6px;">{{ $item['num'] }}</div>
                        <div style="font-size:.8rem;color:var(--gray-600);font-weight:600;">{{ $item['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── CTA AKHIR ──────────────────────────────────────────── --}}
    <section style="padding:80px 0;background:linear-gradient(135deg,var(--navy-900) 0%,var(--brand-900) 100%);position:relative;overflow:hidden;">
        <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 50% 50%,rgba(20,184,166,.2) 0%,transparent 60%);"></div>
        <div class="container" style="position:relative;z-index:1;text-align:center;">
            <div class="hero-eyebrow" style="display:inline-flex;margin-bottom:20px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Pesan Sekarang
            </div>
            <h2 style="color:#fff;margin-bottom:16px;font-size:clamp(1.5rem,4vw,2.5rem);">
                Siap untuk Perjalanan<br>Berikutnya?
            </h2>
            <p style="color:rgba(255,255,255,.65);max-width:500px;margin:0 auto 36px;font-size:1rem;line-height:1.8;">
                Daftar sekarang dan dapatkan kemudahan booking kendaraan kapan saja, di mana saja.
                Proses cepat, aman, dan terpercaya.
            </p>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                @guest
                <a href="{{ route('register') }}" class="btn btn-amber btn-lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    Daftar Gratis Sekarang
                </a>
                @endguest
                <a href="{{ route('cars.index') }}" class="btn btn-lg" style="background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.2);">
                    Lihat Kendaraan
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>
    </section>

    @push('styles')
    <style>
    .mobile-search-bar { display: none; }
    @media (max-width: 768px) {
        .mobile-search-bar { display: block !important; }
        #tentang-kami .container > div { grid-template-columns: 1fr !important; gap: 32px !important; }
    }
    </style>
    @endpush
</x-guest-layout>

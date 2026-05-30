<x-guest-layout>
    <x-slot:title>Cari Kendaraan</x-slot:title>

    <div style="padding:36px 0 60px;">
        <div class="container">

            {{-- Page Header --}}
            <div style="margin-bottom:28px;">
                <h1 style="font-size:1.6rem;margin-bottom:6px;">
                    @if(request('q'))
                        Hasil pencarian: "{{ request('q') }}"
                    @elseif(request('category'))
                        {{ \App\Enums\VehicleCategory::from(request('category'))->label() }}
                    @else
                        Semua Kendaraan
                    @endif
                </h1>
                <p class="text-sm text-muted">{{ $vehicles->total() }} kendaraan tersedia untuk disewa</p>
            </div>

            <div style="display:grid;grid-template-columns:268px 1fr;gap:28px;" class="cars-layout">

                {{-- ── Sidebar Filters ─────────────────────────── --}}
                <aside class="filter-sidebar">
                    <div class="card" style="position:sticky;top:calc(var(--navbar-height) + 16px);">
                        <div class="card-header">
                            <div class="card-header-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                                Filter
                            </div>
                            @if(request()->hasAny(['q','category','min_price','max_price','transmission','capacity','has_driver']))
                            <a href="{{ route('cars.index') }}" class="btn btn-ghost btn-sm" style="font-size:.75rem;color:var(--danger);">Hapus Filter</a>
                            @endif
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('cars.search') }}" id="filter-form">
                                {{-- Search --}}
                                <div class="form-group">
                                    <label class="form-label">Cari Kendaraan</label>
                                    <div class="input-icon">
                                        <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                        <input type="text" name="q" class="form-input" value="{{ request('q') }}" placeholder="Merk, model..." style="padding-left:36px;">
                                    </div>
                                </div>

                                {{-- Kategori --}}
                                <div class="form-group">
                                    <label class="form-label">Jenis Kendaraan</label>
                                    <div style="display:flex;flex-direction:column;gap:6px;">
                                        @foreach(\App\Enums\VehicleCategory::cases() as $cat)
                                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.875rem;padding:6px 8px;border-radius:var(--radius-sm);transition:background .15s;"
                                               onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                                            <input type="radio" name="category" value="{{ $cat->value }}"
                                                   {{ request('category') === $cat->value ? 'checked' : '' }}
                                                   style="accent-color:var(--brand-600);">
                                            {{ $cat->label() }}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Harga --}}
                                <div class="form-group">
                                    <label class="form-label">Harga per Hari</label>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                        <div>
                                            <div class="form-label-sm">Min</div>
                                            <input type="number" name="min_price" class="form-input" value="{{ request('min_price') }}" placeholder="0" step="50000">
                                        </div>
                                        <div>
                                            <div class="form-label-sm">Maks</div>
                                            <input type="number" name="max_price" class="form-input" value="{{ request('max_price') }}" placeholder="Bebas" step="50000">
                                        </div>
                                    </div>
                                </div>

                                {{-- Transmisi --}}
                                <div class="form-group">
                                    <label class="form-label">Transmisi</label>
                                    <select name="transmission" class="form-select">
                                        <option value="">Semua</option>
                                        <option value="manual" {{ request('transmission') === 'manual' ? 'selected' : '' }}>Manual</option>
                                        <option value="otomatis" {{ request('transmission') === 'otomatis' ? 'selected' : '' }}>Otomatis</option>
                                    </select>
                                </div>

                                {{-- Kapasitas --}}
                                <div class="form-group">
                                    <label class="form-label">Kapasitas Minimum</label>
                                    <select name="capacity" class="form-select">
                                        <option value="">Berapa saja</option>
                                        @foreach([2,4,5,6,7,8] as $cap)
                                        <option value="{{ $cap }}" {{ request('capacity') == $cap ? 'selected' : '' }}>≥ {{ $cap }} orang</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Sopir --}}
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                                    <input type="checkbox" id="has_driver" name="has_driver" value="1"
                                           {{ request('has_driver') ? 'checked' : '' }}
                                           style="width:17px;height:17px;accent-color:var(--brand-600);cursor:pointer;">
                                    <label for="has_driver" style="font-size:.875rem;color:var(--gray-700);cursor:pointer;font-weight:500;">
                                        Tersedia dengan sopir
                                    </label>
                                </div>

                                {{-- Tanggal --}}
                                <div class="form-group">
                                    <label class="form-label">Tanggal Sewa</label>
                                    <div style="display:grid;gap:8px;">
                                        <div>
                                            <div class="form-label-sm">Mulai</div>
                                            <input type="date" name="start_date" class="form-input" value="{{ request('start_date') }}" min="{{ today()->format('Y-m-d') }}">
                                        </div>
                                        <div>
                                            <div class="form-label-sm">Selesai</div>
                                            <input type="date" name="end_date" class="form-input" value="{{ request('end_date') }}">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    Terapkan Filter
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>

                {{-- ── Results ─────────────────────────────────── --}}
                <div>
                    {{-- Sort & Count --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
                        <div style="font-size:.875rem;color:var(--gray-500);">
                            Menampilkan <strong style="color:var(--gray-900);">{{ $vehicles->firstItem() ?? 0 }}</strong>–<strong style="color:var(--gray-900);">{{ $vehicles->lastItem() ?? 0 }}</strong> dari <strong style="color:var(--gray-900);">{{ $vehicles->total() }}</strong> kendaraan
                        </div>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <span style="font-size:.8rem;color:var(--gray-500);">Urutkan:</span>
                            <select name="sort" class="form-select" style="width:auto;padding:7px 28px 7px 10px;font-size:.82rem;"
                                    onchange="window.location.href=updateQueryParam('sort',this.value)">
                                <option value="popular" {{ request('sort','popular') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Rating Terbaik</option>
                                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            </select>
                        </div>
                    </div>

                    {{-- Category Chips --}}
                    <div class="filter-chips" style="margin-bottom:20px;">
                        <a href="{{ route('cars.index') }}" class="filter-chip {{ !request('category') ? 'active' : '' }}">
                            Semua Jenis
                        </a>
                        @foreach(\App\Enums\VehicleCategory::cases() as $cat)
                        <a href="{{ route('cars.search', ['category' => $cat->value] + request()->except('category','page')) }}"
                           class="filter-chip {{ request('category') === $cat->value ? 'active' : '' }}">
                            {{ $cat->label() }}
                        </a>
                        @endforeach
                    </div>

                    {{-- Grid --}}
                    @if($vehicles->isEmpty())
                    <div class="card">
                        <div class="empty-state" style="padding:72px 24px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <h3>Tidak ada kendaraan ditemukan</h3>
                            <p>Coba ubah filter atau kata kunci pencarian Anda.</p>
                            <a href="{{ route('cars.index') }}" class="btn btn-primary" style="margin-top:4px;">
                                Hapus Filter
                            </a>
                        </div>
                    </div>
                    @else
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;margin-bottom:28px;">
                        @foreach($vehicles as $vehicle)
                            <x-vehicle-card :vehicle="$vehicle" />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($vehicles->hasPages())
                    <div style="display:flex;justify-content:center;">
                        {{ $vehicles->withQueryString()->links('vendor.pagination.simple-rentwheels') }}
                    </div>
                    @endif
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Mobile Filter Button --}}
    <div style="display:none;position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:50;" class="mobile-filter-btn">
        <button onclick="document.querySelector('.filter-sidebar').style.display='block'"
                class="btn btn-primary" style="box-shadow:var(--shadow-lg);border-radius:var(--radius-full);padding:12px 24px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
            Filter
        </button>
    </div>

    @push('styles')
    <style>
    @media (max-width: 900px) {
        .cars-layout { grid-template-columns: 1fr !important; }
        .filter-sidebar { display: none; }
        .mobile-filter-btn { display: flex !important; }
    }
    </style>
    @endpush

    @push('scripts')
    <script>
    function updateQueryParam(key, value) {
        const url = new URL(window.location.href);
        url.searchParams.set(key, value);
        url.searchParams.delete('page');
        return url.toString();
    }
    </script>
    @endpush
</x-guest-layout>

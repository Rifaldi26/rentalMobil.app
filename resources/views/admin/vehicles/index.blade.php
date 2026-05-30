<x-app-layout>
    <x-slot:title>Kelola Armada</x-slot:title>

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;margin-bottom:4px;">Kelola Armada</h1>
            <p class="text-sm text-muted">{{ $vehicles->total() }} kendaraan terdaftar</p>
        </div>
        <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Kendaraan
        </a>
    </div>

    {{-- Filters --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET">
                <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                    <div style="flex:1;min-width:200px;">
                        <div class="form-label-sm">Cari Kendaraan</div>
                        <div class="input-icon">
                            <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="q" class="form-input" placeholder="Merk, model, plat..." value="{{ request('q') }}" style="padding-left:36px;">
                        </div>
                    </div>
                    <div style="min-width:140px;">
                        <div class="form-label-sm">Kategori</div>
                        <select name="category" class="form-select">
                            <option value="">Semua</option>
                            @foreach(\App\Enums\VehicleCategory::cases() as $c)
                            <option value="{{ $c->value }}" {{ request('category') === $c->value ? 'selected' : '' }}>{{ $c->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width:130px;">
                        <div class="form-label-sm">Status</div>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach(\App\Enums\VehicleStatus::cases() as $s)
                            <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width:130px;">
                        <div class="form-label-sm">Publikasi</div>
                        <select name="published" class="form-select">
                            <option value="">Semua</option>
                            <option value="1" {{ request('published') === '1' ? 'selected' : '' }}>Dipublikasi</option>
                            <option value="0" {{ request('published') === '0' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Cari
                        </button>
                        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.73"/></svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Toggle View --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;" x-data="{ view: 'grid' }">
        <div class="filter-chips">
            <a href="{{ route('admin.vehicles.index') }}" class="filter-chip {{ !request()->hasAny(['category','status','published']) ? 'active' : '' }}">Semua</a>
            @foreach(\App\Enums\VehicleStatus::cases() as $s)
            <a href="{{ route('admin.vehicles.index', ['status' => $s->value]) }}"
               class="filter-chip {{ request('status') === $s->value ? 'active' : '' }}">
                {{ $s->label() }}
            </a>
            @endforeach
        </div>
        <div style="display:flex;gap:4px;">
            <button @click="view='grid'" :style="view==='grid' ? 'background:var(--brand-50);color:var(--brand-600);border-color:var(--brand-300)' : ''"
                    class="btn btn-sm btn-secondary btn-icon" title="Grid view">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </button>
            <button @click="view='list'" :style="view==='list' ? 'background:var(--brand-50);color:var(--brand-600);border-color:var(--brand-300)' : ''"
                    class="btn btn-sm btn-secondary btn-icon" title="List view">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
        </div>
    </div>

    {{-- ── Grid View ────────────────────────────────────────── --}}
    <div x-data="{ view: 'grid' }">

    {{-- Grid --}}
    <div x-show="view === 'grid'" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-bottom:24px;">
        @forelse($vehicles as $vehicle)
        <div class="card card-hover" style="overflow:hidden;">
            {{-- Image --}}
            <div style="aspect-ratio:16/9;overflow:hidden;background:var(--gray-100);position:relative;">
                <img src="{{ $vehicle->primary_photo_url }}"
                     alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                     style="width:100%;height:100%;object-fit:cover;">
                {{-- Published toggle --}}
                <div style="position:absolute;top:10px;left:10px;">
                    <form method="POST" action="{{ route('admin.vehicles.toggle-publish', $vehicle) }}" style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit"
                                style="padding:4px 10px;border-radius:var(--radius-full);font-size:.68rem;font-weight:700;border:none;cursor:pointer;background:{{ $vehicle->is_verified ? 'var(--success)' : 'var(--gray-500)' }}..."
                                title="{{ $vehicle->is_verified ? 'Sudah Diverifikasi' : 'Belum Diverifikasi' }}">
                                {{ $vehicle->is_verified ? '✓ Terverifikasi' : 'Belum Verified' }}
                        </button>
                    </form>
                </div>
                <div style="position:absolute;top:10px;right:10px;">
                    <span class="badge badge-{{ $vehicle->status->value === 'tersedia' ? 'available' : ($vehicle->status->value === 'disewa' ? 'rented' : 'maintenance') }}">
                        {{ $vehicle->status->label() }}
                    </span>
                </div>
            </div>

            {{-- Body --}}
            <div style="padding:14px 16px;">
                <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:.95rem;margin-bottom:6px;">
                    {{ $vehicle->brand }} {{ $vehicle->model }}
                </div>
                <div style="display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                    <span style="font-size:.75rem;color:var(--gray-500);display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                        {{ $vehicle->year }}
                    </span>
                    <span style="font-size:.75rem;color:var(--gray-500);display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        {{ $vehicle->capacity }} kursi
                    </span>
                    <span style="font-size:.75rem;color:var(--gray-500);display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/></svg>
                        {{ $vehicle->plate_number }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <div>
                        <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.05rem;color:var(--brand-700);">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</span>
                        <span style="font-size:.72rem;color:var(--gray-500);">/hari</span>
                    </div>
                    @if($vehicle->avg_rating > 0)
                    <div style="display:flex;align-items:center;gap:4px;font-size:.8rem;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span style="font-weight:700;">{{ number_format($vehicle->avg_rating, 1) }}</span>
                        <span style="color:var(--gray-400);">({{ $vehicle->reviews_count }})</span>
                    </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                    </a>
                    <a href="{{ route('admin.bookings.index', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-ghost btn-sm btn-icon" title="Lihat pemesanan">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </a>
                    <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}"
                          onsubmit="return confirm('Hapus kendaraan ini? Tindakan tidak dapat dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-sm btn-icon" style="color:var(--danger);" title="Hapus">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column:1/-1;padding:64px 24px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect width="13" height="8" x="8" y="13" rx="2"/></svg>
            <h3>Belum ada kendaraan</h3>
            <p>Mulai tambahkan armada kendaraan Anda.</p>
            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary" style="margin-top:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Kendaraan
            </a>
        </div>
        @endforelse
    </div>

    </div>{{-- x-data --}}

    {{-- Pagination --}}
    @if($vehicles->hasPages())
    <div style="display:flex;justify-content:center;margin-top:8px;">
        {{ $vehicles->withQueryString()->links('vendor.pagination.simple-rentwheels') }}
    </div>
    @endif

    @push('styles')
    <style>
    @media(max-width:768px){
        .admin-vehicles-grid { grid-template-columns: 1fr 1fr !important; }
    }
    @media(max-width:480px){
        .admin-vehicles-grid { grid-template-columns: 1fr !important; }
    }
    </style>
    @endpush
</x-app-layout>

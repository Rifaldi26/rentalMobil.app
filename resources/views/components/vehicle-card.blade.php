{{-- components/vehicle-card.blade.php --}}
{{-- Reusable vehicle card component --}}
@props(['vehicle'])

<a href="{{ route('cars.show', $vehicle) }}" class="vehicle-card card-hover"
    style="display:block;text-decoration:none;">
    {{-- Image --}}
    <div class="vehicle-card-img">
        <img src="{{ $vehicle->primary_photo_url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" loading="lazy">
        {{-- Status Badge --}}
        <div class="vehicle-card-badge">
            @if(!$vehicle->is_active)
            <span class="badge badge-maintenance">Perawatan</span>
            @elseif($vehicle->bookings()->whereIn('status', ['confirmed', 'active'])->whereDate('end_date', '>=',
            now())->exists())
            <span class="badge badge-rented">Disewa</span>
            @else
            <span class="badge badge-available">
                <svg width="8" height="8" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="4" fill="currentColor" />
                </svg>
                Tersedia
            </span>
            @endif
        </div>
        {{-- Category label --}}
        <div style="position:absolute;bottom:10px;left:10px;">
            <span
                style="background:rgba(0,0,0,.55);backdrop-filter:blur(4px);color:#fff;font-size:.68rem;font-weight:700;padding:3px 10px;border-radius:var(--radius-full);text-transform:uppercase;letter-spacing:.5px;">
                {{ $vehicle->category->label() ?? $vehicle->category->value }}
            </span>
        </div>
    </div>

    {{-- Body --}}
    <div class="vehicle-card-body">
        <div class="vehicle-card-title">{{ $vehicle->brand }} {{ $vehicle->model }}</div>

        {{-- Meta --}}
        <div class="vehicle-card-meta">
            <div class="vehicle-card-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                </svg>
                {{ $vehicle->year }}
            </div>
            <div class="vehicle-card-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
                {{ $vehicle->capacity }} Kursi
            </div>
            <div class="vehicle-card-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                </svg>
                {{ ucfirst($vehicle->transmission) }}
            </div>
            @if($vehicle->avg_rating > 0)
            <div class="vehicle-card-meta-item">
                <svg viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24" stroke-width="1">
                    <polygon
                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
                <span style="font-weight:700;color:var(--gray-900);">{{ number_format($vehicle->avg_rating, 1) }}</span>
                <span style="color:var(--gray-400);">({{ $vehicle->reviews_count }})</span>
            </div>
            @endif
        </div>

        {{-- Price --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:4px;">
            <div class="vehicle-card-price">
                <span
                    class="vehicle-card-price-value">{{ 'Rp '.number_format($vehicle->price_per_day, 0, ',', '.') }}</span>
                <span class="vehicle-card-price-unit">/hari</span>
            </div>
            <div style="display:flex;gap:6px;">
                @if($vehicle->has_driver)
                <span
                    style="font-size:.7rem;background:var(--brand-50);color:var(--brand-700);padding:3px 8px;border-radius:var(--radius-full);font-weight:700;">
                    Sopir
                </span>
                @endif
                @if($vehicle->fuel_included)
                <span
                    style="font-size:.7rem;background:#fffbeb;color:var(--amber-600);padding:3px 8px;border-radius:var(--radius-full);font-weight:700;">
                    BBM
                </span>
                @endif
            </div>
        </div>
    </div>
</a>

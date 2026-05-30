<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\VehicleCategory;
use App\Enums\VehicleStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category',
        'brand',
        'model',
        'year',
        'plate_number',
        'price_per_day',
        'deposit',
        'capacity',
        'transmission',
        'fuel_type',
        'description',
        'rental_terms',
        'features',          // JSON: ['ac', 'gps', 'baby_seat', 'driver']
        'min_rental_days',
        'max_rental_days',
        'city',
        'is_active',
        'is_verified',
        'verified_at',
        'avg_rating',
        'review_count',
    ];

    protected $casts = [
        'price_per_day'   => 'decimal:2',
        'deposit'         => 'decimal:2',
        'year'            => 'integer',
        'capacity'        => 'integer',
        'min_rental_days' => 'integer',
        'max_rental_days' => 'integer',
        'is_active'       => 'boolean',
        'is_verified'     => 'boolean',
        'verified_at'     => 'datetime',
        'avg_rating'      => 'decimal:2',
        'review_count'    => 'integer',
        'features'        => 'array',
        'category'        => VehicleCategory::class,
        'status'          => VehicleStatus::class,
    ];

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) return 'nonaktif';
        if (!$this->is_verified) return 'belum_diverifikasi';
        return 'tersedia';
    }
    // ─── Relationships ────────────────────────────────────────────

    public function photos(): HasMany
    {
        return $this->hasMany(VehiclePhoto::class)->orderBy('order');
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(VehiclePhoto::class)->where('is_primary', true);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(VehicleAvailability::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_visible', true);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('is_verified', true);
    }

    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        return $category ? $query->where('category', $category) : $query;
    }

    public function scopeByCity(Builder $query, ?string $city): Builder
    {
        return $city ? $query->where('city', 'like', "%{$city}%") : $query;
    }

    public function scopePriceRange(Builder $query, ?int $min, ?int $max): Builder
    {
        return $query
            ->when($min, fn ($q) => $q->where('price_per_day', '>=', $min))
            ->when($max, fn ($q) => $q->where('price_per_day', '<=', $max));
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }
        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('brand', 'like', "%{$keyword}%")
              ->orWhere('model', 'like', "%{$keyword}%")
              ->orWhere('city', 'like', "%{$keyword}%");
        });
    }

    // ─── Availability ─────────────────────────────────────────────

    /**
     * Cek apakah kendaraan tersedia pada rentang tanggal tertentu.
     *
     * Menerima Carbon atau string — dinormalisasi ke 'Y-m-d' di dalam method
     * untuk menghindari bug timezone yang terjadi jika Carbon di-cast implisit.
     */
    public function isAvailableOn(Carbon|string $startDate, Carbon|string $endDate): bool
    {
        $start = $startDate instanceof Carbon
            ? $startDate->format('Y-m-d')
            : $startDate;

        $end = $endDate instanceof Carbon
            ? $endDate->format('Y-m-d')
            : $endDate;

        // Cek tanggal yang diblokir manual oleh admin
        $blocked = $this->availabilities()
            ->whereBetween('blocked_date', [$start, $end])
            ->exists();

        if ($blocked) {
            return false;
        }

        // Cek konflik dengan booking yang sudah ada
        return ! Booking::konflikTanggal($this->id, $start, $end)->exists();
    }

    /**
     * Ambil semua tanggal yang tidak tersedia dalam satu bulan.
     * Dipakai oleh kalender UI di halaman detail dan halaman jadwal admin.
     */
    public function getUnavailableDatesForMonth(int $year, int $month): array
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        // Tanggal diblokir manual
        $blocked = $this->availabilities()
            ->whereBetween('blocked_date', [$startOfMonth, $endOfMonth])
            ->pluck('blocked_date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->toArray();

        // Tanggal dari booking aktif
        $booked = [];
        $this->bookings()
            ->whereIn('status', array_map(
                fn (BookingStatus $s) => $s->value,
                BookingStatus::conflictStatuses()
            ))
            ->where('start_date', '<=', $endOfMonth)
            ->where('end_date', '>=', $startOfMonth)
            ->get(['start_date', 'end_date'])
            ->each(function ($booking) use (&$booked, $startOfMonth, $endOfMonth) {
                $current = max($booking->start_date, $startOfMonth)->copy();
                $end     = min($booking->end_date, $endOfMonth);
                while ($current->lte($end)) {
                    $booked[] = $current->format('Y-m-d');
                    $current->addDay();
                }
            });

        return array_unique(array_merge($blocked, $booked));
    }

    // ─── Accessors & Helpers ──────────────────────────────────────

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_day, 0, ',', '.');
    }

    public function getDepositFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->deposit, 0, ',', '.');
    }

    public function getPrimaryPhotoUrlAttribute(): string
    {
        $photo = $this->primaryPhoto;
        return $photo
            ? asset('storage/' . $photo->path)
            : asset('images/car-placeholder.png');
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? [], true);
    }
}
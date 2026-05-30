<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'vehicle_id',
        'start_date',
        'end_date',
        'total_price',
        'deposit',
        'service_fee',
        'status',
        'payment_status',
        'pickup_location',
        'notes',
        'rejected_reason',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'total_price'    => 'decimal:2',
        'deposit'        => 'decimal:2',
        'service_fee'    => 'decimal:2',
        'status'         => BookingStatus::class,
        'payment_status' => PaymentStatus::class,
    ];

    // ─── Lifecycle ────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = static::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        do {
            $code = 'RW-' . strtoupper(Str::random(8));
        } while (static::where('booking_code', $code)->exists());

        return $code;
    }

    // ─── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function vehicle(): BelongsTo  { return $this->belongsTo(Vehicle::class); }

    public function payment(): HasOne     { return $this->hasOne(Payment::class); }
    public function review(): HasOne      { return $this->hasOne(Review::class); }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeFilterBulan(Builder $query, ?string $bulan): Builder
    {
        if (! $bulan || ! str_contains($bulan, '-')) {
            return $query;
        }
        [$tahun, $bulanNum] = explode('-', $bulan);
        return $query->whereYear('created_at', $tahun)->whereMonth('created_at', $bulanNum);
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }
        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('booking_code', 'like', "%{$keyword}%")
              ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', "%{$keyword}%"))
              ->orWhereHas('vehicle', fn (Builder $v) =>
                  $v->where('brand', 'like', "%{$keyword}%")
                    ->orWhere('model', 'like', "%{$keyword}%")
              );
        });
    }

    /**
     * Query builder untuk deteksi konflik tanggal.
     * Menggunakan BookingStatus::conflictStatuses() sehingga tetap sinkron
     * jika ada status baru yang perlu memblokir kendaraan.
     */
    public static function konflikTanggal(
        int $vehicleId,
        string $mulai,
        string $selesai,
        ?int $excludeBookingId = null
    ): Builder {
        return static::where('vehicle_id', $vehicleId)
            ->whereIn('status', array_map(
                fn (BookingStatus $s) => $s->value,
                BookingStatus::conflictStatuses()
            ))
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where(function (Builder $q) use ($mulai, $selesai) {
                $q->whereBetween('start_date', [$mulai, $selesai])
                ->orWhereBetween('end_date', [$mulai, $selesai])
                ->orWhere(function (Builder $q2) use ($mulai, $selesai) {
                    $q2->where('start_date', '<=', $mulai)
                        ->where('end_date', '>=', $selesai);
                });
            });
    }

    // ─── Accessors & Helpers ──────────────────────────────────────

    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getGrandTotalAttribute(): float
    {
        return (float) $this->total_price
            + (float) $this->deposit
            + (float) $this->service_fee;
    }

    public function getTotalPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getGrandTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    public function canBeCancelledBy(User $user): bool
    {
        return $this->user_id === $user->id
            && $this->status->canBeCancelledByUser();
    }

    public function isReviewable(): bool
    {
        return $this->status === BookingStatus::Selesai
            && $this->review === null;
    }
}
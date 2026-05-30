<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'vehicle_id',
        'rating',
        'comment',
        'is_visible',
    ];

    protected $casts = [
        'rating'     => 'integer',
        'is_visible' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeByRating(Builder $query, ?int $rating): Builder
    {
        return $rating ? $query->where('rating', $rating) : $query;
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}

<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'partner_id',
        'amount',
        'status',
        'bank_account',
        'bank_name',
        'bank_holder',
        'admin_note',
        'processed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'status'       => WithdrawalStatus::class,
        'processed_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', WithdrawalStatus::Pending);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function getAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}

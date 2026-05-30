<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'gateway_ref',     // Token/Order ID dari Midtrans/Xendit
        'gateway_name',    // midtrans | xendit
        'method',          // bank_transfer | credit_card | gopay | ovo | qris
        'amount',
        'status',
        'gateway_payload', // JSON response dari gateway
        'paid_at',
        'snap_token',     // Token Snap khusus untuk Midtrans
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'status'           => PaymentStatus::class,
        'paid_at'          => 'datetime',
        'gateway_payload'  => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function getAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}

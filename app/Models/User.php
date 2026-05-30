<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'password',
        'role',
        'google_id',
        'avatar',
        'ktp_path',
        'is_suspended',
        // Field bisnis (hanya relevan untuk admin/pemilik usaha)
        'business_name',
        'balance',
        'bank_account',
        'bank_name',
        'bank_holder',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class,
            'is_suspended'      => 'boolean',
            'balance'           => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────

    public function bookings(): HasMany  { return $this->hasMany(Booking::class); }
    public function reviews(): HasMany   { return $this->hasMany(Review::class); }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeAdmin($query)
    {
        return $query->where('role', UserRole::Admin);
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', UserRole::Customer);
    }

    // ─── Role Helpers ─────────────────────────────────────────────

    public function isAdmin(): bool      { return $this->role === UserRole::Admin; }
    public static function getOwner(): self
    {
        return static::where('role', UserRole::Admin)->firstOrFail();
    }
    public function isCustomer(): bool   { return $this->role === UserRole::Customer; }
    public function isSuspended(): bool  { return (bool) $this->is_suspended; }

    // ─── Business Helpers (hanya admin) ──────────────────────────

    /**
     * Kredit saldo usaha setelah booking selesai.
     */
    public function creditBalance(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    /**
     * Debit saldo saat withdrawal diproses.
     *
     * @throws \App\Exceptions\InsufficientBalanceException
     */
    public function debitBalance(float $amount): void
    {
        if ($this->balance < $amount) {
            throw new \App\Exceptions\InsufficientBalanceException(
                "Saldo tidak mencukupi. Saldo: Rp {$this->balance_formatted}, dibutuhkan: Rp " . number_format($amount, 0, ',', '.')
            );
        }

        $this->decrement('balance', $amount);
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }

    public function getBalanceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->balance ?? 0, 0, ',', '.');
    }
}
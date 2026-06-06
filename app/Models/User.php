<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Favorit;
use App\Events\VerifikasiEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'password',
        'google_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi: satu user punya banyak pemesanan
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    // Relasi: mobil yang difavoritkan user ini
    public function favorits()
    {
        return $this->hasMany(Favorit::class);
    }

    // Relasi: pesan yang dikirim user ini
    public function pesanTerkirim()
    {
        return $this->hasMany(\App\Models\Pesan::class, 'pengirim_id');
    }

    // Relasi: pesan yang diterima user ini
    public function pesanDiterima()
    {
        return $this->hasMany(\App\Models\Pesan::class, 'penerima_id');
    }
    // Helper: cek apakah user ini sudah memfavoritkan mobil tertentu
    public function hasFavorited(int $mobilId): bool
    {
        return $this->favorits()->where('mobil_id', $mobilId)->exists();
    }

    /**
     * Override notifikasi verifikasi email bawaan Laravel
     * agar menggunakan template custom berbahasa Indonesia.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifikasiEmail());
    }
}
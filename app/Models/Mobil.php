<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Favorit;

class Mobil extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'merek',
        'tahun',
        'plat_nomor',
        'harga_per_hari',
        'status',
        'foto',
        'deskripsi',
    ];

    // Relasi: satu mobil punya banyak pemesanan
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    // Relasi: user yang memfavoritkan mobil ini
    public function favorits()
    {
        return $this->hasMany(Favorit::class);
    }

    // Helper: cek apakah mobil tersedia
    public function tersedia(): bool
    {
        return $this->status === 'tersedia';
    }
}
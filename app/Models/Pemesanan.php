<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mobil_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'total_harga'     => 'decimal:2',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Mobil
    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    // Helper: hitung durasi sewa dalam hari
    public function durasiHari(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai);
    }
}
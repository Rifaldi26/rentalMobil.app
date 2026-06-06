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
    // Tambahkan di dalam class Pemesanan

/**
 * CSS class untuk badge status.
 */
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'dikonfirmasi' => 'status-progress',
            'pending'      => 'status-pending',
            'selesai'      => 'status-confirmed',
            'dibatalkan'   => 'status-cancelled',
            default        => '',
        };
    }

    /**
     * Label teks untuk status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'dikonfirmasi' => 'Berjalan',
            'pending'      => 'Menunggu',
            'selesai'      => 'Selesai',
            'dibatalkan'   => 'Dibatalkan',
            default        => $this->status,
        };
    }
}
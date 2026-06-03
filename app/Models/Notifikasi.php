<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'link',
        'dibaca',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    // Relasi ke user pemilik notifikasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope: notifikasi belum dibaca
    public function scopeBelumDibaca($query)
    {
        return $query->where('dibaca', false);
    }

    // Helper: buat notifikasi untuk user tertentu
    public static function kirim(int $userId, string $judul, string $pesan, string $tipe = 'info', ?string $link = null): self
    {
        return self::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => $tipe,
            'link'    => $link,
            'dibaca'  => false,
        ]);
    }
}

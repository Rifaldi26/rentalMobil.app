<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $fillable = ['pengirim_id', 'penerima_id', 'isi', 'dibaca'];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    /**
     * Ambil semua pesan antara dua user, diurutkan dari lama ke baru.
     */
    public static function percakapan(int $userA, int $userB)
    {
        return static::where(function ($q) use ($userA, $userB) {
            $q->where('pengirim_id', $userA)->where('penerima_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('pengirim_id', $userB)->where('penerima_id', $userA);
        })->oldest()->get();
    }
}

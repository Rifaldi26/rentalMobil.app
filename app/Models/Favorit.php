<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorit extends Model
{
    protected $fillable = ['user_id', 'mobil_id'];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke mobil
    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }
}
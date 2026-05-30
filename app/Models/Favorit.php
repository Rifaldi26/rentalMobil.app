<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorit extends Model
{
    // Nama tabel di database
    protected $table = 'favorits';

    protected $fillable = ['user_id', 'mobil_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }
}
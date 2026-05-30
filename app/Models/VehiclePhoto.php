<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePhoto extends Model
{
    protected $fillable = ['vehicle_id', 'path', 'is_primary', 'order'];

    protected $casts = [
        'is_primary' => 'boolean',
        'order'      => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleAvailability extends Model
{
    protected $fillable = ['vehicle_id', 'blocked_date', 'reason'];

    protected $casts = [
        'blocked_date' => 'date',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}

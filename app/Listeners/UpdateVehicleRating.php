<?php

namespace App\Listeners;

use App\Events\ReviewCreated;
use App\Models\Review;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Recalculate rata-rata rating kendaraan setelah review baru masuk.
 * Menggunakan aggregate DB langsung untuk akurasi — bukan increment yang bisa drift.
 */
class UpdateVehicleRating implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'default';

    public function handle(ReviewCreated $event): void
    {
        $vehicle = $event->review->vehicle;

        $stats = Review::where('vehicle_id', $vehicle->id)
            ->where('is_visible', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as review_count')
            ->first();

        $vehicle->update([
            'avg_rating'   => round($stats->avg_rating ?? 0, 2),
            'review_count' => $stats->review_count ?? 0,
        ]);
    }
}

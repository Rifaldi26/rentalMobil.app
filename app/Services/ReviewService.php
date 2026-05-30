<?php

namespace App\Services;

use App\Exceptions\UnauthorizedBookingException;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    /**
     * Pelanggan submit ulasan setelah booking selesai.
     *
     * @throws UnauthorizedBookingException
     */
    public function create(User $user, Booking $booking, array $data): Review
    {
        if ($booking->user_id !== $user->id) {
            throw new UnauthorizedBookingException('Anda tidak berhak memberi ulasan pada booking ini.');
        }

        if (! $booking->isReviewable()) {
            throw new \RuntimeException('Booking ini belum selesai atau sudah diulas.');
        }

        return DB::transaction(function () use ($user, $booking, $data) {
            $review = Review::create([
                'booking_id' => $booking->id,
                'user_id'    => $user->id,
                'vehicle_id' => $booking->vehicle_id,
                'rating'     => $data['rating'],
                'comment'    => $data['comment'] ?? null,
                'is_visible' => true,
            ]);

            // Update rata-rata rating di vehicle
            $this->recalculateVehicleRating($booking->vehicle_id);

            return $review;
        });
    }

    /**
     * Admin hide/show ulasan yang dilaporkan.
     */
    public function toggleVisibility(Review $review): Review
    {
        $review->update(['is_visible' => ! $review->is_visible]);

        return $review->fresh();
    }

    private function recalculateVehicleRating(int $vehicleId): void
    {
        $stats = Review::where('vehicle_id', $vehicleId)
            ->where('is_visible', true)
            ->selectRaw('AVG(rating) as avg, COUNT(*) as total')
            ->first();

        \App\Models\Vehicle::where('id', $vehicleId)->update([
            'avg_rating'   => round($stats->avg, 2),
            'review_count' => $stats->total,
        ]);
    }
}
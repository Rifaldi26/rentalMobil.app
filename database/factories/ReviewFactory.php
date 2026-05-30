<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $booking = Booking::factory()->completed()->create();

        return [
            'booking_id' => $booking->id,
            'user_id'    => $booking->user_id,
            'vehicle_id' => $booking->vehicle_id,
            'rating'     => fake()->numberBetween(3, 5),
            'comment'    => fake()->paragraph(),
            'is_visible' => true,
        ];
    }

    public function hidden(): static
    {
        return $this->state(['is_visible' => false]);
    }
}

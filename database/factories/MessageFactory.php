<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $booking    = Booking::factory()->create();
        $senderId   = $booking->user_id;
        $receiverId = $booking->vehicle->partner->user_id;

        return [
            'booking_id'  => $booking->id,
            'sender_id'   => $senderId,
            'receiver_id' => $receiverId,
            'content'     => fake()->sentence(),
            'read_at'     => null,
        ];
    }

    public function read(): static
    {
        return $this->state(['read_at' => now()]);
    }
}

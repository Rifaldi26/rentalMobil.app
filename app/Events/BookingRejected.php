<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Booking $booking) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.' . $this->booking->user_id);
    }

    public function broadcastAs(): string
    {
        return 'booking.rejected';
    }

    public function broadcastWith(): array
    {
        return [
            'booking_id'      => $this->booking->id,
            'booking_code'    => $this->booking->booking_code,
            'rejected_reason' => $this->booking->rejected_reason,
        ];
    }
}

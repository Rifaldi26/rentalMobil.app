<?php

namespace App\Listeners;

use App\Events\BookingRejected;
use App\Mail\BookingRejectedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingRejectedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(BookingRejected $event): void
    {
        $booking = $event->booking->loadMissing(['user', 'vehicle']);

        Mail::to($booking->user->email)
            ->send(new BookingRejectedMail($booking));
    }
}

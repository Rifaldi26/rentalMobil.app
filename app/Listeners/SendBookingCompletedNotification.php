<?php

namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Mail\BookingCompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingCompletedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(BookingCompleted $event): void
    {
        $booking = $event->booking->loadMissing(['user', 'vehicle']);

        Mail::to($booking->user->email)
            ->send(new BookingCompletedMail($booking));
    }
}

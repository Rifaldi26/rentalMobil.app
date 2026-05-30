<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Mail\BookingConfirmedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Kirim notifikasi email ke pelanggan saat booking dikonfirmasi admin.
 * Berjalan di background queue agar response HTTP tetap cepat.
 */
class SendBookingConfirmedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking->loadMissing(['user', 'vehicle']);

        Mail::to($booking->user->email)
            ->send(new BookingConfirmedMail($booking));
    }
}

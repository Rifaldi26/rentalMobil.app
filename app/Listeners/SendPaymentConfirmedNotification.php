<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPaymentConfirmedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(PaymentConfirmed $event): void
    {
        $booking = $event->booking->loadMissing(['user', 'vehicle']);

        // TODO: kirim email konfirmasi pembayaran ke pelanggan
        // Mail::to($booking->user->email)->send(new PaymentConfirmedMail($booking));

        Log::info('Payment confirmed notification queued', [
            'booking_code' => $booking->booking_code,
            'user_email'   => $booking->user->email,
        ]);
    }
}
<?php

namespace App\Providers;

use App\Events\BookingCompleted;
use App\Events\BookingConfirmed;
use App\Events\BookingRejected;
use App\Events\PaymentConfirmed;
use App\Events\ReviewCreated;
use App\Listeners\SendBookingCompletedNotification;
use App\Listeners\SendBookingConfirmedNotification;
use App\Listeners\SendBookingRejectedNotification;
use App\Listeners\SendPaymentConfirmedNotification;
use App\Listeners\UpdateVehicleRating;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // ─── Booking events ──────────────────────────────────────
        BookingConfirmed::class => [
            SendBookingConfirmedNotification::class,
        ],
        BookingCompleted::class => [
            SendBookingCompletedNotification::class,
        ],
        BookingRejected::class => [
            SendBookingRejectedNotification::class,
        ],

        // ─── Payment events ───────────────────────────────────────
        PaymentConfirmed::class => [
            SendPaymentConfirmedNotification::class,
        ],

        // ─── Review events ────────────────────────────────────────
        ReviewCreated::class => [
            UpdateVehicleRating::class,
        ],
    ];
}

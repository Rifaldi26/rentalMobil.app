<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Vehicle;
use App\Models\Withdrawal;
use App\Policies\BookingPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\VehiclePolicy;
use App\Policies\WithdrawalPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Booking::class    => BookingPolicy::class,
        Vehicle::class    => VehiclePolicy::class,
        Review::class     => ReviewPolicy::class,
        Withdrawal::class => WithdrawalPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

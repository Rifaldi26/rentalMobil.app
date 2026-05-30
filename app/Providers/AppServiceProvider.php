<?php

namespace App\Providers;

use App\Services\BookingService;
use App\Services\ChatService;
use App\Services\PaymentService;
use App\Services\ReviewService;
use App\Services\VehicleService;
use App\Services\WithdrawalService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        URL::forceScheme('https');
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(BookingService::class, fn ($app) =>
            new BookingService($app->make(PaymentService::class))
        );
        $this->app->singleton(VehicleService::class);
        $this->app->singleton(ReviewService::class);
        $this->app->singleton(ChatService::class);
        $this->app->singleton(WithdrawalService::class);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
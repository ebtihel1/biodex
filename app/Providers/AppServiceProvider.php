<?php

namespace App\Providers;

use App\Models\Donation;
use App\Models\Order;
use App\Models\Reservation;
use App\Policies\DonationPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReservationPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Order::class => OrderPolicy::class,
        Donation::class => DonationPolicy::class,
        Reservation::class => ReservationPolicy::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

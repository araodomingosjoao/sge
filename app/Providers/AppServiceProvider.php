<?php

namespace App\Providers;

use App\Listeners\BeforeCreateSubscriber;
use App\Listeners\BeforeUpdateSubscriber;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
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
        Event::subscribe(BeforeCreateSubscriber::class);
        Event::subscribe(BeforeUpdateSubscriber::class);
    }
}

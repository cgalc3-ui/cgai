<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

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
        // Register SMS notification channel
        Notification::extend('fourjawaly-sms', function ($app) {
            return $app->make(\App\Notifications\Channels\FourJawalySmsChannel::class);
        });
    }
}

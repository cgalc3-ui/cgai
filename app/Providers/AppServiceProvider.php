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

        // Register Morph Map for Unified Rating System
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'service' => \App\Models\Service::class,
            'consultation' => \App\Models\Consultation::class,
            'ai_service' => \App\Models\AiService::class,
            'ready_app' => \App\Models\ReadyApp::class,
            'subscription' => \App\Models\Subscription::class,
        ]);
    }
}

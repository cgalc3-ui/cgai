<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule booking status updates every minute
Schedule::command('bookings:update-statuses')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

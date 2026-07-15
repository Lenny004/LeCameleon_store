<?php

use App\Jobs\ReleaseExpiredReservations;
use App\Jobs\SendAbandonedCartReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ReleaseExpiredReservations)->everyFiveMinutes();
Schedule::job(new SendAbandonedCartReminders)->daily();

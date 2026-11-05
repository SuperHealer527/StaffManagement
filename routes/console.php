<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('staff-management:synchronize-statuses')
    ->hourly()
    ->timezone('Asia/Tokyo')
    ->withoutOverlapping();

Schedule::command('staff-management:send-due-notifications')
    ->hourly()
    ->timezone('Asia/Tokyo')
    ->withoutOverlapping();

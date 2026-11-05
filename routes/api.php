<?php

use App\Http\Controllers\InboundEmailWebhookController;
use App\Http\Controllers\LineWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/line', LineWebhookController::class)
    ->middleware('throttle:120,1')
    ->name('line.webhook');

Route::post('/webhooks/inbound-email', InboundEmailWebhookController::class)
    ->middleware('throttle:120,1')
    ->name('inbound-email.webhook');

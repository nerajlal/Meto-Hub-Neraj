<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::command('billing:reconcile')->hourly()->withoutOverlapping();

\Illuminate\Support\Facades\Schedule::command('notifications:deliver-orders')->everyMinute()->withoutOverlapping();

Artisan::command('notifications:deliver-shipments', function () {
    \App\Models\CustomerShipmentAlert::whereNull('sent_at')->whereNull('skipped_at')->where('attempts', '<', 6)
        ->where(fn ($q) => $q->whereNull('retry_at')->orWhere('retry_at', '<=', now()))
        ->where(fn ($q) => $q->whereNull('queued_at')->orWhere('queued_at', '<', now()->subMinutes(10)))
        ->chunkById(100, function ($alerts) {
            foreach ($alerts as $alert) {
                try { app(\App\Services\CustomerShipmentNotifications::class)->enqueue($alert); }
                catch (\Throwable $e) { report($e); }
            }
        });
})->purpose('Queue pending customer shipment emails');

\Illuminate\Support\Facades\Schedule::command('notifications:deliver-shipments')->everyMinute()->withoutOverlapping();

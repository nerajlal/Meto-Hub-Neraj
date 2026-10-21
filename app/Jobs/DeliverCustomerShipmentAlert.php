<?php

namespace App\Jobs;

use App\Services\CustomerShipmentNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeliverCustomerShipmentAlert implements ShouldQueue
{
    use Queueable;
    public int $tries = 1;
    public int $timeout = 240;
    public function __construct(public int $alertId) {}
    public function handle(CustomerShipmentNotifications $notifications): void { $notifications->deliver($this->alertId); }
}

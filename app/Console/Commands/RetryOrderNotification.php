<?php

namespace App\Console\Commands;

use App\Models\MerchantOrderAlert;
use App\Services\MerchantOrderNotifications;
use Illuminate\Console\Command;

class RetryOrderNotification extends Command
{
    protected $signature = 'notifications:retry-order {alert : Notification ID, not the order number}';

    protected $description = 'Retry undelivered channels for one order notification; successful channels are preserved';

    public function handle(MerchantOrderNotifications $notifications): int
    {
        $alert = MerchantOrderAlert::find($this->argument('alert'));
        if (! $alert) {
            $this->error('Notification not found.');

            return self::FAILURE;
        }
        if (! $alert->emailed_at) {
            $alert->update(['email_attempts' => 0, 'email_retry_at' => null, 'email_error' => null]);
        }
        $alert->deliveries()->whereNull('sent_at')->whereHas('subscription', fn ($q) => $q->where('enabled', true))->update(['attempts' => 0, 'retry_at' => null, 'failed_at' => null, 'last_error' => null]);
        $notifications->enqueue($alert);
        $this->info('Undelivered channels queued or already awaiting a worker.');

        return self::SUCCESS;
    }
}

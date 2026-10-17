<?php

namespace App\Console\Commands;

use App\Models\MerchantOrderAlert;
use App\Services\MerchantOrderNotifications;
use Illuminate\Console\Command;

class DeliverOrderNotifications extends Command
{
    protected $signature = 'notifications:deliver-orders';

    protected $description = 'Queue due owner order notifications from the durable outbox';

    public function handle(MerchantOrderNotifications $notifications): int
    {
        $count = 0;
        MerchantOrderAlert::where(fn ($q) => $q->whereNull('queued_at')->orWhere('queued_at', '<', now()->subMinutes(10)))
            ->where(function ($q) {
                $q->where(function ($mail) {
                    $mail->whereNull('emailed_at')->where('email_attempts', '<', 6)->where(fn ($retry) => $retry->whereNull('email_retry_at')->orWhere('email_retry_at', '<=', now()));
                })
                    ->orWhereHas('deliveries', fn ($push) => $push->whereNull('sent_at')->whereNull('failed_at')->where('attempts', '<', 6)->where(fn ($retry) => $retry->whereNull('retry_at')->orWhere('retry_at', '<=', now())));
            })
            ->orderBy('id')->limit(200)->get()->each(function ($alert) use ($notifications, &$count) {
                if ($notifications->enqueue($alert)) {
                    $count++;
                }
            });
        $this->info('Queued '.$count.' owner order alert(s).');

        return self::SUCCESS;
    }
}

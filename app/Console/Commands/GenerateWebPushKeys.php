<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateWebPushKeys extends Command
{
    protected $signature = 'notifications:vapid-keys';

    protected $description = 'Generate VAPID keys for the server environment; keep the private key secret';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();
        $this->line('WEBPUSH_VAPID_PUBLIC_KEY='.$keys['publicKey']);
        $this->line('WEBPUSH_VAPID_PRIVATE_KEY='.$keys['privateKey']);
        $this->warn('Store these in the server .env, never in source control. Do not rotate keys while devices are subscribed.');

        return self::SUCCESS;
    }
}

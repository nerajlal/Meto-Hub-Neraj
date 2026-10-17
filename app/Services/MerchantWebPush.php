<?php

namespace App\Services;

use App\Models\MerchantPushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class MerchantWebPush
{
    public function configured(): bool
    {
        return (bool) config('order_notifications.vapid.public_key') && (bool) config('order_notifications.vapid.private_key');
    }

    public function allowedEndpoint(string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');

        return ($parts['scheme'] ?? '') === 'https' && ! isset($parts['user']) && ! isset($parts['pass']) && ! isset($parts['fragment']) && (! isset($parts['port']) || $parts['port'] === 443)
         && (in_array($host, config('order_notifications.endpoint_hosts', []), true) || str_ends_with($host, '.notify.windows.com'));
    }

    public function send(MerchantPushSubscription $device, array $payload): array
    {
        if (! $this->configured()) {
            throw new \RuntimeException('Web Push is not configured.');
        }
        if (! $this->allowedEndpoint($device->endpoint)) {
            return ['success' => false, 'expired' => true];
        }
        $push = new WebPush(['VAPID' => ['subject' => config('order_notifications.vapid.subject'), 'publicKey' => config('order_notifications.vapid.public_key'), 'privateKey' => config('order_notifications.vapid.private_key')]], ['TTL' => 3600, 'urgency' => 'high'], 15, ['allow_redirects' => false, 'connect_timeout' => 5]);
        $report = $push->sendOneNotification(Subscription::create(['endpoint' => $device->endpoint, 'publicKey' => $device->public_key, 'authToken' => $device->auth_token, 'contentEncoding' => 'aes128gcm']), json_encode($payload, JSON_THROW_ON_ERROR));

        return ['success' => $report->isSuccess(), 'expired' => $report->isSubscriptionExpired()];
    }
}

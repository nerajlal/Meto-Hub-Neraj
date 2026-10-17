<?php

namespace App\Services;

use App\Jobs\DeliverMerchantOrderAlert;
use App\Mail\NewStoreOrderMail;
use App\Models\MerchantOrderAlert;
use App\Models\MerchantPushDelivery;
use App\Models\MerchantPushSubscription;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MerchantOrderNotifications
{
    public function record(Order $order): MerchantOrderAlert
    {
        $owner = User::withoutGlobalScopes()->where('tenant_id', $order->tenant_id)->where('type', 'admin')->oldest('id')->first();
        $alert = MerchantOrderAlert::firstOrCreate(['order_id' => $order->id], ['tenant_id' => $order->tenant_id, 'user_id' => $owner?->id]);
        if ($alert->wasRecentlyCreated && $owner) {
            foreach (MerchantPushSubscription::where('tenant_id', $order->tenant_id)->where('user_id', $owner->id)->where('enabled', true)->get() as $device) {
                MerchantPushDelivery::firstOrCreate(['merchant_order_alert_id' => $alert->id, 'merchant_push_subscription_id' => $device->id]);
            }
            DB::afterCommit(function () use ($alert) {
                try {
                    $this->enqueue($alert);
                } catch (\Throwable $e) {
                    report($e);
                }
            });
        }

        return $alert;
    }

    public function owner(MerchantOrderAlert $alert): ?User
    {
        return User::withoutGlobalScopes()->where('tenant_id', $alert->tenant_id)->where('type', 'admin')->whereKey($alert->user_id)->first();
    }

    public function enqueue(MerchantOrderAlert $alert): bool
    {
        $connection = config('order_notifications.queue_connection');
        $driver = config('queue.connections.'.$connection.'.driver');
        if (! in_array($driver, ['database', 'redis', 'sqs', 'beanstalkd'], true)) {
            throw new \RuntimeException('Owner notifications require a persistent background queue connection.');
        }
        $claimed = MerchantOrderAlert::whereKey($alert->id)->where(fn ($q) => $q->whereNull('queued_at')->orWhere('queued_at', '<', now()->subMinutes(10)))->update(['queued_at' => now()]);
        if (! $claimed) {
            return false;
        }
        try {
            DeliverMerchantOrderAlert::dispatch($alert->id)->onConnection(config('order_notifications.queue_connection'))->onQueue(config('order_notifications.queue'));
        } catch (\Throwable $e) {
            MerchantOrderAlert::whereKey($alert->id)->update(['queued_at' => null]);
            throw $e;
        }

        return true;
    }

    public function orderUrl(MerchantOrderAlert $alert): string
    {
        return rtrim(config('app.url'), '/').'/'.$alert->tenant_id.'/admin/orders/'.$alert->order_id.'?notification_alert='.$alert->id;
    }

    public function deliver(int $id): void
    {
        $lock = Cache::lock('merchant-order-alert-'.$id, 300);
        if (! $lock->get()) {
            return;
        }
        try {
            $alert = MerchantOrderAlert::find($id);
            if (! $alert) {
                return;
            }
            $owner = $this->owner($alert);
            if (! $owner) {
                $owner = User::withoutGlobalScopes()->where('tenant_id', $alert->tenant_id)->where('type', 'admin')->oldest('id')->first();
                if (! $owner) {
                    $alert->update(['queued_at' => null, 'email_retry_at' => now()->addHour(), 'email_error' => 'Store owner unavailable']);

                    return;
                }
                $alert->update(['user_id' => $owner->id, 'read_at' => null]);
            }
            $order = Order::withoutGlobalScopes()->where('tenant_id', $alert->tenant_id)->with('items')->find($alert->order_id);
            $tenant = Tenant::find($alert->tenant_id);
            if (! $order || ! $tenant) {
                return;
            }
            if (! $alert->emailed_at && $alert->email_attempts < 6 && (! $alert->email_retry_at || $alert->email_retry_at->isPast())) {
                $alert->increment('email_attempts');
                try {
                    if (! filter_var($owner->email, FILTER_VALIDATE_EMAIL) || ($owner->email_verification_required && ! $owner->email_verified_at)) {
                        throw new \RuntimeException('Owner email is unavailable or unverified.');
                    }
                    if (in_array(config('mail.default'), ['log', 'array'])) {
                        throw new \RuntimeException('A delivery mail provider must be configured.');
                    }
                    Mail::to($owner->email)->send(new NewStoreOrderMail($order, $tenant->name, $this->orderUrl($alert), $tenant->getRawOriginal('currency') ?: 'INR'));
                    $alert->update(['emailed_at' => now(), 'email_error' => null, 'email_retry_at' => null]);
                } catch (\Throwable $e) {
                    $alert->update(['email_error' => 'Owner email delivery failed; check mail settings and worker logs.', 'email_retry_at' => now()->addMinutes(min(360, 5 * 2 ** ($alert->email_attempts - 1)))]);
                    \Illuminate\Support\Facades\Log::warning('Owner email delivery failed', ['alert_id' => $alert->id, 'error_class' => get_class($e)]);
                }
            }
            $push = app(MerchantWebPush::class);
            foreach ($alert->deliveries()->whereNull('sent_at')->whereNull('failed_at')->where('attempts', '<', 6)->where(fn ($q) => $q->whereNull('retry_at')->orWhere('retry_at', '<=', now()))->with('subscription')->get() as $delivery) {
                $device = $delivery->subscription;
                if (! $device || ! $device->enabled || (int) $device->tenant_id !== (int) $alert->tenant_id || (int) $device->user_id !== (int) $owner->id) {
                    $delivery->update(['failed_at' => now(), 'last_error' => 'Device no longer belongs to this recipient']);

                    continue;
                }
                if (! $push->configured()) {
                    $delivery->update(['retry_at' => now()->addMinutes(15), 'last_error' => 'Web Push configuration missing']);

                    continue;
                }
                $delivery->increment('attempts');
                try {
                    // Recipient ownership is checked above; no customer details appear on lock screens.
                    $result = $push->send($device, ['title' => 'New store order', 'body' => 'Your store has received a new order. Open MetoHub to review it.', 'tag' => 'metohub-order-'.$alert->id, 'url' => '/'.$alert->tenant_id.'/admin/notifications/'.$alert->id.'/open']);
                    if ($result['success']) {
                        $delivery->update(['sent_at' => now(), 'last_error' => null]);
                    } elseif ($result['expired']) {
                        $device->update(['enabled' => false]);
                        $delivery->update(['failed_at' => now(), 'last_error' => 'Push subscription expired']);
                    } else {
                        throw new \RuntimeException('Push service did not accept notification.');
                    }
                } catch (\Throwable $e) {
                    $delivery->update(['last_error' => 'Push delivery failed', 'retry_at' => now()->addMinutes(min(360, 5 * 2 ** ($delivery->attempts - 1)))]);
                    \Illuminate\Support\Facades\Log::warning('Owner push delivery failed', ['alert_id' => $alert->id, 'device_id' => $device->id, 'error_class' => get_class($e)]);
                }
            }
            $alert->update(['queued_at' => null]);
        } finally {
            $lock->release();
        }
    }
}

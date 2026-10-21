<?php

namespace App\Services;

use App\Jobs\DeliverCustomerShipmentAlert;
use App\Mail\OrderShippedMail;
use App\Models\CustomerShipmentAlert;
use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CustomerShipmentNotifications
{
    public function fingerprint(Order $order): string
    {
        return hash('sha256', json_encode([$order->tracking_number, $order->delivery_partner_id, $order->customer_email]));
    }

    public function record(Order $order): void
    {
        if ($order->status !== 'shipped' || !trim((string) $order->tracking_number) || !filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) return;
        $alert = CustomerShipmentAlert::firstOrCreate(['order_id' => $order->id, 'fingerprint' => $this->fingerprint($order)],
            ['tenant_id' => $order->tenant_id, 'recipient' => $order->customer_email]);
        DB::afterCommit(function () use ($alert) {
            try { $this->enqueue($alert); } catch (\Throwable $e) { report($e); }
        });
    }

    public function enqueue(CustomerShipmentAlert $alert): void
    {
        $connection = config('order_notifications.queue_connection');
        if (!in_array(config('queue.connections.'.$connection.'.driver'), ['database', 'redis', 'sqs', 'beanstalkd'], true)) {
            throw new \RuntimeException('Shipment emails require a persistent background queue.');
        }
        $claimed = CustomerShipmentAlert::whereKey($alert->id)->whereNull('sent_at')->whereNull('skipped_at')->where('attempts', '<', 6)
            ->where(fn ($q) => $q->whereNull('retry_at')->orWhere('retry_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('queued_at')->orWhere('queued_at', '<', now()->subMinutes(10)))
            ->update(['queued_at' => now()]);
        if (!$claimed) return;
        try {
            DeliverCustomerShipmentAlert::dispatch($alert->id)->onConnection($connection)->onQueue(config('order_notifications.queue'));
        } catch (\Throwable $e) {
            CustomerShipmentAlert::whereKey($alert->id)->update(['queued_at' => null]);
            throw $e;
        }
    }

    public function deliver(int $id): void
    {
        $lock = Cache::lock('customer-shipment-'.$id, 300);
        if (!$lock->get()) return;
        try {
            $alert = CustomerShipmentAlert::find($id);
            if (!$alert || $alert->sent_at || $alert->skipped_at || $alert->attempts >= 6 || $alert->retry_at?->isFuture()) return;
            $order = Order::withoutGlobalScopes()->where('tenant_id', $alert->tenant_id)->find($alert->order_id);
            $tenant = Tenant::find($alert->tenant_id);
            if (!$order || !$tenant || $order->status !== 'shipped' || $this->fingerprint($order) !== $alert->fingerprint) {
                $alert->update(['skipped_at' => now()]);
                return;
            }
            // Queue workers have no tenant request context; explicitly constrain the relation.
            $order->setRelation('deliveryPartner', \App\Models\DeliveryPartner::withoutGlobalScopes()->where('tenant_id', $alert->tenant_id)->find($order->delivery_partner_id));
            $alert->increment('attempts');
            try {
                Mail::to($alert->recipient)->send(new OrderShippedMail($order, $tenant->name));
                $alert->update(['sent_at' => now(), 'queued_at' => null, 'retry_at' => null]);
            } catch (\Throwable $e) {
                $alert->update(['queued_at' => null, 'retry_at' => now()->addMinutes(min(60, 5 * $alert->attempts))]);
                report($e);
            }
        } finally { $lock->release(); }
    }
}

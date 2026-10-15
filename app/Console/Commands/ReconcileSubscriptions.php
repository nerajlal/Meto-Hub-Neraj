<?php

namespace App\Console\Commands;

use App\Models\StoreSubscription;
use App\Models\Tenant;
use App\Services\SubscriptionBilling;
use Illuminate\Console\Command;

class ReconcileSubscriptions extends Command
{
    protected $signature = 'billing:reconcile {--tenant= : Only synchronize this store}';

    protected $description = 'Synchronize subscription, invoice and payment records from Razorpay without creating charges';

    public function handle(SubscriptionBilling $billing): int
    {
        $failures = 0;
        $tenants = Tenant::whereNotNull('razorpay_subscription_id');
        if ($this->option('tenant')) {
            $tenants->whereKey($this->option('tenant'));
        }
        $tenants->eachById(fn ($tenant) => $billing->local($tenant));
        $query = StoreSubscription::query();
        if ($this->option('tenant')) {
            $query->where('tenant_id', $this->option('tenant'));
        }
        $query->eachById(function ($subscription) use ($billing, &$failures) {
            try {
                $billing->reconcile($subscription);
                $this->info('Store '.$subscription->tenant_id.': synchronized.');
            } catch (\Throwable $e) {
                $failures++;
                $this->error('Store '.$subscription->tenant_id.': synchronization failed ('.class_basename($e).').');
            }
        });

        return $failures ? self::FAILURE : self::SUCCESS;
    }
}

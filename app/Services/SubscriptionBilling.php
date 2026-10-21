<?php

namespace App\Services;

use App\Models\StoreSubscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPayment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class SubscriptionBilling
{
    public function api()
    {
        abort_unless(config('services.razorpay.key') && config('services.razorpay.secret'), 503, 'Subscription billing is not configured.');

        return app(Api::class, ['key' => config('services.razorpay.key'), 'secret' => config('services.razorpay.secret')]);
    }

    public function array($entity): array
    {
        return is_array($entity) ? $entity : (method_exists($entity, 'toArray') ? $entity->toArray() : (array) $entity);
    }

    public function local(Tenant $tenant): ?StoreSubscription
    {
        if ($uncertain = $tenant->subscriptions()->where('status', 'creating')->whereNull('gateway_id')->latest('id')->first()) {
            return $uncertain;
        }
        if (! $tenant->razorpay_subscription_id) {
            return $tenant->subscriptions()->latest('id')->first();
        }
        $local = StoreSubscription::firstOrCreate(['gateway_id' => $tenant->razorpay_subscription_id], [
            'tenant_id' => $tenant->id, 'checkout_attempt' => (string) Str::uuid(),
            'status' => $tenant->subscription_status ?: 'pending', 'plan' => $tenant->plan,
        ]);
        abort_unless((int) $local->tenant_id === (int) $tenant->id, 409, 'Subscription ownership requires review.');

        return $local;
    }

    public function start(Tenant $tenant, string $tier, string $cycle): StoreSubscription
    {
        abort_unless(in_array($cycle, ['monthly', 'yearly'], true), 422, 'Choose monthly or yearly billing.');
        abort_unless(config('services.razorpay.webhook_secret'), 503, 'Subscription webhooks must be configured before starting checkout.');
        return Cache::lock('billing-tenant-'.$tenant->id, 600)->block(5, function () use ($tenant, $tier, $cycle) {
            $tenant->refresh();
            $current = $tenant->subscriptions()->whereNotIn('status', ['cancelled', 'completed', 'expired'])->latest('id')->first() ?? $this->local($tenant);
            if ($current?->gateway_id && $tenant->razorpay_subscription_id !== $current->gateway_id) {
                $tenant->update(['razorpay_subscription_id' => $current->gateway_id, 'subscription_status' => $current->status]);
            }
            if ($current && ! in_array($current->status, ['cancelled', 'completed', 'expired'], true)) {
                if ($current->gateway_id && $current->plan === $tier && $current->cycle === $cycle && in_array($current->status, ['created', 'pending'], true) && ! $current->paid_count) {
                    $current = $this->sync($current);
                    abort_unless(in_array($current->status, ['created', 'pending'], true) && ! $current->paid_count, 409, 'An existing subscription is already authorized. Manage it from Billing.');

                    return $current;
                }
                abort(409, 'A subscription or checkout attempt already exists. Refresh its status or cancel it before starting another.');
            }
            if ($current?->gateway_id && in_array($current->status, ['cancelled', 'completed', 'expired'], true)) {
                $current = $this->sync($current);
                abort_unless(in_array($current->status, ['cancelled', 'completed', 'expired'], true), 409, 'The provider reports an existing subscription. Refresh Billing before starting another checkout.');
                abort_if($current->paid_count > 0 && ! $current->paid_through, 409, 'The previous paid period needs provider review before another subscription is created.');
            }
            abort_if($current?->paid_through?->isFuture() && $current->paid_count > 0, 409, 'The existing paid subscription period has not ended yet.');
            $api = $this->api();
            $definition = config('saas.plans.'.$tier);
            abort_unless($definition, 422, 'Choose a current subscription plan.');
            $amount = $definition['monthly_price'] * 100 * ($cycle === 'yearly' ? config('saas.annual_months') : 1);
            $planName = 'MetoHub '.$definition['name'].($cycle === 'yearly' ? ' Yearly' : '');
            $planId = $this->resolvePlan($api, $tier, $cycle, $planName, $amount);
            $tenant->subscriptions()->whereIn('status', ['cancelled', 'completed', 'expired'])->where(fn ($q) => $q->whereNull('paid_through')->orWhere('paid_through', '<=', now()))->update(['checkout_guard' => null]);
            // Persist before making a remote request. Uncertain attempts must be reconciled, never blindly recreated.
            $local = StoreSubscription::create(['tenant_id' => $tenant->id, 'checkout_attempt' => (string) Str::uuid(),
                'plan' => $tier, 'cycle' => $cycle, 'amount' => $amount, 'currency' => 'INR', 'status' => 'creating', 'checkout_guard' => $tenant->id]);
            $local->update(['gateway_plan_id' => $planId]);
            $remote = $this->array($api->subscription->create(['plan_id' => $planId, 'customer_notify' => 1,
                'total_count' => $cycle === 'yearly' ? 10 : 120, 'notes' => ['tenant_id' => $tenant->id, 'checkout_attempt' => $local->checkout_attempt]]));
            abort_unless(! empty($remote['id']), 502, 'Provider did not return a subscription reference.');
            $local->update(['gateway_id' => $remote['id'], 'status' => $remote['status'] ?? 'created']);
            // The selected plan becomes an entitlement only after a verified paid period is recorded.
            $tenant->update(['razorpay_subscription_id' => $local->gateway_id, 'subscription_status' => 'pending']);

            return $local;
        });
    }

    private function resolvePlan($api, string $tier, string $cycle, string $name, int $amount): string
    {
        $matches = fn ($plan) => (int) ($plan['item']['amount'] ?? 0) === $amount
            && ($plan['item']['currency'] ?? '') === 'INR' && ($plan['period'] ?? '') === $cycle
            && (int) ($plan['interval'] ?? 0) === 1;
        if ($id = config('services.razorpay.plans.'.$tier.'.'.$cycle)) {
            $plan = $this->array($api->plan->fetch($id));
            abort_unless(($plan['id'] ?? '') === $id && $matches($plan), 422, 'The configured Razorpay plan does not match the selected price, currency or billing period. Contact support.');

            return $id;
        }

        // Serialize plan discovery across tenants in this application/account.
        return Cache::lock('billing-plan-'.hash('sha256', config('services.razorpay.key').'|'.$tier.'|'.$cycle.'|'.$amount), 120)->block(5, function () use ($api, $matches, $name, $cycle, $amount) {
            for ($skip = 0; $skip < 10000; $skip += 100) {
                $page = $this->array($api->plan->all(['count' => 100, 'skip' => $skip]));
                $items = $page['items'] ?? null;
                abort_unless(is_array($items), 502, 'Unable to confirm the provider plan list.');
                foreach ($items as $plan) {
                    if (($plan['item']['name'] ?? '') === $name && $matches($plan) && ! empty($plan['id'])) return $plan['id'];
                }
                if (count($items) < 100) {
                    $plan = $this->array($api->plan->create(['period' => $cycle, 'interval' => 1,
                        'item' => ['name' => $name, 'amount' => $amount, 'currency' => 'INR']]));
                    abort_unless(! empty($plan['id']), 502, 'Provider did not return a plan reference.');

                    return $plan['id'];
                }
            }
            abort(409, 'Plan discovery requires support review. Configure a Razorpay plan ID before checkout.');
        });
    }

    public function reconcile(StoreSubscription $subscription): StoreSubscription
    {
        if (app()->environment(['local', 'testing']) && $subscription->is_demo) {
            return $subscription;
        }

        if ($subscription->gateway_id) {
            return $this->sync($subscription);
        }

        return Cache::lock('billing-tenant-'.$subscription->tenant_id, 600)->block(5, function () use ($subscription) {
            $subscription->refresh();
            if ($subscription->gateway_id) {
                return $this->sync($subscription);
            }
            $found = null;
            for ($skip = 0; $skip < 10000; $skip += 100) {
                $page = $this->array($this->api()->subscription->all(['count' => 100, 'skip' => $skip]));
                foreach ($page['items'] ?? [] as $remote) {
                    if (($remote['notes']['checkout_attempt'] ?? '') === $subscription->checkout_attempt
                        && (string) ($remote['notes']['tenant_id'] ?? '') === (string) $subscription->tenant_id) {
                        $found = $remote;
                        break;
                    }
                }
                if ($found || count($page['items'] ?? []) < 100) {
                    break;
                }
            }
            abort_unless($found, 409, 'The checkout outcome remains uncertain. Contact support for provider review; do not pay again.');
            $subscription->update(['gateway_id' => $found['id']]);
            $subscription->tenant->update(['razorpay_subscription_id' => $found['id']]);

            return $this->sync($subscription);
        });
    }

    public function sync(StoreSubscription $subscription): StoreSubscription
    {
        abort_unless($subscription->gateway_id, 409, 'This checkout has an uncertain provider result. Run billing reconciliation before retrying.');

        return Cache::lock('billing-sync-'.$subscription->id, 120)->block(5, function () use ($subscription) {
            $api = $this->api();
            $remote = $this->array($api->subscription->fetch($subscription->gateway_id));
            abort_unless(($remote['id'] ?? '') === $subscription->gateway_id, 502, 'Subscription reference mismatch.');
            abort_if($subscription->gateway_plan_id && ($remote['plan_id'] ?? '') !== $subscription->gateway_plan_id, 502, 'Subscription plan reference mismatch.');
            if (! $subscription->cycle && ! empty($remote['plan_id'])) {
                $plan = $this->array($api->plan->fetch($remote['plan_id']));
                $subscription->update(['cycle' => $plan['period'] ?? null, 'amount' => $plan['item']['amount'] ?? null, 'currency' => $plan['item']['currency'] ?? 'INR']);
            }
            $subscription = $this->snapshot($subscription, $remote);
            $invoices = [];
            // Fetch every page before marking invoice reconciliation complete.
            for ($skip = 0; ; $skip += 100) {
                abort_if($skip >= 10000, 502, 'Invoice reconciliation requires an extended history import.');
                $page = $this->array($api->invoice->all(['subscription_id' => $subscription->gateway_id, 'count' => 100, 'skip' => $skip]));
                $items = $page['items'] ?? [];
                foreach ($items as $invoice) {
                    abort_unless(($invoice['subscription_id'] ?? '') === $subscription->gateway_id, 502, 'Invoice ownership mismatch.');
                    $invoices[] = $invoice;
                }
                if (count($items) < 100) {
                    break;
                }
            }
            foreach ($invoices as $invoice) {
                $this->invoice($subscription, $invoice);
                if (! empty($invoice['payment_id'])) {
                    $payment = $this->array($api->payment->fetch($invoice['payment_id']));
                    abort_unless(($payment['invoice_id'] ?? '') === $invoice['id'], 502, 'Payment invoice mismatch.');
                    $payment['paid_at'] = $invoice['paid_at'] ?? null;
                    $this->payment($subscription, $payment);
                }
            }
            $paid = $subscription->invoices()->where('status', 'paid')->where('amount_paid', '>', 0)->where('amount_due', 0)
                ->where('currency', $subscription->currency)->whereNotNull('billing_start')->whereNotNull('billing_end');
            $period = (clone $paid)->where('billing_start', '<=', now())->orderByDesc('billing_end')->first()
                ?? $paid->orderBy('billing_start')->first();
            $subscription->update(['invoices_synced_at' => now(), 'paid_from' => $period?->billing_start, 'paid_through' => $period?->billing_end]);
            if (in_array($subscription->status, ['cancelled', 'completed', 'expired'], true) && ! $subscription->paid_through?->isFuture()) {
                $subscription->update(['checkout_guard' => null]);
            }
            if ($period?->billing_start?->lte(now()) && $period?->billing_end?->isFuture() && isset(config('saas.plans')[$subscription->plan])) {
                Tenant::whereKey($subscription->tenant_id)->where('razorpay_subscription_id', $subscription->gateway_id)->update(['plan' => $subscription->plan]);
            }

            return $subscription->fresh();
        });
    }

    public function snapshot(StoreSubscription $local, array $remote): StoreSubscription
    {
        return DB::transaction(function () use ($local, $remote) {
            $local = StoreSubscription::lockForUpdate()->findOrFail($local->id);
            $state = $remote['status'] ?? '';
            abort_unless(in_array($state, ['created', 'authenticated', 'active', 'pending', 'halted', 'paused', 'cancelled', 'completed', 'expired'], true), 502, 'Unknown provider subscription state.');
            $local->fill(['status' => $state, 'gateway_plan_id' => $remote['plan_id'] ?? $local->gateway_plan_id,
                'paid_count' => (int) ($remote['paid_count'] ?? 0), 'synced_at' => now()]);
            foreach (['current_start', 'current_end', 'ended_at'] as $key) {
                $local->{$key} = $this->date($remote[$key] ?? null);
            }
            $local->next_charge_at = $this->date($remote['charge_at'] ?? null);
            $local->save();
            $tenant = Tenant::lockForUpdate()->findOrFail($local->tenant_id);
            if ($tenant->razorpay_subscription_id === $local->gateway_id) {
                $values = ['subscription_status' => $state];
                if (! empty($remote['customer_id'])) {
                    $values['razorpay_customer_id'] = $remote['customer_id'];
                }
                $tenant->update($values);
            }

            return $local;
        });
    }

    public function payment(StoreSubscription $subscription, array $data): void
    {
        abort_unless(! empty($data['id']) && isset($data['amount'], $data['currency'], $data['status']), 422, 'Incomplete payment record.');
        $existing = SubscriptionPayment::where('gateway_id', $data['id'])->first();
        abort_if($existing && $existing->store_subscription_id !== $subscription->id, 422, 'Payment already belongs to another store.');
        // Never store card, contact, token, VPA or raw gateway payload fields.
        SubscriptionPayment::updateOrCreate(['gateway_id' => $data['id']], [
            'store_subscription_id' => $subscription->id, 'invoice_id' => $data['invoice_id'] ?? null,
            'amount' => (int) $data['amount'], 'amount_refunded' => (int) ($data['amount_refunded'] ?? 0),
            'currency' => $data['currency'], 'status' => $data['status'], 'method' => $data['method'] ?? null,
            'attempted_at' => $this->date($data['created_at'] ?? null),
            'paid_at' => $data['status'] === 'captured' ? ($existing?->paid_at ?? $this->date($data['paid_at'] ?? $data['created_at'] ?? null)) : $existing?->paid_at,
        ]);
    }

    public function invoice(StoreSubscription $subscription, array $data): void
    {
        $existing = SubscriptionInvoice::where('gateway_id', $data['id'])->first();
        abort_if($existing && $existing->store_subscription_id !== $subscription->id, 422, 'Invoice already belongs to another store.');
        SubscriptionInvoice::updateOrCreate(['gateway_id' => $data['id']], [
            'store_subscription_id' => $subscription->id, 'payment_id' => $data['payment_id'] ?? null,
            'status' => $data['status'], 'currency' => $data['currency'], 'amount' => (int) $data['amount'],
            'amount_paid' => (int) ($data['amount_paid'] ?? 0), 'amount_due' => (int) ($data['amount_due'] ?? 0),
            'issued_at' => $this->date($data['issued_at'] ?? null), 'paid_at' => $this->date($data['paid_at'] ?? null), 'due_at' => $this->date($data['expire_by'] ?? null),
            'billing_start' => $this->date($data['billing_start'] ?? null), 'billing_end' => $this->date($data['billing_end'] ?? null),
        ]);
    }

    public function cancel(StoreSubscription $subscription): StoreSubscription
    {
        return Cache::lock('billing-tenant-'.$subscription->tenant_id, 120)->block(5, function () use ($subscription) {
            $subscription = $this->sync($subscription);
            if ($subscription->cancel_at_cycle_end || in_array($subscription->status, ['cancelled', 'completed', 'expired'], true)) {
                return $subscription;
            }
            $atEnd = $subscription->paid_count > 0 && $subscription->paid_through?->isFuture();
            $this->api()->subscription->fetch($subscription->gateway_id)->cancel(['cancel_at_cycle_end' => $atEnd ? 1 : 0]);
            $subscription->update(['cancel_at_cycle_end' => (bool) $atEnd]);

            return $this->sync($subscription);
        });
    }

    private function date($timestamp): ?Carbon
    {
        return is_numeric($timestamp) && (int) $timestamp > 0 ? Carbon::createFromTimestamp((int) $timestamp) : null;
    }
}

<?php

namespace App\Services;

use App\Models\Tenant;

class PlanAccess
{
    public function inTrial(Tenant $tenant): bool
    {
        return now()->lt($tenant->created_at->copy()->addDays(config('saas.trial_days', 7)));
    }

    public function hasPaidAccess(Tenant $tenant): bool
    {
        $subscription = $tenant->subscriptions()->where('gateway_id', $tenant->razorpay_subscription_id)->first();
        if (! $subscription || (! $subscription->synced_at && ! $subscription->cycle && $subscription->status === 'active')) {
            return false;
        } // A legacy status alone cannot establish a paid billing period.

        return $subscription->paid_count > 0 && $subscription->paid_from?->lte(now()) && $subscription->paid_through?->isFuture()
            && in_array($subscription->status, ['active', 'pending', 'halted', 'paused', 'cancelled', 'completed'], true);
    }

    public function adminAvailable(Tenant $tenant): bool
    {
        return $this->inTrial($tenant) || $this->hasPaidAccess($tenant);
    }

    public function expiredAt(Tenant $tenant): \Carbon\Carbon
    {
        $end = $tenant->created_at->copy()->addDays(config('saas.trial_days', 7));
        $paidEnd = $tenant->subscriptions()->where('paid_count', '>', 0)->where('paid_from', '<=', now())->max('paid_through');
        if ($paidEnd && \Carbon\Carbon::parse($paidEnd)->gt($end)) {
            $end = \Carbon\Carbon::parse($paidEnd);
        }

        return $end;
    }

    public function storefrontGraceEndsAt(Tenant $tenant): \Carbon\Carbon
    {
        return $this->expiredAt($tenant)->addDays(config('saas.storefront_grace_days', 30));
    }

    public function storefrontAvailable(Tenant $tenant): bool
    {
        return $this->adminAvailable($tenant) || now()->lt($this->storefrontGraceEndsAt($tenant));
    }

    public function storefrontDomainAvailable(Tenant $tenant): bool
    {
        if (app(StoreDomains::class)->availableHostname((string) $tenant->domain)) {
            return $this->storefrontAvailable($tenant);
        }

        return $this->allows($tenant, 'custom_domain')
            || (! $this->adminAvailable($tenant) && $this->storefrontAvailable($tenant));
    }

    public function allows(Tenant $tenant, string $feature): bool
    {
        if ($this->inTrial($tenant)) {
            return in_array($feature, ['custom_domain', 'advanced_analytics', 'remove_branding'], true);
        }
        if (! $this->hasPaidAccess($tenant)) {
            return false;
        }
        $plan = ['sprout' => 'base', 'blossom' => 'pro', 'tree' => 'enterprise', 'maison' => 'pro', 'heritage' => 'enterprise'][$tenant->plan] ?? $tenant->plan;

        return in_array($feature, config('saas.plans.'.$plan.'.entitlements', []), true);
    }
}

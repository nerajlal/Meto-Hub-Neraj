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
            return $tenant->subscription_status === 'active';
        } // Preserve existing stores until their first provider reconciliation.

        return $subscription->paid_count > 0 && $subscription->paid_from?->lte(now()) && $subscription->paid_through?->isFuture()
            && in_array($subscription->status, ['active', 'pending', 'halted', 'paused', 'cancelled', 'completed'], true);
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

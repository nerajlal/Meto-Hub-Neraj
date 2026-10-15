<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantParam = $request->route('tenant');

        if ($tenantParam) {
            // Find tenant by ID
            $tenant = Tenant::find($tenantParam);

            if (!$tenant) {
                // Alternatively try finding by slug/name matching
                $tenant = Tenant::where('name', 'like', str_replace('-', ' ', $tenantParam))->first();
            }

            if (!$tenant) {
                abort(404, 'Tenant storefront not found.');
            }

            // Calculate trial remaining days (7 day trial)
            // Calculate trial remaining days accurately by exact date and time
            $access = app(\App\Services\PlanAccess::class);
            $isPaid = $access->hasPaidAccess($tenant);
            $trialEndsAt = $tenant->created_at->copy()->addDays(config('saas.trial_days', 7));
            $isTrialExpired = !$isPaid && now()->greaterThanOrEqualTo($trialEndsAt);
            $daysRemaining = !$isPaid && !$isTrialExpired ? (int) ceil(now()->diffInSeconds($trialEndsAt) / 86400) : 0;
            $record = $tenant->subscriptions()->where('gateway_id', $tenant->razorpay_subscription_id)->first();
            $renewsIn = !$record?->cancel_at_cycle_end && $record?->next_charge_at?->isFuture() ? (int) ceil(now()->diffInSeconds($record->next_charge_at) / 86400) : null;

            // Share resolved tenant globally with all views
            view()->share('currentTenant', $tenant);
            view()->share('trialDaysRemaining', $daysRemaining);
            view()->share('isTrialExpired', $isTrialExpired);
            view()->share('isPaidPlan', $isPaid);
            view()->share('renewsIn', $renewsIn);
            $request->user(); // Resolve authentication before applying tenant scopes to users.
            $request->attributes->set('resolved_tenant_id', $tenant->id);
            // Set session/config for reference in scope or controllers
            session(['active_tenant_id' => $tenant->id]);

            // Set global route default for 'tenant' parameter so we don't need to specify it manually everywhere
            \Illuminate\Support\Facades\URL::defaults(['tenant' => $tenantParam]);

            // Forget the tenant parameter so it is not passed to the controller methods
            if ($request->route()) {
                $request->route()->forgetParameter('tenant');
            }
        }

        return $next($request);
    }
}

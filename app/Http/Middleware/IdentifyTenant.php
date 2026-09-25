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
            $isPaid = $tenant->subscription_status === 'active';
            $trialEndsAt = $tenant->created_at->copy()->addDays(7);
            
            $isTrialExpired = !$isPaid && now()->greaterThanOrEqualTo($trialEndsAt);
            $daysRemaining = 0;
            $renewsIn = 0;
            
            if (!$isPaid && !$isTrialExpired) {
                // ceil() ensures that 6.1 days remaining shows as 7 days, and 0.5 days shows as 1 day.
                $daysRemaining = (int) ceil(now()->floatDiffInDays($trialEndsAt));
            } else if ($isPaid) {
                // Calculate roughly when the next billing cycle is based on updated_at
                $daysSincePayment = now()->diffInDays($tenant->updated_at);
                $renewsIn = max(0, 30 - ($daysSincePayment % 30));
                // Ensure it doesn't say 0 if it just renewed
                if ($renewsIn === 0) $renewsIn = 30;
            }

            // Share resolved tenant globally with all views
            view()->share('currentTenant', $tenant);
            view()->share('trialDaysRemaining', $daysRemaining);
            view()->share('isTrialExpired', $isTrialExpired);
            view()->share('isPaidPlan', $isPaid);
            view()->share('renewsIn', $renewsIn);
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

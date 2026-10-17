<?php

namespace App\Http\Middleware;

use App\Services\PlanAccess;
use Closure;
use Illuminate\Http\Request;

class EnforceSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = \App\Models\Tenant::findOrFail($request->user()->tenant_id);
        $access = app(PlanAccess::class);
        if ($request->routeIs('admin.billing*', 'admin.notifications.*')) {
            return $next($request);
        }
        if (! $request->isMethodSafe() && ! $access->inTrial($tenant) && ! $access->hasPaidAccess($tenant)) {
            abort(402, 'Your trial or paid period has ended. Open Billing to activate a subscription. Historical records remain available.');
        }
        if ($request->routeIs('admin.analytics*')) {
            abort_unless($access->allows($tenant, 'advanced_analytics'), 403, 'Advanced analytics requires Pro or Enterprise.');
        }
        if ($request->routeIs('admin.settings.domain.update') && $request->filled('domain')) {
            abort_unless($access->allows($tenant, 'custom_domain'), 403, 'Custom domains require Pro or Enterprise.');
        }

        return $next($request);
    }
}

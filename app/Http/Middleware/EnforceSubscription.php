<?php

namespace App\Http\Middleware;

use App\Services\PlanAccess;
use Closure;
use Illuminate\Http\Request;

class EnforceSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = \App\Models\Tenant::findOrFail($request->attributes->get('resolved_tenant_id'));
        $access = app(PlanAccess::class);
        if ($request->routeIs('admin.billing*') || $request->user()->type === 'super_admin') {
            return $next($request);
        }
        if (! $access->adminAvailable($tenant)) {
            $url = route('admin.billing', ['tenant' => $tenant->id]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Your subscription has ended. Renew from Billing to unlock your dashboard.', 'billing_url' => $url], 402);
            }
            return redirect()->to($url, $request->isMethodSafe() ? 302 : 303)->with('billing_required', 'Renew your subscription to unlock your dashboard.');
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

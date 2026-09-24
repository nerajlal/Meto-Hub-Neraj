<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyStorefrontTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        if (!in_array($host, ['localhost', '127.0.0.1', 'localhost:8000', 'metohub.com', 'www.metohub.com'])) {
            $matchingTenant = Tenant::where('domain', $host)->first();
            if ($matchingTenant) {
                session(['active_tenant_id' => $matchingTenant->id]);
            }
        }

        if ($request->query('preview') == 1) {
            session(['active_tenant_id' => $request->query('tenant_id') ?? 2]);
        } elseif ($request->has('tenant_id')) {
            session(['active_tenant_id' => $request->query('tenant_id')]);
        } elseif ($request->has('tenant')) {
            session(['active_tenant_id' => $request->query('tenant')]);
        }

        // Fetch and share the current tenant with all storefront views
        $tenantId = session('active_tenant_id') 
            ?? (auth()->check() ? auth()->user()->tenant_id : null) 
            ?? session('demo_tenant_id') 
            ?? 2;
        session(['active_tenant_id' => $tenantId]);
        
        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            view()->share('currentTenant', $tenant);
        }

        return $next($request);
    }
}

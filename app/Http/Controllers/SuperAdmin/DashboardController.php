<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index(\App\Services\SuperAdminStoreOverview $overview)
    {
        $counts = $overview->counts();
        $totalStores = $counts['total'];
        $totalOrders = \App\Models\Order::withoutGlobalScopes()->count();
        $totalCustomers = User::withoutGlobalScopes()->where('type', 'user')->count();
        $recentOrders = \App\Models\Order::withoutGlobalScopes()->with('tenant')->latest()->take(5)->get();
        $tenants = $overview->query()->latest()->orderByDesc('id')->take(8)->get();
        $planBreakdown = \App\Models\Tenant::selectRaw('plan, COUNT(*) as store_count')->groupBy('plan')->pluck('store_count', 'plan');
        $salesByCurrency = \App\Models\Order::withoutGlobalScopes()->join('tenants', 'tenants.id', '=', 'orders.tenant_id')
            ->where('orders.status', '!=', 'cancelled')->selectRaw('tenants.currency, SUM(orders.total_amount) as amount')
            ->groupBy('tenants.currency')->get();

        $subscriptionCollections = \App\Models\SubscriptionPayment::where('status', 'captured')->selectRaw('currency, SUM(amount) as captured, SUM(amount_refunded) as refunded')->groupBy('currency')->get();
        $outstandingInvoices = \App\Models\SubscriptionInvoice::whereIn('status', ['issued', 'partially_paid'])->selectRaw('currency, SUM(amount_due) as due')->groupBy('currency')->get();

        return view('super_admin.dashboard', compact('counts', 'totalStores', 'totalOrders', 'totalCustomers', 'recentOrders', 'tenants', 'planBreakdown', 'salesByCurrency', 'overview', 'subscriptionCollections', 'outstandingInvoices'));
    }

    private function storeFilters(Request $request): array
    {
        return $request->validate([
            'q' => 'nullable|string|max:200',
            'status' => ['nullable', \Illuminate\Validation\Rule::in(array_keys(\App\Services\SuperAdminStoreOverview::STATUSES))],
            'plan' => 'nullable|string|max:50', 'trial_ending' => 'nullable|in:1',
        ]);
    }

    public function subscriptions(Request $request, \App\Services\SuperAdminStoreOverview $overview)
    {
        $filters = $this->storeFilters($request);
        $tenants = $overview->filter($overview->query(), $filters)->latest()->orderByDesc('id')->paginate(15)->withQueryString();
        $counts = $overview->counts();
        $planOptions = \App\Models\Tenant::select('plan')->distinct()->orderBy('plan')->pluck('plan')->filter();

        return view('super_admin.subscriptions', compact('tenants', 'counts', 'filters', 'planOptions', 'overview'));
    }

    public function refreshBilling($id, \App\Services\SubscriptionBilling $billing)
    {
        $tenant = \App\Models\Tenant::findOrFail($id);
        $subscription = $billing->local($tenant);
        abort_unless($subscription, 404, 'No subscription recorded.');
        try { $billing->reconcile($subscription); }
        catch (\Throwable $e) { return back()->withErrors(['billing' => 'Provider synchronization could not be completed. Check gateway configuration or retry later.']); }
        return back()->with('success', 'Store subscription, invoices and payment history refreshed.');
    }

    public function createTenant()
    {
        return view('super_admin.create_tenant', ['plans' => config('saas.plans'), 'themes' => app(\App\Services\StorefrontTheme::class)->all()]);
    }

    public function storeTenant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'site_name' => 'required|string|max:255',
            'plan' => ['required', \Illuminate\Validation\Rule::in(array_keys(config('saas.plans')))],
            'theme' => ['required', \Illuminate\Validation\Rule::in(array_keys(config('storefront_themes')))],
        ]);

        // Create Tenant with Plan and Theme
        $tenant = \App\Models\Tenant::create([
            'name' => $validated['site_name'],
            'plan' => $validated['plan'],
            'theme' => $validated['theme'],
        ]);

        // Create Tenant Admin User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => 'admin',
            'site_name' => $validated['site_name'],
            'tenant_id' => $tenant->id,
        ]);

        // Auto-seed initial template catalog for this manually provisioned tenant
        \App\Services\TenantCatalogSeeder::seedForTenant($tenant->id);

        return redirect()->route('super_admin.dashboard')->with('success', 'Tenant Admin and store catalog created successfully.');
    }

    public function tenants(Request $request, \App\Services\SuperAdminStoreOverview $overview)
    {
        $filters = $this->storeFilters($request);
        $tenants = $overview->filter($overview->query(), $filters)->latest()->orderByDesc('id')->paginate(15)->withQueryString();
        $counts = $overview->counts();
        $planOptions = \App\Models\Tenant::select('plan')->distinct()->orderBy('plan')->pluck('plan')->filter();

        return view('super_admin.tenants', compact('tenants', 'counts', 'filters', 'planOptions', 'overview'));
    }

    public function orders()
    {
        $orders = \App\Models\Order::with('tenant')->latest()->paginate(15);
        return view('super_admin.orders', compact('orders'));
    }

    public function customers()
    {
        $customers = \App\Models\User::where('type', 'user')->latest()->paginate(15);
        return view('super_admin.customers', compact('customers'));
    }

    public function plans()
    {
        $plans = config('saas.plans');
        $planBreakdown = \App\Models\Tenant::selectRaw('plan, COUNT(*) as store_count')->groupBy('plan')->pluck('store_count', 'plan');

        return view('super_admin.plans', compact('plans', 'planBreakdown'));
    }

    public function status()
    {
        $dbConnected = false;
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $dbConnected = true;
        } catch (\Exception $e) {}

        $statusInfo = [
            'db_connection' => $dbConnected ? 'Connected' : 'Disconnected',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'database_name' => config('database.connections.mysql.database'),
            'total_tenants' => \App\Models\Tenant::count(),
            'total_products' => \App\Models\Product::count(),
            'total_orders' => \App\Models\Order::count(),
            'total_users' => \App\Models\User::count(),
        ];
        return view('super_admin.status', compact('statusInfo'));
    }

    public function showTenant($id)
    {
        $overview = app(\App\Services\SuperAdminStoreOverview::class);
        $tenant = $overview->query()->findOrFail($id);
        $billing = $overview->describe($tenant);
        $subscription = app(\App\Services\SubscriptionBilling::class)->local($tenant);
        $payments = \App\Models\SubscriptionPayment::whereHas('subscription', fn ($q) => $q->where('tenant_id', $tenant->id))->latest('attempted_at')->take(30)->get();

        $totalProducts = \App\Models\Product::withoutGlobalScopes()->where('tenant_id', $id)->count();
        $totalOrders = \App\Models\Order::withoutGlobalScopes()->where('tenant_id', $id)->count();
        
        $totalSales = \App\Models\Order::withoutGlobalScopes()
            ->where('tenant_id', $id)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
            
        $totalCustomers = \App\Models\User::withoutGlobalScopes()->where('tenant_id', $id)
            ->where('type', 'user')
            ->count();

        $recentOrders = \App\Models\Order::withoutGlobalScopes()
            ->where('tenant_id', $id)
            ->latest()
            ->take(5)
            ->get();

        $lowStockItems = \App\Models\ProductVariant::where('stock', '<', 10)
            ->whereHas('product', function($q) use ($id) {
                $q->withoutGlobalScopes()->where('tenant_id', $id);
            })
            ->with(['product' => function($q) {
                $q->withoutGlobalScopes();
            }])
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        return view('super_admin.show_tenant', compact(
            'tenant',
            'totalProducts',
            'totalOrders',
            'totalSales',
            'totalCustomers',
            'recentOrders',
            'lowStockItems',
            'billing',
            'subscription',
            'payments'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $tenant = Tenant::find($tenantId);

        // Store Setup Onboarding Checklist
        $hasLogo = !empty($tenant?->logo);
        $hasProducts = Product::where('tenant_id', $tenantId)->exists();
        $hasCustomTheme = !empty($tenant?->theme) && $tenant->theme !== 'minimal';
        
        $completedSteps = 0;
        if ($hasLogo) $completedSteps++;
        if ($hasProducts) $completedSteps++;
        if ($hasCustomTheme) $completedSteps++;

        $totalSteps = 4;
        $allOrdersCount = Order::count();
        $isNewStore = ($allOrdersCount === 0 || $completedSteps < $totalSteps);

        // Define Period (Last 30 days)
        $startDate = now()->subDays(30);
        $previousStartDate = now()->subDays(60);
        $previousEndDate = now()->subDays(30);

        // --- Total Sales ---
        $currentSales = Order::where('created_at', '>=', $startDate)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_amount');
            
        $prevSales = Order::where('created_at', '>=', $previousStartDate)
            ->where('created_at', '<', $previousEndDate)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_amount');

        $salesGrowth = $this->calculateGrowth($currentSales, $prevSales);

        // --- Total Orders ---
        $currentOrders = Order::where('created_at', '>=', $startDate)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        $prevOrders = Order::where('created_at', '>=', $previousStartDate)
            ->where('created_at', '<', $previousEndDate)
            ->whereNotIn('status', ['cancelled'])
            ->count();
            
        $ordersGrowth = $this->calculateGrowth($currentOrders, $prevOrders);

        // --- New Customers (Manually scoped as TenantScope bypasses User model) ---
        $currentCustomers = User::where('tenant_id', $tenantId)->where('created_at', '>=', $startDate)->count();
        $prevCustomers = User::where('tenant_id', $tenantId)->where('created_at', '>=', $previousStartDate)
            ->where('created_at', '<', $previousEndDate)
            ->count();
            
        $customersGrowth = $this->calculateGrowth($currentCustomers, $prevCustomers);

        // --- Low Stock Items ---
        $lowStockItems = ProductVariant::where('stock', '<', 10)
            ->whereHas('product', function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->with('product')
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
            
        $lowStockCount = ProductVariant::where('stock', '<', 10)
            ->whereHas('product', function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->count();

        // --- Recent Orders ---
        $recentOrders = Order::latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'currentSales', 'prevSales', 'salesGrowth',
            'currentOrders', 'prevOrders', 'ordersGrowth',
            'currentCustomers', 'prevCustomers', 'customersGrowth',
            'lowStockItems', 'lowStockCount',
            'recentOrders',
            'hasLogo', 'hasProducts', 'hasCustomTheme',
            'completedSteps', 'totalSteps', 'isNewStore', 'tenant'
        ));
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }
}

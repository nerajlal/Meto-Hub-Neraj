<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Collection;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $period = $request->get('period', '30_days');
        
        $startDate = match($period) {
            '7_days' => now()->subDays(7),
            '30_days' => now()->subDays(30),
            '90_days' => now()->subDays(90),
            'year' => now()->subYear(),
            default => now()->subDays(30)
        };

        $previousStartDate = match($period) {
            '7_days' => now()->subDays(14),
            '30_days' => now()->subDays(60),
            '90_days' => now()->subDays(180),
            'year' => now()->subYears(2),
            default => now()->subDays(60)
        };

        $previousEndDate = $startDate->copy();

        // --- Current Period Metrics ---
        $currentOrders = Order::where('created_at', '>=', $startDate)
            ->whereNotIn('status', ['cancelled'])
            ->get();
            
        $totalSales = $currentOrders->sum('total_amount');
        $totalOrders = $currentOrders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // --- Previous Period Metrics ---
        $previousOrders = Order::where('created_at', '>=', $previousStartDate)
            ->where('created_at', '<', $previousEndDate)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $prevTotalSales = $previousOrders->sum('total_amount');
        $prevTotalOrders = $previousOrders->count();
        $prevAvgOrderValue = $prevTotalOrders > 0 ? $prevTotalSales / $prevTotalOrders : 0;

        // --- Growth Percentages ---
        $salesGrowth = $this->calculateGrowth($totalSales, $prevTotalSales);
        $ordersGrowth = $this->calculateGrowth($totalOrders, $prevTotalOrders);
        $aovGrowth = $this->calculateGrowth($avgOrderValue, $prevAvgOrderValue);

        // --- Top Products ---
        $topProducts = OrderItem::select('product_id', 'name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_revenue'))
            ->whereHas('order', function($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate)
                  ->whereNotIn('status', ['cancelled']);
            })
            ->whereNotNull('product_id')
            ->groupBy('product_id', 'name')
            ->orderByDesc('total_qty')
            ->with(['product.images'])
            ->limit(5)
            ->get();

        // --- Store Overview Stats ---
        $totalProducts = Product::where('status', 'active')->count();
        $totalCollections = Collection::where('status', 1)->count();
        $totalCustomers = User::where('tenant_id', $tenantId)->where('type', '!=', 'admin')->count();
        $totalInventory = ProductVariant::whereHas('product', fn($q) => $q->where('tenant_id', $tenantId))->sum('stock');
        $outOfStockCount = ProductVariant::whereHas('product', fn($q) => $q->where('tenant_id', $tenantId))->where('stock', 0)->count();

        // --- Order Status Breakdown ---
        $orderStatusBreakdown = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $periodLabel = match($period) {
            '7_days' => 'Last 7 days',
            '30_days' => 'Last 30 days',
            '90_days' => 'Last 90 days',
            'year' => 'Last Year',
            default => 'Last 30 days'
        };

        return view('admin.analytics', compact(
            'totalSales',
            'totalOrders',
            'avgOrderValue',
            'salesGrowth',
            'ordersGrowth',
            'aovGrowth',
            'topProducts',
            'period',
            'periodLabel',
            'totalProducts',
            'totalCollections',
            'totalCustomers',
            'totalInventory',
            'outOfStockCount',
            'orderStatusBreakdown'
        ));
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    public function export(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? session('active_tenant_id') ?? 1;
        $period = $request->get('period', '30_days');
        
        $startDate = match($period) {
            '7_days' => now()->subDays(7),
            '30_days' => now()->subDays(30),
            '90_days' => now()->subDays(90),
            'year' => now()->subYear(),
            default => now()->subDays(30)
        };

        // Current Period Metrics
        $currentOrders = Order::where('created_at', '>=', $startDate)
            ->whereNotIn('status', ['cancelled'])
            ->get();
            
        $totalSales = $currentOrders->sum('total_amount');
        $totalOrders = $currentOrders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Top Products
        $topProducts = OrderItem::select('product_id', 'name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_revenue'))
            ->whereHas('order', function($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate)
                  ->whereNotIn('status', ['cancelled']);
            })
            ->whereNotNull('product_id')
            ->groupBy('product_id', 'name')
            ->orderByDesc('total_qty')
            ->limit(50)
            ->get();

        // Store Stats
        $totalProducts = Product::where('status', 'active')->count();
        $totalCustomers = User::where('tenant_id', $tenantId)->where('type', '!=', 'admin')->count();
        $totalInventory = ProductVariant::whereHas('product', fn($q) => $q->where('tenant_id', $tenantId))->sum('stock');
        
        $orderStatusBreakdown = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fileName = 'analytics_report_' . $period . '_' . date('Y_m_d') . '.csv';
        
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use($period, $totalSales, $totalOrders, $avgOrderValue, $topProducts, $totalProducts, $totalCustomers, $totalInventory, $orderStatusBreakdown) {
            $file = fopen('php://output', 'w');
            
            // Section 1: Summary
            fputcsv($file, ['ANALYTICS REPORT', 'Period: ' . $period]);
            fputcsv($file, []);
            fputcsv($file, ['SUMMARY METRICS']);
            fputcsv($file, ['Total Sales (INR)', 'Total Orders', 'Average Order Value (INR)']);
            fputcsv($file, [$totalSales, $totalOrders, round($avgOrderValue, 2)]);
            fputcsv($file, []);
            
            // Section 2: Store Overview
            fputcsv($file, ['STORE OVERVIEW']);
            fputcsv($file, ['Active Products', 'Total Customers', 'Total Inventory Stock']);
            fputcsv($file, [$totalProducts, $totalCustomers, $totalInventory]);
            fputcsv($file, []);

            // Section 3: Order Status
            fputcsv($file, ['ORDER STATUS BREAKDOWN']);
            fputcsv($file, ['Status', 'Count']);
            foreach ($orderStatusBreakdown as $status => $count) {
                fputcsv($file, [ucfirst($status), $count]);
            }
            fputcsv($file, []);

            // Section 4: Top Products
            fputcsv($file, ['TOP PERFORMING PRODUCTS']);
            fputcsv($file, ['Product Name', 'Quantity Sold', 'Total Revenue Generated']);
            foreach ($topProducts as $product) {
                fputcsv($file, [$product->name, $product->total_qty, $product->total_revenue]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

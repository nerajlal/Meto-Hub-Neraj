<?php

namespace App\Http\Controllers\DeliveryBoy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function dashboard(Request $request)
    {
        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;
        $deliveryBoyId = Auth::id();

        // Active orders (processing, shipped)
        $activeOrders = Order::where('tenant_id', $tenantId)
            ->where('delivery_partner_id', $deliveryBoyId)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Completed orders today
        $completedOrdersCount = Order::where('tenant_id', $tenantId)
            ->where('delivery_partner_id', $deliveryBoyId)
            ->where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->count();

        return view('delivery.dashboard', compact('activeOrders', 'completedOrdersCount'));
    }

    public function showOrder($tenant, $id)
    {
        $tenantId = session('active_tenant_id') ?? $tenant ?? 1;
        $deliveryBoyId = Auth::id();

        $order = Order::with('items.product')->where('tenant_id', $tenantId)
            ->where('delivery_partner_id', $deliveryBoyId)
            ->findOrFail($id);

        return view('delivery.order', compact('order'));
    }

    public function updateStatus(Request $request, $tenant, $id)
    {
        $tenantId = session('active_tenant_id') ?? $tenant ?? 1;
        $deliveryBoyId = Auth::id();

        $order = Order::where('tenant_id', $tenantId)
            ->where('delivery_partner_id', $deliveryBoyId)
            ->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:processing,shipped,delivered,cancelled', // Depending on statuses
        ]);

        $order->status = $validated['status'];
        $order->save();

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}

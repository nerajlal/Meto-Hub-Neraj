<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Product;
use App\Models\Tenant;

class ShippingCalculator
{
    /** Attach authoritative per-line delivery fees and calculate the order delivery total. */
    public function calculate(array &$cart, float $discountedSubtotal, ?Tenant $tenant = null): array
    {
        $tenant ??= $this->currentTenant();
        $defaultFee = max(0, (float) ($tenant?->default_delivery_fee ?? 0));
        $lineFees = 0.0;

        foreach ($cart as &$item) {
            $fee = null;
            if (($item['type'] ?? 'product') === 'bundle' && !empty($item['bundle_id'])) {
                $fee = Bundle::find($item['bundle_id'])?->delivery_fee;
            } elseif (!empty($item['product_id'])) {
                $fee = Product::find($item['product_id'])?->delivery_fee;
            }

            $unitFee = $fee === null ? $defaultFee : max(0, (float) $fee);
            $lineFee = round($unitFee * max(0, (int) ($item['quantity'] ?? 0)), 2);
            $item['unit_delivery_fee'] = round($unitFee, 2);
            $item['line_delivery_fee_before_waiver'] = $lineFee;
            $lineFees += $lineFee;
        }
        unset($item);

        $threshold = $tenant?->free_delivery_threshold !== null ? (float) $tenant->free_delivery_threshold : null;
        $freeDelivery = $threshold !== null && $discountedSubtotal >= $threshold;
        foreach ($cart as &$item) {
            $item['line_delivery_fee'] = $freeDelivery ? 0.0 : (float) ($item['line_delivery_fee_before_waiver'] ?? 0);
        }
        unset($item);

        $deliveryTotal = $freeDelivery ? 0.0 : round($lineFees, 2);

        return [
            'shipping_total' => $deliveryTotal,
            'shipping_before_waiver' => round($lineFees, 2),
            'free_delivery_applied' => $freeDelivery,
            'free_delivery_threshold' => $threshold,
            'amount_to_free_delivery' => $threshold !== null
                ? round(max(0, $threshold - $discountedSubtotal), 2)
                : null,
        ];
    }

    private function currentTenant(): ?Tenant
    {
        $request = request();
        $tenantId = $request->attributes->get('resolved_tenant_id')
            ?? session('active_tenant_id')
            ?? (auth()->check() ? auth()->user()->tenant_id : null);

        return $tenantId ? Tenant::find($tenantId) : null;
    }
}

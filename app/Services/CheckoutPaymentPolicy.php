<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantPaymentSetting;

class CheckoutPaymentPolicy
{
    public function availableMethods(Tenant $tenant): array
    {
        $access = app(PlanAccess::class);
        if (! $access->storefrontAvailable($tenant)) return [];
        $settings = TenantPaymentSetting::withoutGlobalScopes()->where('tenant_id', $tenant->id)->first();
        $fields = $tenant->checkout_fields ?? [];
        $cod = ($settings?->cod_enabled ?? true) && ($fields['cash_on_delivery']['enabled'] ?? true);

        $methods = [];
        if ($cod) {
            $methods[] = 'cod';
        }

        if ($settings) {
            if ($settings->razorpay_enabled && !empty($settings->razorpay_key) && !empty($settings->razorpay_secret)) {
                $methods[] = 'razorpay';
                $methods[] = 'online';
            }
            if ($settings->stripe_enabled && !empty($settings->stripe_secret)) {
                $methods[] = 'stripe';
                $methods[] = 'online';
            }
            if ($settings->phonepe_enabled && !empty($settings->phonepe_merchant_id) && !empty($settings->phonepe_salt_key)) {
                $methods[] = 'phonepe';
                $methods[] = 'online';
            }
        }

        return array_values(array_unique($methods));
    }
}

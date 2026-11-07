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

        if ($settings?->whatsapp_checkout_enabled ?? true) {
            $methods[] = 'whatsapp';
        }

        if ($settings && in_array(strtoupper($tenant->currency ?? 'INR'), ['INR', '₹'], true)) {
            if ($settings->razorpay_enabled && $settings->razorpay_key && $settings->razorpay_secret && $settings->razorpay_webhook_secret) $methods[] = 'razorpay';
            if ($settings->paytm_enabled && $settings->paytm_mid && $settings->paytm_merchant_key && $settings->paytm_website_name
                && in_array($settings->paytm_environment, ['test', 'live'], true)) $methods[] = 'paytm';
            if ($settings->phonepe_enabled && $settings->phonepe_client_id && $settings->phonepe_client_secret && $settings->phonepe_client_version
                && $settings->phonepe_webhook_username && $settings->phonepe_webhook_password
                && in_array($settings->phonepe_environment, ['test', 'live'], true)) $methods[] = 'phonepe';
        }

        return array_values(array_unique($methods));
    }
}

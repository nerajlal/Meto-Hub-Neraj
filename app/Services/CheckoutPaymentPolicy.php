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

        // Online methods require a verified gateway and payment confirmation flow.
        return $cod ? ['cod'] : [];
    }
}

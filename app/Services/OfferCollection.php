<?php

namespace App\Services;

use App\Models\{Bundle, Collection, Tenant};
use Illuminate\Support\Facades\DB;

class OfferCollection
{
    public function forTenant(int $tenantId): Collection
    {
        return DB::transaction(function () use ($tenantId) {
            // Serialize first creation per store; the database also enforces uniqueness.
            Tenant::withoutGlobalScopes()->whereKey($tenantId)->lockForUpdate()->firstOrFail();
            $collection = Collection::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('system_key', 'offers')->first();
            if ($collection) return $collection;
            $base = 'bundles-offers-'.$tenantId;
            $slug = $base;
            for ($suffix = 2; Collection::withoutGlobalScopes()->where('slug', $slug)->exists(); $suffix++) $slug = $base.'-'.$suffix;
            $collection = new Collection;
            $collection->forceFill(['tenant_id' => $tenantId, 'system_key' => 'offers', 'name' => 'Bundles & Offers', 'slug' => $slug, 'description' => 'Explore our combos, packs and mix-and-match offers.', 'status' => true])->save();
            return $collection;
        });
    }

    public function assign(Bundle $bundle): void
    {
        if (!$bundle->tenant_id || !in_array($bundle->type, ['bundle', 'pack', 'pool'], true)) return;
        $collection = $this->forTenant((int) $bundle->tenant_id);
        if ((int) $bundle->collection_id !== $collection->id) $bundle->forceFill(['collection_id' => $collection->id])->saveQuietly();
    }
}

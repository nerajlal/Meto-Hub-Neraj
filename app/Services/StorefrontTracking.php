<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreIntegration;
use App\Models\Tenant;
use Illuminate\Support\Str;

class StorefrontTracking
{
    public function settings(Tenant $tenant): ?StoreIntegration
    {
        return StoreIntegration::where('tenant_id', $tenant->id)->first();
    }

    public function bootstrap(Tenant $tenant, array $view): ?array
    {
        try {
            return $this->buildBootstrap($tenant, $view);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private function buildBootstrap(Tenant $tenant, array $view): ?array
    {
        $settings = $this->settings($tenant);
        if (! $settings || request()->has('preview') || Str::contains(request()->userAgent() ?? '', 'GrocerySaaSApp')) {
            return null;
        }
        $page = Str::afterLast(request()->route()?->getName() ?? '', '.');
        if (request()->path() === '/' && request()->getHost() === $tenant->domain) {
            $page = 'home';
        }
        if (! in_array($page, ['home', 'product', 'collection', 'products', 'all-products', 'combos', 'combo', 'about', 'contact', 'shipping-policy', 'return-policy', 'terms-of-service', 'checkout'])) {
            return null;
        }
        // Search text, arbitrary filters and account/customer fields never reach provider URLs.
        $allowed = ['id', 'slug', 'page', 'store', 'tenant', 'tenant_id', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'];
        if (array_diff(array_keys(request()->query()), $allowed)) {
            return null;
        }
        $gtm = $settings->mode === 'gtm' && $settings->gtm_enabled && app(IntegrationDomainVerification::class)->eligible($tenant, $settings) && request()->getHost() === $settings->verified_domain && $page !== 'checkout';
        $ga4 = $settings->mode !== 'gtm' && $settings->ga4_enabled;
        $meta = $settings->mode !== 'gtm' && $settings->meta_enabled && $page !== 'checkout';
        if (! $gtm && ! $ga4 && ! $meta) {
            return null;
        }
        $seo = app(StorefrontSeo::class)->data($tenant, $view);
        $events = [];
        if ($page === 'product' && ($view['product'] ?? null) instanceof Product) {
            $product = $view['product'];
            $selected = app(StorefrontProduct::class)->data($product)['selected'];
            if ($selected) {
                $events[] = $this->event($tenant, 'view_item', [['item_id' => 't'.$tenant->id.'-p'.$product->id, 'item_name' => strip_tags($product->title), 'item_variant' => (string) $selected['size'], 'price' => $selected['price'], 'quantity' => 1]]);
            }
        }
        $snapshot = $this->snapshot($tenant);
        if ($page === 'checkout' && $snapshot) {
            $events[] = $this->event($tenant, 'begin_checkout', array_values($snapshot));
        }

        return ['tenant' => (string) $tenant->id, 'mode' => $gtm ? 'gtm' : 'direct', 'ga4' => $ga4 ? $settings->ga4_id : null, 'meta' => $meta ? $settings->meta_pixel_id : null, 'gtm' => $gtm ? $settings->gtm_id : null,
            'page' => ['location' => $seo['canonical'], 'title' => $seo['title']], 'events' => $events,
            'checkoutUrl' => app(StorefrontSeo::class)->url($tenant, 'checkout'),
            'checkout' => $this->event($tenant, 'begin_checkout', array_values($snapshot)),
            'consentVersion' => 1, 'consentDays' => 180, 'consentScope' => hash('sha256', json_encode([$settings->mode, $settings->ga4_enabled, $settings->ga4_id, $settings->meta_enabled, $settings->meta_pixel_id, $settings->gtm_enabled, $settings->gtm_id]))];
    }

    public function event(Tenant $tenant, string $name, array $items, ?float $value = null, ?string $id = null): array
    {
        return ['id' => $id ?? (string) Str::uuid(), 'name' => $name, 'tenant' => (string) $tenant->id, 'currency' => $tenant->getRawOriginal('currency') ?: 'INR',
            'value' => $value ?? round(array_sum(array_map(fn ($item) => $item['price'] * $item['quantity'], $items)), 2), 'items' => array_values($items)];
    }

    public function order(Order $order, Tenant $tenant): array
    {
        $items = $order->items->map(fn ($item) => ['item_id' => 't'.$tenant->id.($item->product_id ? '-p'.$item->product_id : '-b'.$item->bundle_id),
            'price' => (float) $item->price, 'quantity' => (int) $item->quantity])->all();

        return $this->event($tenant, 'order_placed', $items, (float) $order->total_amount, 't'.$tenant->id.'-order-'.$order->id);
    }

    public function snapshot(Tenant $tenant): array
    {
        $raw = auth()->check() ? Cart::where('user_id', auth()->id())->get()->map(fn ($item) => ['product_id' => $item->product_id, 'bundle_id' => $item->bundle_id, 'size' => $item->size, 'variant_id' => $item->product_variant_id, 'quantity' => $item->quantity, 'type' => $item->bundle_id ? 'bundle' : 'product'])->all() : session('cart', []);
        $cart = [];
        foreach ($raw as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            if ($qty < 1) {
                continue;
            }
            $isBundle = ($item['type'] ?? 'product') === 'bundle';
            $model = $isBundle ? Bundle::where('tenant_id', $tenant->id)->find($item['bundle_id'] ?? null) : Product::where('tenant_id', $tenant->id)->with('variants')->find($item['product_id'] ?? null);
            if (! $model || $model->status !== 'active') {
                continue;
            }
            if (! $isBundle) {
                $variant = ! empty($item['variant_id']) ? $model->variants->firstWhere('id', $item['variant_id']) : (! empty($item['size']) ? $model->variants->firstWhere('size', $item['size']) : $model->variants->sortBy('price')->first());
                if (! $variant) {
                    continue;
                }
            }
            $key = ($isBundle ? 'b' : 'p').$model->id.':'.($isBundle ? '' : $variant->id);
            $cart[$key] = ['type' => $isBundle ? 'bundle' : 'product', 'product_id' => $isBundle ? null : $model->id, 'variant_id' => $isBundle ? null : $variant->id, 'size' => $isBundle ? null : $variant->size,
                'quantity' => $qty, 'price' => $isBundle ? (float) $model->total_price : (float) $variant->price, 'coupon' => $isBundle ? null : CartService::getActiveCoupon($model),
                'item_id' => 't'.$tenant->id.'-'.($isBundle ? 'b' : 'p').$model->id, 'item_name' => strip_tags($model->title)];
        }
        if (! $cart) {
            return [];
        }
        $totals = CartService::calculateTotal($cart);
        $net = array_sum(array_map(fn ($item) => max(0, $item['line_subtotal'] - $item['line_savings']), $cart));
        $factor = $net > 0 ? $totals['total'] / $net : 0;

        return array_map(fn ($item) => ['item_id' => $item['item_id'], 'item_name' => $item['item_name'], 'item_variant' => (string) $item['size'],
            'quantity' => $item['quantity'], 'price' => round(max(0, $item['line_subtotal'] - $item['line_savings']) / $item['quantity'] * $factor, 2)], $cart);
    }
}

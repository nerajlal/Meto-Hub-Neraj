<?php

namespace App\Http\Middleware;

use App\Services\StorefrontTracking;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackStorefrontCart
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->route()?->getName(), ['cart.add', 'cart.update', 'cart.remove'])) {
            return $next($request);
        }
        $tenant = $request->attributes->get('resolved_storefront_tenant');
        $tracker = app(StorefrontTracking::class);
        $settings = $tenant ? $tracker->settings($tenant) : null;
        if (! $settings || (! $settings->ga4_enabled && ! $settings->meta_enabled && ! $settings->gtm_enabled)) {
            return $next($request);
        }
        try {
            $before = $tracker->snapshot($tenant);
        } catch (\Throwable $e) {
            report($e);

            return $next($request);
        }
        $response = $next($request);
        if (! $response instanceof JsonResponse || ! $response->isSuccessful() || ! ($response->getData(true)['success'] ?? false)) {
            return $response;
        }
        try {
            $after = $tracker->snapshot($tenant);
            $added = [];
            $removed = [];
            foreach (array_unique(array_merge(array_keys($before), array_keys($after))) as $key) {
                $delta = ($after[$key]['quantity'] ?? 0) - ($before[$key]['quantity'] ?? 0);
                if ($delta > 0) {
                    $added[] = array_replace($after[$key], ['quantity' => $delta]);
                }
                if ($delta < 0) {
                    $removed[] = array_replace($before[$key], ['quantity' => -$delta]);
                }
            }
            $events = [];
            if ($added) {
                $events[] = $tracker->event($tenant, 'add_to_cart', $added);
            }
            if ($removed) {
                $events[] = $tracker->event($tenant, 'remove_from_cart', $removed);
            }
            $data = $response->getData(true);
            $data['tracking'] = ['tenant' => (string) $tenant->id, 'events' => $events];
            $data['tracking']['cart'] = $tracker->event($tenant, 'begin_checkout', array_values($after));
            $response->setData($data);
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }
}

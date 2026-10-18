<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchIndexing
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $private = $request->is('*/admin', '*/admin/*', 'superadmin', 'superadmin/*', 'account', 'account/*', 'cart/*', 'login', 'register', 'forgot-password', 'reset-password/*', 'verify-email', 'verify-email/*', 'email/*', '*/checkout', '*/wishlist', 'admin', 'admin/*', 'checkout', 'wishlist', 'saas', 'saas/*', 'billing', 'billing/*', 'home', 'home1', 'demo/*');
        if (! config('search.indexable') || $private || $request->has('preview') || $request->has('get_started') || $response->getStatusCode() >= 400) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}

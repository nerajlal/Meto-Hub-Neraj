<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormatCurrencyResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only modify HTML/text responses
        if (method_exists($response, 'getContent') && str_contains($response->headers->get('Content-Type') ?? '', 'text/html')) {
            $tenantId = session('active_tenant_id') 
                ?? (auth()->check() ? auth()->user()->tenant_id : null) 
                ?? 1;
            
            $tenant = Tenant::find($tenantId);
            
            if ($tenant && $tenant->currency !== 'INR') {
                $symbolMap = [
                    'INR' => '₹',
                    'USD' => '$',
                    'EUR' => '€',
                    'GBP' => '£',
                    'AED' => 'د.إ',
                    'CAD' => '$',
                    'AUD' => '$',
                ];
                
                $targetSymbol = $symbolMap[$tenant->currency] ?? '₹';
                
                if ($targetSymbol !== '₹') {
                    $content = $response->getContent();
                    // Replace standard currency symbol with target symbol
                    $content = str_replace('₹', $targetSymbol, $content);
                    $response->setContent($content);
                }
            }
        }

        return $response;
    }
}

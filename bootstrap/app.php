<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: ['billing/razorpay/webhook']);
        $middleware->web(append: [
            \App\Http\Middleware\FormatCurrencyResponse::class,
            \App\Http\Middleware\SearchIndexing::class,
        ]);
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'tenant_role' => \App\Http\Middleware\AuthorizeTenantRole::class,
            'identify_tenant' => \App\Http\Middleware\IdentifyTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, \Illuminate\Http\Request $request) {
            $view = 'nurah.errors.' . $e->getStatusCode();
            if (view()->exists($view)) {
                 return response()->view($view, [], $e->getStatusCode());
            }
        });
    })->create();

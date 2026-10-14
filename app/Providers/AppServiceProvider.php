<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\Blade::if('mobileapp', function () {
            return \Illuminate\Support\Str::contains(request()->header('User-Agent'), 'GrocerySaaSApp');
        });

        \Illuminate\Support\Facades\Blade::directive('currency', function () {
            return "<?php 
                \$symbolMap = [
                    'INR' => '₹',
                    'USD' => '$',
                    'EUR' => '€',
                    'GBP' => '£',
                    'AED' => 'AED ',
                    'CAD' => '$',
                    'AUD' => '$',
                ];
                echo \$symbolMap[\$currentTenant->currency ?? 'INR'] ?? '₹';
            ?>";
        });

        \Illuminate\Support\Facades\View::composer(['template_1.*', 'storefront.themes.*'], function ($view) {
            $view->with('storefrontTheme', app(\App\Services\StorefrontTheme::class)->current());
        });

        // Share pending orders count with sidebar
        \Illuminate\Support\Facades\View::composer('admin.partials.sidebar', function ($view) {
            $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
            $view->with('pendingOrdersCount', $pendingOrdersCount);
        });

        // Add helper to get storefront URL
        \Illuminate\Support\Facades\View::composer(['admin.partials.header', 'admin.partials.sidebar', 'admin.dashboard'], function ($view) {
            $currentTenant = $view->getData()['currentTenant'] ?? null;
            if (!$currentTenant) {
                $view->with('storefrontUrl', route('landing'));
                return;
            }
            
            $view->with('storefrontUrl', $currentTenant->storefrontUrl());
        });
    }
}

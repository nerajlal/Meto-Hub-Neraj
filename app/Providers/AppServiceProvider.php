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

        \Illuminate\Support\Facades\Blade::directive('currency', function () {
            return "<?php 
                \$symbolMap = [
                    'INR' => '₹',
                    'USD' => '$',
                    'EUR' => '€',
                    'GBP' => '£',
                    'AED' => 'د.إ',
                    'CAD' => '$',
                    'AUD' => '$',
                ];
                echo \$symbolMap[\$currentTenant->currency ?? 'INR'] ?? '₹';
            ?>";
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
            
            // Map theme to route prefix
            $themeToRoute = [
                'v1' => 'v1.home',
                'nurah' => 'v1.home',
                'modern_minimal' => 'v1.home',
                'v2' => 'velvet.home',
                'velvet' => 'velvet.home',
                'velvet_dark' => 'velvet.home',
                'v3' => 'v3.home',
                'aura_luxe' => 'v3.home',
                'template_1' => 'v3.home',
                'template_2' => 'v3.home',
                'v4' => 'v4.home',
                'ajmal' => 'v4.home',
                'editorial_cream' => 'v4.home',
                'v5' => 'v5.home',
                'afnan' => 'v5.home',
            ];
            
            $theme = strtolower($currentTenant->theme ?? 'v3');
            $routeName = $themeToRoute[$theme] ?? 'v3.home';
            $view->with('storefrontUrl', route($routeName, ['tenant_id' => $currentTenant->id]));
        });
    }
}

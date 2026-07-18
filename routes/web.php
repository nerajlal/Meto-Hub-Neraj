<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    // Set demo tenant from query parameter if provided
    if (request()->has('demo_tenant')) {
        session(['demo_tenant_id' => request()->demo_tenant]);
    }
    return view('landing.new-landing');
})->name('landing');

// Demo routes to set tenant
Route::get('/demo/{tenantId}', function($tenantId) {
    session(['demo_tenant_id' => $tenantId]);
    return redirect()->route('landing');
})->name('demo.set-tenant');

Route::get('/home1', function() {
    return view('landing.vespr-landing');
})->name('landing.vespr');

Route::get('/home', [App\Http\Controllers\LandingController::class, 'index'])->name('landing.old');
Route::get('/templates', [App\Http\Controllers\LandingController::class, 'templates'])->name('landing.templates');
Route::post('/demo-request', [App\Http\Controllers\LandingController::class, 'handleDemoRequest'])->name('demo.request');

// Shared Actions (Global)
Route::middleware([\App\Http\Middleware\IdentifyStorefrontTenant::class])->group(function () {
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/fetch', [App\Http\Controllers\CartController::class, 'fetch'])->name('cart.fetch');
    Route::get('/cart/state', [App\Http\Controllers\CartController::class, 'state'])->name('cart.state');
    Route::post('/order/place', [App\Http\Controllers\OrderController::class, 'store'])->name('order.place');
});

// v1 Nurah Theme Routes
Route::prefix('v1')->name('v1.')->middleware([\App\Http\Middleware\IdentifyStorefrontTenant::class])->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/collections', [PageController::class, 'collection'])->name('collection');
    Route::get('/products', [PageController::class, 'collection'])->name('products');
    Route::get('/all-products', [PageController::class, 'allProducts'])->name('all-products');
    Route::get('/combos', [PageController::class, 'combos'])->name('combos');
    Route::get('/combo', [PageController::class, 'combo'])->name('combo');
    Route::get('/cosmopolitan', [PageController::class, 'cosmopolitan'])->name('cosmopolitan');
    Route::get('/product', [PageController::class, 'product'])->name('product');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    // Cart drawer is used instead of a separate cart page for v1
    // Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart');
    Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
    Route::post('/wishlist/toggle', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist');
    Route::get('/shipping-policy', [PageController::class, 'shippingPolicy'])->name('shipping-policy');
    Route::get('/return-policy', [PageController::class, 'returnPolicy'])->name('return-policy');
    Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('terms-of-service');
});

// Account Routes
Route::middleware([\App\Http\Middleware\IdentifyStorefrontTenant::class, 'auth'])->group(function () {
    Route::get('/account', [App\Http\Controllers\AccountController::class, 'index'])->name('account.index');
    Route::get('/account/profile', [App\Http\Controllers\AccountController::class, 'profile'])->name('account.profile');
    Route::post('/account/address', [App\Http\Controllers\AccountController::class, 'updateAddress'])->name('account.address.update');
    Route::get('/account/orders', [App\Http\Controllers\AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/reorder', [App\Http\Controllers\AccountController::class, 'reorder'])->name('account.reorder');
    Route::post('/account/orders/reorder/{order}', [App\Http\Controllers\CartController::class, 'reorder'])->name('account.orders.reorder');
});

// User Auth Routes
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
// Google Auth
Route::get('auth/google', [App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);

// Centralized Admin Login Routes

// Super Admin Routes
Route::prefix('superadmin')->name('super_admin.')->group(function () {
    // Guest Routes
    Route::get('/login', [App\Http\Controllers\SuperAdmin\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\SuperAdmin\AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\SuperAdmin\AuthController::class, 'logout'])->name('logout');

    // Protected Routes
    Route::middleware([\App\Http\Middleware\SuperAdminMiddleware::class])->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/tenants', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'tenants'])->name('tenants');
        Route::get('/tenants/{id}', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'showTenant'])->name('tenants.show');
        Route::get('/create-tenant', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'createTenant'])->name('create_tenant');
        Route::post('/create-tenant', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'storeTenant'])->name('store_tenant');
        Route::get('/orders', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'orders'])->name('orders');
        Route::get('/customers', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'customers'])->name('customers');
        Route::get('/plans', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'plans'])->name('plans');
        Route::get('/status', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'status'])->name('status');
    });
});

// Centralized Admin Login Routes
Route::get('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'showCommonLogin'])->name('admin.common.login');
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'commonLogin'])->name('admin.common.login.submit');

// Password Reset Routes (OTP-based)
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showEmailForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::get('/forgot-password/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showOtpForm'])->name('password.otp');
Route::post('/forgot-password/verify', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/forgot-password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/forgot-password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Storefront Customer Auth Routes
Route::post('/customer/login', [App\Http\Controllers\CustomerAuthController::class, 'login'])->name('customer.login');
Route::post('/customer/register', [App\Http\Controllers\CustomerAuthController::class, 'register'])->name('customer.register');
Route::post('/customer/logout', [App\Http\Controllers\CustomerAuthController::class, 'logout'])->name('customer.logout');

// Admin Authentication Routes
Route::prefix('{tenant}')->name('admin.')->middleware(['identify_tenant'])->group(function () {
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
});

// Fallback for auth middleware if 'login' route is missing
Route::get('/login', function() {
    return redirect()->route('admin.common.login');
})->name('login');

// Global Zoho Callback (Zoho doesn't know tenant URL prefix initially)
Route::get('/admin/integrations/zoho/callback', [App\Http\Controllers\Admin\ZohoController::class, 'callback'])->name('admin.zoho.callback');

// Protected Admin Panel Routes
Route::prefix('{tenant}/admin')->name('admin.')->middleware(['identify_tenant', 'auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/export', [App\Http\Controllers\Admin\OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/create', [App\Http\Controllers\Admin\OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [App\Http\Controllers\Admin\OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/print', [App\Http\Controllers\Admin\OrderController::class, 'print'])->name('orders.print');
    Route::post('/orders/{id}/update-status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::get('/collections', [App\Http\Controllers\Admin\CollectionController::class, 'index'])->name('collections');
    Route::get('/collections/create', [App\Http\Controllers\Admin\CollectionController::class, 'create'])->name('collections.create');
    Route::post('/collections', [App\Http\Controllers\Admin\CollectionController::class, 'store'])->name('collections.store');
    Route::get('/collections/{id}', [App\Http\Controllers\Admin\CollectionController::class, 'show'])->name('collections.show');
    Route::get('/collections/{id}/edit', [App\Http\Controllers\Admin\CollectionController::class, 'edit'])->name('collections.edit');
    Route::put('/collections/{id}', [App\Http\Controllers\Admin\CollectionController::class, 'update'])->name('collections.update');
    Route::delete('/collections/{id}', [App\Http\Controllers\Admin\CollectionController::class, 'destroy'])->name('collections.destroy');

    Route::get('/bundles', [App\Http\Controllers\Admin\BundleController::class, 'index'])->name('bundles');
    Route::get('/bundles/create', [App\Http\Controllers\Admin\BundleController::class, 'create'])->name('bundles.create');
    Route::get('/bundles/pool/create', [App\Http\Controllers\Admin\BundleController::class, 'createPool'])->name('bundles.pool.create');
    Route::post('/bundles', [App\Http\Controllers\Admin\BundleController::class, 'store'])->name('bundles.store');
    Route::post('/bundles/pack-of', [App\Http\Controllers\Admin\BundleController::class, 'storePackOf'])->name('bundles.pack-of');
    Route::post('/bundles/pool', [App\Http\Controllers\Admin\BundleController::class, 'storePool'])->name('bundles.pool');
    Route::get('/bundles/{id}/edit', [App\Http\Controllers\Admin\BundleController::class, 'edit'])->name('bundles.edit');
    Route::put('/bundles/{id}', [App\Http\Controllers\Admin\BundleController::class, 'update'])->name('bundles.update');
    Route::delete('/bundles/{id}', [App\Http\Controllers\Admin\BundleController::class, 'destroy'])->name('bundles.destroy');

    Route::get('/products/{id}/variants', [App\Http\Controllers\Admin\ProductController::class, 'getVariants'])->name('products.variants');

    Route::get('/attributes', [App\Http\Controllers\Admin\AttributeController::class, 'index'])->name('attributes');
    Route::post('/attributes', [App\Http\Controllers\Admin\AttributeController::class, 'store'])->name('attributes.store');
    Route::put('/attributes/{id}', [App\Http\Controllers\Admin\AttributeController::class, 'update'])->name('attributes.update');
    Route::delete('/attributes/{id}', [App\Http\Controllers\Admin\AttributeController::class, 'destroy'])->name('attributes.destroy');

    Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products');
    Route::get('/products/sample', [App\Http\Controllers\Admin\ProductController::class, 'downloadSample'])->name('products.sample');
    Route::post('/products/import', [App\Http\Controllers\Admin\ProductController::class, 'import'])->name('products.import');
    Route::get('/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.destroy');

    // Zoho Integration Routes
    Route::get('/integrations/zoho/connect', [App\Http\Controllers\Admin\ZohoController::class, 'connect'])->name('zoho.connect');
    Route::post('/products/zoho-sync', [App\Http\Controllers\Admin\ZohoController::class, 'syncProducts'])->name('zoho.sync');

    // DartPOS Integration Route (Stub for now)
    Route::post('/products/dartpos-sync', function () {
        // Trigger the artisan command in the background or synchronous for MVP
        \Illuminate\Support\Facades\Artisan::call('dartpos:sync-products');
        return redirect()->back()->with('success', 'DartPOS sync started successfully.');
    })->name('dartpos.sync');

    Route::view('/reviews', 'admin.reviews.index')->name('reviews');

    Route::view('/blog', 'admin.blog.index')->name('blog');
    Route::view('/blog/create', 'admin.blog.create')->name('blog.create');
    Route::get('/blog/{id}/edit', function ($id) {
        return view('admin.blog.edit', ['id' => $id]);
    })->name('blog.edit');

    Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers');
    Route::view('/customers/create', 'admin.customers.create')->name('customers.create');
    Route::get('/customers/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');

    Route::get('/carts', [App\Http\Controllers\Admin\CartController::class, 'index'])->name('carts');

    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('analytics.export');
    Route::get('/analytics/{type}', function ($type) {
        $titles = [
            'sales' => ['title' => 'Total Sales', 'value' => '₹45,231.00', 'metricLabel' => 'Revenue'],
            'orders' => ['title' => 'Total Orders', 'value' => '342', 'metricLabel' => 'Orders'],
            'aov' => ['title' => 'Average Order Value', 'value' => '₹1,250.00', 'metricLabel' => 'Order Value'],
            'sessions' => ['title' => 'Online Store Sessions', 'value' => '10,234', 'metricLabel' => 'Sessions'],
        ];

        $data = $titles[$type] ?? ['title' => 'Report', 'value' => '0', 'metricLabel' => 'Count'];

        return view('admin.analytics.report', $data);
    })->name('analytics.show');

    Route::get('/discounts', [App\Http\Controllers\Admin\DiscountController::class, 'index'])->name('discounts');
    Route::get('/discounts/create', [App\Http\Controllers\Admin\DiscountController::class, 'create'])->name('discounts.create');
    Route::post('/discounts', [App\Http\Controllers\Admin\DiscountController::class, 'store'])->name('discounts.store');
    Route::get('/discounts/{id}/edit', [App\Http\Controllers\Admin\DiscountController::class, 'edit'])->name('discounts.edit');
    Route::put('/discounts/{id}', [App\Http\Controllers\Admin\DiscountController::class, 'update'])->name('discounts.update');
    Route::delete('/discounts/{id}', [App\Http\Controllers\Admin\DiscountController::class, 'destroy'])->name('discounts.destroy');

    Route::post('/custom-prices', [App\Http\Controllers\Admin\CustomPriceController::class, 'store'])->name('custom-prices.store');
    Route::delete('/custom-prices/{id}', [App\Http\Controllers\Admin\CustomPriceController::class, 'destroy'])->name('custom-prices.destroy');

    // Group Custom Pricing routes
    Route::post('/customer-groups', [App\Http\Controllers\Admin\GroupPricingController::class, 'storeGroup'])->name('customer-groups.store');
    Route::post('/customer-groups/add-users', [App\Http\Controllers\Admin\GroupPricingController::class, 'addUsersToGroup'])->name('customer-groups.add-users');
    Route::delete('/customer-groups/{id}', [App\Http\Controllers\Admin\GroupPricingController::class, 'destroyGroup'])->name('customer-groups.destroy');
    Route::delete('/customer-groups/{groupId}/remove-user/{userId}', [App\Http\Controllers\Admin\GroupPricingController::class, 'removeUserFromGroup'])->name('customer-groups.remove-user');

    Route::post('/group-custom-prices', [App\Http\Controllers\Admin\GroupPricingController::class, 'storeGroupPrice'])->name('group-custom-prices.store');
    Route::delete('/group-custom-prices/{id}', [App\Http\Controllers\Admin\GroupPricingController::class, 'destroyGroupPrice'])->name('group-custom-prices.destroy');

    Route::get('/settings/slider', [App\Http\Controllers\Admin\SliderController::class, 'index'])->name('settings.slider');
    Route::get('/settings/slider/create', [App\Http\Controllers\Admin\SliderController::class, 'create'])->name('settings.slider.create');
    Route::post('/settings/slider', [App\Http\Controllers\Admin\SliderController::class, 'store'])->name('settings.slider.store');
    Route::post('/settings/slider/reorder', [App\Http\Controllers\Admin\SliderController::class, 'reorder'])->name('settings.slider.reorder');
    Route::delete('/settings/slider/{id}', [App\Http\Controllers\Admin\SliderController::class, 'destroy'])->name('settings.slider.destroy');

    Route::get('/settings/home-products', [App\Http\Controllers\Admin\HomeProductController::class, 'index'])->name('settings.home-products');
    Route::post('/settings/home-products', [App\Http\Controllers\Admin\HomeProductController::class, 'store'])->name('settings.home-products.store');
    Route::post('/settings/home-products/reorder', [App\Http\Controllers\Admin\HomeProductController::class, 'reorder'])->name('settings.home-products.reorder');
    Route::delete('/settings/home-products/{id}', [App\Http\Controllers\Admin\HomeProductController::class, 'destroy'])->name('settings.home-products.destroy');

    Route::view('/settings/managers', 'admin.settings.managers.index')->name('settings.managers');
    Route::view('/settings/managers/create', 'admin.settings.managers.create')->name('settings.managers.create');

    Route::post('/settings/delivery-partners/{id}/default', [App\Http\Controllers\Admin\DeliveryPartnerController::class, 'setDefault'])->name('settings.delivery-partners.default');

    Route::resource('/settings/delivery-partners', App\Http\Controllers\Admin\DeliveryPartnerController::class, [
        'names' => 'settings.delivery-partners'
    ])->except(['show', 'create', 'edit']);

    Route::get('/settings/theme', [App\Http\Controllers\Admin\ThemeSettingsController::class, 'index'])->name('settings.theme');
    Route::post('/settings/theme', [App\Http\Controllers\Admin\ThemeSettingsController::class, 'update'])->name('settings.theme.update');

    Route::get('/settings/videos', [App\Http\Controllers\Admin\TenantVideoController::class, 'index'])->name('settings.videos');
    Route::post('/settings/videos', [App\Http\Controllers\Admin\TenantVideoController::class, 'update'])->name('settings.videos.update');

    Route::get('/settings/domain', [App\Http\Controllers\Admin\SettingController::class, 'domainIndex'])->name('settings.domain');
    Route::post('/settings/domain', [App\Http\Controllers\Admin\SettingController::class, 'domainUpdate'])->name('settings.domain.update');

    Route::get('/settings/payment', [App\Http\Controllers\Admin\SettingController::class, 'paymentIndex'])->name('settings.payment');
    Route::post('/settings/payment', [App\Http\Controllers\Admin\SettingController::class, 'paymentUpdate'])->name('settings.payment.update');

    Route::get('/settings/storefront', [App\Http\Controllers\Admin\SettingController::class, 'storefrontIndex'])->name('settings.storefront');
    Route::post('/settings/storefront', [App\Http\Controllers\Admin\SettingController::class, 'storefrontUpdate'])->name('settings.storefront.update');
    Route::get('/settings/checkout-fields', [App\Http\Controllers\Admin\SettingController::class, 'checkoutFields'])->name('settings.checkout-fields');
    Route::post('/settings/checkout-fields', [App\Http\Controllers\Admin\SettingController::class, 'checkoutFieldsUpdate'])->name('settings.checkout-fields.update');
});


// v2 Velvet Theme Routes
Route::prefix('v2')->name('velvet.')->middleware([\App\Http\Middleware\IdentifyStorefrontTenant::class])->group(function () {
    Route::get('/', function() {
        $tenantId = request('tenant_id') 
            ?? session('active_tenant_id') 
            ?? (auth()->check() ? auth()->user()->tenant_id : null) 
            ?? session('demo_tenant_id') 
            ?? 2;
        $bestsellers = \App\Models\HomeProduct::where('tenant_id', $tenantId)
            ->with(['product.variants', 'product.images', 'product.discounts'])
            ->orderBy('sort_order', 'asc')
            ->get();
        $collections = \App\Models\Collection::where('tenant_id', $tenantId)->where('status', 1)->get();
        $bundles = \App\Models\Bundle::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('type', 'bundle')
            ->with(['products.variants'])
            ->latest()
            ->take(4)
            ->get();
        $sliders = \App\Models\Slider::where('tenant_id', $tenantId)->where('status', true)->orderBy('order', 'asc')->get();
            
        return view('template_2.home', compact('sliders', 'bestsellers', 'collections', 'bundles'));
    })->name('home');

    Route::get('/all-products', [App\Http\Controllers\PageController::class, 'allProducts'])->name('all-products');

    Route::get('/collection/{slug}', function($slug) {
        $tenantId = request('tenant_id') 
            ?? session('active_tenant_id') 
            ?? (auth()->check() ? auth()->user()->tenant_id : null) 
            ?? session('demo_tenant_id') 
            ?? 2;
        $collection = \App\Models\Collection::where('tenant_id', $tenantId)->where('slug', $slug)->firstOrFail();
        $products = $collection->products()->where('status', 'active')->with(['variants', 'images'])->paginate(12)->withQueryString();
        $collections = \App\Models\Collection::where('tenant_id', $tenantId)->where('status', 1)->get();
        return view('template_2.collection', compact('collection', 'products', 'collections'));
    })->name('collection');

    Route::get('/combos', [App\Http\Controllers\PageController::class, 'combos'])->name('combos');
    Route::get('/combo', [App\Http\Controllers\PageController::class, 'combo'])->name('combo');

    Route::get('/product/{id}', [PageController::class, 'velvetProduct'])->name('product');

    Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');
    Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');
    Route::post('/wishlist/toggle', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist');
    Route::get('/shipping-policy', [App\Http\Controllers\PageController::class, 'shippingPolicy'])->name('shipping-policy');
    Route::get('/return-policy', [App\Http\Controllers\PageController::class, 'returnPolicy'])->name('return-policy');
    Route::get('/terms-of-service', [App\Http\Controllers\PageController::class, 'termsOfService'])->name('terms-of-service');
});

// v3 Backup Theme Routes
Route::prefix('v3')->name('v3.')->middleware([\App\Http\Middleware\IdentifyStorefrontTenant::class])->group(function () {
    Route::get('/', [PageController::class, 'v3Home'])->name('home');
    Route::get('/collections', [PageController::class, 'v3Collection'])->name('collection');
    Route::get('/all-products', [PageController::class, 'v3AllProducts'])->name('all-products');
    Route::get('/combos', [PageController::class, 'v3Combos'])->name('combos');
    Route::get('/combo', [PageController::class, 'v3Combo'])->name('combo');
    Route::get('/product', [PageController::class, 'v3Product'])->name('product');
    // Cart drawer is used instead of a separate cart page for v3
    // Route::get('/cart', [App\Http\Controllers\CartController::class, 'v3Index'])->name('cart');
    Route::get('/checkout', [PageController::class, 'v3Checkout'])->name('checkout');
    Route::post('/wishlist/toggle', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::get('/shipping-policy', [PageController::class, 'shippingPolicy'])->name('shipping-policy');
    Route::get('/return-policy', [PageController::class, 'returnPolicy'])->name('return-policy');
    Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('terms-of-service');
});

// v4 Ajmal Theme Routes
Route::prefix('v4')->name('v4.')->middleware([\App\Http\Middleware\IdentifyStorefrontTenant::class])->group(function () {
    Route::get('/', [PageController::class, 'ajmalHome'])->name('home');
    Route::get('/collections', [PageController::class, 'ajmalCollection'])->name('collection');
    Route::get('/all-products', [PageController::class, 'ajmalAllProducts'])->name('all-products');
    Route::get('/product', [PageController::class, 'ajmalProduct'])->name('product');
    Route::get('/combos', [PageController::class, 'ajmalCombos'])->name('combos');
    Route::get('/combo', [PageController::class, 'ajmalCombo'])->name('combo');
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'ajmalCart'])->name('cart');
    Route::get('/checkout', [PageController::class, 'ajmalCheckout'])->name('checkout');
});

// v5 Afnan Theme Routes
Route::prefix('v5')->name('v5.')->middleware([\App\Http\Middleware\IdentifyStorefrontTenant::class])->group(function () {
    Route::get('/', [PageController::class, 'afnanHome'])->name('home');
    Route::get('/collections', [PageController::class, 'afnanCollection'])->name('collection');
    Route::get('/all-products', [PageController::class, 'afnanAllProducts'])->name('all-products');
    Route::get('/product', [PageController::class, 'afnanProduct'])->name('product');
    Route::get('/combos', [PageController::class, 'afnanCombos'])->name('combos');
    Route::get('/combo', [PageController::class, 'afnanCombo'])->name('combo');
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'afnanCart'])->name('cart');
    Route::get('/checkout', [PageController::class, 'afnanCheckout'])->name('checkout');
});


// Storefront Routes

// SaaS Onboarding Routes
Route::prefix('saas')->group(function () {
    Route::post('/register', [App\Http\Controllers\SaaS\RegisterController::class, 'register'])->name('saas.register');
    Route::get('/verify/{id}/{token}', [App\Http\Controllers\SaaS\RegisterController::class, 'verify'])->name('saas.verify');
});

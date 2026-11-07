<?php

require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'session.driver' => 'array', 'cache.default' => 'array', 'app.url' => 'http://127.0.0.1:8000']);
Illuminate\Support\Facades\DB::purge('sqlite');
if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') throw new RuntimeException('In-memory fixtures only');
Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
Illuminate\Support\Facades\Http::preventStrayRequests();
$tenant = App\Models\Tenant::create(['name' => 'The Very Lovely Collection & Everyday Boutique', 'theme' => 'template_12', 'plan' => 'pro']);
$user = App\Models\User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin', 'email' => 'long.merchant.email.for.layout@example.test']);
App\Models\StoreIntegration::create(['tenant_id' => $tenant->id, 'mode' => 'gtm', 'ga4_id' => 'G-EXAMPLE', 'ga4_enabled' => true]);
$mode = $argv[2] ?? '';
// Fixtures must never inherit real provider credentials from the developer environment.
config(['services.razorpay.key' => null, 'services.razorpay.secret' => null, 'services.razorpay.webhook_secret' => null]);
if (in_array($mode, ['--billing', '--billing-resume'], true)) {
    config(['services.razorpay.key' => 'rzp_test_fixture', 'services.razorpay.secret' => 'fixture-api-secret', 'services.razorpay.webhook_secret' => 'fixture-webhook-secret']);
    if ($mode === '--billing-resume') {
        $tenant->update(['razorpay_subscription_id' => 'sub_fixture']);
        App\Models\StoreSubscription::create(['tenant_id' => $tenant->id, 'checkout_attempt' => 'fixture-checkout', 'gateway_id' => 'sub_fixture', 'plan' => 'pro', 'cycle' => 'yearly', 'status' => 'created']);
    }
}

if ($mode === '--expired' || $mode === '--unavailable') {
    $tenant->forceFill(['created_at' => now()->subDays(config('saas.trial_days') + ($mode === '--expired' ? 10 : 31))])->save();
}
if ($mode === '--bundle-create' || $mode === '--bundle-edit') {
    $product = App\Models\Product::create(['tenant_id' => $tenant->id, 'title' => 'Chips <special>', 'status' => 'active']);
    $product->variants()->create(['size' => '100g', 'price' => 100, 'stock' => 10]);
    $large = $product->variants()->create(['size' => '250g', 'price' => 200, 'stock' => 10]);
    $bundle = App\Models\Bundle::create(['tenant_id' => $tenant->id, 'title' => 'Snack Duo', 'slug' => 'snack-duo', 'type' => 'bundle', 'status' => 'active', 'discount_type' => 'fixed', 'discount_value' => 50]);
    $bundle->products()->attach($product->id, ['product_variant_id' => $large->id, 'quantity' => 2]);
}
if (in_array($mode, ['--payment', '--customer-checkout', '--paytm-checkout', '--phonepe-checkout'], true)) {
    $tenant->update(['currency' => 'INR']);
    App\Models\TenantPaymentSetting::create(['tenant_id' => $tenant->id, 'cod_enabled' => true, 'razorpay_enabled' => true,
        'razorpay_key' => 'rzp_test_fixture', 'razorpay_secret' => 'fixture-secret', 'razorpay_webhook_secret' => 'fixture-webhook-secret']);
    if (in_array($mode, ['--paytm-checkout', '--phonepe-checkout'], true)) {
        App\Models\TenantPaymentSetting::withoutGlobalScopes()->where('tenant_id', $tenant->id)->first()->update([
            'cod_enabled' => false, 'paytm_enabled' => true, 'paytm_mid' => 'MIDfixture12345678901', 'paytm_merchant_key' => '1234567890123456',
            'paytm_website_name' => 'WEBSTAGING', 'phonepe_enabled' => true, 'phonepe_client_id' => 'fixture-client', 'phonepe_client_version' => 1,
            'phonepe_client_secret' => 'fixture-client-secret', 'phonepe_webhook_username' => 'fixture-user', 'phonepe_webhook_password' => 'fixture-webhook-password']);
    }
    if (in_array($mode, ['--customer-checkout', '--paytm-checkout', '--phonepe-checkout'], true)) {
        $product = App\Models\Product::create(['tenant_id' => $tenant->id, 'title' => 'Fixture Chips', 'status' => 'active']);
        $variant = $product->variants()->create(['size' => '100g', 'price' => 100, 'stock' => 10]);
        App\Models\Cart::create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'product_id' => $product->id,
            'product_variant_id' => $variant->id, 'quantity' => 1, 'size' => '100g']);
    }
}
Illuminate\Support\Facades\Auth::login($user);
$path = $mode === '--unavailable' ? '/store/'.$tenant->slug : '/'.$tenant->id.'/admin/'.(in_array($mode, ['--expired', '--billing', '--billing-resume'], true) ? 'billing' : 'settings');
if ($mode === '--bundle-create') $path = '/'.$tenant->id.'/admin/bundles/create';
if ($mode === '--bundle-edit') $path = '/'.$tenant->id.'/admin/bundles/'.$bundle->id.'/edit';
if ($mode === '--payment') $path = '/'.$tenant->id.'/admin/settings/payment';
if (in_array($mode, ['--customer-checkout', '--paytm-checkout', '--phonepe-checkout'], true)) $path = '/store/'.$tenant->slug.'/checkout';
$response = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::create('http://127.0.0.1:8000'.$path));
if ($response->getStatusCode() !== ($mode === '--unavailable' ? 503 : 200) || empty($argv[1])) throw new RuntimeException('Settings fixture failed');
file_put_contents($argv[1], $response->getContent());
echo "Rendered Settings using an isolated in-memory database.\n";

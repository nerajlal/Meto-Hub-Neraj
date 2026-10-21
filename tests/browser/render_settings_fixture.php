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
Illuminate\Support\Facades\Auth::login($user);
$path = $mode === '--unavailable' ? '/store/'.$tenant->slug : '/'.$tenant->id.'/admin/'.(in_array($mode, ['--expired', '--billing', '--billing-resume'], true) ? 'billing' : 'settings');
$response = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::create('http://127.0.0.1:8000'.$path));
if ($response->getStatusCode() !== ($mode === '--unavailable' ? 503 : 200) || empty($argv[1])) throw new RuntimeException('Settings fixture failed');
file_put_contents($argv[1], $response->getContent());
echo "Rendered Settings using an isolated in-memory database.\n";

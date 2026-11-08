<?php
// Render safe local previews without connecting to the configured application database.
$project = dirname(__DIR__, 2);
require $project.'/vendor/autoload.php';
$app = require $project.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'session.driver' => 'array', 'cache.default' => 'array']);
Illuminate\Support\Facades\DB::purge('sqlite');
if (config('database.connections.sqlite.database') !== ':memory:') throw new RuntimeException('In-memory fixtures only.');
Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
if (empty($argv[1]) || !is_dir($argv[1])) throw new RuntimeException('Pass an existing output directory.');
$user = App\Models\User::factory()->create(['type' => 'super_admin', 'tenant_id' => null]);
Illuminate\Support\Facades\Auth::login($user);
foreach (['Monthly studio', 'Yearly shop', 'Renewal follow-up'] as $i => $name) {
    $store = App\Models\Tenant::create(['name' => $name, 'plan' => 'pro', 'subscription_status' => 'active']);
    $gateway = 'sub_fixture_'.$i;
    $store->update(['razorpay_subscription_id' => $gateway]);
    $subscription = App\Models\StoreSubscription::create(['tenant_id' => $store->id, 'checkout_attempt' => Illuminate\Support\Str::uuid(), 'gateway_id' => $gateway,
        'plan' => 'pro', 'cycle' => $i === 1 ? 'yearly' : 'monthly', 'status' => 'active', 'paid_count' => 1, 'synced_at' => now(),
        'paid_from' => now()->subDays(20), 'paid_through' => now()->addDays($i === 2 ? -2 : 10), 'next_charge_at' => now()->addDays(3)]);
    App\Models\SubscriptionPayment::create(['store_subscription_id' => $subscription->id, 'gateway_id' => 'pay_fixture_'.$i, 'amount' => 24900,
        'currency' => 'INR', 'status' => 'captured', 'paid_at' => now()->subDay(), 'attempted_at' => now()->subDay()]);
}
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
foreach (['dashboard' => '/superadmin', 'payments' => '/superadmin/payments'] as $name => $path) {
    $response = $kernel->handle(Illuminate\Http\Request::create('http://localhost'.$path));
    if ($response->getStatusCode() !== 200) throw new RuntimeException('Fixture failed: '.$name.' '.$response->getStatusCode());
    file_put_contents($argv[1].'/superadmin-'.$name.'.html', $response->getContent());
}
echo "Rendered isolated superadmin fixtures.\n";

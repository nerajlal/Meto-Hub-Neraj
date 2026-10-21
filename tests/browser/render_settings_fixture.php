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
Illuminate\Support\Facades\Auth::login($user);
$response = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::create('http://127.0.0.1:8000/'.$tenant->id.'/admin/settings'));
if ($response->getStatusCode() !== 200 || empty($argv[1])) throw new RuntimeException('Settings fixture failed');
file_put_contents($argv[1], $response->getContent());
echo "Rendered Settings using an isolated in-memory database.\n";

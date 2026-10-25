<?php
// Critical test-environment checks without requiring intentionally disabled providers.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$checks = [
    'Expected HTTPS URL' => config('app.url') === getenv('DEPLOY_EXPECTED_URL'),
    'Production runtime, debug off' => config('app.env') === 'production' && !config('app.debug'),
    'Application key' => (bool) config('app.key'),
    'Secure cookies' => config('session.secure') === true && config('session.http_only') === true,
    'MySQL' => config('database.default') === 'mysql',
    'Persistent queue' => config('queue.default') === 'database',
    'Queue retry timeout' => (int) config('queue.connections.database.retry_after') > 240,
];
if (getenv('DEPLOY_EXPECTED_URL') === 'https://test.metohub.cloud') {
    $checks['Test indexing disabled'] = config('search.indexable') === false;
}
try {
    Illuminate\Support\Facades\DB::select('SELECT 1');
    $migrator = app('migrator');
    $pending = array_diff(array_keys($migrator->getMigrationFiles(database_path('migrations'))), $migrator->getRepository()->getRan());
    $checks['No pending migrations'] = count($pending) === 0;
} catch (Throwable $e) {
    $checks['Database and migration state'] = false;
}
foreach ([storage_path(), storage_path('app/private'), public_path('storage'), base_path('bootstrap/cache')] as $path) {
    $checks['Writable '.$path] = is_dir($path) && is_writable($path);
}
$checks['Public upload mapping'] = realpath(config('filesystems.disks.public.root')) === realpath(public_path('storage'));
$failed = array_keys(array_filter($checks, fn ($ok) => !$ok));
foreach ($failed as $check) {
    fwrite(STDERR, "Failed: $check\n");
}
exit($failed ? 1 : 0);

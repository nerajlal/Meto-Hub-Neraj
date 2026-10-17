<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductionPreflight extends Command
{
    protected $signature = 'app:preflight';

    protected $description = 'Check production configuration without exposing credentials or sending email';

    public function handle(): int
    {
        $errors = [];
        $check = function (bool $ok, string $message) use (&$errors): void {
            if (! $ok) {
                $errors[] = $message;
            }
        };

        $check(config('app.env') === 'production', 'APP_ENV must be production.');
        $check(! config('app.debug'), 'APP_DEBUG must be false.');
        $url = parse_url((string) config('app.url'));
        $check(($url['scheme'] ?? null) === 'https' && ! in_array($url['host'] ?? '', ['', 'localhost', '127.0.0.1'], true), 'APP_URL must use the public HTTPS domain.');
        $check((bool) config('app.key'), 'APP_KEY is missing; preserve the existing production key.');
        $check(config('session.secure') === true && config('session.http_only') === true, 'Session cookies must be secure and HTTP-only.');
        $check(in_array(config('session.same_site'), ['lax', 'strict'], true), 'Session SameSite must be lax or strict.');

        $mailer = config('mail.default');
        $check(! in_array($mailer, ['log', 'array', 'failover', 'roundrobin'], true), 'Use a real delivery mailer; log/array and fallback logging cannot verify email delivery.');
        $sender = config('mail.from.address');
        $check((bool) filter_var($sender, FILTER_VALIDATE_EMAIL) && ! str_ends_with((string) $sender, '@example.com'), 'Set a real MAIL_FROM_ADDRESS authorized by your provider.');
        if ($mailer === 'smtp') {
            $smtp = config('mail.mailers.smtp');
            $check(! empty($smtp['host']) && ! in_array($smtp['host'], ['localhost', '127.0.0.1'], true), 'MAIL_HOST must be your SMTP provider hostname.');
            $check(! empty($smtp['username']) && ! empty($smtp['password']), 'SMTP username/password are missing.');
            $check(in_array((int) ($smtp['port'] ?? 0), [465, 587], true), 'Use SMTP port 465 or 587 for encrypted delivery.');
            $check(in_array($smtp['scheme'] ?? null, ['smtp', 'smtps'], true), 'Set MAIL_SCHEME to smtp (STARTTLS) or smtps.');
        } elseif ($mailer === 'resend' || $mailer === 'postmark') {
            $check((bool) config("services.{$mailer}.key"), 'Email provider API key is missing.');
        } else {
            $check(false, 'Preflight supports SMTP, Resend, or Postmark; configure and validate a supported delivery provider.');
        }

        $notificationQueue = config('order_notifications.queue_connection');
        $notificationDriver = config('queue.connections.'.$notificationQueue.'.driver');
        $check(in_array($notificationDriver, ['database', 'redis', 'sqs', 'beanstalkd'], true), 'Owner notifications require a persistent background queue connection.');
        if ($notificationDriver === 'database' || $notificationDriver === 'redis' || $notificationDriver === 'beanstalkd') {
            $check((int) config('queue.connections.'.$notificationQueue.'.retry_after') > 240, 'Notification queue retry_after must exceed the 240-second job timeout (use 300).');
        }
        $pushPublic = config('order_notifications.vapid.public_key');
        $pushPrivate = config('order_notifications.vapid.private_key');
        if ($pushPublic || $pushPrivate) {
            $check((bool) $pushPublic && (bool) $pushPrivate, 'Web Push needs both public and private VAPID keys.');
        }

        $check((bool) config('services.razorpay.key') && (bool) config('services.razorpay.secret'), 'Configure Razorpay subscription credentials before charging for plans.');
        $check((bool) config('services.razorpay.webhook_secret'), 'RAZORPAY_WEBHOOK_SECRET is missing; configure signed subscription webhooks.');

        try {
            DB::connection()->getPdo();
            $migrator = app('migrator');
            $ran = $migrator->repositoryExists() ? $migrator->getRepository()->getRan() : [];
            $pending = array_diff(array_keys($migrator->getMigrationFiles(database_path('migrations'))), $ran);
            $check(count($pending) === 0, 'Pending database migrations: '.count($pending).'. Run php artisan migrate --force.');
        } catch (\Throwable $error) {
            $errors[] = 'Database connection or migration check failed. Verify server database settings.';
        }

        foreach ([storage_path(), storage_path('framework'), storage_path('logs'), base_path('bootstrap/cache')] as $path) {
            $check(is_dir($path) && is_writable($path), 'Required runtime directory is not writable: '.$path);
        }
        $root = config('filesystems.disks.public.root');
        $check(is_string($root) && is_dir($root) && is_writable($root) && realpath($root) === realpath(public_path('storage')), 'Public upload disk must be writable and served at public/storage. Preserve existing uploads.');

        foreach ($errors as $message) {
            $this->error($message);
        }
        if ($errors) {
            return self::FAILURE;
        }

        $this->info('Production configuration checks passed.');
        $this->warn('Still validate real email delivery, backups/restores, HTTPS/proxy setup, uptime monitoring, and load on the production server.');

        return self::SUCCESS;
    }
}

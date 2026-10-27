<?php

namespace App\Services;

use App\Models\StoreIntegration;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class IntegrationDomainVerification
{
    public function isPlatformHost(string $host): bool
    {
        $host = strtolower($host);
        $platform = strtolower((string) parse_url(config('app.url'), PHP_URL_HOST));

        return in_array($host, array_filter([$platform, 'www.'.$platform, 'localhost', '127.0.0.1', 'metohub.com', 'www.metohub.com', 'betametohub.metora.in', 'metohub.cloud', 'www.metohub.cloud']));
    }

    public function domain(Tenant $tenant): string
    {
        $domain = strtolower(trim((string) $tenant->domain));
        $platform = strtolower((string) parse_url(config('app.url'), PHP_URL_HOST));
        if (app(StoreDomains::class)->withinNamespace($domain) && ! app(StoreDomains::class)->availableHostname($domain)) {
            throw ValidationException::withMessages(['domain' => 'This platform hostname is reserved or is not a single store subdomain.']);
        }
        if (! $domain || ! filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) || ! str_contains($domain, '.') || filter_var($domain, FILTER_VALIDATE_IP) || $this->isPlatformHost($domain) || str_ends_with($platform, '.'.$domain) || in_array($domain, ['localhost', 'localhost.localdomain']) || str_ends_with($domain, '.local')) {
            throw ValidationException::withMessages(['domain' => 'Connect a valid custom domain different from the platform domain first.']);
        }

        return $domain;
    }

    public function challenge(Tenant $tenant, StoreIntegration $settings): void
    {
        $domain = $this->domain($tenant);
        if ($settings->verified_domain === $domain && $settings->domain_token) {
            return;
        }
        $settings->forceFill(['verified_domain' => $domain, 'domain_token' => Str::random(48), 'domain_verified_at' => null])->save();
    }

    public function records(string $domain): array
    {
        if (! function_exists('dns_get_record')) {
            throw ValidationException::withMessages(['domain' => 'DNS verification is unavailable on this server. Ask the hosting provider to enable DNS TXT lookups.']);
        }
        $records = @dns_get_record('_metohub-verification.'.$domain, DNS_TXT);
        if ($records === false) {
            throw ValidationException::withMessages(['domain' => 'DNS lookup failed. Please retry; your existing verification has not been changed.']);
        }

        return $records;
    }

    public function verify(Tenant $tenant, StoreIntegration $settings): void
    {
        $domain = $this->domain($tenant);
        if ($settings->verified_domain !== $domain || ! $settings->domain_token) {
            throw ValidationException::withMessages(['domain' => 'Generate the DNS verification record first.']);
        }
        $expected = 'metohub='.$settings->domain_token;
        foreach ($this->records($domain) as $record) {
            $value = $record['txt'] ?? implode('', $record['entries'] ?? []);
            if (hash_equals($expected, $value)) {
                $settings->forceFill(['domain_verified_at' => now()])->save();

                return;
            }
        }
        $settings->forceFill(['domain_verified_at' => null])->save();
        throw ValidationException::withMessages(['domain' => 'Verification record not found. Check the TXT name/value and allow time for DNS propagation.']);
    }

    public function eligible(Tenant $tenant, ?StoreIntegration $settings): bool
    {
        if (! $settings?->domain_verified_at || ! $settings->domain_token || $settings->domain_verified_at->lt(now()->subDays(30))) {
            return false;
        }
        try {
            $domain = $this->domain($tenant);
        } catch (ValidationException) {
            return false;
        }

        return $settings->verified_domain === $domain && app(PlanAccess::class)->allows($tenant, 'custom_domain') && ! config('session.domain');
    }
}

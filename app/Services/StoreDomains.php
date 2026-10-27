<?php

namespace App\Services;

class StoreDomains
{
    public function base(): string
    {
        return strtolower((string) (config('store_domains.base') ?: parse_url(config('app.url'), PHP_URL_HOST)));
    }

    public function withinNamespace(string $host): bool
    {
        return $this->base() !== '' && str_ends_with(strtolower($host), '.'.$this->base());
    }

    public function availableHostname(string $host): bool
    {
        $host = strtolower($host);
        if (! $this->withinNamespace($host)) {
            return false;
        }
        $label = substr($host, 0, -strlen('.'.$this->base()));

        return strlen($label) <= 63
            && (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $label)
            && ! in_array($label, config('store_domains.reserved'), true);
    }
}

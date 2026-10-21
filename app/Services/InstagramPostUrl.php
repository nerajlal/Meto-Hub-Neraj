<?php

namespace App\Services;

class InstagramPostUrl
{
    public static function normalize(mixed $value): ?string
    {
        if (! is_string($value) || strlen($value) > 500) return null;
        $parts = parse_url($value);
        if (! is_array($parts) || ($parts['scheme'] ?? '') !== 'https'
            || ! in_array(strtolower($parts['host'] ?? ''), ['instagram.com', 'www.instagram.com'], true)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])
            || ! preg_match('~^/(reel|reels|p)/([A-Za-z0-9_-]{1,100})/?$~D', $parts['path'] ?? '', $match)) return null;

        return 'https://www.instagram.com/'.($match[1] === 'reels' ? 'reel' : $match[1]).'/'.$match[2].'/';
    }
}

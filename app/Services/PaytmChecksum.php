<?php

namespace App\Services;

use paytm\paytmchecksum\PaytmChecksum as OfficialChecksum;

class PaytmChecksum
{
    public static function sign(array|string $body, string $key): string
    {
        return OfficialChecksum::generateSignature($body, $key);
    }

    public static function valid(array|string $body, string $key, mixed $signature): bool
    {
        if (!is_string($signature) || strlen($signature) > 2048 || !base64_decode($signature, true)) return false;
        if (is_array($body)) foreach ($body as $value) if (!is_scalar($value) && $value !== null) return false;
        try { return OfficialChecksum::verifySignature($body, $key, $signature) === true; }
        catch (\Throwable $e) { return false; }
    }
}

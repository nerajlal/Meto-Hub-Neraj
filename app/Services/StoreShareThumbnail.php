<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;

class StoreShareThumbnail
{
    public const SIZE = 200;

    private function localLogo(Tenant $tenant): ?string
    {
        $path = $tenant->seo_image ?: $tenant->logo;
        if (!$path || preg_match('~^https?://~i', $path)) return null; // Never fetch remote URLs from this public endpoint.
        $path = ltrim($path, '/');
        $diskRoot = Storage::disk('public')->path('');
        $candidate = str_starts_with($path, 'storage/') ? Storage::disk('public')->path(substr($path, 8))
            : (str_starts_with(strtolower($path), 'images/') ? public_path($path) : Storage::disk('public')->path($path));
        $real = realpath($candidate);
        if (!$real || !is_file($real) || filesize($real) > 10 * 1024 * 1024) return null;
        foreach ([$diskRoot, public_path('images'), public_path('Images')] as $root) {
            $root = realpath($root);
            if ($root && str_starts_with($real, $root.DIRECTORY_SEPARATOR)) return $real;
        }
        return null;
    }

    public function version(Tenant $tenant): string
    {
        $logo = $this->localLogo($tenant);
        return substr(hash('sha256', json_encode([$tenant->name, $tenant->seo_image, $tenant->logo, $tenant->primary_color,
            $tenant->updated_at?->getTimestamp(), $logo ? filemtime($logo) : null, $logo ? filesize($logo) : null])), 0, 16);
    }

    public function render(Tenant $tenant): string
    {
        $canvas = imagecreatetruecolor(self::SIZE, self::SIZE);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        $source = null;
        if ($logo = $this->localLogo($tenant)) {
            $dimensions = @getimagesize($logo);
            if ($dimensions && $dimensions[0] * $dimensions[1] <= 16000000) $source = @imagecreatefromstring(file_get_contents($logo));
        }
        if ($source) {
            $scale = min(176 / imagesx($source), 176 / imagesy($source));
            $width = max(1, (int) round(imagesx($source) * $scale));
            $height = max(1, (int) round(imagesy($source) * $scale));
            imagecopyresampled($canvas, $source, (int) ((200 - $width)/2), (int) ((200 - $height)/2), 0, 0, $width, $height, imagesx($source), imagesy($source));
            imagedestroy($source);
        } else {
            $hex = preg_match('/^#[a-f0-9]{6}$/i', $tenant->primary_color ?? '') ? $tenant->primary_color : '#234d3c';
            $color = imagecolorallocate($canvas, hexdec(substr($hex,1,2)), hexdec(substr($hex,3,2)), hexdec(substr($hex,5,2)));
            imagefilledellipse($canvas, 100, 100, 176, 176, $color);
            $letter = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $tenant->name), 0, 1)) ?: 'S';
            // A readable initial when the store has no supported local logo.
            $glyph = imagecreatetruecolor(20, 24);
            imagefill($glyph, 0, 0, $color);
            imagestring($glyph, 5, 6, 4, $letter, imagecolorallocate($glyph, 255, 255, 255));
            imagecopyresized($canvas, $glyph, 60, 52, 0, 0, 80, 96, 20, 24);
            imagedestroy($glyph);
        }
        ob_start();
        imagepng($canvas, null, 6);
        $png = ob_get_clean();
        imagedestroy($canvas);
        return $png;
    }
}

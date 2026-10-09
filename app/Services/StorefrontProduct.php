<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorefrontProduct
{
    public function data(Product $product): array
    {
        $discount = $product->active_discount;
        $variants = $product->variants->map(function ($variant) use ($discount, $product) {
            $base = (float) $variant->price;
            $price = $discount ? max(0, $discount->type === 'percentage'
                ? $base * (1 - $discount->value / 100) : $base - $discount->value) : $base;
            return ['id' => $variant->id, 'size' => $variant->size ?: '', 'price' => round($price, 2),
                'compare' => max((float) $variant->compare_at_price, $discount ? $base : 0),
                'stock' => (int) $variant->stock, 'available' => $variant->stock > 0 || (bool) $product->continue_selling_when_out_of_stock,
                'sku' => $variant->sku];
        })->sortBy('price')->values();
        $images = $product->images->map(fn ($image) => $this->imageUrl($image->path))->values();
        return ['variants' => $variants, 'selected' => $variants->first(),
            'images' => $images->isNotEmpty() ? $images : collect([asset('images/default.png')])];
    }

    public function imageUrl(string $path): string
    {
        if (Str::startsWith($path, ['https://', 'http://', '/'])) {
            return $path;
        }
        if (Str::startsWith(strtolower($path), 'images/')) {
            return asset('images/'.Str::after($path, '/'));
        }
        return Storage::url($path);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    use HasFactory, \App\Models\Traits\BelongsToTenant;

    protected $attributes = ['type' => 'bundle'];

    protected $fillable = [
        'title',
        'card_background',
        'slug',
        'type',
        'description',
        'image',
        'status',
        'discount_type',
        'discount_value',
        'min_quantity',
        'tenant_id',
        'min_order_qty',
        'max_order_qty',
    ];

    protected $casts = ['card_background' => 'array'];

    public function promoBackgroundStyle(): ?string
    {
        $settings = $this->card_background ?? [];
        $mode = $settings['mode'] ?? 'auto';
        $color = $settings['color'] ?? '';
        if (!in_array($mode, ['solid', 'gradient'], true) || !preg_match('/^#[a-f0-9]{6}$/i', $color)) return null;
        $background = $color;
        $contrastColor = $color;
        if ($mode === 'gradient') {
            $end = $settings['color_end'] ?? '';
            if (!preg_match('/^#[a-f0-9]{6}$/i', $end)) return null;
            $angle = max(0, min(360, (int) ($settings['angle'] ?? 135)));
            $background = "linear-gradient({$angle}deg, {$color}, {$end})";
            $contrastColor = '#';
            foreach ([1, 3, 5] as $offset) {
                $contrastColor .= sprintf('%02x', (int) round((hexdec(substr($color, $offset, 2)) + hexdec(substr($end, $offset, 2))) / 2));
            }
        }
        $text = app(\App\Services\StorefrontTheme::class)->contrastText($contrastColor);
        return "--bundle-promo-background:{$background};--bundle-promo-text:{$text}";
    }

    protected static function booted(): void
    {
        static::created(fn (Bundle $bundle) => app(\App\Services\OfferCollection::class)->assign($bundle));
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function getBasePriceAttribute()
    {
        $total = 0;
        foreach ($this->products as $product) {
            $quantity = $product->pivot->quantity ?? 1;
            $variantId = $product->pivot->product_variant_id;
            
            if ($variantId) {
                $variant = $product->variants->firstWhere('id', $variantId);
                $price = $variant ? $variant->price : ($product->variants->min('price') ?? 0);
            } else {
                $price = $product->variants->min('price') ?? 0;
            }
            
            $total += ($price * $quantity);
        }
        return $total;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundle_product')->withPivot('quantity', 'product_variant_id')->withTimestamps();
    }

    public function getTotalPriceAttribute()
    {
        $total = 0;
        foreach ($this->products as $product) {
            $quantity = $product->pivot->quantity ?? 1;
            $variantId = $product->pivot->product_variant_id;
            
            if ($variantId) {
                $variant = $product->variants->firstWhere('id', $variantId);
                $price = $variant ? $variant->price : ($product->variants->min('price') ?? 0);
            } else {
                $price = $product->variants->min('price') ?? 0;
            }
            
            $total += ($price * $quantity);
        }

        if ($this->discount_value > 0) {
            if ($this->discount_type === 'percentage') {
                $total = $total - ($total * ($this->discount_value / 100));
            } else {
                $total = $total - $this->discount_value;
            }
        }

        return max(0, $total);
    }

    public function getIsOutOfStockAttribute()
    {
        if ($this->products->isEmpty()) return true;
        $required = [];
        foreach ($this->products as $product) {
            if ($product->status !== 'active') return true;
            $variant = $product->pivot->product_variant_id
                ? $product->variants->firstWhere('id', $product->pivot->product_variant_id)
                : $product->variants->sortBy('price')->first();
            if (!$variant) return true;
            $required[$variant->id] = ($required[$variant->id] ?? 0) + ($product->pivot->quantity ?? 1);
            if (!$product->continue_selling_when_out_of_stock && $variant->stock < $required[$variant->id]) return true;
        }
        return false;
    }
}

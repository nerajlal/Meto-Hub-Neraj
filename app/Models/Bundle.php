<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    use HasFactory, \App\Models\Traits\BelongsToTenant;

    protected $fillable = [
        'title',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_image_id',
        'size',
        'price',
        'compare_at_price',
        'stock',
        'sku',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function image()
    {
        return $this->belongsTo(ProductImage::class, 'product_image_id');
    }

    public function images()
    {
        return $this->belongsToMany(ProductImage::class, 'product_image_product_variant')
            ->withPivot('order')
            ->orderByPivot('order')
            ->withTimestamps();
    }

    public function getPriceAttribute($value)
    {
        if (auth()->check()) {
            // 1. Check individual custom price
            $custom = \App\Models\CustomPrice::where('user_id', auth()->id())
                ->where('product_id', $this->product_id)
                ->first();
            if ($custom) {
                return $custom->price;
            }

            // 2. Check group custom price
            $groupPrice = \App\Models\GroupCustomPrice::whereHas('group.users', function($q) {
                $q->where('users.id', auth()->id());
            })
            ->where('product_id', $this->product_id)
            ->first();
            if ($groupPrice) {
                return $groupPrice->price;
            }
        }
        return $value;
    }
}

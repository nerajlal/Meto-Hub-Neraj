<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, \App\Models\Traits\BelongsToTenant;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'type',
        'vendor',
        'collection_id',
        'gender',
        'olfactory_family',
        'intensity',
        'oil_concentration',
        'notes_top',
        'notes_heart',
        'notes_base',
        'min_order_qty',
        'max_order_qty',
        'tenant_id',
        'continue_selling_when_out_of_stock',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $slug = Str::slug($product->title);
                $count = Product::withoutGlobalScopes()->where('slug', 'LIKE', "{$slug}%")->count();
                if ($count > 0) {
                    $slug .= '-' . ($count + 1);
                }
                $product->slug = $slug;
            }
        });
        static::updating(function ($product) {
            if (empty($product->slug) || ($product->isDirty('title') && !$product->isDirty('slug'))) {
                $slug = Str::slug($product->title);
                $count = Product::withoutGlobalScopes()->where('slug', 'LIKE', "{$slug}%")->where('id', '!=', $product->id)->count();
                if ($count > 0) {
                    $slug .= '-' . ($count + 1);
                }
                $product->slug = $slug;
            }
        });
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(\App\Models\Discount::class, 'discount_product');
    }

    public function bundles()
    {
        return $this->belongsToMany(\App\Models\Bundle::class, 'bundle_product')->withPivot('quantity', 'product_variant_id')->withTimestamps();
    }

    public function getStartingPriceAttribute()
    {
        return $this->variants->min('price');
    }

    public function getCompareAtPriceAttribute()
    {
        $variant = $this->variants->sortBy('price')->first();
        return $variant ? $variant->compare_at_price : null;
    }

    public function getMainImageUrlAttribute()
    {
        $image = $this->images->first();
        return $image ? \Illuminate\Support\Facades\Storage::url($image->path) : null;
    }
}

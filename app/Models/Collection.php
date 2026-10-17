<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory, \App\Models\Traits\BelongsToTenant;

    protected $fillable = [
        'seo_title', 'seo_description', 'seo_image',
        'name',
        'slug',
        'description',
        'image',
        'status',
        'tenant_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'plan',
        'theme',
        'about_title',
        'about_text',
        'contact_email',
        'contact_phone',
        'contact_address',
        'shipping_policy',
        'return_policy',
        'terms_of_service',
        'mobile_grid_cols',
        'logo',
        'primary_color',
        'dark_color',
        'accent_color',
        'currency',
        'tax_name',
        'tax_rate',
    ];

    public function admin()
    {
        return $this->hasOne(User::class, 'tenant_id')->where('type', 'admin');
    }
}

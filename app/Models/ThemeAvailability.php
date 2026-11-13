<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ThemeAvailability extends Model
{
    protected $fillable = ['theme', 'is_available'];
    protected $casts = ['is_available' => 'boolean'];
}

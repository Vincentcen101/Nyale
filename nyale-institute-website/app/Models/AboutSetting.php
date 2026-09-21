<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutSetting extends Model
{
    use HasFactory;

    protected $table = 'about_settings';

    protected $fillable = [
        'section',
        'title',
        'content',
        'image',
        'is_active',
        'order',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public static function getBySection($section)
    {
        return self::where('section', $section)->where('is_active', true)->first();
    }
}

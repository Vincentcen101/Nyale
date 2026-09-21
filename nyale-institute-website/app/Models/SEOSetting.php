<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SEOSetting extends Model
{
    use HasFactory;

    protected $table = 'seo_settings';

    protected $fillable = [
        'page',
        'meta_title',
        'meta_description',
        'meta_image',
    ];

    public static function getForPage($page)
    {
        return self::where('page', $page)->first() ?? self::where('page', 'global')->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Pages the first slide button can point to, as link => button text.
     * Leaving it unset shows the default "Learn More" button linking to /about.
     * "Get Involved" is left out because every slide already has that button.
     */
    public const BUTTON_OPTIONS = [
        '/programs' => 'Our Programs',
        '/our-impact' => 'See Our Impact',
        '/case-tracker' => 'Track Cases',
        '/campaigns' => 'View Campaigns',
        '/events' => 'View Events',
        '/news' => 'Read the News',
        '/knowledge-hub' => 'Explore Resources',
    ];

    protected $fillable = [
        'title',
        'eyebrow',
        'description',
        'image',
        'button_label',
        'button_url',
        'is_active',
        'order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderByDesc('id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'body',
        'image',
        'location',
        'starts_at',
        'ends_at',
        'registration_url',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime:Y-m-d\TH:i',
        'ends_at' => 'datetime:Y-m-d\TH:i',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

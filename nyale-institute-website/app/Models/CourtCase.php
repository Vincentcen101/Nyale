<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourtCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'case_number',
        'court',
        'lawyer',
        'status',
        'summary',
        'body',
        'image',
        'case_date',
        'likes_count',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'case_date' => 'date:Y-m-d',
        'likes_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function comments()
    {
        return $this->hasMany(CaseComment::class)->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

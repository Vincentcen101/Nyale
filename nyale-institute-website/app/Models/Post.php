<?php

namespace App\Models;

use App\Support\RichText;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'work_area_id',
        'excerpt',
        'body',
        'cover_image',
        'video_url',
        'is_active',
        'likes_count',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'likes_count' => 'integer',
        'published_at' => 'date:Y-m-d',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Posts no longer have a separate excerpt field; cards show the start of the post body instead.
    protected function excerpt(): Attribute
    {
        return Attribute::get(fn ($value, array $attributes) => $value ?: Str::limit(RichText::plain($attributes['body'] ?? ''), 160));
    }

    public function program()
    {
        return $this->belongsTo(WorkArea::class, 'work_area_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class)->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('published_at');
    }
}

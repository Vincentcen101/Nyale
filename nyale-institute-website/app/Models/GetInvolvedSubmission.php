<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GetInvolvedSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'organisation',
        'phone',
        'type',
        'message',
        'status',
    ];

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}

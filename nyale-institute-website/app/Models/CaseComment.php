<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'court_case_id',
        'name',
        'email',
        'body',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function courtCase()
    {
        return $this->belongsTo(CourtCase::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'reviewer_id', 'period', 'year',
        'productivity_score', 'quality_score', 'teamwork_score',
        'initiative_score', 'attendance_score', 'overall_score',
        'strengths', 'improvements', 'goals', 'comments', 'status',
    ];

    protected function casts(): array
    {
        return ['overall_score' => 'decimal:1'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}

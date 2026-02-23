<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'provider', 'start_date',
        'end_date', 'max_participants', 'is_mandatory', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function participants()
    {
        return $this->hasMany(TrainingParticipant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'training_participants')->withPivot('status', 'score', 'completion_date');
    }
}

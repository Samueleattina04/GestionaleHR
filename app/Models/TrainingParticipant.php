<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_course_id', 'user_id', 'status',
        'score', 'completion_date', 'certificate_path',
    ];

    protected function casts(): array
    {
        return ['completion_date' => 'date'];
    }

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'training_course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

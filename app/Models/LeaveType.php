<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'max_days_per_year', 'is_paid', 'requires_document', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_paid' => 'boolean', 'requires_document' => 'boolean', 'is_active' => 'boolean'];
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'surname', 'email', 'phone', 'fiscal_code',
        'birth_date', 'birth_place', 'address', 'city', 'province', 'zip_code',
        'role', 'department_id', 'job_title', 'contract_type',
        'hire_date', 'contract_end_date', 'salary', 'iban',
        'avatar', 'status', 'approved_by', 'approved_at', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'birth_date' => 'date',
            'hire_date' => 'date',
            'contract_end_date' => 'date',
            'password' => 'hashed',
            'salary' => 'decimal:2',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->surname}";
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function expenseReports()
    {
        return $this->hasMany(ExpenseReport::class);
    }

    public function trainingParticipations()
    {
        return $this->hasMany(TrainingParticipant::class);
    }

    public function managedDepartment()
    {
        return $this->hasOne(Department::class, 'manager_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHr(): bool
    {
        return $this->role === 'hr';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canManage(User $user): bool
    {
        if ($this->isAdmin() || $this->isHr()) return true;
        if ($this->isManager() && $this->managedDepartment && $user->department_id === $this->managedDepartment->id) return true;
        return false;
    }
}

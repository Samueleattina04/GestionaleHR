<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'month', 'year', 'base_salary', 'overtime_pay',
        'bonuses', 'deductions', 'tax', 'inps_contribution',
        'net_salary', 'status', 'payment_date', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'base_salary' => 'decimal:2',
            'overtime_pay' => 'decimal:2',
            'bonuses' => 'decimal:2',
            'deductions' => 'decimal:2',
            'tax' => 'decimal:2',
            'inps_contribution' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getMonthNameAttribute(): string
    {
        $months = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
        return $months[$this->month - 1] ?? '';
    }
}

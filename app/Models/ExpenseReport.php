<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'amount', 'category',
        'expense_date', 'receipt_path', 'status', 'reviewed_by',
        'reviewed_at', 'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'reviewed_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

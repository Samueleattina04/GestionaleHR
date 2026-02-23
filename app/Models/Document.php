<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'type', 'file_path', 'file_name',
        'mime_type', 'file_size', 'description', 'uploaded_by',
        'expiry_date', 'is_private',
    ];

    protected function casts(): array
    {
        return ['expiry_date' => 'date', 'is_private' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

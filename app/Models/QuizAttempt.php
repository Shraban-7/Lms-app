<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAttempt extends Model
{
    use HasFactory;

    public $timestamps = false; // We use created_at manually or custom timestamp column

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'answers',
        'passed',
        'created_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'passed' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}

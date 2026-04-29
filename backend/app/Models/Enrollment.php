<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'completed_lessons',
        'completed_lesson_ids',
        'progress_percentage',
        'completed_at',
        'certificate_generated_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'certificate_generated_at' => 'datetime',
            'completed_lesson_ids' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}

<?php

namespace App\Models;

use Database\Factories\InterviewFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subject_user_id', 'manager_id', 'type', 'method', 'location', 'candidate_times', 'scheduled_at', 'content', 'next_tasks', 'next_timing'])]
class Interview extends Model
{
    /** @use HasFactory<InterviewFactory> */
    use HasFactory;

    public function subject(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subject_user_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    protected function casts(): array
    {
        return ['candidate_times' => 'array', 'scheduled_at' => 'datetime'];
    }
}

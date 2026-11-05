<?php

namespace App\Models;

use Database\Factories\ReportQuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_id', 'type', 'question', 'options', 'display_order'])]
class ReportQuestion extends Model
{
    /** @use HasFactory<ReportQuestionFactory> */
    use HasFactory;

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function casts(): array
    {
        return ['options' => 'array'];
    }
}

<?php

namespace App\Models;

use Database\Factories\QualificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'expires_at', 'certificate_path'])]
class Qualification extends Model
{
    /** @use HasFactory<QualificationFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return ['expires_at' => 'date'];
    }
}

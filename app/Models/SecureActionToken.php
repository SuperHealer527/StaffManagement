<?php

namespace App\Models;

use Database\Factories\SecureActionTokenFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['user_id', 'actionable_type', 'actionable_id', 'purpose', 'token_hash', 'expires_at', 'opened_at', 'used_at'])]
class SecureActionToken extends Model
{
    /** @use HasFactory<SecureActionTokenFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isUsable(): bool
    {
        return $this->used_at === null
            && $this->expires_at->isFuture()
            && ($this->opened_at === null || $this->opened_at->copy()->addMinutes(30)->isFuture());
    }

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'opened_at' => 'datetime', 'used_at' => 'datetime'];
    }
}

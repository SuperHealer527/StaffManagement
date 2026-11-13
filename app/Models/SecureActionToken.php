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

    public function isEligibleForPurpose(): bool
    {
        if ($this->purpose === 'first_password') {
            return $this->user?->account_status === 'invited' && $this->user?->role !== 'temporary';
        }

        if ($this->purpose === 'temporary_profile') {
            return $this->user?->role === 'temporary' && $this->user?->account_status === 'no_login';
        }

        if ($this->purpose === 'line_connect') {
            if ($this->user === null) {
                return false;
            }

            return $this->user->role === 'temporary'
                ? $this->user->account_status === 'no_login'
                : in_array($this->user->account_status, ['active', 'invited'], true);
        }

        if (! $this->actionable instanceof Assignment) {
            return true;
        }

        if ($this->purpose === 'assignment_response') {
            return true;
        }

        if (in_array($this->purpose, ['attendance_confirmation', 'attendance_seven_day', 'attendance_same_day'], true)) {
            return in_array($this->actionable->status, ['confirmed', 'completed'], true);
        }

        if ($this->purpose === 'report_submission') {
            $this->actionable->loadMissing('event');

            return in_array($this->actionable->status, ['confirmed', 'completed'], true)
                && $this->actionable->is_report_representative
                && $this->actionable->event->report_required;
        }

        return true;
    }

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'opened_at' => 'datetime', 'used_at' => 'datetime'];
    }
}

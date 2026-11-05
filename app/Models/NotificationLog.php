<?php

namespace App\Models;

use Database\Factories\NotificationLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'notifiable_context_type', 'notifiable_context_id', 'type', 'channel', 'destination', 'status', 'subject', 'body', 'error_message', 'sent_at'])]
class NotificationLog extends Model
{
    /** @use HasFactory<NotificationLogFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }
}

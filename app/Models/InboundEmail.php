<?php

namespace App\Models;

use Database\Factories\InboundEmailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['provider_message_id', 'sender_email', 'recipient_emails', 'subject', 'body_excerpt', 'client_id', 'meeting_id', 'status', 'received_at'])]
class InboundEmail extends Model
{
    /** @use HasFactory<InboundEmailFactory> */
    use HasFactory;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    protected function casts(): array
    {
        return ['recipient_emails' => 'array', 'received_at' => 'datetime'];
    }
}

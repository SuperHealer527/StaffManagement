<?php

namespace App\Models;

use Database\Factories\PhoneLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['called_at', 'caller_name', 'company', 'phone', 'purpose', 'content', 'recipient_id', 'received_by', 'status'])]
class PhoneLog extends Model
{
    /** @use HasFactory<PhoneLogFactory> */
    use HasFactory;

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    protected function casts(): array
    {
        return ['called_at' => 'datetime'];
    }
}

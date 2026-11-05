<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'type', 'department', 'phone', 'postal_code', 'address', 'notes', 'proposal_status', 'next_action_date', 'proposal_notes', 'sales_owner_id', 'proposal_status_changed_at'])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function salesOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_owner_id');
    }

    protected function casts(): array
    {
        return ['next_action_date' => 'date', 'proposal_status_changed_at' => 'datetime'];
    }
}

<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['client_id', 'business_name', 'name', 'details', 'work_content', 'event_date', 'meeting_time', 'start_time', 'end_time', 'dismissal_time', 'required_staff', 'meeting_place', 'venue', 'items', 'emergency_contacts', 'notes', 'status', 'report_required', 'created_by'])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function reportSubmissions(): HasMany
    {
        return $this->hasMany(ReportSubmission::class);
    }

    protected function casts(): array
    {
        return ['event_date' => 'date', 'items' => 'array', 'emergency_contacts' => 'array', 'report_required' => 'boolean'];
    }
}

<?php

namespace App\Models;

use Database\Factories\AssignmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_id', 'user_id', 'status', 'special_notes', 'fee', 'transportation', 'reimbursement', 'withholding_tax', 'payment_status', 'payment_source', 'paid_at', 'seven_day_confirmation', 'same_day_confirmation', 'is_day_leader', 'is_report_representative', 'requested_at', 'responded_at'])]
class Assignment extends Model
{
    /** @use HasFactory<AssignmentFactory> */
    use HasFactory;

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalPaymentAttribute(): int
    {
        return $this->fee + $this->transportation + $this->reimbursement - $this->withholding_tax;
    }

    protected function casts(): array
    {
        return ['paid_at' => 'date', 'is_day_leader' => 'boolean', 'is_report_representative' => 'boolean', 'requested_at' => 'datetime', 'responded_at' => 'datetime'];
    }
}

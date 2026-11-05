<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /** @param array<string, mixed>|null $oldValues @param array<string, mixed>|null $newValues */
    public function record(Model $auditable, string $action, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        return AuditLog::query()->create([
            'user_id' => request()->user()?->id,
            'auditable_type' => $auditable->getMorphClass(),
            'auditable_id' => $auditable->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

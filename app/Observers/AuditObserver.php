<?php

namespace App\Observers;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    /** @var list<string> */
    private const SENSITIVE_KEYS = ['password', 'remember_token', 'bank_details', 'token_hash'];

    public function created(Model $model): void
    {
        $this->record($model, 'created', null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = Arr::except($model->getChanges(), ['updated_at']);
        if ($changes === []) {
            return;
        }

        $oldValues = [];
        foreach (array_keys($changes) as $key) {
            $oldValues[$key] = $model->getOriginal($key);
        }

        $this->record($model, 'updated', $oldValues, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', $model->getOriginal(), null);
    }

    /** @param array<string, mixed>|null $oldValues @param array<string, mixed>|null $newValues */
    private function record(Model $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        if (! Auth::check()) {
            return;
        }

        app(AuditService::class)->record(
            $model,
            $action,
            $this->redact($oldValues),
            $this->redact($newValues),
        );
    }

    /** @param array<string, mixed>|null $values @return array<string, mixed>|null */
    private function redact(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        foreach (self::SENSITIVE_KEYS as $key) {
            if (array_key_exists($key, $values)) {
                $values[$key] = '[REDACTED]';
            }
        }

        return $values;
    }
}

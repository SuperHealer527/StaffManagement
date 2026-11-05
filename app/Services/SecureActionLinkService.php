<?php

namespace App\Services;

use App\Models\SecureActionToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SecureActionLinkService
{
    public function create(User $user, string $purpose, ?EloquentModel $actionable = null, int $expiresInHours = 168): string
    {
        return $this->createUntil($user, $purpose, $actionable, now()->addHours($expiresInHours));
    }

    public function createUntil(User $user, string $purpose, ?EloquentModel $actionable, \DateTimeInterface $expiresAt): string
    {
        $existingTokens = SecureActionToken::query()
            ->where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNull('used_at');

        if ($actionable !== null) {
            $existingTokens
                ->where('actionable_type', $actionable->getMorphClass())
                ->where('actionable_id', $actionable->getKey());
        } else {
            $existingTokens->whereNull('actionable_type')->whereNull('actionable_id');
        }

        $existingTokens->update(['used_at' => now()]);

        $plainToken = Str::random(64);

        SecureActionToken::query()->create([
            'user_id' => $user->id,
            'actionable_type' => $actionable?->getMorphClass(),
            'actionable_id' => $actionable?->getKey(),
            'purpose' => $purpose,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => Carbon::instance($expiresAt),
        ]);

        return $plainToken;
    }
}

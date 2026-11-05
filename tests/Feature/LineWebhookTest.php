<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\SecureActionToken;
use App\Services\NotificationDeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LineWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_line_postback_records_assignment_response(): void
    {
        config(['services.line.channel_secret' => 'webhook-secret']);
        $assignment = Assignment::factory()->create(['status' => 'requested']);
        SecureActionToken::query()->create([
            'user_id' => $assignment->user_id,
            'actionable_type' => Assignment::class,
            'actionable_id' => $assignment->id,
            'purpose' => 'assignment_response',
            'token_hash' => hash('sha256', 'line-action-token'),
            'expires_at' => now()->addDay(),
        ]);
        $payload = json_encode(['events' => [[
            'type' => 'postback',
            'postback' => ['data' => 'token=line-action-token&response=accepted'],
        ]]], JSON_THROW_ON_ERROR);

        $this->call('POST', '/api/webhooks/line', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_LINE_SIGNATURE' => $this->signature($payload),
        ], $payload)->assertNoContent();

        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $assignment->id, 'action' => 'line_assignment_response']);
    }

    public function test_line_push_uses_postback_buttons_for_assignment_request(): void
    {
        config([
            'services.line.channel_access_token' => 'channel-token',
        ]);
        Http::fake(['api.line.me/*' => Http::response([], 200)]);
        $assignment = Assignment::factory()->create();
        $assignment->user->update([
            'notification_method' => 'line',
            'line_user_id' => 'line-user-id',
        ]);

        app(NotificationDeliveryService::class)->deliver(
            $assignment->user->fresh(),
            $assignment,
            'assignment_request',
            'お仕事のご依頼',
            'イベントのお仕事をお願いします。',
            'https://example.test/action/plain-token',
            '回答する',
        );

        Http::assertSent(function ($request): bool {
            $actions = $request['messages'][0]['template']['actions'];

            return $request['messages'][0]['type'] === 'template'
                && $actions[0]['type'] === 'postback'
                && str_contains($actions[0]['data'], 'response=accepted')
                && $actions[3]['type'] === 'uri';
        });
    }

    public function test_line_webhook_rejects_invalid_signature(): void
    {
        config(['services.line.channel_secret' => 'webhook-secret']);

        $this->withHeader('X-Line-Signature', 'invalid')
            ->postJson('/api/webhooks/line', ['events' => []])
            ->assertForbidden();
    }

    private function signature(string $payload): string
    {
        return base64_encode(hash_hmac('sha256', $payload, 'webhook-secret', true));
    }
}

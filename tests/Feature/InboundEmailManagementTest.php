<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientContact;
use App\Models\InboundEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class InboundEmailManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_webhook_matches_client_contact_and_is_idempotent(): void
    {
        config(['services.inbound_email.signing_secret' => 'email-secret']);
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $client = Client::factory()->create(['sales_owner_id' => $employee->id]);
        ClientContact::query()->create([
            'client_id' => $client->id,
            'name' => '取引先担当者',
            'email' => 'contact@example.test',
        ]);
        $payload = json_encode([
            'message_id' => 'provider-message-1',
            'sender_email' => 'sales@staffmanagement.test',
            'recipient_emails' => ['contact@example.test'],
            'subject' => '次回イベントのお見積り',
            'text' => '来月のイベントについて見積りをお願いします。',
            'received_at' => now()->toIso8601String(),
        ], JSON_THROW_ON_ERROR);

        $this->postSignedWebhook($payload)->assertCreated()->assertJson(['status' => 'matched']);
        $this->postSignedWebhook($payload)->assertOk()->assertJson(['status' => 'matched']);

        $this->assertDatabaseCount('inbound_emails', 1);
        $this->assertDatabaseCount('meetings', 1);
        $this->assertDatabaseHas('meetings', [
            'client_id' => $client->id,
            'method' => 'email',
            'content' => "次回イベントのお見積り\n来月のイベントについて見積りをお願いします。",
        ]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'inbound_email_received']);
    }

    public function test_employee_can_assign_an_unmatched_email_to_a_client(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $client = Client::factory()->create();
        $inboundEmail = InboundEmail::factory()->create([
            'subject' => '依頼先不明のメール',
            'sender_email' => 'unknown@example.test',
            'body_excerpt' => '担当者を確認してください。',
        ]);

        $this->actingAs($employee)->get(route('meetings.index'))
            ->assertOk()
            ->assertSee('依頼先不明のメール')
            ->assertSee(route('meetings.inbound.assign', $inboundEmail));

        $this->actingAs($employee)->post(route('meetings.inbound.assign', $inboundEmail), [
            'client_id' => $client->id,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('inbound_emails', [
            'id' => $inboundEmail->id,
            'client_id' => $client->id,
            'status' => 'matched',
        ]);
        $this->assertDatabaseHas('meetings', [
            'client_id' => $client->id,
            'recorded_by' => $employee->id,
            'method' => 'email',
        ]);
    }

    public function test_webhook_rejects_invalid_signature_and_only_employees_can_assign(): void
    {
        config(['services.inbound_email.signing_secret' => 'email-secret']);
        $this->withHeader('X-Inbound-Signature', 'invalid')
            ->postJson('/api/webhooks/inbound-email', ['message_id' => 'invalid'])
            ->assertForbidden();

        $semiEmployee = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $inboundEmail = InboundEmail::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($semiEmployee)->post(route('meetings.inbound.assign', $inboundEmail), [
            'client_id' => $client->id,
        ])->assertForbidden();
    }

    private function postSignedWebhook(string $payload): TestResponse
    {
        $signature = base64_encode(hash_hmac('sha256', $payload, 'email-secret', true));

        return $this->call('POST', '/api/webhooks/inbound-email', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_INBOUND_SIGNATURE' => $signature,
        ], $payload);
    }
}

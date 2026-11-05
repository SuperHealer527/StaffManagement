<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientContact;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_management_user_searches_clients_by_client_or_contact_name(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $matchingClient = Client::factory()->create(['name' => '検索対象会社']);
        ClientContact::factory()->create(['client_id' => $matchingClient->id, 'name' => '検索担当者']);
        Client::factory()->create(['name' => '表示されない会社']);

        $this->actingAs($employee)->get(route('clients.index', ['q' => '検索担当者']))
            ->assertOk()
            ->assertSee('検索対象会社')
            ->assertDontSee('表示されない会社');
    }

    public function test_employee_creates_client_with_contacts_and_proposal_data(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $response = $this->actingAs($employee)->post(route('clients.store'), [
            'name' => '新規法人',
            'type' => 'corporation',
            'proposal_status' => 'proposing',
            'next_action_date' => '2026-11-20',
            'sales_owner_id' => $employee->id,
            'contacts' => [[
                'name' => '担当 花子',
                'position' => '課長',
                'mobile' => '090-1111-2222',
                'email' => 'contact@example.com',
                'is_retired' => false,
            ]],
        ]);

        $client = Client::query()->where('name', '新規法人')->firstOrFail();
        $response->assertRedirect(route('clients.edit', $client));
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'proposal_status' => 'proposing',
            'sales_owner_id' => $employee->id,
        ]);
        $this->assertDatabaseHas('client_contacts', [
            'client_id' => $client->id,
            'name' => '担当 花子',
        ]);
    }

    public function test_proposal_status_change_records_change_date_and_updates_contacts(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $client = Client::factory()->create(['proposal_status' => 'uncontacted']);
        $contact = ClientContact::factory()->create(['client_id' => $client->id, 'name' => '旧担当者']);

        $this->actingAs($employee)->patch(route('clients.update', $client), [
            'name' => $client->name,
            'type' => $client->type,
            'proposal_status' => 'approaching',
            'contacts' => [[
                'id' => $contact->id,
                'name' => '新担当者',
                'is_retired' => true,
            ]],
        ])->assertRedirect();

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'proposal_status' => 'approaching']);
        $this->assertNotNull($client->fresh()->proposal_status_changed_at);
        $this->assertDatabaseHas('client_contacts', [
            'id' => $contact->id,
            'name' => '新担当者',
            'is_retired' => true,
        ]);
    }

    public function test_semi_employee_can_search_but_cannot_open_client_edit_page(): void
    {
        $semiEmployee = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $client = Client::factory()->create();

        $this->actingAs($semiEmployee)->get(route('clients.index'))->assertOk();
        $this->actingAs($semiEmployee)->get(route('clients.edit', $client))->assertForbidden();
    }
}

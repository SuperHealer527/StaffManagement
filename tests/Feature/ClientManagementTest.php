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

        $this->actingAs($employee)->get(route('clients.prospects', ['q' => '検索担当者']))
            ->assertOk()
            ->assertSee('検索対象会社')
            ->assertDontSee('表示されない会社');
    }

    public function test_client_list_displays_pagination_for_more_than_twenty_five_clients(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        Client::factory()->count(26)->create(['proposal_status' => 'won']);

        $this->actingAs($employee)->get(route('clients.index'))
            ->assertOk()
            ->assertViewHas('clients', fn ($clients): bool => $clients->total() === 26 && $clients->lastPage() === 2)
            ->assertSee('page=2', false);
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
        $this->assertNotNull($client->proposal_status_changed_at);
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

    public function test_client_detail_links_to_event_creation_with_the_client_selected(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $client = Client::factory()->create(['name' => 'イベント登録元の依頼先', 'proposal_status' => 'won']);

        $this->actingAs($employee)->get(route('clients.edit', $client))
            ->assertOk()
            ->assertSee($client->name)
            ->assertSee(route('events.create', ['client_id' => $client->id]), false)
            ->assertDontSee('log@example.co.jp');
    }

    public function test_sales_home_uses_database_status_counts_and_current_year_outcomes(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        Client::factory()->count(2)->create(['proposal_status' => 'proposing']);
        Client::factory()->create(['proposal_status' => 'approaching']);
        Client::factory()->create([
            'proposal_status' => 'won',
            'proposal_status_changed_at' => now()->startOfYear()->addMonth(),
        ]);
        Client::factory()->create([
            'proposal_status' => 'won',
            'proposal_status_changed_at' => now()->subYear(),
        ]);
        Client::factory()->create([
            'proposal_status' => 'lost',
            'proposal_status_changed_at' => null,
            'created_at' => now()->startOfYear()->addMonths(2),
        ]);

        $this->actingAs($employee)->get(route('sales.home'))
            ->assertOk()
            ->assertViewHas('salesStatusCounts', fn ($counts): bool => $counts->all() === [
                'uncontacted' => 0,
                'approaching' => 1,
                'proposing' => 2,
                'won' => 2,
                'lost' => 1,
                'on_hold' => 0,
            ])
            ->assertViewHas('yearlyOutcomeCounts', fn ($counts): bool => $counts->all() === [
                'won' => 1,
                'lost' => 1,
            ])
            ->assertSee(route('sales.proposals'), false)
            ->assertSee(route('meetings.index'), false);
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
        $client = Client::factory()->create(['proposal_status' => 'won']);

        $this->actingAs($semiEmployee)->get(route('clients.index'))->assertOk();
        $this->actingAs($semiEmployee)->get(route('clients.edit', $client))->assertForbidden();
    }

    public function test_sales_prospects_and_event_destinations_are_separate(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $prospect = Client::factory()->create(['name' => '営業中の顧客', 'proposal_status' => 'proposing']);
        $destination = Client::factory()->create(['name' => '受注済み依頼先', 'proposal_status' => 'won']);

        $this->actingAs($employee)->get(route('clients.prospects'))
            ->assertOk()
            ->assertSee($prospect->name)
            ->assertDontSee($destination->name);

        $this->actingAs($employee)->get(route('clients.index'))
            ->assertOk()
            ->assertSee($destination->name)
            ->assertDontSee($prospect->name);

        $this->actingAs($employee)->get(route('events.create'))
            ->assertOk()
            ->assertSee($destination->name)
            ->assertDontSee($prospect->name);
    }
}

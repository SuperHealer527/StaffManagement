<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_sees_dashboard_data(): void
    {
        $user = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        Event::factory()->create(['name' => '検証用イベント']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()->assertSee('社員のホーム')->assertSee('検証用イベント');
    }

    public function test_dashboard_renders_confirmation_lists_without_lazy_loading(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $event = Event::factory()->create(['event_date' => today()]);
        $assignment = Assignment::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'same_day_confirmation' => 'not_sent',
            'seven_day_confirmation' => 'not_sent',
        ]);

        $this->actingAs($employee)->get('/dashboard')
            ->assertOk()
            ->assertSee($assignment->user->name)
            ->assertSee($event->name);
    }

    public function test_unknown_screen_returns_not_found(): void
    {
        $user = User::factory()->create(['account_status' => 'active']);

        $this->actingAs($user)->get('/screens/not-real')->assertNotFound();
    }

    public function test_all_client_screen_routes_render(): void
    {
        $this->seed();
        $user = User::query()->where('email', 'admin@staffmanagement.test')->firstOrFail();
        $screens = ['staff-home', 'semi-home', 'assignments', 'event-form', 'event-copy', 'event-detail', 'assignment-response', 'attendance-confirmed', 'report-submit', 'payments', 'profit-loss', 'payment-statement', 'profile', 'reports', 'clients', 'client-detail', 'events', 'staff', 'staff-detail', 'shortages', 'accounts', 'notifications', 'my-requests', 'first-password', 'mobile-areas', 'sales-home', 'proposal-board', 'meeting-history', 'interviews', 'interview-select', 'phone-log', 'line-connect', 'temporary-bank'];

        foreach ($screens as $screen) {
            $this->actingAs($user)->get('/screens/'.$screen)->assertOk();
        }
    }
}

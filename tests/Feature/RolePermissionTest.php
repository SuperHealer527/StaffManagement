<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_semi_employee_can_open_management_pages_but_not_employee_only_pages(): void
    {
        $semiEmployee = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $event = Event::factory()->create();
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        foreach ([
            route('dashboard'), route('clients.index'), route('events.index'), route('events.show', $event),
            route('events.assignments.index', $event), route('shortages.index'), route('reports.index'),
            route('staff.index'), route('staff.show', $staff), route('phone-logs.index'),
            route('profile.edit'), route('my.home'), route('my.requests'), route('my.payments'),
        ] as $url) {
            $this->actingAs($semiEmployee)->get($url)->assertSuccessful();
        }

        foreach ([
            route('sales.home'), route('sales.proposals'), route('clients.create'), route('events.create'),
            route('finance.payments'), route('finance.profit'), route('interviews.index'),
            route('meetings.index'), route('notifications.index'), route('screens.show', 'accounts'),
        ] as $url) {
            $this->actingAs($semiEmployee)->get($url)->assertForbidden();
        }
    }

    public function test_staff_and_trainer_can_only_open_self_service_and_assigned_event_pages(): void
    {
        foreach (['staff', 'trainer'] as $role) {
            $user = User::factory()->create(['role' => $role, 'account_status' => 'active']);
            $assignedEvent = Event::factory()->create();
            Assignment::factory()->create([
                'event_id' => $assignedEvent->id,
                'user_id' => $user->id,
                'status' => 'confirmed',
            ]);
            $unassignedEvent = Event::factory()->create();

            foreach ([
                route('dashboard'), route('my.home'), route('my.requests'), route('my.payments'),
                route('profile.edit'), route('events.show', $assignedEvent),
            ] as $url) {
                $this->actingAs($user)->get($url)->assertSuccessful();
            }

            foreach ([
                route('clients.index'), route('events.index'), route('events.show', $unassignedEvent),
                route('shortages.index'), route('reports.index'), route('staff.index'),
                route('phone-logs.index'), route('finance.payments'), route('interviews.index'),
                route('notifications.index'), route('screens.show', 'assignment-response'),
            ] as $url) {
                $this->actingAs($user)->get($url)->assertForbidden();
            }
        }
    }

    public function test_temporary_worker_cannot_open_any_authenticated_page(): void
    {
        $temporary = User::factory()->create(['role' => 'temporary', 'account_status' => 'no_login']);

        $this->actingAs($temporary)->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}

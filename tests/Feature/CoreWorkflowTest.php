<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CoreWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_employee_can_create_client(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);

        $response = $this->actingAs($employee)->post('/clients', ['name' => '新規依頼先', 'type' => 'corporation']);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', ['name' => '新規依頼先']);
    }

    public function test_staff_cannot_create_client(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $response = $this->actingAs($staff)->post('/clients', ['name' => '不正な依頼先', 'type' => 'corporation']);

        $response->assertForbidden();
        $this->assertDatabaseMissing('clients', ['name' => '不正な依頼先']);
    }

    public function test_semi_employee_can_change_assignment_status_but_not_fee(): void
    {
        $semiEmployee = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $assignment = Assignment::factory()->create(['status' => 'accepted', 'fee' => 9000]);

        $response = $this->actingAs($semiEmployee)->patch('/assignments/'.$assignment->id, ['status' => 'confirmed', 'fee' => 50000]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'status' => 'confirmed', 'fee' => 9000]);
    }
}

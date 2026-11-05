<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_management_user_can_filter_staff_list(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        User::factory()->create(['name' => '対象 学生', 'role' => 'staff', 'account_status' => 'active', 'is_student' => true]);
        User::factory()->create(['name' => '対象 社員', 'role' => 'employee', 'account_status' => 'active', 'is_student' => false]);

        $this->actingAs($employee)->get(route('staff.index', ['keyword' => '対象', 'role' => 'staff', 'student' => 1]))
            ->assertOk()
            ->assertSee('対象 学生')
            ->assertDontSee('対象 社員');
    }

    public function test_semi_employee_cannot_see_sensitive_staff_fields(): void
    {
        $semi = User::factory()->create(['role' => 'semi_employee', 'account_status' => 'active']);
        $staff = User::factory()->create([
            'role' => 'staff',
            'account_status' => 'active',
            'address' => '秘密住所ABC',
            'base_fee' => 12345,
            'bank_details' => ['bank_name' => '秘密銀行XYZ', 'account_number' => '9876543'],
        ]);

        $this->actingAs($semi)->get(route('staff.show', $staff))
            ->assertOk()
            ->assertSee($staff->name)
            ->assertDontSee('秘密住所ABC')
            ->assertDontSee('秘密銀行XYZ')
            ->assertDontSee('12,345');

        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $staff->id, 'action' => 'view_staff_profile']);
    }

    public function test_employee_can_see_and_update_management_fields(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $staff = User::factory()->create([
            'role' => 'staff',
            'account_status' => 'active',
            'address' => '社員閲覧住所',
            'bank_details' => ['bank_name' => '確認銀行', 'account_number' => '1234567'],
        ]);

        $this->actingAs($employee)->get(route('staff.show', $staff))
            ->assertOk()
            ->assertSee('社員閲覧住所')
            ->assertSee('確認銀行');

        $this->actingAs($employee)->patch(route('staff.update', $staff), [
            'base_fee' => 15000,
            'account_status' => 'suspended',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $staff->id, 'base_fee' => 15000, 'account_status' => 'suspended']);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $staff->id, 'action' => 'update_staff_management']);
    }

    public function test_user_updates_profile_bank_and_availability(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($staff)->patch(route('profile.update'), [
            'name' => '更新 花子',
            'gender' => 'female',
            'birth_date' => '2000-01-02',
            'is_student' => 1,
            'nearest_station' => '池袋駅',
            'postal_code' => '171-0000',
            'address' => '東京都豊島区',
            'phone' => '090-1111-2222',
            'email' => $staff->email,
            'notification_method' => 'email',
            'has_car' => 0,
            'bank_name' => 'テスト銀行',
            'branch_name' => '本店',
            'account_type' => 'ordinary',
            'account_number' => '1234567',
            'account_name' => 'コウシン ハナコ',
            'availabilities' => ['1|8時前' => 'available', '2|8時前' => 'unavailable'],
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $staff->id, 'name' => '更新 花子', 'is_student' => true]);
        $this->assertDatabaseHas('availabilities', ['user_id' => $staff->id, 'weekday' => 1, 'time_slot' => '8時前', 'availability' => 'available']);
        $this->assertSame('テスト銀行', $staff->fresh()->bank_details['bank_name']);
    }

    public function test_qualification_and_contract_files_are_private_and_authorized(): void
    {
        Storage::fake('local');
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);

        $this->actingAs($staff)->post(route('staff.qualifications.store', $staff), [
            'name' => '防災士',
            'expires_at' => today()->addYear()->format('Y-m-d'),
            'certificate' => UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf'),
        ])->assertRedirect();
        $qualification = $staff->qualifications()->firstOrFail();
        Storage::assertExists($qualification->certificate_path);

        $this->actingAs($employee)->post(route('staff.contract.store', $staff), [
            'contract' => UploadedFile::fake()->create('contract.pdf', 100, 'application/pdf'),
        ])->assertRedirect();
        Storage::assertExists($staff->fresh()->contract_path);

        $this->actingAs($staff)->get(route('staff.contract.file', $staff))->assertOk();
        $other = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $this->actingAs($other)->get(route('staff.contract.file', $staff))->assertForbidden();
    }

    public function test_staff_detail_displays_real_assignment_history(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $staff = User::factory()->create(['role' => 'staff', 'account_status' => 'active']);
        $event = Event::factory()->create(['name' => '勤務履歴イベント']);
        Assignment::factory()->create(['event_id' => $event->id, 'user_id' => $staff->id, 'status' => 'completed']);

        $this->actingAs($employee)->get(route('staff.show', $staff))
            ->assertOk()
            ->assertSee('勤務履歴イベント')
            ->assertSee('実施済み');
    }
}

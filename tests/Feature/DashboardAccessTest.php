<?php

namespace Tests\Feature;

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

    public function test_authenticated_employee_sees_the_exact_client_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        Event::factory()->create(['name' => '秋季健康フェア', 'event_date' => today()->addWeek()]);
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('ホーム')
            ->assertSee('秋季健康フェア')
            ->assertSee('人数不足のイベント');
    }

    public function test_dashboard_renders_the_client_confirmation_sections_without_lazy_loading(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'account_status' => 'active']);
        $this->actingAs($employee)->get('/dashboard')
            ->assertOk()
            ->assertSee('当日確認が済んでいない人')
            ->assertSee('7日前確認が済んでいない人')
            ->assertSee('回答が来ていない依頼');
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

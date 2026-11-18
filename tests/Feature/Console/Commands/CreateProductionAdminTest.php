<?php

namespace Tests\Feature\Console\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateProductionAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_active_administrator_with_a_hashed_password(): void
    {
        $this->artisan('staff-management:create-admin')
            ->expectsQuestion('管理者名', '管理 太郎')
            ->expectsQuestion('メールアドレス', 'ADMIN@EXAMPLE.COM')
            ->expectsQuestion('パスワード（12文字以上）', 'secure-password-123')
            ->expectsQuestion('パスワード（確認）', 'secure-password-123')
            ->expectsOutput('管理者アカウント admin@example.com を作成しました。')
            ->assertSuccessful();

        $administrator = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('管理 太郎', $administrator->name);
        $this->assertSame('employee', $administrator->role);
        $this->assertSame('active', $administrator->account_status);
        $this->assertNotNull($administrator->email_verified_at);
        $this->assertTrue(Hash::check('secure-password-123', $administrator->password));
        $this->assertNotSame('secure-password-123', $administrator->password);
    }

    public function test_it_refuses_to_replace_an_existing_account(): void
    {
        $existingAdministrator = User::factory()->create([
            'email' => 'admin@example.com',
            'name' => '既存管理者',
        ]);

        $this->artisan('staff-management:create-admin')
            ->expectsQuestion('管理者名', '新規管理者')
            ->expectsQuestion('メールアドレス', 'ADMIN@EXAMPLE.COM')
            ->expectsOutput('このメールアドレスは既に登録されています。')
            ->assertFailed();

        $this->assertSame(1, User::query()->where('email', 'admin@example.com')->count());
        $this->assertSame('既存管理者', $existingAdministrator->fresh()->name);
    }

    public function test_it_does_not_create_an_account_when_password_confirmation_differs(): void
    {
        $this->artisan('staff-management:create-admin')
            ->expectsQuestion('管理者名', '管理 太郎')
            ->expectsQuestion('メールアドレス', 'admin@example.com')
            ->expectsQuestion('パスワード（12文字以上）', 'secure-password-123')
            ->expectsQuestion('パスワード（確認）', 'different-password-123')
            ->expectsOutput('確認用パスワードが一致しません。')
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
    }
}

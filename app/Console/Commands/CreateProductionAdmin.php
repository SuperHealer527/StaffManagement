<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('staff-management:create-admin')]
#[Description('本番環境の初期管理者アカウントを安全に作成します')]
class CreateProductionAdmin extends Command
{
    public function handle(): int
    {
        $name = trim((string) $this->ask('管理者名'));

        if ($name === '') {
            $this->error('管理者名は必須です。');

            return self::FAILURE;
        }

        $email = Str::lower(trim((string) $this->ask('メールアドレス')));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('有効なメールアドレスを入力してください。');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('このメールアドレスは既に登録されています。');

            return self::FAILURE;
        }

        $password = (string) $this->secret('パスワード（12文字以上）');

        if (mb_strlen($password) < 12) {
            $this->error('パスワードは12文字以上で入力してください。');

            return self::FAILURE;
        }

        $passwordConfirmation = (string) $this->secret('パスワード（確認）');

        if (! hash_equals($password, $passwordConfirmation)) {
            $this->error('確認用パスワードが一致しません。');

            return self::FAILURE;
        }

        $administrator = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'employee',
            'account_status' => 'active',
        ]);

        $administrator->forceFill(['email_verified_at' => now()])->save();

        $this->info("管理者アカウント {$email} を作成しました。");

        return self::SUCCESS;
    }
}

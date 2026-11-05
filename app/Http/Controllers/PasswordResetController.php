<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordResetLinkRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function send(SendPasswordResetLinkRequest $request): RedirectResponse
    {
        $user = User::query()
            ->where('email', $request->validated('email'))
            ->where('account_status', 'active')
            ->where('role', '!=', 'temporary')
            ->first();

        if ($user !== null) {
            Password::sendResetLink(['email' => $user->email]);
        }

        return back()->with('status', '入力されたメールアドレスが利用中のアカウントに登録されている場合、再設定用メールを送信しました。');
    }

    public function edit(string $token): View
    {
        return view('static-pages.SetPassword', [
            'passwordResetToken' => $token,
            'email' => request()->string('email')->toString(),
        ]);
    }

    public function update(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->safe()->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, string $password): void {
                abort_unless($user->account_status === 'active' && $user->role !== 'temporary', 422);

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PasswordReset) {
            return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
        }

        return redirect()->route('login')->with('status', 'パスワードを再設定しました。新しいパスワードでログインしてください。');
    }
}

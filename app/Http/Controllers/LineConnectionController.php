<?php

namespace App\Http\Controllers;

use App\Models\SecureActionToken;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class LineConnectionController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function start(Request $request): RedirectResponse
    {
        return $this->authorizationRedirect($request, $request->user()->id, null);
    }

    public function startPublic(Request $request, string $token): RedirectResponse
    {
        $actionToken = SecureActionToken::query()
            ->where('token_hash', hash('sha256', $token))
            ->where('purpose', 'line_connect')
            ->firstOrFail();
        abort_unless($actionToken->isUsable() && $actionToken->user_id !== null, 410);

        return $this->authorizationRedirect($request, $actionToken->user_id, $actionToken->id);
    }

    public function callback(Request $request): RedirectResponse
    {
        $connection = $request->session()->pull('line_connection');
        abort_unless(is_array($connection) && isset($connection['state'], $connection['user_id']), 419);
        abort_unless(is_string($request->query('state')) && hash_equals($connection['state'], $request->query('state')), 419);

        if (isset($connection['action_token_id'])) {
            $actionToken = SecureActionToken::query()->find($connection['action_token_id']);
            if ($actionToken === null || ! $actionToken->isUsable()) {
                return $this->failureRedirect($connection, '連携リンクの操作時間が終了しました。社員に再送を依頼してください。');
            }
        }

        if ($request->filled('error')) {
            return $this->failureRedirect($connection, 'LINE連携がキャンセルされました。');
        }

        try {
            $tokenResponse = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
                'grant_type' => 'authorization_code',
                'code' => $request->string('code')->toString(),
                'redirect_uri' => route('line.connect.callback'),
                'client_id' => config('services.line.login_channel_id'),
                'client_secret' => config('services.line.login_channel_secret'),
            ])->throw()->json();
            $profile = Http::withToken($tokenResponse['access_token'])
                ->get('https://api.line.me/v2/profile')
                ->throw()
                ->json();

            $alreadyLinked = User::query()
                ->where('line_user_id', $profile['userId'])
                ->where('id', '!=', $connection['user_id'])
                ->exists();
            if ($alreadyLinked) {
                return $this->failureRedirect($connection, 'このLINEアカウントは別の利用者に連携済みです。');
            }

            $user = User::query()->findOrFail($connection['user_id']);
            $user->update([
                'line_user_id' => $profile['userId'],
                'line_linked_at' => now(),
                'notification_method' => 'line',
            ]);
            $this->auditService->record($user, 'connect_line_account', null, ['line_linked_at' => $user->line_linked_at]);
            if (isset($connection['action_token_id'])) {
                SecureActionToken::query()->whereKey($connection['action_token_id'])->update(['used_at' => now()]);
            }
        } catch (Throwable $exception) {
            report($exception);

            return $this->failureRedirect($connection, 'LINEと連携できませんでした。設定を確認してもう一度お試しください。');
        }

        if (isset($connection['action_token_id'])) {
            return redirect()->route('public-actions.complete', $connection['plain_token'])->with('status', 'LINE連携が完了しました。');
        }

        return redirect()->route('profile.edit')->with('status', 'LINEと連携しました。');
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->update([
            'line_user_id' => null,
            'line_linked_at' => null,
            'notification_method' => 'email',
        ]);
        $this->auditService->record($user, 'disconnect_line_account');

        return back()->with('status', 'LINE連携を解除しました。今後はメールで通知します。');
    }

    private function authorizationRedirect(Request $request, int $userId, ?int $actionTokenId): RedirectResponse
    {
        if (blank(config('services.line.login_channel_id')) || blank(config('services.line.login_channel_secret'))) {
            return back()->withErrors(['line' => 'LINE Loginの設定がまだ完了していません。管理者に確認してください。']);
        }

        $state = Str::random(64);
        $request->session()->put('line_connection', [
            'state' => $state,
            'user_id' => $userId,
            'action_token_id' => $actionTokenId,
            'plain_token' => $actionTokenId === null ? null : (string) $request->route('token'),
        ]);

        return redirect()->away('https://access.line.me/oauth2/v2.1/authorize?'.http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.line.login_channel_id'),
            'redirect_uri' => route('line.connect.callback'),
            'state' => $state,
            'scope' => 'profile openid',
        ]));
    }

    /** @param array<string, mixed> $connection */
    private function failureRedirect(array $connection, string $message): RedirectResponse
    {
        if (isset($connection['action_token_id'], $connection['plain_token'])) {
            return redirect()->route('public-actions.show', $connection['plain_token'])->withErrors(['line' => $message]);
        }

        return redirect()->route('profile.edit')->withErrors(['line' => $message]);
    }
}

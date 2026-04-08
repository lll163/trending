<?php

namespace App\Http\Controllers\Auth;

// AI-GEN-BEGIN
use App\Http\Controllers\Controller;
use App\Models\OauthAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

/**
 * GitHub OAuth：已登录用户绑定 GitHub 账户。
 */
class GitHubController extends Controller
{
    /**
     * 跳转至 GitHub 授权页。
     */
    public function redirect(): RedirectResponse|SymfonyRedirect
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * GitHub 回调：写入 oauth_accounts 并标记用户已绑定。
     */
    public function callback(): RedirectResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return redirect()->route('login');
        }

        $githubUser = Socialite::driver('github')->user();
        $providerUserId = (string) $githubUser->getId();
        $login = $githubUser->getNickname() ?? $githubUser->getName() ?? '';

        $boundElsewhere = OauthAccount::query()
            ->where('provider', 'github')
            ->where('provider_user_id', $providerUserId)
            ->where('user_id', '!=', $user->id)
            ->exists();

        if ($boundElsewhere) {
            return redirect()
                ->route('profile.edit')
                ->withErrors(['github' => '该 GitHub 账户已被其他用户绑定。']);
        }

        DB::transaction(function () use ($user, $providerUserId, $login, $githubUser): void {
            OauthAccount::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider' => 'github',
                ],
                [
                    'provider_user_id' => $providerUserId,
                    'provider_login' => $login,
                    'access_token' => $githubUser->token,
                    'refresh_token' => $githubUser->refreshToken,
                ]
            );

            $user->forceFill(['github_bound_at' => now()])->save();
        });

        return redirect()
            ->route('profile.edit')
            ->with('status', 'github-bound');
    }
}
// AI-GEN-END
